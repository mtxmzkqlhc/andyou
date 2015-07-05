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
                                    if("log_scorechange" == $table){//积分
                                        $dstr = $item["direction"] ? "-":"+";
                                        $sql  = "update member set score = score {$dstr} {$item["score"]},upTm=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("log_cardchange" == $table){//会员卡
                                        $dstr = $item["direction"] ? "-":"+";
                                        $sql  = "update member set balance = balance {$dstr} {$item["card"]},upTm=".SYSTEM_TIME." where phone = '{$phone}'";
                                    }else if("bills" == $table){//消费记录
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
     * 同步所有的数据
     * 是将单机上的所有数据都同步到云端，一般情况下不需要都执行
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
                $okIdArr = array();
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from member where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//如果不存在就插入到本地
                        $okIdArr[] = $d["siteObjId"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  "Db_AndyouYun",    #数据库名
                            'tblName'       =>  "member",   #表名
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
     * 同步所有的会员其他产品数据
     * 是将单机上的所有数据都同步到云端，一般情况下不需要都执行
     */
    public function doUpAllOtherPro(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token的验证
        //
        if($token != md5("c=Rsync_Member&a=UpAllOtherPro"."AAFDFDF&RE3")){
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
                $okIdArr = array();
                foreach($data as $d){
                    $phone = $d["phone"];
                    $sql = "select 'x' from memeberotherpro where phone = '{$phone}' limit 1 ";
                    $has = $db->getOne($sql);
                    if(!$has){//如果不存在就插入到本地
                        $okIdArr[] = $d["siteObjId"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  "Db_AndyouYun",    #数据库名
                            'tblName'       =>  "memeberotherpro",   #表名
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
     * 同步最新数据
     */
    public function doUpNew(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        //token的验证
        //
        if($token != md5("c=Rsync_Member&a=UpNew"."AAFDFDF&RE3")){
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
                
                $okIdArr = array(); 
                foreach($data as $d){
                    
                    $phone = $d["phone"];
                    $sql   = "select * from member where phone = '{$phone}' limit 1 ";
                    $info  = $db->getRow($sql);
                    if(!$info){//如果不存在就插入到云端
                        $okIdArr[] = $d["id"];
                        unset($d["id"]);
                        unset($d["rsync"]);
                        $item = $d;
                        $item["upTm"] = SYSTEM_TIME;
                        Helper_Dao::insertItem(array(                            
                            'addItem'       =>  $item, #数据列
                            'dbName'        =>  "Db_AndyouYun",    #数据库名
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
                                'introducer'     => $d["introducer"],
                                'introducerId'   => $d["introducerId"],
                                'upTm'           => $d["upTm"],
                            );
                            
                            Helper_Dao::updateItem(array( 
                                'editItem'      =>  $item, #数据列
                                'dbName'        =>  "Db_AndyouYun",    #数据库名
                                'tblName'       =>  "member",   #表名
                                'where'         =>  "phone = '{$phone}'",    #条件
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
     * 获得最新的数据
     */
    public function doGetNew(ZOL_Request $input, ZOL_Response $output){
        
        $token = $input->get("token");
        $tm    = (int)$input->get("tm");
        //token的验证
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

