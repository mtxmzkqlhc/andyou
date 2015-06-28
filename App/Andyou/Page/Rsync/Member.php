<?php
/**
 * 会员信息的同步
 *
 */
class  Andyou_Page_Rsync_Member  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Member';
        if (!parent::baseValidate($input, $output)) { return false; }
        
        //获得站点
        $output->sysName    = $output->sysCfg['SysId']["value"] ;
        
		return true;
	}
    
    
    /** 
     * 同步所有的数据
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $db = Db_Andyou::instance();
        $pageSize = 100;
        //获得总数
        $allSum = $db->getOne("select count(*) from member");
        for($i=0;$i<=$allSum;$i++){
            $s = $i * $pageSize;
            //获得本地的会员
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
                'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAll&token={$token}", #要请求的URL数组
                'postdata' => "data=$jsonstr", #POST的数据
                'timeout'  => 3,#超时时间 s
            ));            
        }
        
        echo "OK";
        exit;
        
    }
    /**
     * 更新会员的增量信息
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        $db = Db_Andyou::instance();
        //------------------------------------
        //将本地最新添加或者修改的会员同步到远端
        //------------------------------------
        
        //获取一个同步的时间
        $sql = "select tm from log_yunrsync where name = 'memberinfo_up'";
        $lastUpTm = $db->getOne($sql);
        
        //获得最新添加、修改的会员
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
            'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpNew&token={$token}", #要请求的URL数组
            'postdata' => "data=$jsonstr", #POST的数据
            'timeout'  => 3,#超时时间 s
        ));         
        $db->query("update log_yunrsync set tm = ". SYSTEM_TIME ."  where name = 'memberinfo_up'");
        
        
        echo "OK"; 
    }
    
    
    /** 
     * 同步所有的日志
     */
    public function doUpLog(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        $db = Db_Andyou::instance();
        
        //---------------------------
        //积分、会员卡 更改历史
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
                //获得会员的信息
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
                'url'      => $output->yunUrl ."?". $output->url ."&token={$token}", #要请求的URL数组
                'postdata' => "table={$output->table}&data=$jsonstr", #POST的数据
                'timeout'  => 3,#超时时间 s
            ));
             return $rtn;
        }
        return false;
    }
	
}

