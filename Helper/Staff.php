<?php
/**
 * Ա�����
 */
class Helper_Staff extends Helper_Abstract {
   
    
    /**
     * ��û�Ա��������б�
     */
    public static function getStaffCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from staffcate","id","name");
            
    }


    
    
    /**
     * ���Ա�������б�
     */
    public static function getStaffList($params){
        $options = array(
            'num'             => 10,    #����
            'cateId'          => false, #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($cateId)$whereSql .= "and cateId = '{$cateId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'staff',    #����
                    'cols'          => 'id id,name name,inDate inDate,byear byear,bmonth bmonth,bday bday,cateId cateId,salary salary,percentage percentage',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ��Ա��������Ϣ
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
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'staff',    #����
                    'cols'          => 'id id,name name,inDate inDate,byear byear,bmonth bmonth,bday bday,cateId cateId,salary salary,percentage percentage',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }


    /**
     * ���Ա����������б�
     */
    public static function getStaffCateList($params){
        $options = array(
            'num'             => 10,    #����
            'name'            => false, #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'staffcate',    #����
                    'cols'          => 'id id,name name,salary salary,percentage percentage',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ��Ա�����������Ϣ
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
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'staffcate',    #����
                    'cols'          => 'id id,name name,salary salary,percentage percentage',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }




    
    
}
