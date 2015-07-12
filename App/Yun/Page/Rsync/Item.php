<?php
/**
 * ��Ա��Ϣ��ͬ��
 *
 */
class  Yun_Page_Rsync_Item  extends Yun_Page_RsyncAbstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Item';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     * ͬ��������־
     */
    public function doUpData(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $token = $input->get("token");
        //token����֤
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
                        if(!$has){//��������ھͲ��뵽����
                            Helper_Dao::insertItem(array(                            
                                'addItem'       =>  $item, #������
                                'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                                'tblName'       =>  $table,   #����
                            ));
                        }else{
                           Helper_Dao::updateItem(array(                            
                                'addItem'       =>  $item, #������
                                'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                                'tblName'       =>  $table,   #����
                                'where'         =>  $subSql,    #����
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

