<?php
/**
 * Redis��
 * @author ��ΰ��
 * 2011-7
 */
/**
 * redis������������
 */
class API_ZOL_NoSql_DAL_Redis
{
    /**
     * ����KEY��ע��
     */
    private $dataKey = array(
        #���Ͱ��� STRING HASH SET ZSET LIST
        'commentUp' => array('type'=>'STRING','time' => 86400,'serverId'=>0, 'app'=>'Mobile'),  #����֧�ֵȲ���
        'checkCode' => array('type'=>'STRING','time' => 1800,'serverId'=>0,  'app'=>'Mobile'),  #��֤��
        'qrcode'    => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #��λ��
        'recommend' => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #�Ƽ���Ŀ
        'picRecomm' => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #ͼƬ�Ƽ���Ŀ
        'checkRecomm' => array('type'=>'STRING','time' => 84600,'serverId'=>0,'app'=>'Mobile'),  #�Ƽ�����
    );

    /**
     * ������
     */
    private $connentServer = array( 
        0 =>  array( #�����񣬱�ҵ��ķ�����redis
                    'host' => '10.15.185.118', #IP
                    'port' => '6505'           #port
                )
    );

    /**
     * ���������Ϣ����ͬҵ��������Ϣ��ͬ
     * $snId : Ҫ���ӵ�redis sn
     */
    public function getConnectInfo($snId=0){
        return $this->connentServer[$snId];
	}

    /**
     * �������key
     */
    public function getKeyInfo($name){
        if(isset($this->dataKey[$name])){
            $info     = $this->dataKey[$name];

            return array(
                'server' => $this->getConnectInfo($info['serverId']),
                'key'    => $info['app'] . ":" . $name,
                'type'   => $info['type'],
                'time'   => $info['time'],
            );
        }
        return false;
    }


}
class API_ZOL_NoSql_Redis
{

    protected static $redisArr  = array();#������redis����ļ���
    protected static $redis     = false;  #��ǰ����������redis����
    protected static $dalCfg    = false;

    protected static function init($key){
        
        if (!isset(self::$redisArr[$key])){
            if (class_exists("Redis")) {
                #���������Ϣ
                $dal            = new API_ZOL_NoSql_DAL_Redis();
                $keyInfo        = $dal->getKeyInfo($key);
                if($keyInfo){
                    $hostInfo   = $keyInfo['server'];
                    self::$dalCfg[$key]   = $keyInfo;
                    #����redis
                    self::$redis    = new Redis();
                    self::$redis->connect($hostInfo['host'],$hostInfo['port']);
                    self::$redisArr[$key] = self::$redis;
                }
            } else {
                die("Redis�ӿ�ģ�鲻����");
            }
        }else{
            self::$redis = self::$redisArr[$key];
        }

    }

    /**
     * ����ѹ����ʽ
     */
    public static function compress($value){
        if (!is_array($value) && !is_object($value)) {
            $value = array('D' => $value);
        }
        return serialize($value);
    }

    /**
     * ����ѹ����ʽ
     */
    public static function unCompress($value){
        if (self::isSerialized($value)) {
            $unValue = unserialize($value);
        } else {
            $unValue = $value;
        }
        if (is_array($unValue) && array_key_exists('D', $unValue)) {
            return $unValue['D'];
        }
        return $unValue ;
    }
    
    
    private static  function isSerialized($str) {
        return ($str == serialize(false) || @unserialize($str) !== false);
    }

    /*--------------------------------------------------------------------
                            key-value����
    ---------------------------------------------------------------------*/
    /**
     * key-value д
     */
    public static function set($key,$subKey,$value){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];

        $key = $keyInfo['key'] . ':' . $subKey;
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
    public static function get($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;

        $keyInfo = self::$dalCfg[$key];
        $key = $keyInfo['key'] . ':' . $subKey;
        return self::unCompress(self::$redis->get($key));
    }


