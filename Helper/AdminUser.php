<?php
/**
 * ��̨����Ա
 */
class Helper_AdminUser extends Helper_Abstract {
   
    
    public static function passwdEncrypt($str){
        
        $salt = "@kfdldf*zhongwt*&";
        return md5(md5($salt .$str. $salt));
    } 
    

    /**
     * ��ù���Ա�����б�
     */
    public static function getAdminUserList($params){
        $options = array(
            'num'             => 10,    #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'adminUser',    #����
                    'cols'          => 'id id,userId userId,passwd passwd,isAdmin isAdmin',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ������Ա������Ϣ
     */
    public static function getAdminUserInfo($params){
        $options = array(
            'userId'          => false, #�û���
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($userId)$whereSql .= "and userId = '{$userId}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'adminUser',    #����
                    'cols'          => '*',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }




    
    
}
