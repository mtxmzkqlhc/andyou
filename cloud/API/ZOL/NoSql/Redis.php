<?php
/**
 * Redis类
 * @author 仲伟涛
 * 2011-7
 */
/**
 * redis访问数据设置
 */
class API_ZOL_NoSql_DAL_Redis
{
    /**
     * 数据KEY的注册
     */
    private $dataKey = array(
        #类型包括 STRING HASH SET ZSET LIST
        'commentUp' => array('type'=>'STRING','time' => 86400,'serverId'=>0, 'app'=>'Mobile'),  #评论支持等操作
        'checkCode' => array('type'=>'STRING','time' => 1800,'serverId'=>0,  'app'=>'Mobile'),  #验证码
        'qrcode'    => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #二位码
        'recommend' => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #推荐数目
        'picRecomm' => array('type'=>'STRING','time' => 592200,'serverId'=>0,'app'=>'Mobile'),  #图片推荐数目
        'checkRecomm' => array('type'=>'STRING','time' => 84600,'serverId'=>0,'app'=>'Mobile'),  #推荐限制
    );

    /**
     * 服务器
     */
    private $connentServer = array( 
        0 =>  array( #主服务，本业务的服务器redis
                    'host' => '10.15.185.118', #IP
                    'port' => '6505'           #port
                )
    );

    /**
     * 获得链接信息，不同业务链接信息不同
     * $snId : 要连接的redis sn
     */
    public function getConnectInfo($snId=0){
        return $this->connentServer[$snId];
	}

    /**
     * 获得数据key
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

    protected static $redisArr  = array();#创建的redis对象的集合
    protected static $redis     = false;  #当前方法操作的redis对象
    protected static $dalCfg    = false;

    protected static function init($key){
        
        if (!isset(self::$redisArr[$key])){
            if (class_exists("Redis")) {
                #获得链接信息
                $dal            = new API_ZOL_NoSql_DAL_Redis();
                $keyInfo        = $dal->getKeyInfo($key);
                if($keyInfo){
                    $hostInfo   = $keyInfo['server'];
                    self::$dalCfg[$key]   = $keyInfo;
                    #连接redis
                    self::$redis    = new Redis();
                    self::$redis->connect($hostInfo['host'],$hostInfo['port']);
                    self::$redisArr[$key] = self::$redis;
                }
            } else {
                die("Redis接口模块不可用");
            }
        }else{
            self::$redis = self::$redisArr[$key];
        }

    }

    /**
     * 数据压缩格式
     */
    public static function compress($value){
        if (!is_array($value) && !is_object($value)) {
            $value = array('D' => $value);
        }
        return serialize($value);
    }

    /**
     * 数据压缩格式
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
                            key-value类型
    ---------------------------------------------------------------------*/
    /**
     * key-value 写
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
     * key-value 读
     */
    public static function get($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;

        $keyInfo = self::$dalCfg[$key];
        $key = $keyInfo['key'] . ':' . $subKey;
        return self::unCompress(self::$redis->get($key));
    }


    /**
     * 设置多值
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
     * 获取多值
     * @param subKey 传入的是数组
     */
    public function getMulti($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        #key的处理
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
     * 删除元素
     */
    public static function delete($key, $subKey){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->delete($key);
    }
    
    
    /*--------------------------------------------------------------------
                                   hash类型
    ---------------------------------------------------------------------*/
    /**
     * 存单键值
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
     * 取单键值
     */
    public static function hashGet($key, $subKey,$snId=0){
        self::init($snId);
        $key = self::$dalCfg->getKey('HASH',$key);
        return self::unCompress(self::$redis->hGet($key, $subKey));
    }

    /*--------------------------------------------------------------------
                                   set类型
    ---------------------------------------------------------------------*/
    /**
     * 增加集合元素
     */
    public static function sAdd($key,$subKey,$value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        $re     = self::$redis->sAdd($key, $value);#认为set的处理是不压缩的
        $time   = $keyInfo['time'];
        if ($time > 0) {
            self::$redis->expire($key, $time);
        }
        return $re;
    }

    /**
     * 删除一个指定的元素
     */
    public static function sDelete($key , $subKey , $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sRemove($key, $value);
    }

    /**
     * 移动元素
     *
     * @param 要移动涉及的key $fromKey
     * @param 移动到的key $toKey
     * @param 元素 $value
     */
    public static function sMove($key ,$fromKey, $toKey, $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $fromKey = $keyInfo['key'] . ':' . $fromKey;
        $toKey   = $keyInfo['key'] . ':' . $toKey;

        $value  = self::compress($value);
        return self::$redis->sMove($fromKey, $toKey, $value);
    }

    /**
     * 统计元素个数
     */
    public static function sSize($key,$subKey){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sSize($key);
    }

    /**
     * 判断元素是否属于某个key
     */
    public static function sIsMember($key,$subKey, $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->sIsMember($key, $value);
    }

    /**
     * 求交集
     *
     * @param key集合 $keyArr
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
     * 求交集并存储到另外的key中
     *
     * @param key集合 $keyArr 'output', 'key1', 'key2', 'key3'
     */
    public static function sInterStore($key,$ouput,$keyArr){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        array_unshift($keyArr,$ouput);  #插入到数组的开头
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
        }
        return call_user_func_array(array(self::$redis, "sInterStore"), $keyArr);
    }

    /**
     * 求并集
     *
     * @param key集合 $keyArr
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
     * 求差集 A-B的操作
     *
     * @param key集合 $keyArr
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
     * 获取当前key下的所有元素
     *
     * @param key集合 $key
     */
    public static function sMembers($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sMembers($key);
    }



}

