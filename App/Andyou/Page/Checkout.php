<?php
/**
 * ǰ̨����
 *
 */
class  Andyou_Page_Checkout  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Checkout';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * ��������б�
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
        //������л�Ա����
        $output->memberCate = Helper_Member::getMemberCatePairs();
		//������е�Ա��
        $output->staffArr  = Helper_Staff::getStaffPairs();
        
		$output->setTemplate('Checkout');
	}
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        $billInfo    = $input->post("bill"); 
        $itemIdArr   = $input->post("item_id");//���в�ƷID 
        $itemDiscArr = $input->post("item_disc");//���в�Ʒ�ۿ�
        $itemNumArr  = $input->post("item_num");//���в�Ʒ��Ʒ����
        $staffid     = (int)$input->post("staffid");//Ա��
        
        //----------------------
        //��û�Ա��Ϣ
        //----------------------
        $memberId    = (int)$input->post("memberId");//��ԱID
        $memberInfo  = Helper_Member::getMemberInfo(array("id"=>$memberId));
        $memberScore = $memberInfo["score"];   //��Ա�Ļ���
        $memberCard  = $memberInfo["balance"]; //��Ա�Ŀ������
        
        
        //----------------------
        //���˵�е���Ʒ��Ϣ
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
        //����һ������
        $bno = Helper_Bill::getMaxBno();
        
        //----------------------
        //�����ܽ��
        //----------------------
        $billDisc        = $billInfo["bill_disc"];//���ۿ�
        $sumPriceAftDisc = $sumPrice * $billDisc; //���ۿ۵ļ۸�
        
        //�۳���Ա�������
        $leftCard = $memberCard;//�۳��󣬿��ڻ��е����
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = min($useCard,$sumPriceAftDisc);//�������շѵĽ��Ƚ�
            $leftCard = $memberCard - $useCard;
            
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
        }
        
        //��Ա���Ļ���
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            
        }
        
        //��ö�������Ϣ
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

