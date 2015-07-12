<?php
/**
* Db封装的KV
* 仲伟涛 2012-9-14
*/
class API_Item_Kv_Db
{
    
    public static function set($paramArr){
		$options = array(
            'key'           => '', #
            'val'           => '', #
            'life'          => 0, #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $db = API_Db_AndyouYun::instance();
        
        $startTm = SYSTEM_TIME;
        $endTm   = $life ? SYSTEM_TIME + $life : SYSTEM_TIME + 86400000; 
        $db->query("delete from kv where `key` = '{$key}'");
        echo
        $sql = "insert into kv(`key`,`value`,starTm,endTm) values('{$key}','{$val}',{$startTm},{$endTm})";
        $db->query($sql);
        return true;
    }
    
    

    /**
     * 封装的get请求
     */
    public static function get($paramArr){
		$options = array(
            'key'           => '', #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $db = API_Db_AndyouYun::instance();
        
        $sql = "select * from kv where `key` = '{$key}' limit 1";
        $row = $db->getRow($sql);
        $value = false;
        if($row && $row['endTm'] > SYSTEM_TIME){
            $value = $row['value'];
        }
        return $value;
    }
    

}
