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
        $memberScore = $memberInfo["score"];   //会员的积分
        $memberCard  = $memberInfo["balance"]; //会员的卡内余额
        
        
        //----------------------
        //获得说有的商品信息
        //----------------------
        $proInfoArr = array();
        $sumPrice   = 0;
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $num = (int)$itemNumArr[$idx];
               $sumPrice += $price = $num * $proInfo["price"] * $itemDiscArr[$idx];
               $proInfoArr[] = array(
                   'proId'      => $pid,
                   'num'        => $num,
                   'discount'   => $itemDiscArr[$idx]*100,
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
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = min($useCard,$sumPriceAftDisc);//卡余额和收费的金额比较
            $leftCard = $memberCard - $useCard;
            
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
        }
        
        //会员卡的积分
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            
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
        
        echo "<pre>";
        print_r($billInfo);
        print_r($itemInfo);
        print_r($proInfoArr);
        print_r($billDetail);
        print_r($memberInfo);
        exit;
    }
}

