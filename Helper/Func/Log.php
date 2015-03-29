<?php
/**
 * 日志相关函数
 * 仲伟涛
 * 2013-11-26
 */
class Helper_Func_Log extends Helper_Abstract {
    
    /**
     * 记录日志-根据数组各键对应值前后变化
     */
    public static function recordArr($param_arr = array()) {
        $options = array(
            'appName'       => '',      #APP名
			'subId'         => 0,       #二级ID
			'objId'         => 0,       #objid
            'currentArr'    => array(), #当前修改数组
            'originArr'     => array(), #原始记录数组
            'isLogEmpty'    => 1,       #是否记录无变动的记录,1-是,0-否
			'userId'        => false,   #指定userId
        );
        if (is_array($param_arr))$options = array_merge($options, $param_arr);
        extract($options);
        
        //日志内容
        $content = '';
        
        //如果存在当前数组
        if ($currentArr) {
            foreach ($currentArr as $key => $value) {
                //如果是数组
                if (is_array($value)) {
                    //进行排序
                    arsort($value);
                    //组合成字符串
                    $value = json_encode($value);
                }
                
                //如果没有原始数组,则认为是插入
                if (empty($originArr)) {
                    $content .= $key . '为' . $value . "\r\n";
                } else {
                    //如果是原始的数组 , 因为从数据库读取出来之后,与 POST 差别在addslashes
                    if (isset($originArr[$key])) {
                        //如果是数组
                        if (is_array($originArr[$key])) {
                            arsort($originArr[$key]);
                            //组合成字符串
                            $cvalue = json_encode($originArr[$key]);
                        } else {
                            $cvalue = addslashes($originArr[$key]);
                        }
                    } else {
                        $cvalue = '';
                    }
                    //判断有无变化
                    if ($value != $cvalue) {
                        $content .= $key . '从' . $cvalue . '变成' . $value . "\r\n";
                    }
                }
            }
        }
        
        //如果未指定记录方式 或者 非空才记录
        if ($isLogEmpty || $content) {
            self::record(array(
                'appName'   =>  $appName,    #APP名
                'subId'     =>  $subId,     #二级ID
                'objId'     =>  $objId,     #objid
                'log'       =>  $content,   #日志内容
                'userId'    =>  $userId,    #指定用户名
            ));
        }
    }
    
    /**
     * 记录日志
     */    
    public static function record($paramArr) {
		$options = array(
			'appName'   =>  '',    #APP名
			'subId'     =>  0,     #二级ID
			'objId'     =>  0,     #objid
			'log'       =>  '',    #日志内容
			'userId'    => false,   #指定userId
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $idMap = ZOL_Config::get("Star/InnerApp","ID");        #顶级的分类        
        if(!isset($idMap[$appName]))return false;
        
        $subIdMap = ZOL_Config::get("Star/InnerApp","SUBID");  #二级的分类 
        #将subid从英文形式转换为数字
        if(isset($subIdMap[$appName]) && isset($subIdMap[$appName][$subId])) $subId = $subIdMap[$appName][$subId]["id"];
        $output = ZOL_Registry::get('response'); #获得output对象 
        #用户ID,如果没有传入，就从父类获得而用户ID
        if(!$userId){ 
            $userId = $output->userId;
        }
        $item = array(
            "appId"    => $idMap[$appName]["id"],
            "subId"    => $subId,
            "objId"    => $objId,
            "content"  => addslashes($log),
            "tm"       => SYSTEM_TIME,
            "adder"    => $userId,
            "pageType" => $output->pageType,
        );
        
        Helper_Dao::insertItem(array(
            'addItem'  =>  $item,
            'dbName'   =>  "Db_Star",      #数据库名
            'tblName'  =>  "log_operations",   #表名
            'debug'    =>  0,
        ));
        
        return true;
    }
    
     
    /**
     * 获得日志列表
     */    
    public static function getList($paramArr) {
		$options = array(
			'appName'   =>  '',    #APP名
			'subId'     =>  0,     #二级ID
			'objId'     =>  0,     #objid
			'num'       =>  10,    #条数
            'offset'    => '',
			'getNum'    =>  0,     #是否获得总数量
            'whereSql'  => ""      #条件
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $idMap = ZOL_Config::get("Star/InnerApp","ID");        #顶级的分类    
        if(!isset($idMap[$appName]))return false;
        
        $subIdMap = ZOL_Config::get("Star/InnerApp","SUBID");  #二级的分类 
        #将subid从英文形式转换为数字
        if(isset($subIdMap[$appName]) && isset($subIdMap[$appName][$subId])) $subId = $subIdMap[$appName][$subId]["id"];   
        
         
        if($appName)$whereSql .= " and appId = " . $idMap[$appName]["id"];
        if($subId)  $whereSql .= " and subId = " . $subId;
        if($objId)  $whereSql .= " and objId = '" . $objId."'";
        
        $res = Helper_Dao::getRows(array(
                                            'dbName'   =>  "Db_Star",      #数据库名
                                            'tblName'  =>  "log_operations",   #表名
                                            'offset'   =>  $offset,     #offset
                                            'limit'    =>  $num,    #条数
                                            'whereSql' =>  $whereSql . " order by id desc ",    #where条件
                                            'debug'    =>  0,
                                    ));
        
        if($getNum){#是否获得数量
            $cnt = Helper_Dao::getCount(array(
                                            'dbName'   =>  "Db_Star",      #数据库名
                                            'tblName'  =>  "log_operations",   #表名
                                            'whereSql' =>  $whereSql ,    #where条件
                                            'debug'    =>  0,
                                    ));
            return array('allNum' => $cnt , 'data' => $res );
            
        }
        return $res;
    }
        
}
