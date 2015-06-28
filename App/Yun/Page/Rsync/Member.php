<?php
/**
 * 会员信息的同步
 *
 */
class  Yun_Page_Rsync_Member  extends Yun_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Member';
        if (!parent::baseValidate($input, $output)) { return false; }
		return true;
	}
    
    /**
     * 同步所有日志
     */
    public function doUpLog(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $token = $input->get("token");
        //token的验证
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
                        if(!$has){//如果不存在就插入到本地
                            unset($d['id']);
                            unset($d['rsync']);
                            $item = $d;
                            $h = Helper_Dao::insertItem(array(                            
                                'addItem'       =>  $item, #数据列
                                'dbName'        =>  "Db_AndyouYun",    #数据库名
                                'tblName'       =>  $table,   #表名
                            ));
                            if($h){//更新ok
                                //查看会员是否存在,先判断的目的，会员信息还没有同步上来
                                $mhas = $db->getOne("select 'x' from member where phone = '{$phone}' limit 1");
                                if($mhas){
                                    //更新会员的数据
                                    $dstr = $item["direction"] ? "-":"+";
                                    if("log_scorechange" == $table){//积分
                                        $sql  = "update member set score = score {$dstr} {$item["score"]},uptim=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("log_cardchange" == $table){//会员卡
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
     * 同步所有的数据
     */
    public function doUpAll(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token的验证
        //
        if($token != md5("c=Rsync_Member&a=UpAll"."AAFDFDF&RE3")){
            echo "001";
            exit;
        }
        $db = Db_AndyouYun::instance();
        
        //获得本地的会员
        $data = $input->post("data");
        if($data){
            $data = base64_decode($data);
            $data = api_json_decode($data);
            if($data){
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from member where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//如果不存在就插入到本地
                        unset($d["id"]);
                        $item = $d;
                        $item["uptim"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  "Db_AndyouYun",    #数据库名
                            'tblName'       =>  "member",   #表名
                        ));
                    }
                }
            }
        }
        
        echo 1;
        exit;
    }
    
    
	
}

