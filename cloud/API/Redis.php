<?php
/**
 * Redis��
 * @author ��ΰ��
 * 2011-7
 */
class API_Redis{

    protected static $redisArr  = array();#������redis����ļ���
    protected static $redis     = false;  #��ǰ����������redis����
    protected static $dalCfg    = false;

    /**
     * ����������
     */
    public static  $server = array(
        'Default' =>  array( #�����񣬱�ҵ��ķ�����redis
                    'host' => 'redis_cache_server_1_6505.zoldbs.com.cn', #IP
                    'port' => '6505'           #port
                ),
        'My' =>  array( #My���
                    'host' => 'redis_cache_server_1_6503.zoldbs.com.cn', #IP
                    'port' => '6503'           #port
                ),
        'ZCloud1' =>  array( #˽���Ƶ�Reids
                    'host' => '10.15.184.51', #IP
                    'port' => '6379'           #port
                ),
        'ZCloud2' =>  array( #˽���Ƶ�Reids
                    'host' => '10.15.184.55', #IP
                    'port' => '6379'           #port
                ),
        'Mojing1' =>  array( #ħ����Reids
                    'host' => '10.15.184.97', #IP
                    'port' => '6379'           #port
                ),
        '10.15.184.60' =>  array( #˽���Ƶ�Reids
                    'host' => '10.15.184.60', #IP
                    'port' => '6379'           #port
                ),
        '10.15.184.63' =>  array( #˽���Ƶ�Reids
                    'host' => '10.15.184.63', #IP
                    'port' => '6379'           #port
                ),
        '10.15.184.112' =>  array( #˽���Ƶ�Reids
                    'host' => '10.15.184.112', #IP
                    'port' => '6379'           #port
                ),
        'RubbishKill' =>  array( #��������
                    'host' => 'redis_cache_server_1_6504.zoldbs.com.cn', #IP
                    'port' => '6504'           #port
                ),
        'Merchant' =>  array( #������
                    'host' => 'redis_cache_server_1_6501.zoldbs.com.cn', #IP
                    'port' => '6501'           #port
                ),
        '55bbs' =>  array( #55bbs��Reids
                    'host' => '10.15.184.39',  #IP
                    'port' => '6379'           #port
                ),
        'ResysQ' =>  array( #�Ƽ�ϵͳ�Ķ���
                    'host' => '10.15.184.97',  #IP
                    'port' => '6379'           #port
                ),
        '10.15.184.205' =>  array( #�Ƽ�ϵͳ�Ķ���
                    'host' => '10.15.184.205',  #IP
                    'port' => '6379'           #port
                ),
        '10.15.184.80' =>  array( #��ά
                    'host' => '10.15.184.80',  #IP
                    'port' => '6379'           #port
                ),
        'DataWarehouse' =>  array( #���ݲֿ⣬���Ǳ�
                    'host' => '10.15.185.118',  #IP
                    'port' => '6380'           #port
        ),
        'SaaS' =>  array( #SaaS
                    'host' => '10.15.184.161',  #IP
                    'port' => '6379'           #port
                )
    );
    public static function init($server = 'Default'){

        if (!isset(self::$redisArr[$server])){
            if (class_exists("Redis")) {
                #���������Ϣ
                $hostInfo        = self::$server[$server];
                if($hostInfo){
                    #����redis
                    self::$redis    = new Redis();
                    self::$redis->connect($hostInfo['host'],$hostInfo['port']);
                    self::$redisArr[$server] = self::$redis;
                }
            } else {
                die("Redis�ӿ�ģ�鲻����");
            }
        }else{
            self::$redis = self::$redisArr[$server];
        }

    }
    /**
     * ���Redis���ӣ�����ֱ����������ӽ������ݲ���
     */
    public static function getLink($server){

        if(!self::$server[$server])return false;
        self::init($server);
        return self::$redis;
        
    }

    /**
     * ����ѹ����ʽ
     */
    private static function compress($value){
        $value = array('D' => $value);
        return serialize($value);
    }

    /**
     * ����ѹ����ʽ
     */
    private static function unCompress($value){
        $unValue = unserialize($value);
        return $unValue && isset($unValue['D']) || $unValue['D']==null ? $unValue['D'] : $value;
    }

    /*--------------------------------------------------------------------
                            key-value����
    ---------------------------------------------------------------------*/
    /**
     * key-value д
     */
    public static function set($server,$key,$value,$time=86400){
        
        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        $value  = self::compress($value);

        $time   = $keyInfo['time'];
        if ($time <> 0) {
            return self::$redis->setex($key,$time,$value);
        } else {
            return self::$redis->set($key,$value);
        }
    }

