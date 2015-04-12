<?php
/**
 * 后台管理员
 */
class Helper_AdminUser extends Helper_Abstract {
   
    
    public static function passwdEncrypt($str){
        
        $salt = "@kfdldf*zhongwt*&";
        return md5(md5($salt .$str. $salt));
    } 
    

    /**
     * 获得管理员管理列表
     */
    public static function getAdminUserList($params){
        $options = array(
            'num'             => 10,    #数量
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'adminUser',    #表名
                    'cols'          => 'id id,userId userId,passwd passwd,isAdmin isAdmin',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条管理员管理信息
     */
    public static function getAdminUserInfo($params){
        $options = array(
            'userId'          => false, #用户名
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($userId)$whereSql .= "and userId = '{$userId}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'adminUser',    #表名
                    'cols'          => '*',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }




    
    
}
