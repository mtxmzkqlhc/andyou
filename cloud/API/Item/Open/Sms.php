<?php
/**
 * ���Žӿ�
 * �����ˣ����
 * ����ʱ�䣺2013-3-15
 */
class API_Item_Open_Sms
{
    /**
     * ע���˺�	����	   �ط���	����
        SDK-BBX-010-01440	095362	12460	������
        SDK-BBX-010-07099	877364	67264	�йش��̳�
        SDK-BBX-010-06943	142044	6666	΢����
        SDK-BBX-010-07837	506381	83224	���ⱦ
     */
    private static $_userList = array(
        '6666' => array(
            'key'    => 'SDK-BBX-010-06943',//sn
            'pass'   => '142044',
            'name'   => '6666',
            'cnName' => '�йش�����',
        ),
        '12460' => array(
            'key'    => 'SDK-BBX-010-01440',
            'pass'   => '095362',
            'name'   => '12460',
            'cnName' => '������',
        ),
        '67264' => array(
            'key'    => 'SDK-BBX-010-07099',
            'pass'   => '877364',
            'name'   => '67264',
            'cnName' => '�йش��̳�',
        ),
        '83224' => array(
            'key'    => 'SDK-BBX-010-07837',
            'pass'   => '506381',
            'name'   => '83224',
            'cnName' => '�йش��̳�',
        ),
        '111106' => array(
            'key'    => 'SDK-BBX-010-10210',
            'pass'   => '022698',
            'name'   => '111106',
            'cnName' => '�Ұ�������',
        ),
        '15588' => array(
            'key'    => 'SDK-BBX-010-07374',
            'pass'   => '494288',
            'name'   => '15588',
            'cnName' => '��ά�ҵ���',
        ),
        '62893' => array(
            'key'    => 'SDK-BBX-010-06652',
            'pass'   => '434224',
            'name'   => '62893',
            'cnName' => 'ABABС��Ϸ',
        ),
    );
    /**
     * �����˺�
     * 
     */
    private static $_gdUserList = array(
        'ceshi7' => array(
            'key'    => 'ceshi7',
            'pass'   => 'zlj0320',
            'name'   => 'ceshi7',
            'cnName' => '����ϵͳ',
        ),
        'zolgw' => array(
            'key'    => 'zolgw',
            'pass'   => 'zolgw88',
            'name'   => 'zolgw',
            'cnName' => '�йش�����',  
        ),
        'zolss' => array(
            'key'    => 'zolss',
            'pass'   => 'zolss88',
            'name'   => 'zolss',
            'cnName' => '�йش��̳�',
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
            'cnName' => 'ABABС��Ϸ',
        ),
        'zolzx' =>array(
            'key'    => 'zolzx',
            'pass'   => 'zolzx88',
            'name'   => 'zolzx',
            'cnName' => 'ר��ͨ��',
            
        )
      
    );

    /**
	 * ����˻���Ϣ
	 */
    public static function getUserList() {
        return self::$_userList;
    }
    
    /**
	 * ��ù����˻���Ϣ
	 */
    public static function getGDUserList() {
        return self::$_gdUserList;
    }
    /**
	 * ���Ͷ���Ϣ
	 */
    public static function sendSms($paramArr) {
		$options = array(
			'sn'     => '', #��Ӫ���ʺ�
			'mobile' => '', #�ֻ����б��Զ���,����
			'content'=> '', #Ҫ���͵�����
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
        //����Ҫpost���ַ���
        foreach ($smsParam as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);
            $flag = 1;
        }
        
        $length = strlen($params);
        //����socket����
        $fp = fsockopen("agent.bucp.net",80,$errno,$errstr,10) or exit($errstr."--->".$errno);
        //����post�����ͷ
        $header = "POST /websend/webservice.asmx/SendSMS HTTP/1.1\r\n";
        $header .= "Host:agent.bucp.net\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: ".$length."\r\n"; 
        $header .= "Connection: Close\r\n\r\n";
        //���post���ַ���
        $header .= $params."\r\n";
        //����post������
        fputs($fp,$header);
        $inheader = 1;
        while (!feof($fp)) { 
             $line = fgets($fp,1024); //ȥ���������ͷֻ��ʾҳ��ķ������� 
             if ($inheader && ($line == "\n" || $line == "\r\n")) { 
                     $inheader = 0; 
              } 
        } 
        if (!$line) {
            $data = array(
                    'code'    => 0,
                    'msg'     => '���ͳɹ�'
                    );
        } else {
            $data = array(
                    'code'    => mb_convert_encoding($line, "GBK","UTF-8"),
                    'msg'     => '����ʧ��'
            );
        }
        return $data;
    }
    
