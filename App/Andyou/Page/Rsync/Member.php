<?php
/**
 * ��Ա��Ϣ��ͬ��
 *
 */
class  Andyou_Page_Rsync_Member  extends Andyou_Page_Abstract {
    /**
     * ��֤
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Member';
        if (!parent::baseValidate($input, $output)) { return false; }
        
        //���վ��
        $output->sysName    = $output->sysCfg['SysId']["value"] ;
        
		return true;
	}
    
    
    /** 
     * ͬ�����е�����
     * �ǽ������ϵ��������ݶ�ͬ�����ƶˣ�һ������²���Ҫ��ִ��
     * ?c=Rsync_Member&a=UpAll
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $db = Db_Andyou::instance();
        $pageSize = 100;
        //��û�Ա����
        $allSum  = $db->getOne("select count(*) from member");
        $loopCnt = ceil($allSum/$pageSize);
       
        for($i=0;$i<$loopCnt;$i++){
            echo "==LOOP:{$i}==<br/>";
            $s = $i * $pageSize;
            //��ñ��صĻ�Ա
            $sql = "select * from member order by id desc limit {$s},{$pageSize}";
            $res = $db->getAll($sql);
            
            $data = array();
            if($res){
                foreach($res as $re){
                    $re["siteObjId"] = $re["id"];
                    $re["site"]      = $output->sysName;
                    unset($re['upTm']);
                    unset($re['rsync']);
                    $data[] = $re;
                }
                
                $jsonstr = base64_encode(api_json_encode($data));
                $token   = md5("c=Rsync_Member&a=UpAll"."AAFDFDF&RE3");
                $rtnJson = ZOL_Http::curlPost(array(                
                    'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAll&token={$token}", #Ҫ�����URL����
                    'postdata' => "data=$jsonstr", #POST������
                    'timeout'  => 3,#��ʱʱ�� s
                ));  
                #����ͬ��״̬
                $okIdArr = json_decode($rtnJson);
                if($okIdArr && is_array($okIdArr)){
                    foreach($okIdArr as $id){
                        echo "{$id} OK<br/>";
                        $db->query("update member set rsync = 1 where id = {$id} ");
                    }
                }
            }
        }
        
        //��û�Ա������Ʒ����
        $allSum  = $db->getOne("select count(*) from memeberotherpro");
        $loopCnt = ceil($allSum/$pageSize);
       
        for($i=0;$i<$loopCnt;$i++){
            echo "==LOOP:{$i}==<br/>";
            $s = $i * $pageSize;
            //��ñ��صĻ�Ա
            $sql = "select * from memeberotherpro order by id desc limit {$s},{$pageSize}";
            $res = $db->getAll($sql);
            
            $data = array();
            if($res){
                foreach($res as $re){
                    $re["siteObjId"] = $re["id"];
                    $re["site"]      = $output->sysName;
                    if(empty($re['phone'])){
                        $minfo      = Helper_Member::getMemberInfo(array('id'=>$re["id"]));
                        $re['phone'] = $minfo["phone"];
                    }
                    unset($re['upTm']);
                    unset($re['rsync']);
                    $data[] = $re;
                }
                
                $jsonstr = base64_encode(api_json_encode($data));
                $token   = md5("c=Rsync_Member&a=UpAllOtherPro"."AAFDFDF&RE3");
                $rtnJson = ZOL_Http::curlPost(array(                
                    'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAllOtherPro&token={$token}", #Ҫ�����URL����
                    'postdata' => "data=$jsonstr", #POST������
                    'timeout'  => 3,#��ʱʱ�� s
                ));  
                #����ͬ��״̬
                $okIdArr = json_decode($rtnJson);
                if($okIdArr && is_array($okIdArr)){
                    foreach($okIdArr as $id){
                        echo "{$id} OK<br/>";
                        $db->query("update memeberotherpro set rsync = 1 where id = {$id} ");
                    }
                }
            }
        }
        
        echo "OK";
        exit;
        
    }
    /**
     * ���»�Ա��������Ϣ
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        error_reporting(E_ALL);
        ini_set("display_errors",1);
        $db = Db_Andyou::instance();
        $onlyGetFromYun = (int)$input->get("onlyGetFromYun");//�Ƿ�������ƶ�����
        $allData        = (int)$input->get("allData");//�Ƿ�����������
        //------------------------------------
        //������������ӻ����޸ĵĻ�Աͬ����Զ��
        //------------------------------------
        
        //��ȡһ��ͬ����ʱ��
        $sql = "select tm from log_yunrsync where name = 'memberinfo_up'";
        $lastUpTm = (int)$db->getOne($sql);
        if($lastUpTm > 0)$lastUpTm = $lastUpTm - 1;
        
        if($allData)$lastUpTm = 0;
        
        if(!$onlyGetFromYun){//�Ƿ�������Զ������
        
            //���������ӡ��޸ĵĻ�Ա
            $sql = "select id,name,phone,cardno,cateId,byear,bmonth,bday,addTm,remark,introducer,introducerId,allsum,upTm "
                 . " from member where (addTm > {$lastUpTm} or upTm > {$lastUpTm} or rsync = 0) limit 1000";
            $res = $db->getAll($sql);

            $data = array();
            if($res){
                foreach($res as $re){
                    $re["site"]      = $output->sysName;
                    $re["siteObjId"] = $re["id"];
                    $data[] = $re;
                }
            }
            $jsonstr = base64_encode(api_json_encode($data));
            $token   = md5("c=Rsync_Member&a=UpNew"."AAFDFDF&RE3");
            $rtnJson = ZOL_Http::curlPost(array(                
                'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpNew&token={$token}", #Ҫ�����URL����
                'postdata' => "data=$jsonstr", #POST������
                'timeout'  => 3,#��ʱʱ�� s
            )); 

            #����ͬ��״̬
            $okIdArr = json_decode($rtnJson);
            if($okIdArr && is_array($okIdArr)){
                foreach($okIdArr as $id){
                    echo "{$id} OK<br/>";
                    $db->query("update member set rsync = 1 where id = {$id} ");
                }
            }
            
        }
            
       //����ƶ����µ�����
        $urlPart = "c=Rsync_Member&a=GetNew&tm=".$lastUpTm;
        $token   = md5($urlPart."AAFDFDF&RE3");
        $url     = $output->yunUrl . "?{$urlPart}&token={$token}";
        $html    = ZOL_Http::curlPage( array(
            'url'      => $url, #Ҫ�����URL����
            'timeout'  => 30,#��ʱʱ�� 
		));
        if($html){
            $data = api_json_decode($html);
            if($data){
                foreach($data as $d){
                    
                    $phone = $d["phone"];
                    $sql   = "select * from member where phone = '{$phone}' limit 1 ";
                    $info  = $db->getRow($sql);
                    if(!$info){//��������ھͲ��뵽�ƶ�
                        unset($d["id"]);
                        $item = $d;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #������
                            'dbName'        =>  "Db_Andyou",    #���ݿ���
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
                                'score'    => $d["score"],
                                'balance'  => $d["balance"],
                                'allsum'   => $d["allsum"],
                                'introducer'     => $d["introducer"],
                                'introducerId'   => $d["introducerId"],
                                'upTm'           => $d["upTm"],
                            );
                            
                            Helper_Dao::updateItem(array( 
                                'editItem'      =>  $item, #������
                                'dbName'        =>  "Db_Andyou",    #���ݿ���
                                'tblName'       =>  "member",   #����
                                'where'         =>  "phone = '{$phone}'",    #����
                            ));
                        }
                        
                    }
                    
                }
            }
            if(!$onlyGetFromYun){//�Ƿ�������Զ������
                $db->query("delete from log_yunrsync where name = 'memberinfo_up'");
                $db->query("insert into log_yunrsync(name,tm) values('memberinfo_up',". SYSTEM_TIME .")");
            }
        }
        
        echo "OK"; 
        exit;
    }
    
    
    /** 
     * ͬ�����е���־
     */
    public function doUpLog(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        $db = Db_Andyou::instance();
        
        //---------------------------
        //���֡���Ա�� ������ʷ
        //---------------------------
        $tableArr = array("log_scorechange","log_cardchange","bills");
        if($tableArr){
            foreach($tableArr as $table){
                $sql = "select * from {$table} where rsync = 0";
                $output->data = $db->getAll($sql);
                if($output->data){
                    $output->url  = "c=Rsync_Member&a=UpLog";
                    $output->table = $table;
                    $rtnJson = $this->doPost($input,$output);
                    echo "<hr/>";
                    echo $rtnJson;
                    $okIdArr = json_decode($rtnJson);
                    
                    if($okIdArr && is_array($okIdArr)){
                        foreach($okIdArr as $id){
                            $db->query("update {$table} set rsync = 1 where id = {$id} ");
                        }
                    }
                }
                
            }
        }
        
        
        //---------------------------
        //ͬ���ο���otherpro
        //---------------------------
        #�������ƻ�Ա�绰
        
        
        //---------------------------
        //ͬ������bills
        //---------------------------
        
        
        echo "OK";
        exit;
    }
    
