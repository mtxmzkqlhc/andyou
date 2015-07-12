<?php
/**
* Memcache������װ
* ��ΰ�� 2012-9-14
*/
class API_Item_Kv_Memcached
{
    /**
     * ���Memcache�����Ӷ���
     */
    public static function getLink($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Memcached�Ķ���
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$serverName)return false;
        
        return API_Memcached::getLink($serverName); #���redis����

    }

    /**
     * ��װ��get����
     */
    public static function get($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
            'key'           => '', #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $link = self::getLink(array('serverName' => $serverName));
        if($link){
            $link = $link['obj'];
            return $link->get($key);
        }
    }
    
    /**
     * ��װ��set����
     */
    public static function set($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
            'key'           => '', #
            'value'         => '', #
            'time'          => 0, #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $link = self::getLink(array('serverName' => $serverName));
        if($link){
            if($link['memcached']){#�����memcached������,memcache��memcached�����ǲ�ͬ��
                $time = $time ? $time() + $time : 0;
                $link = $link['obj'];
                $link->set($key, $value,$time);
            }else{
                $link = $link['obj'];
                $link->set($key, $value,0,$time);
            }
        }

    }

}

?>