    /**
     * ���ö�ֵ
     */
    public function setMulti($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        $keyArr = array();
        if (is_array($subKey)) {
            foreach ($subKey as $k => $v) {
                $key            = $keyInfo['key'] . ':' . $k;
                $keyArr[$key]   = self::compress($v);
            }
        }
        return self::$redis->mset($keyArr);
    }

    /**
     * ��ȡ��ֵ
     * @param subKey �����������
     */
    public function getMulti($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        #key�Ĵ���
        $keyArr = array();
        if($subKey){

        }

        $arr = $this -> obj -> mget($keyArr);
        if (is_array($arr)) {
            foreach ($arr as $key=>$row) {
                $arr[$key]  = $this -> unCompress($row);
            }
        }
    }

    /**
     * ɾ��Ԫ��
     */
    public static function delete($key, $subKey){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->delete($key);
    }
    
    
    /*--------------------------------------------------------------------
                                   hash����
    ---------------------------------------------------------------------*/
    /**
     * �浥��ֵ
     */
    public static function hashSet($key, $subKey, $value,$snId=0)
    {
        self::init($snId);
        $key = self::$dalCfg->getKey('HASH',$key);
        if($key){
            $re = self::$redis->hSet($key, $subKey, self::compress($value));
            if ($time > 0) {
                self::$redis->expire($key, $time);
            }
            return $re;
        }
    }

    /**
     * ȡ����ֵ
     */
    public static function hashGet($key, $subKey,$snId=0){
        self::init($snId);
        $key = self::$dalCfg->getKey('HASH',$key);
        return self::unCompress(self::$redis->hGet($key, $subKey));
    }

    /*--------------------------------------------------------------------
                                   set����
    ---------------------------------------------------------------------*/
    /**
     * ���Ӽ���Ԫ��
     */
    public static function sAdd($key,$subKey,$value){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        $re     = self::$redis->sAdd($key, $value);#��Ϊset�Ĵ����ǲ�ѹ����
        $time   = $keyInfo['time'];
        if ($time > 0) {
            self::$redis->expire($key, $time);
        }
        return $re;
    }

    /**
     * ɾ��һ��ָ����Ԫ��
     */
    public static function sDelete($key , $subKey , $value){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sRemove($key, $value);
    }

    /**
     * �ƶ�Ԫ��
     *
     * @param Ҫ�ƶ��漰��key $fromKey
     * @param �ƶ�����key $toKey
     * @param Ԫ�� $value
     */
    public static function sMove($key ,$fromKey, $toKey, $value){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $fromKey = $keyInfo['key'] . ':' . $fromKey;
        $toKey   = $keyInfo['key'] . ':' . $toKey;

        $value  = self::compress($value);
        return self::$redis->sMove($fromKey, $toKey, $value);
    }

    /**
     * ͳ��Ԫ�ظ���
     */
    public static function sSize($key,$subKey){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sSize($key);
    }

    /**
     * �ж�Ԫ���Ƿ�����ĳ��key
     */
    public static function sIsMember($key,$subKey, $value){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->sIsMember($key, $value);
    }

    /**
     * �󽻼�
     *
     * @param key���� $keyArr
     */
    public static function sInter($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sInter($keyArr);
        }
    }

    /**
     * �󽻼����洢�������key��
     *
     * @param key���� $keyArr 'output', 'key1', 'key2', 'key3'
     */
    public static function sInterStore($key,$ouput,$keyArr){
        self::init($key);
        #ת��Ϊ��ʵ��KEY
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        array_unshift($keyArr,$ouput);  #���뵽����Ŀ�ͷ
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
        }
        return call_user_func_array(array(self::$redis, "sInterStore"), $keyArr);
    }

    /**
     * �󲢼�
     *
     * @param key���� $keyArr
     */
    public static function sUnion($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sUnion($keyArr);
        }
    }

    /**
     * �� A-B�Ĳ���
     *
     * @param key���� $keyArr
     */
    public static function sDiff($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sDiff($keyArr);
        }
    }
    /**
     * ��ȡ��ǰkey�µ�����Ԫ��
     *
     * @param key���� $key
     */
    public static function sMembers($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sMembers($key);
    }



}

