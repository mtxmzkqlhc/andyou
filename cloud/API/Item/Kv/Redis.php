<?php
/**
* Redis������װ
* ��ΰ�� 2012-9-14
*/
class API_Item_Kv_Redis
{
    /**
     * ���Redis�����Ӷ���
     */
    public static function getObj($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$serverName)return false;

        return API_Redis::getLink($serverName); #���redis����

    }

    /**
     * ������е�Redis��������Ϣ
     */
    public static function getAllServer(){
        return API_Redis::$server;
    }

    /**
     * string���͵�get����
     */
    public static function stringGet($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
            'key'           => false, #������ݵ�Key
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$key || !$serverName)return false;

        $redis = self::getObj(array('serverName'=>$serverName));

        return $redis->get($key);
    }

    /**
     * string���͵�set����
     */
    public static function stringSet($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
            'key'           => false, #������ݵ�Key
            'value'         => false, #����ֵ
            'life'          => 86400, #����ʱ��
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
     *  ɾ������
     */
    public static function delete($paramArr){
		$options = array(
            'serverName'    => 'Default', #�������������ռ�API_Redis�Ķ���
            'key'           => false,     #Ҫɾ����Key������������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$key || !$serverName)return false;

        $redis = self::getObj(array('serverName'=>$serverName));

        return $redis->delete($key);
    }

}

?>