    /**
     * key-value ��
     */
    public static function get($server,$key){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);
        return self::unCompress(self::$redis->get($key));
    }


    /**
     * ���ö�ֵ
     */
    public function setMulti($server,$key){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        $keyArr = array();
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $keyArr[$k]   = self::compress($v);
            }
        }
        return self::$redis->mset($keyArr);
    }

    /**
     * ��ȡ��ֵ
     * @param subKey �����������
     */
    public function getMulti($server,$keyArr){
        if(!self::$server[$server])return false;
        self::init($server);


        $arr = self::$redis->mget($keyArr);
        if (is_array($arr)) {
            foreach ($arr as $key=>$row) {
                $arr[$key]  = self::unCompress($row);
            }
        }
        return $arr;
    }

    /**
     * ɾ��Ԫ��
     */
    public static function delete($server,$key){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);        
        return self::$redis->delete($key);
    }


    /*--------------------------------------------------------------------
                                   hash����
    ---------------------------------------------------------------------*/
    /**
     * �浥��ֵ
     */
    public static function hashSet($server,$key,$subKey,$value,$time=86400)
    {
        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        $re = self::$redis->hSet($key, $subKey, self::compress($value));
        if ($time > 0) {
            self::$redis->expire($key, $time);
        }
        return $re;
    }

    /**
     * ȡ����ֵ
     */
    public static function hashGet($server,$key, $subKey){
        if(!self::$server[$server] || !$key)return false;
        self::init($server);
        return self::unCompress(self::$redis->hGet($key, $subKey));
    }

    /*--------------------------------------------------------------------
                                   set����
    ---------------------------------------------------------------------*/
    /**
     * ���Ӽ���Ԫ��
     */
    public static function sAdd($server,$key,$value,$time=86400){
        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        $re     = self::$redis->sAdd($key, $value);#��Ϊset�Ĵ����ǲ�ѹ����
        if ($time > 0) {
            self::$redis->expire($key, $time);
        }
        return $re;
    }

    /**
     * ɾ��һ��ָ����Ԫ��
     */
    public static function sDelete($server,$key , $value){
        if(!self::$server[$server] || !$key)return false;
        self::init($server);
        return self::$redis->sRemove($key, $value);
    }

    /**
     * �ƶ�Ԫ��
     *
     * @param Ҫ�ƶ��漰��key $fromKey
     * @param �ƶ�����key $toKey
     * @param Ԫ�� $value
     */
    public static function sMove($server ,$fromKey, $toKey, $value){

        if(!self::$server[$server])return false;
        self::init($server);

        $value  = self::compress($value);
        return self::$redis->sMove($fromKey, $toKey, $value);
    }

    /**
     * ͳ��Ԫ�ظ���
     */
    public static function sSize($server,$key){
        
        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        return self::$redis->sSize($key);
    }

    /**
     * �ж�Ԫ���Ƿ�����ĳ��key
     */
    public static function sIsMember($server,$key, $value){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);
        
        return self::$redis->sIsMember($key, $value);
    }

    /**
     * �󽻼�
     *
     * @param key���� $keyArr
     */
    public static function sInter($server,$keyArr = array()){
        if(!self::$server[$server])return false;
        self::init($server);
       
       return self::$redis->sInter($keyArr);
    }

    /**
     * �󽻼����洢�������key��
     *
     * @param key���� $keyArr 'output', 'key1', 'key2', 'key3'
     */
    public static function sInterStore($server,$key,$ouput,$keyArr){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        array_unshift($keyArr,$ouput);  #���뵽����Ŀ�ͷ
        return call_user_func_array(array(self::$redis, "sInterStore"), $keyArr);
    }

    /**
     * �󲢼�
     *
     * @param key���� $keyArr
     */
    public static function sUnion($server,$key,$keyArr = array()){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        if($keyArr){
            return self::$redis->sUnion($keyArr);
        }
    }

    /**
     * �� A-B�Ĳ���
     *
     * @param key���� $keyArr
     */
    public static function sDiff($server,$key,$keyArr = array()){

        if(!self::$server[$server] || !$key)return false;
        self::init($server);


        if($keyArr){
            return self::$redis->sDiff($keyArr);
        }
    }
    /**
     * ��ȡ��ǰkey�µ�����Ԫ��
     *
     * @param key���� $key
     */
    public static function sMembers($server,$key){
        if(!self::$server[$server] || !$key)return false;
        self::init($server);

        return self::$redis->sMembers($key);
    }



}

