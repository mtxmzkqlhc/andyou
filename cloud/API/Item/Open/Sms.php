<?php
/**
 * 短信接口
 * 开发人：齐辉
 * 开发时间：2013-3-15
 */
class API_Item_Open_Sms
{
    /**
     * 注册账号	密码	   特服号	名称
        SDK-BBX-010-01440	095362	12460	蜂鸟网
        SDK-BBX-010-07099	877364	67264	中关村商城
        SDK-BBX-010-06943	142044	6666	微博号
        SDK-BBX-010-07837	506381	83224	生意宝
     */
    private static $_userList = array(
        '6666' => array(
            'key'    => 'SDK-BBX-010-06943',//sn
            'pass'   => '142044',
            'name'   => '6666',
            'cnName' => '中关村在线',
        ),
        '12460' => array(
            'key'    => 'SDK-BBX-010-01440',
            'pass'   => '095362',
            'name'   => '12460',
            'cnName' => '蜂鸟网',
        ),
        '67264' => array(
            'key'    => 'SDK-BBX-010-07099',
            'pass'   => '877364',
            'name'   => '67264',
            'cnName' => '中关村商城',
        ),
        '83224' => array(
            'key'    => 'SDK-BBX-010-07837',
            'pass'   => '506381',
            'name'   => '83224',
            'cnName' => '中关村商城',
        ),
        '111106' => array(
            'key'    => 'SDK-BBX-010-10210',
            'pass'   => '022698',
            'name'   => '111106',
            'cnName' => '我爱购物网',
        ),
        '15588' => array(
            'key'    => 'SDK-BBX-010-07374',
            'pass'   => '494288',
            'name'   => '15588',
            'cnName' => '万维家电网',
        ),
        '62893' => array(
            'key'    => 'SDK-BBX-010-06652',
            'pass'   => '434224',
            'name'   => '62893',
            'cnName' => 'ABAB小游戏',
        ),
    );
    /**
     * 国都账号
     * 
     */
    private static $_gdUserList = array(
        'ceshi7' => array(
            'key'    => 'ceshi7',
            'pass'   => 'zlj0320',
            'name'   => 'ceshi7',
            'cnName' => '恒星系统',
        ),
        'zolgw' => array(
            'key'    => 'zolgw',
            'pass'   => 'zolgw88',
            'name'   => 'zolgw',
            'cnName' => '中关村在线',  
        ),
        'zolss' => array(
            'key'    => 'zolss',
            'pass'   => 'zolss88',
            'name'   => 'zolss',
            'cnName' => '中关村商城',
        ),
        '55bbs' => array(
            'key'    => '55bbs',
            'pass'   => '55bbs88',
            'name'   => '55bbs',
            'cnName' => '55BBS',
        ),
        'abgame' => array(
            'key'    => 'abgame',
            'pass'   => 'abgame88',
            'name'   => 'abgame',
            'cnName' => 'ABAB小游戏',
        ),
        'zolzx' =>array(
            'key'    => 'zolzx',
            'pass'   => 'zolzx88',
            'name'   => 'zolzx',
            'cnName' => '专属通道',
            
        )
      
    );

    /**
	 * 获得账户信息
	 */
    public static function getUserList() {
        return self::$_userList;
    }
    
    /**
	 * 获得国都账户信息
	 */
    public static function getGDUserList() {
        return self::$_gdUserList;
    }
    /**
	 * 发送短信息
	 */
    public static function sendSms($paramArr) {
		$options = array(
			'sn'     => '', #运营商帐号
			'mobile' => '', #手机号列表，以逗号,隔开
			'content'=> '', #要发送的内容
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        if (!$sn || !isset(self::$_userList[$sn]) || !$mobile || !$content) {
            return false;
        }
        $content = strip_tags($content);
        $smsParam = array(
            'sn'      => self::$_userList[$sn]['key'],
            'pwd'     => self::$_userList[$sn]['pass'],
            'mobile'  => $mobile,
            'content' => $content,
        );
        $flag   = 0;
        $params = '';
        //构造要post的字符串
        foreach ($smsParam as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);
            $flag = 1;
        }
        
        $length = strlen($params);
        //创建socket连接
        $fp = fsockopen("agent.bucp.net",80,$errno,$errstr,10) or exit($errstr."--->".$errno);
        //构造post请求的头
        $header = "POST /websend/webservice.asmx/SendSMS HTTP/1.1\r\n";
        $header .= "Host:agent.bucp.net\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: ".$length."\r\n"; 
        $header .= "Connection: Close\r\n\r\n";
        //添加post的字符串
        $header .= $params."\r\n";
        //发送post的数据
        fputs($fp,$header);
        $inheader = 1;
        while (!feof($fp)) { 
             $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据 
             if ($inheader && ($line == "\n" || $line == "\r\n")) { 
                     $inheader = 0; 
              } 
        } 
        if (!$line) {
            $data = array(
                    'code'    => 0,
                    'msg'     => '发送成功'
                    );
        } else {
            $data = array(
                    'code'    => mb_convert_encoding($line, "GBK","UTF-8"),
                    'msg'     => '发送失败'
            );
        }
        return $data;
    }
    
