<?php
/**
* Tower�Ŀ��Žӿ�
*/

class API_Item_Open_Tower{
    
    
    /**
     * �ӿ�����
     */
	public static function postData($paramArr) {
		$options = array(
			'url'             => '', #�� AdspaceService/GetSite
			'postdata'        => '', #POST����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $customerId = 2;
        $authToken  = "VsvEbkySGnqAdvFH7CHJkK";
        //$baseUrl = "http://10.15.184.249/RestService/v2014032/";
        $baseUrl = "http://10.15.184.249/RestService/201404/";//����Զ���Ĭ�Ϲ�棻 ���λ����private Ad exchangeʱ�����Ĭ�ϵͼۣ�
        //$customerId = 1866;
        //$authToken  = "62khx3YhWb7m5bj/eNfefe";
        //$baseUrl = "http://testapi.adchina.com/RestService/v2014012/";
        
        #echo  $url . "<br/>\n";
        //echo $baseUrl . $url . "<br/>";
        //echo $postdata . "\n";
        //$postdata = mb_convert_encoding($postdata, "UTF-8", "GBK" );
        
        self::getSnoopyObj();
        
        self::$snoopyObj->rawheaders["Accept"]            = "text/json";
        self::$snoopyObj->rawheaders["Content-Type"]      = "text/json";
        self::$snoopyObj->rawheaders["ContentType"]       = "text/json";
        self::$snoopyObj->rawheaders["CustomerId"]        = $customerId;
        self::$snoopyObj->rawheaders["AuthToken"]         = $authToken;
        self::$snoopyObj->rawheaders["Content-Length"]    = strlen($postdata);
        self::$snoopyObj->_submit_type                    = "text/json";
        self::$snoopyObj->read_timeout                    = 10;
        self::$snoopyObj->submit($baseUrl . $url,"","",$postdata);
        
        #echo $baseUrl . $url."<br/>";
        $content =  self::$snoopyObj->results;
        #echo "<br/>".$content."<br/>\n";
        if(!in_array(substr($content, 0,1),array("[","{"))){#������ص����ݲ����ϸ�ʽ��ǿ��Ϊ��
            return false;
        }
        
        return $content;       
        
            
    }

}
