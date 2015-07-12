<?php
/**
 * 一些通用子路的同步
 *
 */
error_reporting(E_ALL);
ini_set("display_errors",1);
class  Andyou_Page_Rsync_Item  extends Andyou_Page_Abstract {
    /**
     * 验证
     */
    public function validate(ZOL_Request $input, ZOL_Response $output){
		$output->pageType = 'Rsync_Item';
        if (!parent::baseValidate($input, $output)) { return false; }
        
        //获得站点
        $output->sysName    = $output->sysCfg['SysId']["value"] ;
        
		return true;
	}
    
    public function doUpData(ZOL_Request $input, ZOL_Response $output){
        set_time_limit(0);
        $db = Db_Andyou::instance();
        
        $isAll = (int)$input->get("isAll");
        $whereSql = "";
        if(!$isAll){

            //获取一个同步的时间
            $sql = "select tm from log_yunrsync where name = 'itemtimestamp_up'";
            $lastUpTm = (int)$db->getOne($sql);
            if($lastUpTm > 0)$lastUpTm = $lastUpTm - 1;
            else $lastUpTm = SYSTEM_TIME;
            
            $whereSql .= " and rowTm > '".date("Y-m-d H:i:s",$lastUpTm)."'";
        }
        
        $tableArr = array("staff","product");
        if($tableArr){
            foreach($tableArr as $table){
                
                $sql = "select * from {$table} where 1 {$whereSql}";
                $output->data = $db->getAll($sql);
                if($output->data){
                    $output->url  = "c=Rsync_Item&a=UpData";
                    $output->table = $table;
                    $rtnJson = $this->doPost($input,$output);
                    echo "<hr/>";
                    echo $rtnJson;
                   
                }
                
            }
        }
        
        $db->query("delete from log_yunrsync where name = 'itemtimestamp_up'");
        $db->query("insert into log_yunrsync(name,tm) values('itemtimestamp_up',". SYSTEM_TIME .")");
        echo "OK";
        exit;
    }
    
    
    private function doPost(ZOL_Request $input, ZOL_Response $output){
        $db = Db_Andyou::instance();
        $res = $output->data;
        if($res){
            $data = array();
            foreach ($res as $re){
                $re["site"]  = $output->sysName;
                $re["objId"] = $re["id"];
                
                unset($re["id"]);
                unset($re['rowTm']);
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