    /**
     * 查询账户余额
     */
    public static function getBalance($paramArr) 
    {
        $options = array(
                'sn'     => '', #运营商帐号
                'pwd'    => '', #运营商密码
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$sn || !$pwd) {
            return false;
        }
        $flag   = 0;
        $params = '';
        //构造要post的字符串
        foreach ($options as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);
            $flag = 1;
        }
        $length = strlen($params);
        
        //创建socket连接
        $fp = fsockopen("agent.bucp.net",80,$errno,$errstr,10) or exit($errstr."--->".$errno);
        //构造post请求的头
        $header = "POST /websend/webservice.asmx/GetBalance HTTP/1.1\r\n";
        $header .= "Host:agent.bucp.net\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: ".$length."\r\n";
        $header .= "Connection: Close\r\n\r\n";
        //添加post的字符串
        $header .= $params."\r\n";
        //发送post的数据
        fputs($fp,$header);
        $inheader = 1;
        while (!feof($fp)) {
            $line = fgets($fp,1024); //去除请求包的头只显示页面的返回数据
            if ($inheader && ($line == "\n" || $line == "\r\n")) {
                $inheader = 0;
            }
        
            if ($inheader == 0) {
                $data = $line;
            }
        }
        fclose($fp);
        return trim(addslashes(strip_tags($data)));
    }
    
    
      /**
     * 北京国都发送短信接口 
     */
    public static function sendGDSms($paramArr){
        $options = array(
            'openId'       =>'',   #运营商账号
            'telno'        => '',
            'content'      => '',  #发送消息内容：最长不要超过500个字
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);

        
        if(empty(self::$_gdUserList[$openId]) || empty($telno)  ||  empty($content)){
            return  false;
        }
        $telStr = "";
        if(is_array($telno)){
             $telStr  = implode(',', $telno);
        }  else {
             $telStr  = $telno;
        }
        $telStr     = str_replace(" ","", $telStr);
        $config     = self::$_gdUserList[$openId];
        #发送时间，如果有值就在这个值的时间发送，即定时发送，如果没有值就立即发送
        $sendTime   = ""; 
        #类型是8为长短信，15为短短信，国都服务器由于是自动识别的，所以写8就可以了，15将无法发送
        $conType    = 8;
        #消息的有效期，最好不要填写，国都默认是发送时间,即sendTime+3如果填写错误将导致无法发送
        $validTime  = "";
        #用户自己扩展号码
        $appendId   = "";
        $paramStr = "OperID=" . $config['name'] . "&OperPass=" . $config['pass'] . "&SendTime=" . $sendTime . "&ValidTime=" . $validTime . "&AppendID=" . $appendId . "&DesMobile=" . trim($telStr) . "&Content=" . urlencode($content) . "&ContentType=" . $conType;
        $interfaceUrl = "http://221.179.180.158:9007/QxtSms/QxtFirewall";
        $configInfo   = API_ZOL_Config::get('Service/Message');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $interfaceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramStr);
        $response = curl_exec($ch);
        /*post响应报文*/
        /*打印返回响应，响应为XML格式，用户得到响应串后可自定义拆分解析方法，获得响应中的参数，以便逻辑处理*/
        $xml      = simplexml_load_string($response);
        $codeConfig     = $configInfo['codeConfig'];
        if(isset($xml) && isset ($xml->code)){
             $code  = (int)$xml->code;
             return   array(
                    'flag'  =>($code=='1' || $code=='3') ? 1:0 ,
                    'msg'   =>!empty($codeConfig[$code]) ? $codeConfig[$code]:"未知状态"
             );
            
        }else{
             return  false;
        }

    }
    
    /**
     * 获得国都接口的余额信息 
     */
    public static function getGDBalance($paramArr = array()){
        $options = array(
                'openId'      => '', #运营商帐号
                'openPass'    => '', #运营商密码
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$openId || !$openPass) {
            return false;
        }
        
        $paramStr     = "OperID={$openId}&OperPass={$openPass}";
        $interfaceUrl = "http://221.179.180.158:8081/QxtSms_surplus/surplus";

        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $interfaceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramStr);
        $response = curl_exec($ch);
        $xml      = simplexml_load_string($response);
        
        if(isset($xml) && isset($xml->rcode)){
            return $xml->rcode;
        }else{
            return false;
        }
    }
    
    
    
}