    /**
     * ��ѯ�˻����
     */
    public static function getBalance($paramArr) 
    {
        $options = array(
                'sn'     => '', #��Ӫ���ʺ�
                'pwd'    => '', #��Ӫ������
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$sn || !$pwd) {
            return false;
        }
        $flag   = 0;
        $params = '';
        //����Ҫpost���ַ���
        foreach ($options as $key=>$value) {
            if ($flag!=0) {
                $params .= "&";
                $flag = 1;
            }
            $params.= $key."="; $params.= urlencode($value);
            $flag = 1;
        }
        $length = strlen($params);
        
        //����socket����
        $fp = fsockopen("agent.bucp.net",80,$errno,$errstr,10) or exit($errstr."--->".$errno);
        //����post�����ͷ
        $header = "POST /websend/webservice.asmx/GetBalance HTTP/1.1\r\n";
        $header .= "Host:agent.bucp.net\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: ".$length."\r\n";
        $header .= "Connection: Close\r\n\r\n";
        //���post���ַ���
        $header .= $params."\r\n";
        //����post������
        fputs($fp,$header);
        $inheader = 1;
        while (!feof($fp)) {
            $line = fgets($fp,1024); //ȥ���������ͷֻ��ʾҳ��ķ�������
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
     * �����������Ͷ��Žӿ� 
     */
    public static function sendGDSms($paramArr){
        $options = array(
            'openId'       =>'',   #��Ӫ���˺�
            'telno'        => '',
            'content'      => '',  #������Ϣ���ݣ����Ҫ����500����
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
        #����ʱ�䣬�����ֵ�������ֵ��ʱ�䷢�ͣ�����ʱ���ͣ����û��ֵ����������
        $sendTime   = ""; 
        #������8Ϊ�����ţ�15Ϊ�̶��ţ������������������Զ�ʶ��ģ�����д8�Ϳ����ˣ�15���޷�����
        $conType    = 8;
        #��Ϣ����Ч�ڣ���ò�Ҫ��д������Ĭ���Ƿ���ʱ��,��sendTime+3�����д���󽫵����޷�����
        $validTime  = "";
        #�û��Լ���չ����
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
        /*post��Ӧ����*/
        /*��ӡ������Ӧ����ӦΪXML��ʽ���û��õ���Ӧ������Զ����ֽ��������������Ӧ�еĲ������Ա��߼�����*/
        $xml      = simplexml_load_string($response);
        $codeConfig     = $configInfo['codeConfig'];
        if(isset($xml) && isset ($xml->code)){
             $code  = (int)$xml->code;
             return   array(
                    'flag'  =>($code=='1' || $code=='3') ? 1:0 ,
                    'msg'   =>!empty($codeConfig[$code]) ? $codeConfig[$code]:"δ֪״̬"
             );
            
        }else{
             return  false;
        }

    }
    
    /**
     * ��ù����ӿڵ������Ϣ 
     */
    public static function getGDBalance($paramArr = array()){
        $options = array(
                'openId'      => '', #��Ӫ���ʺ�
                'openPass'    => '', #��Ӫ������
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
