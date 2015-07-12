<?php
/**
* Redis操作封装
* 仲伟涛 2012-9-14
*/
class API_Item_Kv_Redis
{
    /**
     * 获得Redis的连接对象
     */
    public static function getObj($paramArr){
		$options = array(
            'serverName'    => 'Default', #服务器名，参照见API_Redis的定义
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$serverName)return false;

        return API_Redis::getLink($serverName); #获得redis对象

    }

    /**
     * 获得所有的Redis服务器信息
     */
    public static function getAllServer(){
        return API_Redis::$server;
    }

    /**
     * string类型的get方法
     */
    public static function stringGet($paramArr){
		$options = array(
            'serverName'    => 'Default', #服务器名，参照见API_Redis的定义
            'key'           => false, #获得数据的Key
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$key || !$serverName)return false;

        $redis = self::getObj(array('serverName'=>$serverName));

        return $redis->get($key);
    }

    /**
     * string类型的set方法
     */
    public static function stringSet($paramArr){
		$options = array(
            'serverName'    => 'Default', #服务器名，参照见API_Redis的定义
            'key'           => false, #获得数据的Key
            'value'         => false, #数据值
            'life'          => 86400, #缓存时间
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$key || !$serverName)return false;

        $redis = self::getObj(array('serverName'=>$serverName));
        if($life){
            $redis->setex($key, $life, $value);
        }else{
            $redis->set($key, $value);
        }
        return true;
    }

     /**
     *  删除操作
     */
    public static function delete($paramArr){
		$options = array(
            'serverName'    => 'Default', #服务器名，参照见API_Redis的定义
            'key'           => false,     #要删除的Key，可以是数组
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$key || !$serverName)return false;

        $redis = self::getObj(array('serverName'=>$serverName));

        return $redis->delete($key);
    }

}

?>