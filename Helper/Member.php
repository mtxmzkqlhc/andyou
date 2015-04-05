<?php
/**
 * 会员相关
 */
class Helper_Member extends Helper_Abstract {
   
    
    /**
     * 获得会员分类管理列表
     */
    public static function getMemberCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from membercate ","id","name");
            
    }


    
    /**
     * 获得会员分类管理列表
     */
    public static function getMemberCateList($params){
        $options = array(
            'num'             => 10,    #数量
            'name'            => false, #分类名
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'membercate',    #表名
                    'cols'          => 'id id,name name',   #列名
                    'limit'         => $num,    #条数
                    'whereSql'      => $whereSql,    #where条件
                    #'debug'        => 1,    #调试
       ));
       
       return $data;
    }



    /**
     * 获得一条会员分类管理信息
     */
    public static function getMemberCateInfo($params){
        $options = array(
            'id'              => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';

        if($id)$whereSql .= "and id = '{$id}' " ;

        $data = Helper_Dao::getRow(array(
                'dbName'        => 'Db_Andyou',    #数据库名
                'tblName'       => 'membercate',    #表名
                'cols'          => 'id id,name name',   #列名
                'whereSql'      => $whereSql,    #where条件
                #'debug'        => 1,    #调试
       ));
       
       return $data;
    }

  /**
     * 获得一条会员信息
     */
    public static function getMemberInfo($params){
        $options = array(
            'id'              => false, #ID
            'phone'           => false, #ID
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);        
            
        $whereSql   = '';
        if(!$id && !$phone)return false;
        
        if($id)$whereSql .= "and id = '{$id}' " ;
        if($phone)$whereSql .= "and phone = '{$phone}' " ;

        $data = Helper_Dao::getRow(array(
                'dbName'        => 'Db_Andyou',    #数据库名
                'tblName'       => 'member',    #表名
                'cols'          => '*',   #列名
                'whereSql'      => $whereSql,    #where条件
               # 'debug'        => 1,    #调试
       ));
        //获得会员类型
       $memberCate = Helper_Member::getMemberCatePairs();
       if($data){
            $data['cateName'] = isset( $memberCate[ $data["cateId"] ]) ? $memberCate[$data["cateId"]]:'未知';
       }
       return $data;
    }

    
    
}
