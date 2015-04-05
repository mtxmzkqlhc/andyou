<?php
/**
 * ��Ʒ���
 */
class Helper_Product extends Helper_Abstract {
   
    
    /**
     * ��û�Ա��������б�
     */
    public static function getProductCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from productcate ","id","name");
            
    }



    /**
     * �����Ʒ�����б�
     */
    public static function getProductList($params){
        $options = array(
            'num'             => 10,    #����
            'code'            => false, #�����
            'cateId'          => false, #����ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($code)$whereSql .= "and code = '{$code}' " ;
        if($cateId)$whereSql .= "and cateId = '{$cateId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'product',    #����
                    'cols'          => 'id id,name name,code code,cateId cateId,price price,inPrice inPrice,stock stock,score score,discut discut,addtm addtm',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ����Ʒ������Ϣ
     */
    public static function getProductInfo($params){
        $options = array(
            'id'              => false, #ID
            'code'            => false, #����
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;
        if($code)$whereSql .= "and code = '{$code}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'product',    #����
                    'cols'          => '*',   #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    
    
    
}
