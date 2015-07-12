<?php
/**
* MongoDB������װ
* ��ΰ�� 2012-10-29
*/
class API_Item_Kv_MongoDB
{

    /**
     * ������е�MongoDB��������Ϣ
     */
    public static function getAllServer(){
        return API_MongoDB::$serverCfgArr;
    }

    /**
     * �鿴db��
     */
    public static function stats($paramArr){
		$options = array(
            'server'    => 'localhost', #�������������ռ�API_Redis�Ķ���
            'db'        => false, #���ݿ�
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if(!$server)return false;

        return API_MongoDB::stats($server,$db);
    }



    /**
     * ���һ������
     */
    public static function findOne($paramArr){
		$options = array(
            'server'      => 'localhost', #�������������ռ�API_Redis�Ķ���
            'db'          => false, #���ݿ�
            'collection'  => false, #collection
            'key'         => false, #������ݵ�key
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        return API_MongoDB::findOne($server,$db,$collection,$key);
    }

    /**
     * ����һ������
     */
    public static function save($paramArr){
		$options = array(
            'server'      => 'localhost', #�������������ռ�API_Redis�Ķ���
            'db'          => false, #���ݿ�
            'collection'  => false, #collection
            'key'         => false, #������ݵ�key
            'value'       => false, #�������ֵ
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        return API_MongoDB::save($server,$db,$collection,$key,$value);
    }
}

?>