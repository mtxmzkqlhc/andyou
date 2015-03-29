<?php
/**
 * MemcacheЭ�����Ϣ����
 * @author ��ΰ��
 * 2011-7
 */
class ZOL_Queue_Memcache extends ZOL_Queue_Abstraction
{
    protected static $serverIp   = '10.15.184.112'; #��Ϣ���еķ������������Ϣ
    protected static $serverPort = 11213;
    protected static $mem;

    protected static function init()
    {
        if (empty(self::$mem))
        {
            self::$mem = new Memcache;
            self::$mem->connect(self::$serverIp, self::$serverPort,1);//�����������ǳ�ʱʱ��(��),�����Ϊʲô��Ϊ��λ��
        }
    }
    /**
     * ���һ�������е�����
     * @param $key ���key����
     * @return 
     */
    public static function get($key,$limit=1)
    {
        self::init();
        $key = $key . '-' . $limit;
        return self::$mem->get($key);
    }
    /**
     * ����������һ������
     * @param $key ����KEY
     * @param $var ����
     */
    public static function set($key = '', $var = '')
    {
        self::init();
        return self::$mem->set($key, $var, 0, 0);
    }
}

