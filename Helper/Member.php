<?php
/**
 * ��Ա���
 */
class Helper_Member extends Helper_Abstract {
   
    
    /**
     * ��û�Ա��������б�
     */
    public static function getMemberCatePairs(){
        
        $db = Db_Andyou::instance();
        return $db->getPairs("select id,name from membercate ","id","name");
            
    }


    
    /**
     * ��û�Ա��������б�
     */
    public static function getMemberCateList($params){
        $options = array(
            'num'             => 10,    #����
            'name'            => false, #������
        );
        if(is_array($params)) $options = array_merge($options, $params);
        extract($options);
                    
        $whereSql   = '';

        if($name)$whereSql .= "and name = '{$name}' " ;
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #���ݿ���
                    'tblName'       => 'membercate',    #����
                    'cols'          => 'id id,name name',   #����
                    'limit'         => $num,    #����
                    'whereSql'      => $whereSql,    #where����
                    #'debug'        => 1,    #����
       ));
       
       return $data;
    }



    /**
     * ���һ����Ա���������Ϣ
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
                'dbName'        => 'Db_Andyou',    #���ݿ���
                'tblName'       => 'membercate',    #����
                'cols'          => 'id id,name name',   #����
                'whereSql'      => $whereSql,    #where����
                #'debug'        => 1,    #����
       ));
       
       return $data;
    }

  /**
     * ���һ����Ա��Ϣ
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
                'dbName'        => 'Db_Andyou',    #���ݿ���
                'tblName'       => 'member',    #����
                'cols'          => '*',   #����
                'whereSql'      => $whereSql,    #where����
               # 'debug'        => 1,    #����
       ));
        //��û�Ա����
       $memberCate = Helper_Member::getMemberCatePairs();
       if($data){
            $data['cateName'] = isset( $memberCate[ $data["cateId"] ]) ? $memberCate[$data["cateId"]]:'δ֪';
       }
       return $data;
    }

    
    
}
