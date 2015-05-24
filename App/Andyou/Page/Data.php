<?php
/**
 * 数据操作相关
 *
 */
class  Andyou_Page_Data  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Data';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     *  数据备份
     */
    public function doBackUp(ZOL_Request $input, ZOL_Response $output){
        
        $db = Db_Andyou::instance();
        //需要备份的数据库
        $tablesArr = array("adminuser","bills","billsitem","member","membercate","options","product","productcate","staff","staffcate","log_backup","log_cardchange","log_productinstorage","log_scorechange");
        $sqlTxt    = "";
        foreach($tablesArr as $tbl){
            $sql = "select * from {$tbl} order by id desc limit 10000";
            $res = $db->getAll($sql);
            if($res){
                $keys = false;
                $sqlTxt .= "\n\ninsert into {$tbl}";
                $comma  = "";
                foreach ($res as $re){
                    if(!$keys){
                        $keys = array_keys ($re);
                        $sqlTxt .=  "(".  implode(",", $keys).") values \n";
                    }
                    $sqlTxt .= $comma . "('" .  implode("','",$re) ."')";
                    $comma = ",";
                }
            }
            $sqlTxt .= ";";
        }
        file_put_contents(SYSTEM_VAR . "backup/".date("Ymd").".sql", $sqlTxt);
        echo "OK";
        exit;
    }

	
}

