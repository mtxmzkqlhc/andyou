<?php
/**
 * 会员信息的同步
 *
 */
class  Yun_Page_Rsync_Item  extends Yun_Page_RsyncAbstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Item';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     * 同步所有日志
     */
    public function doUpData(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $token = $input->get("token");
        //token的验证
        //
        if($token != md5("c=Rsync_Item&a=UpData"."AAFDFDF&RE3")){
            echo "001";
            exit;
        }
        $db = Db_AndyouYun::instance();
        
        $data  = $input->post("data");
        $table = $input->post("table");
        
        if($data){
            $data = base64_decode($data);
            $data = api_json_decode($data);
            
            if($data){
                if(in_array($table, array("staff","product"))){//log_cardchange
                    $okIdArr = array();
                    foreach($data as $d){
                        
                        $site   = $d['site'];
                        $objId  = $d['objId'];
                        $subSql = "site = '{$site}' and objId = '{$objId}'";
                        $sql    = "select 'x' from {$table} where {$subSql} limit 1 ";
                        $has    = $db->getOne($sql);
                        $okIdArr[] = $objId;
                        unset($d['id']);
                        unset($d['rsync']);
                        unset($d['rowTm']);
                        $item = $d;
                        if(!$has){//如果不存在就插入到本地
                            Helper_Dao::insertItem(array(                            
                                'addItem'       =>  $item, #数据列
                                'dbName'        =>  "Db_AndyouYun",    #数据库名
                                'tblName'       =>  $table,   #表名
                            ));
                        }else{
                           Helper_Dao::updateItem(array(                            
                                'addItem'       =>  $item, #数据列
                                'dbName'        =>  "Db_AndyouYun",    #数据库名
                                'tblName'       =>  $table,   #表名
                                'where'         =>  $subSql,    #条件
                            ));
                        }
                    }
                    echo json_encode($okIdArr);
                }
            }
        }
        exit;
    }
    
    
}

