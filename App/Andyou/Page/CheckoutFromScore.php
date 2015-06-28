<?php
/**
 * ���ֶһ�ҳ��
 *
 */

//error_reporting(0);
class  Andyou_Page_CheckoutFromScore  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'CheckoutFromScore';
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
        $output->scoreRatio = !empty($output->sysOptions["DuihuanRatio"]) ? $output->sysOptions["DuihuanRatio"]["value"] : 0;
        $output->minCheckoutScore = !empty($output->sysOptions["MinCheckoutScore"]) ? $output->sysOptions["MinCheckoutScore"]["value"] : 100;
        
        
        
        //�����Ʒ�����з���
        $output->productCateArr = Helper_Product::getProductCatePairs();
        $output->productCateJson = api_json_encode($output->productCateArr);
        
		$output->setTemplate('CheckoutFromScore');
	}
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        $billInfo    = $input->post("bill"); 
        $itemIdArr   = $input->post("item_id");//���в�ƷID 
        $itemDiscArr = $input->post("item_disc");//���в�Ʒ�ۿ�
        $itemNumArr  = $input->post("item_num");//���в�Ʒ��Ʒ����
        $staffid     = (int)$input->post("staffid");//Ա��
        $remark      = $input->post("remark");//��д�ı�ע
        $endSumModifyFlag = (int)$input->post("endSumModifyFlag");//�Ƿ��ֹ����������ܼ۸�
        $endBillPrice = $billInfo["bill_end_sum"]; //���յļ۸�����۸��ǿ����޸ĵ�
        $db = Db_Andyou::instance();
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
        //ԭ���ܽ��
        $orgSumPrice = 0; //��Ʒ�ܶ�
        //�ۿۻ�õĽ��
        $discGetMoney = 0;
        //��¼������Ʒ���ܼ۸�
        $itemSumPrice = 0;
        if($itemIdArr){
            foreach($itemIdArr as $idx => $pid){
               $proInfo = Helper_Product::getProductInfo(array('id'=>$pid));
               $num = (int)$itemNumArr[$idx];
               $price       = $num * $proInfo["oprice"] * $itemDiscArr[$idx];
               $orgSumPrice += $num * $proInfo["oprice"];
               $sumPrice += $price;
               $itemSumPrice += $price;
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
        #$bno = Helper_Bill::getMaxBno();
        $bno = Helper_Bill::getCommonMaxBno();
        
        //----------------------
        //�����ܽ��
        //----------------------
        $billDisc        = $billInfo["bill_disc"];//���ۿ�
        $sumPriceAftDisc = $sumPrice;//$orgSumPrice * $billDisc;//���ۿ۵ļ۸�  $sumPrice * $billDisc; //�����Ը���Ʒ���õ�����
        $discGetMoney    = $orgSumPrice - $sumPriceAftDisc;//�ۿ�ʡ�����Ľ��
        
        //�۳���Ա�������
        $leftCard = $memberCard;//�۳��󣬿��ڻ��е����
        $useCardFlag = false;
        $useCardMoney = 0;
        if($sumPriceAftDisc && $memberCard && $billInfo["bill_member_card"]){
            
            $useCard = min($memberCard,$billInfo["bill_member_card"]);
            $useCard = $useCard * 100;
            $useCard = min($useCard,$sumPriceAftDisc);//�������շѵĽ��Ƚ�
            $leftCard = $memberCard - $useCard /100;
            
            $useCardMoney = $useCard /100;//��¼���˶��ٿ��Ľ��
            $sumPriceAftDisc = $sumPriceAftDisc - $useCard;
            
            $useCardFlag = true;
        }
        //��Ա���ּ���
        $sysOptions = Helper_Option::getAllOptions();        
        $scoreRatio = !empty($sysOptions["ScoreRatio"]) ? $sysOptions["ScoreRatio"]["value"] : 0;
        $useScore   = (int)$billInfo["bill_member_score"];
        /*
         * �Ȳ��û�Ա������
        if($sumPriceAftDisc && $memberScore && $billInfo["bill_member_score"]){
            $useScore = min($memberScore,$billInfo["bill_member_score"]);//��Ҫ�����ٻ��֣�������û��Ļ��ֻ���
            
            //���û��Ļ���ת����Ǯ
            $scoreMoney = round($useScore / $scoreRatio,2);// 9 = 270 /30
            $scoreMoney = $scoreMoney * 100;             // 900 = 9 * 100
            $scoreMoney = min($scoreMoney,$sumPriceAftDisc); //���⻨�ѵĻ��ֱ�ʣ����� 
            
            $sumPriceAftDisc = $sumPriceAftDisc - $scoreMoney;
            
        }*/
        
        //��ö�������Ϣ
        $sumPriceAftDisc = round($sumPriceAftDisc/100)*100; //������������        
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
        
        //���������ǰ̨�޸���Ӧ�տ���Ǽ�������ģ��ͼ�¼
        if($endSumModifyFlag){
           if($sumPriceAftDisc != $endBillPrice){
               $billDetail["priceTrue"] = $billDetail["price"];
               $billDetail["price"]     = $endBillPrice*100;//ʹ������Ա�޸ĵ�
               
           } 
        }
        
        //���ֵ����¼���
        //         =  ����ʣ���                                    +  ʵ�����Ѳ�Ʒ�Ļ���
        $leftScore = ($memberScore - (int)$billDetail["useScore"]) +  ($billDetail["price"] / 100 + $billDetail["useCard"]) * $scoreRatio ;
        $memLeftInfo = array();
        $memLeftInfo['score'] = round($leftScore);
        
        if($useCardFlag){//ʹ���˻�Ա��
            $memLeftInfo['balance'] = $leftCard;
        }
        //�����û��������Ѷ�
        $memLeftInfo['allsum'] = $memberInfo['allsum'] + round($sumPriceAftDisc/100) + $billInfo["bill_member_card"];
        //���붩����
        
        $output->newScore       = (int)(($billDetail['useCard'] + ($billDetail['price']/100)) * $scoreRatio);//��õĻ���
        $billDetail["getScore"] = $output->newScore;
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
         
        //������Ʒ�Ŀ�����
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
        
        //��¼������ʷ
        if($output->newScore && $memberId){
            //��¼�Լ��Ļ�����ʷ
            Helper_Member::addScoreLog(array(
                'memberId'         => $memberId, #ID
                'direction'        => 1, #1 �� 0 ��
                'score'            => $output->newScore, #����
                'orgScore'         => $memberInfo["score"], #ԭʼ����
                'bno'              => $bno, #������
                'remark'           => '����', #
            ));
            
            //�����������ӻ���
            if($sysOptions && !empty($sysOptions["MemberParentRatio"])&& !empty($sysOptions["MemberParentRatio"]["value"])){
                $introducerId = $memberInfo["introducerId"];
                $introducer   = $memberInfo["introducer"];
                if(!$introducerId || !$introducer){#���û��ID,�ͳ��Ի��

                    $introInfo = Helper_Member::getMemberInfo(array('phone'=>$introducer,'id'=>$introducerId));
                    $introducerId = $introInfo["id"];
                    $iscore = $output->newScore * $sysOptions["MemberParentRatio"]["value"];
                    
                    //��¼����
                    Helper_Member::addScoreLog(array(
                        'memberId'         => $introducerId, #ID
                        'direction'        => 0, #1 �� 0 ��
                        'score'            => $iscore, #����
                        'orgScore'         => $memberInfo["score"], #ԭʼ����
                        'bno'              => $bno, #������
                        'remark'           => '���ߡ�'.$memberInfo["phone"]."-".$memberInfo["name"].'�����ѵû���' . $output->newScore, #
                    ));
                    
                }
            }
        }
        
        //��¼��Ա��Ա��ʹ�ü�¼ 
        if($memberId && $billDetail["useCard"]){
            Helper_Member::addCardLog(array(
                'memberId'         => $introducerId, #ID
                'direction'        => 1, #1 �� 0 ��
                'card'             => $billDetail["useCard"], #
                'orgCard'          => $memberInfo["balance"], 
                'bno'              => $bno, #������
                'remark'           => '����', #
            ));
        }
        
        
         //׼�������ӡҳ��
        $output->bno          = $bno;
        $output->bid          = $bid;
        $output->bsn          = substr(md5($bid."HOOHAHA"), 0,10);
        $output->billDetail   = $billDetail;
        $output->proInfoArr   = $proInfoArr;
        $output->memLeftInfo  = $memLeftInfo;
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        $output->memberInfo   = $memberInfo;//��Ա��Ϣ
        
        $output->discGetMoney = $discGetMoney; //�ۿ�ʡ�µ�Ǯ
        $output->orgSumPrice  = $orgSumPrice; //ԭʼ�ܼ�
        Helper_Bill::createOneCommonBno();//����һ��ͨ�ö�����
              
		$output->setTemplate('BillPrint3');
        
        
    }
}

