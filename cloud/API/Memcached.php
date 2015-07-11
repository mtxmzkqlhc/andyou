<?
/***********************************************************
 * memcache�࣬
 *����ʱ�䣺2007��05��23��
 *����汾��V1.0
***********************************************************/
class API_Memcached 
{
    public static $_memObjs = array(); #�������
    public static $_ipArr = array(

        #Ĭ����memcache
        'Default' => array(
            'memcached' => false,
            'ip' => array(#
                    '10.15.184.30',
                    '10.15.184.104',
                    '10.15.184.110',
                    '10.15.184.124',
                    '10.15.184.125',
                    '10.15.184.199',
                    '10.15.184.209',
            )
        ),
        
        #����ר��memcache
        'User'    => array(
            'memcached' => true,
            'ip' => array(#
                    '10.15.184.56',
                    '10.15.184.57',
                    '10.15.184.58',
                    '10.15.184.59',
                    '10.15.184.190',
                    '10.15.184.210',
                    '10.15.184.215'
            )
        ),
        
        #����ר��memcache
        'local'    => array(
            'memcache' => true,
            'ip' => array(#
                    '127.0.0.1',
            )
        ),

    );
    
    public static  function getIpArr($link){
        if($link){
            return self::$_ipArr[$link];
        }else{
            return self::$_ipArr;
        }
    }

    /**
     * ������Ӷ���
     */
    public static function getLink($link){
      
        if(!isset(self::$_ipArr[$link]))$link= 'Default';
        if(!isset(self::$_memObjs[$link])){
            $isMemcached = false; #������memcache���㷨
            if(isset(self::$_ipArr[$link]['memcached']) && self::$_ipArr[$link]['memcached']){
                try{
                    $mem = new Memcached();
                    $isMemcached = true;
                }catch(Exception $e){
                    $mem = new Memcache;
                }
            }else{
                $mem = new Memcache;
            }
            foreach (self::$_ipArr[$link]['ip'] as $value){
                $mem->addServer($value,11211,false);
            }

            self::$_memObjs[$link] = array(
                'memcached' => $isMemcached,
                'obj'       => $mem
            );
        }
        return self::$_memObjs[$link];
        
    }


	public static $mem;								#memcached��
	public static $CONNECT_OL; 						#�Ƿ��Ѿ�����memcached������
	public static $IP = array(
                    '10.15.184.56',
                    '10.15.184.57',
                    '10.15.184.58',
                    '10.15.184.59',
                    '10.15.184.190',
                    '10.15.184.210',
                    '10.15.184.215'
                    );
    public static $PORT = "11211";
	public static function init(){					#��ʼ��
		self::$mem = new Memcache;
		foreach (self::$IP as $key=>$value)
        {
                self::$mem->addServer($value,11211,false);
        }
	}
	
	public static function connect($ip = "",$port = ""){	#����memcached������
		self::init();
		if("" == $ip){
			$ip = self::$IP;
		}
		if("" == $port){
			$port = self::$PORT;
		}
		if(self::$mem->connect($ip,$port)){
			self::$CONNECT_OL = true;
			return 1;
		}else{
			return 0;
		}
		
	}
	
	public static function get($key){				#�õ�memcached����
		self::init();
		if(!$key){
			return false;
		}
		$mem_key = md5($key);
		$mem_content = self::$mem->get($mem_key);
		return $mem_content;
	}
	
	public static function set($key,$value,$time=0){	#����memcached����
		if(!$key){
			return false;
		}
        if(!self::$mem) self::init();
		$mem_key = md5($key);
		self::$mem->set($mem_key,$value,0,$time);
	}
	
    /**
     * ���ö�ֵ
     *
     * @param key-value���� $keyArr
     * @param ��Ч�� $time
     */
    public static  function setMulti($keyArr,$time=0){
        if(!is_array($keyArr)){
            return false;
        }
        foreach ($keyArr as $key=>$val){
            self::set($key,$val,$time);
        }
        return true;
    }

    /**
     * ��ȡ��ֵ
     *
     * @param array $keyArr
     */
    public static  function getMulti($keyArr){
        if(!is_array($keyArr)){
            return false;
        }
        $results_arr = array();
        foreach ($keyArr as $key){
            $val = self::get($key);
            $results_arr[$key] = $val;
        }
        return $results_arr;
    }

    /**
     * ɾ��һ������Ԫ��
     *
     * @param string/array $key
     */
    public static  function delete($key){
        if(!$key){
            return false;
        }
        return self::set($key,'');
    }  
    
    
    /**
     * ��ֵ׷������
     *
     * @param ���� $key
     * @param Ҫ׷�ӵ�ֵ $value
     */
    public static  function append($key, $value){
        if(!$key){
            return false;
        }
        $cur_val = self::get($key);
        $new_val = $cur_val.$value;
        return self::set($key,$new_val);
    }
    
    /**
     * �ڻ�ȡһ��ֵ��ͬʱ����һ���µ�ֵ���ǵ�ǰkey��ֵ
     *
     * @param ���� $key
     * @param ׷�ӵ�ֵ $value
     */
    public static function getSet($key, $value, $time=0){
        if(!$key){
            return false;
        }
        $val = self::get($key);
        self::set($key,$value,$time);
        return $val;
    }
    
	public static function mem_close(){					#�ر�memcached
		self::init();
		self::$mem->close();
	}
}