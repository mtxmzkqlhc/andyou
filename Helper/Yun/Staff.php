<?php
/**
 * 员工相关
 */
class Helper_Yun_Staff extends Helper_Abstract {
   
    
    /**
     * 获得会员分类管理列表
     */
    public static function getStaffCatePairs(){
        
        $db = Db_AndyouYun::instance();
        return $db->getPairs("select id,name from staffcate","id","name");
            
    }

    /**
     * 获得会员分类管理列表
     */
    public static function getStaffPairs(){
        
        $db = Db_AndyouYun::instance();
        return $db->getPairs("select id,name from staff","id","name");
            
    }

    
    /**
     * 根据不同的站点获得员工
     */
    public static function getSiteStaffPairs(){
        
        $db = Db_AndyouYun::instance();
        $res = $db->getAll("select id,name,site,objId from staff");
        $outArr = array();
        if($res){
            foreach($res as $re){
                $outArr[$re["site"]][$re["objId"]] = $re["name"];
            }
        }
        return $outArr;
            
    }

    
    /**
     * 获得员工管理列表
     */
    public static function getStaffList($params){
        $options = array(
            'num'             => 10,    #数量
            'cateId'          => false, #分类
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($cateId)$whereSql .= "and cateId = '{$cateId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_AndyouYun',    #数据库名
                    'tblName'       => 'staff',    #表名
                    'cols'          => 'id id,name name,inDate inDate,byear byear,bmonth bmonth,bday bday,cateId cateId,salary salary,percentage percentage',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条员工管理信息
     */
    public static function getStaffInfo($params){
        $options = array(
            'id'              => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_AndyouYun',    #数据库名
                    'tblName'       => 'staff',    #表名
                    'cols'          => 'id id,name name,inDate inDate,byear byear,bmonth bmonth,bday bday,cateId cateId,salary salary,percentage percentage',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }


    /**
     * 获得员工分类管理列表
     */
    public static function getStaffCateList($params){
        $options = array(
            'num'             => 10,    #数量
            'name'            => false, #名称
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_AndyouYun',    #数据库名
                    'tblName'       => 'staffcate',    #表名
                    'cols'          => 'id id,name name,salary salary,percentage percentage',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条员工分类管理信息
     */
    public static function getStaffCateInfo($params){
        $options = array(
            'id'              => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_AndyouYun',    #数据库名
                    'tblName'       => 'staffcate',    #表名
                    'cols'          => 'id id,name name,salary salary,percentage percentage',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }




    
    
}
