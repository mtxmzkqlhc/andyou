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
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $db = Db_Andyou::instance();
        $pageSize = 100;
        //�������
        $allSum = $db->getOne("select count(*) from member");
        for($i=0;$i<=$allSum;$i++){
            $s = $i * $pageSize;
            //��ñ��صĻ�Ա
            $sql = "select * from member order by id desc limit {$s},{$pageSize}";
            $res = $db->getAll($sql);
            
            $data = array();
            if($res){
                foreach($res as $re){
                    unset($re['id']);
                    unset($re['upTm']);
                    $re["site"] = $output->sysName;
                    $data[] = $re;
                }
            }
            $jsonstr = base64_encode(api_json_encode($data));
            $token   = md5("c=Rsync_Member&a=UpAll"."AAFDFDF&RE3");
            $rtn = ZOL_Http::curlPost(array(                
                'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAll&token={$token}", #Ҫ�����URL����
                'postdata' => "data=$jsonstr", #POST������
                'timeout'  => 3,#��ʱʱ�� s
            ));            
        }
        
        echo "OK";
        exit;
        
    }
    /**
     * ���»�Ա��������Ϣ
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        $db = Db_Andyou::instance();
        //------------------------------------
        //������������ӻ����޸ĵĻ�Աͬ����Զ��
        //------------------------------------
        
        //��ȡһ��ͬ����ʱ��
        $sql = "select tm from log_yunrsync where name = 'memberinfo_up'";
        $lastUpTm = $db->getOne($sql);
        
        //���������ӡ��޸ĵĻ�Ա
        $sql = "select name,phone,cardno,cateId,byear,bmonth,bday,addTm,remark,introducer,introducerId,allsum "
             . " from member where (addTm > {$lastUpTm} or upTm > {$lastUpTm}) limit 100";
        $res = $db->getAll($sql);
        
        $data = array();
        if($res){
            foreach($res as $re){
                unset($re['id']);
                unset($re['upTm']);
                $re["site"] = $output->sysName;
                $data[] = $re;
            }
        }
        $jsonstr = base64_encode(api_json_encode($data));
        $token   = md5("c=Rsync_Member&a=UpNew"."AAFDFDF&RE3");
        $rtn = ZOL_Http::curlPost(array(                
            'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpNew&token={$token}", #Ҫ�����URL����
            'postdata' => "data=$jsonstr", #POST������
            'timeout'  => 3,#��ʱʱ�� s
        ));         
        $db->query("update log_yunrsync set tm = ". SYSTEM_TIME ."  where name = 'memberinfo_up'");
        
        
        echo "OK"; 
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
        $tableArr = array("log_scorechange","log_cardchange");
        if($tableArr){
            foreach($tableArr as $table){
                $sql = "select * from {$table} where rsync = 0";
                $output->data = $db->getAll($sql);
                if($output->data){
                    $output->url  = "c=Rsync_Member&a=UpLog";
                    $output->table = $table;
                    $rtnJson = $this->doPost($input,$output);
                    echo "<hr/>";
                    $okIdArr = json_decode($rtnJson);
                    
                    if($okIdArr && is_array($okIdArr)){
                        foreach($okIdArr as $id){
                            $db->query("update {$table} set rsync = 1 where id = {$id} ");
                        }
                    }
                }
                
            }
        }
        
        
        echo "OK";
        exit;
    }
    
    private function doPost(ZOL_Request $input, ZOL_Response $output){
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

