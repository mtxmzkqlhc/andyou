<?php
/**
 * 前台结账
 *
 */

error_reporting(0);
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
        $remark      = $input->post("remark");//填写的备注
        
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
        //原价总金额
        $orgSumPrice = 0; //商品总额
        //折扣获得的金额
        $discGetMoney = 0;
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $num = (int)$itemNumArr[$idx];
               $price       = $num * $proInfo["oprice"] * $itemDiscArr[$idx];
               $orgSumPrice += $num * $proInfo["oprice"];
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
        $sumPriceAftDisc = $orgSumPrice * $billDisc;//总折扣的价格  $sumPrice * $billDisc; //不可以给商品设置单价了
        $discGetMoney    = $orgSumPrice - $sumPriceAftDisc;//折扣省下来的金额
        
        //扣除会员卡内余额
        $leftCard = $memberCard;//扣除后，卡内还有的余额
        $useCardFlag = false;
        $useCardMoney = 0;
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = $useCard * 100;
            $useCard = min($useCard,$sumPriceAftDisc);//卡余额和收费的金额比较
            $leftCard = $memberCard - $useCard /100;
            
            $useCardMoney = $useCard /100;//记录用了多少卡的金额
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
            
            $useCardFlag = true;
        }
        /*
         * 先不用会员积分了
        //会员积分计算
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        $useScore   = (int)$billInfo["bill_member_score"];
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            $useScore = min($memberScore,$billInfo["bill_member_score"]);//需要花多少积分，避免比用户的积分还多
            
            //将用户的积分转换成钱
            $scoreMoney = round($useScore / $scoreRatio,2);// 9 = 270 /30
            $scoreMoney = $scoreMoney * 100;             // 900 = 9 * 100
            $scoreMoney = min($scoreMoney,$sumPriceAftDisc); //避免花费的积分比剩余金额还多 
            
            $sumPriceAftDisc = $sumPriceAftDisc - $scoreMoney;
            
        }*/
        
        //获得订单的信息
        $sumPriceAftDisc = round($sumPriceAftDisc/100)*100; //金额的四舍五入
        $billDetail = array(
            'useScore' => $useScore,
            'useScoreAsMoney' => round($scoreMoney/100,2),
            'useCard'  => $billInfo["bill_member_card"],
            'price'    => $sumPriceAftDisc,
            'discount' => $billDisc,
            'orgPrice' => $orgSumPrice,
            'staffid'  => $staffid,
            'memberId' => $memberId,
            'bno'      => $bno,
            'tm'       => SYSTEM_TIME,
            'dateDay'  => date("Ymd"),
            'memberScore'     => $memberScore,
            'memberCard'      => $memberCard,
            'remark'          => $remark,
        );
        
        //积分的重新计算
        //         =  积分剩余额                                    +  实际消费产品的积分
        $leftScore = ($memberScore - (int)$billDetail["useScore"]) +  ($billDetail["price"] / 100 + $billDetail["useCard"]) * $scoreRatio ;
        $memLeftInfo = array();
        $memLeftInfo['score'] = round($leftScore);
        
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
         
        //更新商品的库存情况
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $stock   = (int)$proInfo["stock"];
               if($stock){
                   $num = (int)$itemNumArr[$idx];
                   $stock = $stock - $num;
                   if($stock < 0)$stock = 0;
                   
                    Helper_Dao::updateItem(array(
                            'editItem'       =>  array("stock"=>$stock),
                            'dbName'         =>  'Db_Andyou',
                            'tblName'        =>  'product',
                            'where'          =>  ' id=' . $pid, 
                    ));
                   
               }
            }
        }
        
         //准备进入打印页面
        $output->bno          = $bno;
        $output->billDetail   = $billDetail;
        $output->proInfoArr   = $proInfoArr;
        $output->memLeftInfo  = $memLeftInfo;
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        $output->memberInfo   = $memberInfo;//会员信息
        
        $output->discGetMoney = $discGetMoney; //折扣省下的钱
        $output->orgSumPrice  = $orgSumPrice; //原始总价
        $output->newScore     = (int)(($billDetail['useCard'] + ($billDetail['price']/100)) * $scoreRatio);//获得的积分
        
		$output->setTemplate('BillPrint');
        
        
    }
}