    private function doPost(ZOL_Request $input, ZOL_Response $output){
        $db = Db_Andyou::instance();
        $res = $output->data;
        if($res){
            $data = array();
            foreach ($res as $re){
                $re["site"] = $output->sysName;
                //��û�Ա����Ϣ
                if(in_array($output->table,array("log_scorechange","log_cardchange"))){
                    $minfo = Helper_Member::getMemberInfo(array("id"=>$re["memberId"]));
                    $re["phone"] = $minfo["phone"];
                }
                if(in_array($output->table,array("bills"))){
                    if(empty($re["phone"])){
                        $minfo = Helper_Member::getMemberInfo(array("id"=>$re["memberId"]));
                        $re["phone"] = $minfo["phone"];
                        $db->query("update {$output->table} set phone = '{$minfo["phone"]}' where memberId = {$re["memberId"]}");
                    }
                }
                $data[] = $re;
                
            }

            $jsonstr = base64_encode(api_json_encode($data));
            echo $jsonstr . "<hr/>";
            $token   = md5($output->url."AAFDFDF&RE3");
            $rtn = ZOL_Http::curlPost(array(                
                'url'      => $output->yunUrl ."?". $output->url ."&token={$token}", #Ҫ�����URL����
                'postdata' => "table={$output->table}&data=$jsonstr", #POST������
                'timeout'  => 3,#��ʱʱ�� s
            ));
             return $rtn;
        }
        return false;
    }
	
}

