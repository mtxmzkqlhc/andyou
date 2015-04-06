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
                    'cols'          => '*',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       //�Ա��۵Ĵ���
        if($data){
            foreach($data as $k => $v){
                $data[$k]['oprice'] = $v["price"];//����ʵ�ʵļ۸�
                $data[$k]['price']  = $v["price"]/100;//����ʵ�ʵļ۸�
                
                
                $data[$k]['oinPrice'] = $v["inPrice"];//����ʵ�ʵļ۸�
                $data[$k]['inPrice']  = $v["inPrice"]/100;//����ʵ�ʵļ۸�
            }
        }
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
        //�Ա��۵Ĵ���
        if($data){
            $data['oprice']   = $data["price"];//����ʵ�ʵļ۸�
            $data['price']    = $data["price"]/100;//����ʵ�ʵļ۸�
            $data['oinPrice']  = $data["inPrice"];//����ʵ�ʵļ۸�
            $data['inPrice']  = $data["inPrice"]/100;//����ʵ�ʵļ۸�
        }
       
       return $data;
    }



    
    
    
}
