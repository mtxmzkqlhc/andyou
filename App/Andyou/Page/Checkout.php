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
        
        //�����������
        $output->sysOptions = Helper_Option::getAllOptions();        
        $output->scoreRatio = !empty($output->sysOptions["ScoreRatio"]) ? $output->sysOptions["ScoreRatio"]["value"] : 0;
        
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
        if($memberInfo){ 
            $memberScore = $memberInfo["score"];   //��Ա�Ļ���
            $memberCard  = $memberInfo["balance"]; //��Ա�Ŀ������
        }else{//���û�������Ա
            $memberScore = $memberCard = 0;
            $billInfo["bill_member_card"]  = 0;//���ݹ����Ļ�Ա�۷ѣ�ǿ��ʧЧ��
            $billInfo["bill_member_score"] = 0;
        }
        
        //----------------------
        //���˵�е���Ʒ��Ϣ
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
        //����һ������
        $bno = Helper_Bill::getMaxBno();
        
        //----------------------
        //�����ܽ��
        //----------------------
        $billDisc        = $billInfo["bill_disc"];//���ۿ�
        $sumPriceAftDisc = $sumPrice * $billDisc; //���ۿ۵ļ۸�
        
        //�۳���Ա�������
        $leftCard = $memberCard;//�۳��󣬿��ڻ��е����
        $useCardFlag = false;
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = $useCard * 100;
            $useCard = min($useCard,$sumPriceAftDisc);//�������շѵĽ��Ƚ�
            $leftCard = $memberCard - $useCard /100;
            
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
            
            $useCardFlag = true;
        }
        
        //��Ա���Ļ���
        $leftScore = $memberScore;
        $useScoreFlag = false;
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            $useScore = min($memberScore,$billInfo["bill_member_score"]);//��Ҫ�����ٻ���
            
            //���û��Ļ���ת����Ǯ
            $scoreMoney = floor($useScore / $scoreRatio);
            $useCard    = $scoreMoney * 100;
            $scoreMoney = min($scoreMoney,$sumPriceAftDisc);
            $leftScore  = $memberScore - ($scoreMoney/100) * $scoreRatio; //�û���ʣ���ٻ���
            
            
            $sumPriceAftDisc = $sumPriceAftDisc - $scoreMoney;
            $useScoreFlag = true;
            
        }
        //���������
        if($sumPriceAftDisc && $scoreRatio){
            $leftScore = $leftScore + floor(($sumPriceAftDisc/$scoreRatio)/100);
            $useScoreFlag = true;
            
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
        $memLeftInfo = array();
        if($useScoreFlag){//ʹ���˻���
            $memLeftInfo['score'] = round($leftScore);
        }
        if($useCardFlag){//ʹ���˻�Ա��
            $memLeftInfo['balance'] = $leftCard;
        }
        
        //���붩����
        $bid = Helper_Dao::insertItem(array(
		        'addItem'       =>  $billDetail,
		        'dbName'        =>  'Db_Andyou',
		        'tblName'       =>  'bills',
		));
        
        //���붩������
        if($proInfoArr){
            foreach($proInfoArr as $item){
                //������Ϣ
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
        
        //�����û�������Ϣ
        Helper_Dao::updateItem(array(
	            'editItem'       =>  $memLeftInfo,
	            'dbName'         =>  'Db_Andyou',
	            'tblName'        =>  'member',
	            'where'          =>  ' id=' . $memberId, 
	    ));
        
        $staffArr  = Helper_Staff::getStaffPairs();
         
         //׼�������ӡҳ��
        $output->bno          = $bno;
        $output->billDetail   = $billDetail;
        $output->proInfoArr   = $proInfoArr;
        $output->memLeftInfo  = $memLeftInfo;
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        
        
		$output->setTemplate('BillPrint');
        
        
    }
}

