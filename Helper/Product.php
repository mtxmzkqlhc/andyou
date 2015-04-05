<?php
/**
 * 商品相关
 */
class Helper_Product extends Helper_Abstract {
   
    
    /**
     * 获得会员分类管理列表
     */
    public static function getProductCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from productcate ","id","name");
            
    }



    /**
     * 获得商品管理列表
     */
    public static function getProductList($params){
        $options = array(
            'num'             => 10,    #数量
            'code'            => false, #条码号
            'cateId'          => false, #分类ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($code)$whereSql .= "and code = '{$code}' " ;
        if($cateId)$whereSql .= "and cateId = '{$cateId}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'product',    #表名
                    'cols'          => 'id id,name name,code code,cateId cateId,price price,inPrice inPrice,stock stock,score score,discut discut,addtm addtm',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条商品管理信息
     */
    public static function getProductInfo($params){
        $options = array(
            'id'              => false, #ID
            'code'            => false, #条码
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;
        if($code)$whereSql .= "and code = '{$code}' " ;

        $data = Helper_Dao::getRow(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'product',    #表名
                    'cols'          => '*',   #列名
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    
    
    
}
