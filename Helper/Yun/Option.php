<?php
/**
 * �������Helper
 */
class Helper_Yun_Option extends Helper_Abstract {
   
    
    /**
     * ���������������
     */
    public static function getAllOptions(){
        
        $data = Helper_Dao::getRows(array(
                    'dbName'        => 'Db_AndyouYun',    #���ݿ���
                    'tblName'       => 'options',    #����
                    'cols'          => '*',   #����
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