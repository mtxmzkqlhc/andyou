<?php
/**
 * 配置相关Helper
 */
class Helper_Option extends Helper_Abstract {
   
    
    /**
     * 获得所有配置数组
     */
    public static function getAllOptions(){
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_Andyou',    #数据库名
                    'tblName'       => 'options',    #表名
                    'cols'          => '*',   #列名
       ));
       
       $outArr = array();
       if($data){
           foreach($data as $d){
               $d["value"] = $d["isInt"] ? (int)$d["value"] : $d["value"];
               $outArr[$d["name"]] = $d;
           }
       }
       return $outArr;
    }


    
    
}
