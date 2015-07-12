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
     * 是将单机上的所有数据都同步到云端，一般情况下不需要都执行
     * ?c=Rsync_Member&a=UpAll
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $db = Db_Andyou::instance();
        $pageSize = 100;
        //获得会员总数
        $allSum  = $db->getOne("select count(*) from member");
        $loopCnt = ceil($allSum/$pageSize);
       
        for($i=0;$i<$loopCnt;$i++){
            echo "==LOOP:{$i}==<br/>";
            $s = $i * $pageSize;
            //获得本地的会员
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
                    'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAll&token={$token}", #要请求的URL数组
                    'postdata' => "data=$jsonstr", #POST的数据
                    'timeout'  => 3,#超时时间 s
                ));  
                #设置同步状态
                $okIdArr = json_decode($rtnJson);
                if($okIdArr && is_array($okIdArr)){
                    foreach($okIdArr as $id){
                        echo "{$id} OK<br/>";
                        $db->query("update member set rsync = 1 where id = {$id} ");
                    }
                }
            }
        }
        
        //获得会员其他商品总数
        $allSum  = $db->getOne("select count(*) from memeberotherpro");
        $loopCnt = ceil($allSum/$pageSize);
       
        for($i=0;$i<$loopCnt;$i++){
            echo "==LOOP:{$i}==<br/>";
            $s = $i * $pageSize;
            //获得本地的会员
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
                    'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpAllOtherPro&token={$token}", #要请求的URL数组
                    'postdata' => "data=$jsonstr", #POST的数据
                    'timeout'  => 3,#超时时间 s
                ));  
                #设置同步状态
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
     * 更新会员的增量信息
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(600);
        error_reporting(E_ALL);
        ini_set("display_errors",1);
        $db = Db_Andyou::instance();
        $onlyGetFromYun = (int)$input->get("onlyGetFromYun");//是否仅更新云端数据
        $allData        = (int)$input->get("allData");//是否获得所有数据
        //------------------------------------
        //将本地最新添加或者修改的会员同步到远端
        //------------------------------------
        
        //获取一个同步的时间
        $sql = "select tm from log_yunrsync where name = 'memberinfo_up'";
        $lastUpTm = (int)$db->getOne($sql);
        if($lastUpTm > 0)$lastUpTm = $lastUpTm - 1;
        
        if($allData)$lastUpTm = 0;
        
        if(!$onlyGetFromYun){//是否仅仅获得远端数据
        
            //获得最新添加、修改的会员
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
                'url'      => $output->yunUrl . "?c=Rsync_Member&a=UpNew&token={$token}", #要请求的URL数组
                'postdata' => "data=$jsonstr", #POST的数据
                'timeout'  => 3,#超时时间 s
            )); 

            #设置同步状态
            $okIdArr = json_decode($rtnJson);
            if($okIdArr && is_array($okIdArr)){
                foreach($okIdArr as $id){
                    echo "{$id} OK<br/>";
                    $db->query("update member set rsync = 1 where id = {$id} ");
                }
            }
            
        }
            
       //获得云端最新的数据
        $urlPart = "c=Rsync_Member&a=GetNew&tm=".$lastUpTm;
        $token   = md5($urlPart."AAFDFDF&RE3");
        $url     = $output->yunUrl . "?{$urlPart}&token={$token}";
        $html    = ZOL_Http::curlPage( array(
            'url'      => $url, #要请求的URL数组
            'timeout'  => 30,#超时时间 
		));
        if($html){
            $data = api_json_decode($html);
            if($data){
                foreach($data as $d){
                    
                    $phone = $d["phone"];
                    $sql   = "select * from member where phone = '{$phone}' limit 1 ";
                    $info  = $db->getRow($sql);
                    if(!$info){//如果不存在就插入到云端
                        unset($d["id"]);
                        $item = $d;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  "Db_Andyou",    #数据库名
                            'tblName'       =>  "member",   #表名
                        ));
                    }else{#如果云端已经存在了
                        if($info["upTm"] < $d["upTm"]){//云端的更新时间比较老
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
                                'editItem'      =>  $item, #数据列
                                'dbName'        =>  "Db_Andyou",    #数据库名
                                'tblName'       =>  "member",   #表名
                                'where'         =>  "phone = '{$phone}'",    #条件
                            ));
                        }
                        
                    }
                    
                }
            }
            if(!$onlyGetFromYun){//是否仅仅获得远端数据
                $db->query("delete from log_yunrsync where name = 'memberinfo_up'");
                $db->query("insert into log_yunrsync(name,tm) values('memberinfo_up',". SYSTEM_TIME .")");
            }
        }
        
        echo "OK"; 
        exit;
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
        //同步次卡，otherpro
        //---------------------------
        #首先完善会员电话
        
        
        //---------------------------
        //同步订单bills
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
                //获得会员的信息
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
                'url'      => $output->yunUrl ."?". $output->url ."&token={$token}", #要请求的URL数组
                'postdata' => "table={$output->table}&data=$jsonstr", #POST的数据
                'timeout'  => 3,#超时时间 s
            ));
             return $rtn;
        }
        return false;
    }
	
}

