<?php
/**
 * 入库处理
 *
 */
class  Andyou_Page_InStorage  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'InStorage';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}

    /**
     * 获得数据列表
     */
	public function doDefault(ZOL_Request $input, ZOL_Response $output){
		
		$output->setTemplate('InStorage');
	}
	
    /**
     * 更新库存
     */
	public function doAddIn(ZOL_Request $input, ZOL_Response $output){
	       
        $db = Db_Andyou::instance();
        $itemIdArr   = $input->post("item_id");//所有产品ID 
        $itemNumArr  = $input->post("item_num");//所有产品产品数量
        if($itemIdArr){
            foreach($itemIdArr as $i => $pid){
                $num = $itemNumArr[$i];
                if($num){
                    $orgNum = (int)$db->getOne("select stock from product where id = {$pid}");//获得以前的库存数
                    //更新库存信息
                    $sql = "update product set stock = stock + {$num} where id = {$pid} ";
                    $db->query($sql);
                    
                    //记录日志 log_productInStorage
                    $item = array(
                        'proId'    => $pid,
                        'adminer'  => $output->admin,
                        'dateTm'   => SYSTEM_TIME,
                        'orgNum'   => $orgNum,
                        'addNum'   => $num,
                    );
                    
                    Helper_Dao::insertItem(array(
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  'Db_Andyou',    #数据库名
                            'tblName'       =>  'log_productInStorage',    #表名
                    ));
                    
                }
            }
        }
        $urlStr = "?c={$output->ctlName}";
	    echo "<script>document.location='{$urlStr}';</script>";
		exit;
        
	}
    
	
}

