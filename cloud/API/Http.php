<?php

class API_Http {

    /**
     *  �Ƿ���headͷ������Ӧ��������ͳ�ƽű�
     */
    public static function sendHeaderOnly($paramArr) {
		$options = array(
            'url'       => false, #url
            'getFull'   => false, #�Ƿ������е��ı�
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		if (!$url) return false;

		$urlArr = parse_url($url);
		if (!is_array($urlArr) || empty($urlArr)){
			return false;
		}

		//��ȡ��������
		$host = $urlArr['host'];
        $path = "/";
        if(isset($urlArr['path']) || isset($urlArr['query'])){
            $path = (isset($urlArr['path']) ? $urlArr['path'] :'') ."?". (isset($urlArr['query']) ? $urlArr['query'] : '');
        }
		$port = isset($urlArr['port']) ? $urlArr['port'] : "80";

		//���ӷ�����
		$fp = fsockopen($host, $port, $errNo, $errStr, 30);
		if (!$fp){
			return false;
		}

		//��������Э��
		$requestStr = "GET ".$path." HTTP/1.1\r\n";
		$requestStr .= "Host: ".$host."\r\n";
		$requestStr .= "Connection: Close\r\n\r\n";

		//��������
		fwrite($fp, $requestStr);
		$firstHeader = fgets($fp, 1024);
		fclose($fp);

        if($getFull){
            return $firstHeader;
        }
        $headerArr = explode(" ",$firstHeader);
        if(count($headerArr) >= 3){
            return $headerArr[1];
        }
		return true;

    }
    /**
     *  ����curl����ʽ���ҳ������ �����������ȡ��file_get_contents
     */
    public static function curlPage($paramArr){
       if (is_array($paramArr)) {
			$options = array(
				'url'       => false, #Ҫ�����URL����
				'timeout'   => 2,#��ʱʱ�� s
				'recErrLog' => 0,#�Ƿ��¼������־
				'reConnect' => 0,#�Ƿ���������
				'keepAlive' => 0,#�Ƿ�ִ�б��ֳ����ӵĴ���
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        $timeout = (int)$timeout;

        if(0 == $timeout || empty($url))return false;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if(defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); #�������Ƚ���ipv6
        }
        if($keepAlive){
            $rch = curl_copy_handle($ch);
            curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($rch, CURLOPT_HTTPHEADER, array(
                'Connection: Keep-Alive',
                'Keep-Alive: 3'
            ));
            $data = curl_exec($rch);
        }else{
            $data = curl_exec($ch);
        }
        #��¼������־
        if($recErrLog || $reConnect){
           $errNo  = curl_errno($ch);
           if($reConnect && (28 == $errNo || 7 == $errNo || 6 == $errNo)){ #��ʱ���� 6:name lookup timed out
                $errMsg = curl_error($ch);
                $data = self::curlPage(array('url'=>$url,'timeout'=>1,'recErrLog'=>1,'reConnect'=>0));#��β���Ҫ����
                #ZOL_Log::write("[api_curl_toreconn][{$url}] [{$errNo}]{$errMsg}", ZOL_Log::TYPE_ERROR);
           }elseif($errNo && $recErrLog){#��¼����
               $errMsg = curl_error($ch);
               #ZOL_Log::write("[api_curl][{$url}] [{$errNo}]" . $errMsg, ZOL_Log::TYPE_ERROR);
           }
        }

        if(!$keepAlive){
            curl_close($ch);
        }

        return $data;
    }
    
    /*
     * @Desc ���ô����ȡ��ַ����
     * @AUthor jiebl
     * @Version 14-5-9 ����3:52 
     * @Todo �������ƴ����ж�
     * @From ץȡ��Ҫ 
     */
    public static function curlPageByProxy($paramArr)
    {
        $options = array(
			'url'       =>  0, #URL
            'proxy'     => true,  #�Ƿ�ʹ�ô���
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
		#$url = "https://assoc-datafeeds-cn.amazon.com/datafeed/listFeeds?format=text/html";
        #��������
        $user = "10mgzol";
        $pass = "10mgzol";
        $proxy_host = "vps.zol.com.cn:80";
        $proxy_port = 80;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_NOPROGRESS, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy_port);
        curl_setopt($ch, CURLOPT_PROXY, $proxy_host);
        curl_setopt($ch, CURLOPT_USERPWD, $user.":".$pass);

		$response = curl_exec($ch);
		if(curl_errno($ch) > 0){
			throw_exception("CURL ERROR:$url ".curl_error($ch));
		}
		curl_close($ch);
		return $response;
    }
    
	 /**
     *  ����curl POST����
     */
    public static function curlPost($paramArr){
       
		$options = array(
			'url'      => false, #Ҫ�����URL����
			'postdata' => '', #POST������
			'timeout'  => 2,#��ʱʱ�� s
		);
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $timeout = (int)$timeout;
        if(0 == $timeout || empty($url))return false;
        #�����post�������� ��ת��һ��
        $postdata  = is_array($postdata) ? http_build_query($postdata):$postdata;
        $ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_AUTOREFERER, true);
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        }
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        if(defined('CURLOPTfa_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 ); #�������Ƚ���ipv6
        }
		$content = curl_exec( $ch );
		curl_close ( $ch );

        return $content;
    }
    /**
     *  ���� curl_multi_** �ĺ���,�����������
     */
    public static function multiCurl($paramArr){
       if (is_array($paramArr)) {
			$options = array(
				'urlArr'   => false, #Ҫ�����URL����
				'timeout'  => 10,#��ʱʱ�� s
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        $timeout = (int)$timeout;

        if(0 == $timeout)return false;

        $result = $res = $ch = array();
        $nch = 0;
        $mh = curl_multi_init();
        foreach ($urlArr as $nk => $url) {

            $ch[$nch] = curl_init();
            curl_setopt_array($ch[$nch], array(
                                            CURLOPT_URL => $url,
                                            CURLOPT_HEADER => false,
                                            CURLOPT_RETURNTRANSFER => true,
                                            CURLOPT_TIMEOUT => $timeout,
                                            ));
            curl_multi_add_handle($mh, $ch[$nch]);
            ++$nch;
        }
        /* ִ������ */
        do {
            $mrc = curl_multi_exec($mh, $running);
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);

        while ($running && $mrc == CURLM_OK) {
            if (curl_multi_select($mh, 0.5) > -1) {
                do {
                    $mrc = curl_multi_exec($mh, $running);
                } while (CURLM_CALL_MULTI_PERFORM == $mrc);
            }
        }

        if ($mrc != CURLM_OK) {

        }

        /* ������� */
        $nch = 0;
        foreach ($urlArr as $moudle=>$node) {
            if (($err = curl_error($ch[$nch])) == '') {
                $res[$nch]=curl_multi_getcontent($ch[$nch]);
                $result[$moudle]=$res[$nch];
            }
            curl_multi_remove_handle($mh,$ch[$nch]);
            curl_close($ch[$nch]);
            ++$nch;
        }
        curl_multi_close($mh);
        return 	$result;

    }
    /**
     * ����û���IP��ַ
     */
    public static function getUserIp(){
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : NULL;
    }
		/**
	 * �õ����ѵ���ϸIP��Ϣ
	 * ********�ر�ע��:************
	 *      �����õ�ַ�Ƕ����:10.19.8.12, 118.67.120.27, 127.0.0.1 ���Ҫ�����������
	 * ���ֻ����һ��IP,��������� getClientIp()
	 */
	public static function getClientIpMulti(){
	  if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	  }elseif(!empty($_SERVER["HTTP_CLIENT_IP"])){
		$realip = $_SERVER["HTTP_CLIENT_IP"];
	  }else{
		$realip = $_SERVER["REMOTE_ADDR"];
	  }
	  return $realip;
	}

	/**
	 * �õ����ѵ���ϸIP��Ϣ
	 * ֻ���õ�һ��IP��ַ,
	 * @param toLongFlag �Ƿ�������
	 */
	public static function getClientIp($toLongFlag = false){
		$ip = self::getClientIpMulti();
		$ipArr = explode(",",$ip);
		$ip = is_array($ipArr) ? $ipArr[0] : $ipArr;
		if($toLongFlag)$ip = ip2long($ip);
		return $ip;

	}

	public static function ip2location($ip)
    {
        $ipClass = new IpLocation;
        $location = $ipClass->getlocation($ip);
		return $location;
	}

	/**
	 * �õ��û����ڵ�����Ϣ
	 */
	public static function getUserArea()
	{
		$ip       = self::getClientIpMulti();
		$ipArr    = explode(",",$ip);
		$ipNum    = count($ipArr);
		$ip       = $ipArr[0];#����Ǽ���һ������������ip��������ˣ�ȡ��һ�� 2010-06-18
		$cacheObj = ZOL_DAL_RefreshCacheLoader::getInstance();

		$tmpProvinceArr = $cacheObj->loadCacheObject('Province',array());
		$provinceArr = array();
		if($tmpProvinceArr){
			foreach($tmpProvinceArr as $key=>$value){
				$provinceName = ZOL_String::substr($value,4);
				$provinceArr[$provinceName] = $key;
			}
		}
		$ipName = self::ip2location($ip);

		if (strpos($ipName, 'ʡ')) {
			$ipName       = explode('ʡ', $ipName);
			$cityName     = ZOL_String::substr($ipName[1], 4);
			$provinceName = ZOL_String::substr($ipName[0], 4);
		} else {
			$cityName     = '';
			$provinceName = ZOL_String::substr($ipName, 4);
		}

		#ʡ��
		$provinceId = 1;
		if ($cityName && array_key_exists($cityName, $provinceArr)) {
			$provinceId = $provinceArr[$cityName];
		} elseif ($provinceName && array_key_exists($provinceName, $provinceArr)) {
			$provinceId = $provinceArr[$provinceName];
		}

		#����
		$cityId = 0;
		if ($cityName && $provinceId) {
			$tmpCityArr = $cacheObj->loadCacheObject('City',array('provinceId' => $provinceId));
			foreach ($tmpCityArr as $key => $val) {
				$val = ZOL_String::substr($val, 4);
				if($val == $cityName){
					$cityId = $key;
					break;
				}
			}
		}

		#locationId���
		$tmpLocationArr = $cacheObj->loadCacheObject('Location',array('type' => 'LOCATION'));
		$locationArr = array();
		if ($tmpLocationArr) {
			foreach($tmpLocationArr as $key=>$value){
				$locationName = ZOL_String::substr($value['name'],4);
				$locationArr[$locationName] = $key;
			}
		}
		#����ʡ�����ƺͳ��е�����,���LocationId
		$locationId = 1;
		if($cityName && isset($locationArr[$cityName])){
			$locationId = $locationArr[$cityName];
		} elseif ($provinceName && isset($locationArr[$provinceName])) {
			$locationId = $locationArr[$provinceName];
			
			if(isset($tmpLocationArr[$locationId]['defaultId'])) {
				$locationId = $tmpLocationArr[$locationId]['defaultId'];
			}
		}

		setcookie('userProvinceId', $provinceId, SYSTEM_TIME + 86400, '/', '.zol.com.cn');
		setcookie('userCityId', $cityId, SYSTEM_TIME + 86400, '/', '.zol.com.cn');
		setcookie('userLocationId', $locationId, SYSTEM_TIME + 86400, '/', '.zol.com.cn');


		return array('provinceId' => $provinceId, 'cityId' => $cityId, 'userLocationId' => $locationId);
	}

    /**
     * ����404 Header��Ϣ
     */
    public static function send404Header(){
        Plugin_Expires::setExpires(0); #�������ʱ��
        header('Content-type:text/html; Charset=gb2312');
        header(self::getStatusByCode(404)); #����404 header��Ϣ

    }
    protected static function getStatusByCode($code) {
        $status = array(
    100 => "HTTP/1.1 100 Continue",
    101 => "HTTP/1.1 101 Switching Protocols",
    200 => "HTTP/1.1 200 OK",
    201 => "HTTP/1.1 201 Created",
    202 => "HTTP/1.1 202 Accepted",
    203 => "HTTP/1.1 203 Non-Authoritative Information",
    204 => "HTTP/1.1 204 No Content",
    205 => "HTTP/1.1 205 Reset Content",
    206 => "HTTP/1.1 206 Partial Content",
    300 => "HTTP/1.1 300 Multiple Choices",
    301 => "HTTP/1.1 301 Moved Permanently",
    302 => "HTTP/1.1 302 Found",
    303 => "HTTP/1.1 303 See Other",
    304 => "HTTP/1.1 304 Not Modified",
    305 => "HTTP/1.1 305 Use Proxy",
    307 => "HTTP/1.1 307 Temporary Redirect",
    400 => "HTTP/1.1 400 Bad Request",
    401 => "HTTP/1.1 401 Unauthorized",
    402 => "HTTP/1.1 402 Payment Required",
    403 => "HTTP/1.1 403 Forbidden",
    404 => "HTTP/1.1 404 Not Found",
    405 => "HTTP/1.1 405 Method Not Allowed",
    406 => "HTTP/1.1 406 Not Acceptable",
    407 => "HTTP/1.1 407 Proxy Authentication Required",
    408 => "HTTP/1.1 408 Request Time-out",
    409 => "HTTP/1.1 409 Conflict",
    410 => "HTTP/1.1 410 Gone",
    411 => "HTTP/1.1 411 Length Required",
    412 => "HTTP/1.1 412 Precondition Failed",
    413 => "HTTP/1.1 413 Request Entity Too Large",
    414 => "HTTP/1.1 414 Request-URI Too Large",
    415 => "HTTP/1.1 415 Unsupported Media Type",
    416 => "HTTP/1.1 416 Requested range not satisfiable",
    417 => "HTTP/1.1 417 Expectation Failed",
    500 => "HTTP/1.1 500 Internal Server Error",
    501 => "HTTP/1.1 501 Not Implemented",
    502 => "HTTP/1.1 502 Bad Gateway",
    503 => "HTTP/1.1 503 Service Unavailable",
    504 => "HTTP/1.1 504 Gateway Time-out"  
        );
        if (!empty($status[$code])) {
            
            return $status[$code];
        }
        return false;
    }
}
