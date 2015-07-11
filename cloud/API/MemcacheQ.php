<?php
/**
 * 
 * memcacheq������װ,������memcache�Ļ����ϰѶ˿ڻ���,��set,get����������������
 * 
 * 
 */

class API_MemcacheQ
{
    //memcache����
    private static $memObj;
    //������
    private static $IP = array(
            '10.15.185.118'
    );
    private static $PORT = "22201";
    //socket���ӵ���Դ����
    private static $_socket = null;
    private static $EOL     = "\r\n";
    
    public static function init()
    {
        if (class_exists("Memcache")) {
	        self::$memObj = new Memcache();
			foreach (self::$IP as $key=>$value)
	        {
	         	self::$memObj->addServer($value,self::$PORT,false);
	        }
        } else {
            die("���棺ϵͳδ��װmemcache����");
        }
    }

    /**
     * ��������
     *
     */
    public static function push($key, $value)
    {
        self::init();
    	if ($key) {
	        if (self::$memObj -> set($key, $value)) {
	            return true;
	        }
    	}
    	return false;
    }

    /**
     * �������
     *
     * @param key $key
     */
    public static function pop($key)
    {
        self::init();
        if ($re = self::$memObj -> get($key)) {
            return $re;
        } else {
            return false;
        }
    }

    /**
     * ������еĿ��ö���
     */
    public static function getQueues(){
		#$this->_queues = array();

		$response = self::sendCommand('stats queue', array('END'));
        
        $outArr = array();
		foreach($response as $i => $line) {
			$queue = explode(' ', str_replace('STAT ', '', $line));
            $qName = $queue[0];
            list($iCnt,$oCnt) = explode("/", $queue[1]);
            $outArr[$qName] = array(
                'i' => $iCnt,
                'o' => $oCnt,
                'l' => $iCnt - $oCnt,
            );
		}
        print_r($outArr);
        #return $outArr;

		#return $this->_queues;
	}
    /**
     * �ر����ӣ��ͷ���Դ
     */
    public static function close(){

		if (is_resource(self::$_socket)) {
			$cmd = 'quit' . self::$EOL;
			fwrite(self::$_socket, $cmd);
			fclose(self::$_socket);
            self::$_socket = null;
		}

    }

    /**
     * ����Socket����ķ�ʽ��memcacheqͨѶ
     */
    private static function sendCommand($command, array $terminator, $include_term=false)
	{
		if (!is_resource(self::$_socket)) {
			self::$_socket = fsockopen(self::$IP[0], self::$PORT, $errno, $errstr, 10);
		}
		if (self::$_socket === null) {
			return false;
		}

		$response = array();

		$cmd = $command . self::$EOL;
		fwrite(self::$_socket, $cmd);

		$continue_reading = true;
		while (!feof(self::$_socket) && $continue_reading) {
			$resp = trim(fgets(self::$_socket, 1024));
			if (in_array($resp, $terminator)) {
				if ($include_term) {
					$response[] = $resp;
				}
				$continue_reading = false;
			} else {
				$response[] = $resp;
			}
		}

		return $response;
    }
    
 
    
}
