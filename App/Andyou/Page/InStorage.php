<?php
/**
 * ��⴦��
 *
 */
class  Andyou_Page_InStorage  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'InStorage';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * ��������б�
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
		$output->setTemplate('InStorage');
	}
	
    /**
     * ���¿��
     */
	public function doAddIn(ZOL_Request $input, ZOL_Response $output){
	       
        $db = Db_Andyou::instance();
        $itemIdArr   = $input->post("item_id");//���в�ƷID 
        $itemNumArr  = $input->post("item_num");//���в�Ʒ��Ʒ����
        if($itemIdArr){
            foreach($itemIdArr as $i => $pid){
                $num = $itemNumArr[$i];
                if($num){
                    $orgNum = (int)$db->getOne("select stock from product where id = {$pid}");//�����ǰ�Ŀ����
                    //���¿����Ϣ
                    $sql = "update product set stock = stock + {$num} where id = {$pid} ";
                    $db->query($sql);
                    
                    //��¼��־ log_productInStorage
                    $item = array(
                        'proId'    => $pid,
                        'adminer'  => $output->admin,
                        'dateTm'   => SYSTEM_TIME,
                        'orgNum'   => $orgNum,
                        'addNum'   => $num,
                    );
                    
                    Helper_Dao::insertItem(array(
                            'addItem'       =>  $item, #������
                            'dbName'        =>  'Db_Andyou',    #���ݿ���
                            'tblName'       =>  'log_productInStorage',    #����
                    ));
                    
                }
            }
        }
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
        
	}
    
	
}

