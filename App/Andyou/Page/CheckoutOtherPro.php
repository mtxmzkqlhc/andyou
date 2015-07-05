<?php
/**
 * �������Ͳ�Ʒ��������ο�
 *
 */

//error_reporting(0);
class  Andyou_Page_CheckoutOtherPro  extends Andyou_Page_Abstract {
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
		
        $output->ctype = (int)$input->get("ctype");//������Ʒ����������
        if(!$output->ctype)$output->ctype = 2;
        
        //������л�Ա����
        $output->memberCate = Helper_Member::getMemberCatePairs();
		//������е�Ա��
        $output->staffArr  = Helper_Staff::getStaffPairs();
        $output->proCtypeArr = ZOL_Config::get("GLOBAL","PRO_CTYPE");
        if(!isset($output->proCtypeArr[$output->ctype])){
            echo "ERROR";exit;
        }
        $output->ctypeName = $output->proCtypeArr[$output->ctype]['name'];
		$output->setTemplate('CheckoutOtherPro');
	}
    
    private function showErrMsg(){
        echo "���ݴ���";
        exit;
    }
    
	
    public function doDone(ZOL_Request $input, ZOL_Response $output){
        
        $otherProIdArr  = $input->post("otherProId");//������Ʒ
        $staffid        = (int)$input->post("staffid");//Ա��
        $remark         = $input->post("remark");//��д�ı�ע
        $memberId       = (int)$input->post("memberId");//��ԱID
        
        if(!$staffid || empty($otherProIdArr)){
            $this->showErrMsg();
        }
        
        //��û�Ա��Ϣ
        $memberInfo  = Helper_Member::getMemberInfo(array("id"=>$memberId));
        if(!$memberInfo){
            $this->showErrMsg();
        }
        
        //�����Ʒ�б�
        $proInfoArr = array();
        foreach($otherProIdArr as $id){
           $info = Helper_Product::getMemberOtherPro(array('id'=>$id,'memberId'=>$memberId));
           if($info && $info["num"] > 0){//ɸѡһ����Ʒ
               $proInfoArr[] = $info;
           }
        }
        //����һ������
        $bno = Helper_Bill::getCommonMaxBno();
        
        $db = Db_Andyou::instance();
        if($proInfoArr){
            foreach($proInfoArr as $info){
                
                //��������
                $sql = "update memeberotherpro set num = num - 1 where id = ".$info["id"];
                $db->query($sql);
                
                //��¼������־
                $tmpLogRow = array(
                    'memberId'      => $memberId,
                    'phone'         => $memberInfo["phone"],
                    'otherproId'    => $info["id"],
                    'name'          => $info["name"],
                    'direction'     => 1,
                    'cvalue'        => 1,
                    'orgcvalue'     => $info["num"],
                    'ctype'         => $info["ctype"],
                    'dateTm'        => SYSTEM_TIME,
                    'staffid'       => $staffid,
                    'bno'           => $bno,
                    'remark'        => $remark,
                );
                
                Helper_Dao::insertItem(array(
                        'addItem'       =>  $tmpLogRow,
                        'dbName'        =>  'Db_Andyou',
                        'tblName'       =>  'log_useotherpro',
                ));
                
            }
        }
        $staffArr  = Helper_Staff::getStaffPairs();
        
        
         //׼�������ӡҳ��
        $output->proInfoArr   = $proInfoArr;
        $output->bno          = $bno;
        $output->memberId     = $memberId;
        $output->memberInfo   = $memberInfo;//��Ա��Ϣ
        $output->staffid      = $staffid;
        $output->staffName    = $staffArr[$staffid];
        Helper_Bill::createOneCommonBno();//����һ��ͨ�ö�����
		$output->setTemplate('OtherProPrint');        
    }
}

