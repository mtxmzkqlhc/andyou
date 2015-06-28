<?php
/**
 * ��Ա��Ϣ��ͬ��
 *
 */
class  Yun_Page_Rsync_Member  extends Yun_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Member';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     * ͬ��������־
     */
    public function doUpLog(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $token = $input->get("token");
        //token����֤
        //
        if($token != md5("c=Rsync_Member&a=UpLog"."AAFDFDF&RE3")){
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
                if(in_array($table, array("log_scorechange","log_cardchange"))){//log_cardchange
                    $okIdArr = array();
                    foreach($data as $d){
                        $bno    = $d["bno"];
                        $phone  = $d["phone"];
                        $dateTm = $d["dateTm"];
                        
                        $sql = "select 'x' from {$table} where phone = '{$phone}' and bno = '{$bno}' and dateTm = '{$dateTm}' limit 1 ";
                        $has = $db->getOne($sql);
                        $orgId = $d['id'];
                        if(!$has){//��������ھͲ��뵽����
                            unset($d['id']);
                            unset($d['rsync']);
                            $item = $d;
                            $h = Helper_Dao::insertItem(array(                            
                                'addItem'       =>  $item, #������
                                'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                                'tblName'       =>  $table,   #����
                            ));
                            if($h){//����ok
                                //�鿴��Ա�Ƿ����,���жϵ�Ŀ�ģ���Ա��Ϣ��û��ͬ������
                                $mhas = $db->getOne("select 'x' from member where phone = '{$phone}' limit 1");
                                if($mhas){
                                    //���»�Ա������
                                    $dstr = $item["direction"] ? "-":"+";
                                    if("log_scorechange" == $table){//����
                                        $sql  = "update member set score = score {$dstr} {$item["score"]},uptim=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("log_cardchange" == $table){//��Ա��
                                        $sql  = "update member set balance = balance {$dstr} {$item["card"]},uptim=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }
                                    $db->query($sql);
                                    $okIdArr[] =  $orgId;
                                }
                            }
                        }else{
                            $okIdArr[] =  $orgId;
                        }
                    }
                    echo json_encode($okIdArr);
                }
            }
        }
        exit;
    }
    
    /** 
     * ͬ�����е�����
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token����֤
        //
        if($token != md5("c=Rsync_Member&a=UpAll"."AAFDFDF&RE3")){
            echo "001";
            exit;
        }
        $db = Db_AndyouYun::instance();
        
        //��ñ��صĻ�Ա
        $data = $input->post("data");
        if($data){
            $data = base64_decode($data);
            $data = api_json_decode($data);
            if($data){
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from member where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//��������ھͲ��뵽����
                        unset($d["id"]);
                        $item = $d;
                        $item["uptim"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #������
                            'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                            'tblName'       =>  "member",   #����
                        ));
                    }
                }
            }
        }
        
        echo 1;
        exit;
    }
    
    
	
}

