<?php
/**
 * 前台结账
 *
 */
class  Andyou_Page_Checkout  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Checkout';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得数据列表
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
        //获得所有会员类型
        $output->memberCate = Helper_Member::getMemberCatePairs();
		//获得所有的员工
        $output->staffArr  = Helper_Staff::getStaffPairs();
        
        //获得所有配置
        $output->sysOptions = Helper_Option::getAllOptions();        
        $output->scoreRatio = !empty($output->sysOptions["ScoreRatio"]) ? $output->sysOptions["ScoreRatio"]["value"] : 0;
        
		$output->setTemplate('Checkout');
	}
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        $billInfo    = $input->post("bill"); 
        $itemIdArr   = $input->post("item_id");//所有产品ID 
        $itemDiscArr = $input->post("item_disc");//所有产品折扣
        $itemNumArr  = $input->post("item_num");//所有产品产品数量
        $staffid     = (int)$input->post("staffid");//员工
        
        //----------------------
        //获得会员信息
        //----------------------
        $memberId    = (int)$input->post("memberId");//会员ID
        $memberInfo  = Helper_Member::getMemberInfo(array("id"=>$memberId));
        if($memberInfo){ 
            $memberScore = $memberInfo["score"];   //会员的积分
            $memberCard  = $memberInfo["balance"]; //会员的卡内余额
        }else{//如果没有这个会员
            $memberScore = $memberCard = 0;
            $billInfo["bill_member_card"]  = 0;//传递过来的会员扣费，强制失效掉
            $billInfo["bill_member_score"] = 0;
        }
        
        //----------------------
        //获得说有的商品信息
        //----------------------
        $proInfoArr = array();
        $sumPrice   = 0;
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $num = (int)$itemNumArr[$idx];
               $price     = $num * $proInfo["oprice"] * $itemDiscArr[$idx];
               $sumPrice += $price;
               $proInfoArr[] = array(
                   'proId'      => $pid,
                   'num'        => $num,
                   'discount'   => $itemDiscArr[$idx],
                   'price'      => $price,
                   'staffid'    => $staffid,
               );
            }
        }
        //生成一个单号
        $bno = Helper_Bill::getMaxBno();
        
        //----------------------
        //计算总金额
        //----------------------
        $billDisc        = $billInfo["bill_disc"];//总折扣
        $sumPriceAftDisc = $sumPrice * $billDisc; //总折扣的价格
        
        //扣除会员卡内余额
        $leftCard = $memberCard;//扣除后，卡内还有的余额
        $useCardFlag = false;
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = $useCard * 100;
            $useCard = min($useCard,$sumPriceAftDisc);//卡余额和收费的金额比较
            $leftCard = $memberCard - $useCard /100;
            
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
            
            $useCardFlag = true;
        }
        
        //会员卡的积分
        $leftScore = $memberScore;
        $useScoreFlag = false;
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            $useScore = min($memberScore,$billInfo["bill_member_score"]);//需要花多少积分
            
            //将用户的积分转换成钱
            $scoreMoney = floor($useScore / $scoreRatio);
            $useCard    = $scoreMoney * 100;
            $scoreMoney = min($scoreMoney,$sumPriceAftDisc);
            $leftScore  = $memberScore - ($scoreMoney/100) * $scoreRatio; //用户还剩多少积分
            
            
            $sumPriceAftDisc = $sumPriceAftDisc - $scoreMoney;
            $useScoreFlag = true;
            
        }
        //将金额换算积分
        if($sumPriceAftDisc && $scoreRatio){
            $leftScore = $leftScore + floor(($sumPriceAftDisc/$scoreRatio)/100);
            $useScoreFlag = true;
            
        }
        
        //获得订单的信息
        $billDetail = array(
            'useScore' => (int)$billInfo["bill_member_score"],
            'useCard'  => $billInfo["bill_member_card"],
            'price'    => $sumPriceAftDisc,
            'discount' => $billDisc,
            'staffid'  => $staffid,
            'memberId' => $memberId,
            'bno'      => $bno,
            'tm'       => SYSTEM_TIME,
        );
        $memLeftInfo = array();
        if($useScoreFlag){//使用了积分
            $memLeftInfo['score'] = round($leftScore);
        }
        if($useCardFlag){//使用了会员卡
            $memLeftInfo['balance'] = $leftCard;
        }
        
        //记入订单库
        $bid = Helper_Dao::insertItem(array(
		        'addItem'       =>  $billDetail,
		        'dbName'        =>  'Db_Andyou',
		        'tblName'       =>  'bills',
		));
        
        //记入订单详情
        if($proInfoArr){
            foreach($proInfoArr as $item){
                //补充信息
                $item["bid"] = $bid;
                $item["bno"] = $bno;
                $item["memberId"] = $memberId;
                $item["tm"]       = SYSTEM_TIME;
                
                 Helper_Dao::insertItem(array(
                        'addItem'       =>  $item,
                        'dbName'        =>  'Db_Andyou',
                        'tblName'       =>  'billsitem',
                ));
            }
        }
        
        //更新用户详情信息
        Helper_Dao::updateItem(array(
	            'editItem'       =>  $memLeftInfo,
	            'dbName'         =>  'Db_Andyou',
	            'tblName'        =>  'member',
	            'where'          =>  ' id=' . $memberId, 
	    ));
        
        $staffArr  = Helper_Staff::getStaffPairs();
         
         //准备进入打印页面
        $output->bno          = $bno;
        $output->billDetail   = $billDetail;
        $output->proInfoArr   = $proInfoArr;
        $output->memLeftInfo  = $memLeftInfo;
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        
        
		$output->setTemplate('BillPrint');
        
        
    }
}

