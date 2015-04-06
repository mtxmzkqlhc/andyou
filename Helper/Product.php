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
                    'cols'          => '*',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       //对报价的处理
        if($data){
            foreach($data as $k => $v){
                $data[$k]['oprice'] = $v["price"];//保存实际的价格
                $data[$k]['price']  = $v["price"]/100;//保存实际的价格
                
                
                $data[$k]['oinPrice'] = $v["inPrice"];//保存实际的价格
                $data[$k]['inPrice']  = $v["inPrice"]/100;//保存实际的价格
            }
        }
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
        //对报价的处理
        if($data){
            $data['oprice']   = $data["price"];//保存实际的价格
            $data['price']    = $data["price"]/100;//保存实际的价格
            $data['oinPrice']  = $data["inPrice"];//保存实际的价格
            $data['inPrice']  = $data["inPrice"]/100;//保存实际的价格
        }
       
       return $data;
    }



    
    
    
}
