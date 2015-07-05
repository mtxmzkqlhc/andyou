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
                if(in_array($table, array("log_scorechange","log_cardchange","bills"))){//log_cardchange
                    $okIdArr = array();
                    foreach($data as $d){
                        $bno    = $d["bno"];
                        $phone  = $d["phone"];
                        
                        if($table == "bills"){
                            $dateTm = $d["tm"];                        
                            $sql = "select 'x' from {$table} where phone = '{$phone}' and bno = '{$bno}' and tm = '{$dateTm}' limit 1 ";
                            #echo $sql."<br/>";
                        }else{    
                            $dateTm = $d["dateTm"];                        
                            $sql = "select 'x' from {$table} where phone = '{$phone}' and bno = '{$bno}' and dateTm = '{$dateTm}' limit 1 ";
                        }
                        
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
                                    if("log_scorechange" == $table){//����
                                        $dstr = $item["direction"] ? "-":"+";
                                        $sql  = "update member set score = score {$dstr} {$item["score"]},upTm=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("log_cardchange" == $table){//��Ա��
                                        $dstr = $item["direction"] ? "-":"+";
                                        $sql  = "update member set balance = balance {$dstr} {$item["card"]},upTm=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("bills" == $table){//���Ѽ�¼
                                        $sum  = round($item["price"]/100,2) + $item["useCard"];
                                        $sql  = "update member set allsum = allsum + {$sum},upTm=".SYSTEM_TIME." where phone = '{$phone}'";
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
     * �ǽ������ϵ��������ݶ�ͬ�����ƶˣ�һ������²���Ҫ��ִ��
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
                $okIdArr = array();
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from member where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//��������ھͲ��뵽����
                        $okIdArr[] = $d["siteObjId"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #������
                            'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                            'tblName'       =>  "member",   #����
                        ));
                    }
                }
                
                echo json_encode($okIdArr);
                exit;
            }
        }
        
        exit;
    }
    
    /** 
     * ͬ�����еĻ�Ա������Ʒ����
     * �ǽ������ϵ��������ݶ�ͬ�����ƶˣ�һ������²���Ҫ��ִ��
     */
    public function doUpAllOtherPro(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token����֤
        //
        if($token != md5("c=Rsync_Member&a=UpAllOtherPro"."AAFDFDF&RE3")){
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
                $okIdArr = array();
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from memeberotherpro where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//��������ھͲ��뵽����
                        $okIdArr[] = $d["siteObjId"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #������
                            'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                            'tblName'       =>  "memeberotherpro",   #����
                        ));
                    }
                }
                
                echo json_encode($okIdArr);
                exit;
            }
        }
        
        exit;
    }
    
    /** 
     * ͬ����������
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token����֤
        //
        if($token != md5("c=Rsync_Member&a=UpNew"."AAFDFDF&RE3")){
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
                
                $okIdArr = array(); 
                foreach($data as $d){
                    
                    $phone = $d["phone"];
                    $sql   = "select * from member where phone = '{$phone}' limit 1 ";
                    $info  = $db->getRow($sql);
                    if(!$info){//��������ھͲ��뵽�ƶ�
                        $okIdArr[] = $d["id"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #������
                            'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                            'tblName'       =>  "member",   #����
                        ));
                    }else{#����ƶ��Ѿ�������
                        if($info["upTm"] < $d["upTm"]){//�ƶ˵ĸ���ʱ��Ƚ���
                            $item = array(
                                'name'     => $d["name"],
                                'cardno'   => $d["cardno"],
                                'cateId'   => $d["cateId"],
                                'byear'    => $d["byear"],
                                'bmonth'   => $d["bmonth"],
                                'bday'     => $d["bday"],
                                'remark'   => $d["remark"],
                                'introducer'     => $d["introducer"],
                                'introducerId'   => $d["introducerId"],
                                'upTm'           => $d["upTm"],
                            );
                            
                            Helper_Dao::updateItem(array( 
                                'editItem'      =>  $item, #������
                                'dbName'        =>  "Db_AndyouYun",    #���ݿ���
                                'tblName'       =>  "member",   #����
                                'where'         =>  "phone = '{$phone}'",    #����
                            ));
                        }
                        
                    }
                    
                }
                
                echo json_encode($okIdArr);
            }
        }
        exit;
    }
        
    /** 
     * ������µ�����
     */
    public function doGetNew(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        $tm    = (int)$input->get("tm");
        //token����֤
        //
        if($token != md5("c=Rsync_Member&a=GetNew&tm={$tm}"."AAFDFDF&RE3")){
            echo "001";
            exit;
        }
        $db = Db_AndyouYun::instance();
        
        $sql = "select name,phone,cardno,cateId,byear,bmonth,bday,addTm,remark,introducer,introducerId,allsum,upTm,score,balance,site,siteObjId "
             . " from member where (addTm > {$tm} or upTm > {$tm})";
        $data = $db->getAll($sql);        
        $jsonstr = api_json_encode($data);
        
        echo $jsonstr;
        exit;
    }
    
}

