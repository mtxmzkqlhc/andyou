<?php 
/**
 * ��ҵ��΢�Ź���ƽ̨�ӿ�
 * ��ΰ��
 */
class API_Item_Open_WeixinQy {
	
	public static $corpId         = '';#��ҵID
	public static $token          = '';#Token
	public static $signature      = '';# #΢�ż���ǩ��
	public static $timestamp      = '';#ʱ���
	public static $encodingAesKey = '';#EncodingAesKey
	public static $nonce          = '';#�����
	public static $agentId        = 0; #��ǰӦ��ID ��΢�ź�̨�ܿ���
	public static $debug		  = 0; #debugģʽ
    public static $accessToken    = '';#access_token
    public static $secret         = '';#secret
	
	/**
	 * ���մ�����
	 *
	 * @param unknown_type $paramArr
	 */
	public static function msgHandlerm($paramArr) {
		$options = array(
			'locationFunc'          => false, #��������
			'clickFunc'             => false, #���������
			'viewFunc'				=> false, #����˵���ת����
			'codePushFunc'		    => false, #ɨ�����¼�����
			'codePushWaitFunc'		=> false, #ɨ�����¼��ȴ���Ϣ����
			'sysPhone'				=> false, #����ϵͳ��ͼ�¼����ͺ���
			'sysPhoneAlbum'		    => false, #�������ջ�����ᷢͼ���ͺ���
			'selectLocation'	    => false, #����ѡ�����¼����ͺ���
			'baseFunc'				=> false, #��������
			'commonFunc'			=> false, #��ͨ���ݽ��ܺ���
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		#������Ϣ
   	    $data = API_Item_Open_WeixinQy::receiveMsg();
   	    #�������ú���
        if($baseFunc){
            call_user_func_array($baseFunc,array($data));
        }
	    if($data){
		    $baseParam = array(
						'userName'  =>$data["FromUserName"],  #Ա������
						'corpName'  =>$data['ToUserName'],	  #��ҵ����
						'createTime'=>$data['CreateTime'],	  #����ʱ��
						'msgType'	=>$data['MsgType'],		  #��Ϣ����
						'agentId'	=>$data['AgentID']		  #Ӧ��Id
			);
			extract($baseParam);
			if($msgType == "event"){#�¼�����Ϣ
				$baseParam['eventKey']   = $data["EventKey"];     #�¼�keyֵ
				$baseParam['event']      = $data["Event"];        #�¼�����
				switch ($data["Event"]){
					
					case "LOCATION":#���������

						$baseParam['Latitude']   = $data["Latitude"];      #ά��
						$baseParam['Longitude']  = $data["Longitude"];     #����
						$baseParam['Precision']  = $data["Precision"];	   #����

						if($locationFunc){							
								call_user_func_array($locationFunc,array($baseParam));
						}
						
						break;
					case "CLICK":#��ͨ��ť�ĵ���¼�			
						if($clickFunc){							
								call_user_func_array($clickFunc,array($baseParam));
						}
						break;	
					case "VIEW":#URL����ת	
						if($viewFunc){							
								call_user_func_array($viewFunc,array($baseParam));
						}
						break; 
					case "scancode_push":#ɨ�����¼�
						if($codePushFunc){							
								$baseParam['eventKey']  = $data["EventKey"];      #�˵��Զ���key
								$baseParam['codeInfo']  = $data["ScanCodeInfo"];  #ɨ����Ϣ
								$baseParam['scanType']  = $data["ScanType"];	  #ɨ������
								$baseParam['result']    = $data["ScanResult"];    #ɨ����
								call_user_func_array($codePushFunc,array($baseParam));
						}
						break; 
					case "scancode_waitmsg":#ɨ����ʱ�䲢������Ϣ��
						if($codePushWaitFunc){			
								$baseParam['eventKey']  = $data["EventKey"];      #�˵��Զ���key
								$baseParam['codeInfo']  = $data["ScanCodeInfo"];  #ɨ����Ϣ
								$baseParam['scanType']  = $data["ScanType"];	  #ɨ������
								$baseParam['result']    = $data["ScanResult"];    #ɨ����				
								call_user_func_array($codePushWaitFunc,array($baseParam));
						}
						break; 
					case "pic_sysphoto":#��������ϵͳ���շ�ͼʱ������
						if($sysPhone){							
                                $baseParam['picInfo']   = $data["SendPicsInfo"];      #����ͼƬ����Ϣ
                                $baseParam['count']     = $data["Count"];  			  #����ͼƬ������
                                $baseParam['picList']   = $data["PicList"];	  		  #ͼƬ�б�
                                $baseParam['picMd5']    = $data["PicMd5Sum"];    	  #ͼƬMd5��֤ͼƬ�Ƿ����	
								call_user_func_array($sysPhone,array($baseParam));
						}
						break; 
					case "pic_photo_or_album":#��������ϵͳ���շ�ͼʱ������
						if($sysPhoneAlbum){			
                                $baseParam['picInfo']   = $data["SendPicsInfo"];      #����ͼƬ����Ϣ
                                $baseParam['count']     = $data["Count"];  			  #����ͼƬ������
                                $baseParam['picList']   = $data["PicList"];	  		  #ͼƬ�б�
                                $baseParam['picMd5']    = $data["PicMd5Sum"];    	  #ͼƬMd5��֤ͼƬ�Ƿ����	
								call_user_func_array($sysPhoneAlbum,array($baseParam));
						}
						break; 
					case "location_select":#��������ϵͳ���շ�ͼʱ������
						if($selectLocation){			
                                $baseParam['locationInfo']   = $data["SendLocationInfo"];     #����λ����Ϣ
                                $baseParam['locationX']      = $data["Location_X"];  		  #X�����Ϣ
                                $baseParam['locationX']   	 = $data["Location_Y"];	  		  #Y������Ϣ
                                $baseParam['scale']    		 = $data["Scale"];    	  		  #����	
                                $baseParam['label']   		 = $data["Label"];      		  #����λ���ַ���
                                $baseParam['poiname']     	 = $data["Poiname"];  			  #����ȦPOI�����֣�����Ϊ��
								call_user_func_array($selectLocation,array($baseParam));
						}
						break; 
				}
				
			}elseif($msgType != "event"){
				$msgId	   = $data['MsgId'];
				switch ($msgType){
					case  'text' :{ 
							$baseParam['content'] = $data['Content'];
					}break;
					case  'image' :{ 
							$baseParam['content'] = $data['Content'];
							$baseParam['mediaId'] = $data['MediaId'];
					}break;
					case  'voice' :{ 
							$baseParam['mediaId'] = $data['MediaId'];
							$baseParam['Format']  = $data['Format'];
					}break;
					case  'video' :{ 
							$baseParam['mediaId']      = $data['MediaId'];
							$baseParam['thumbMediaId'] = $data['ThumbMediaId'];
					}break;
					case  'location' :{ 
							$baseParam['localX']  = $data['Location_X'];
							$baseParam['localY']  = $data['Location_Y'];
							$baseParam['scale']   = $data['Scale'];
							$baseParam['label']   = $data['Label'];
					}break;
					
					
				}
				if($commonFunc){
					call_user_func_array($commonFunc,array($baseParam));
				}
			}
	    
	    }		
	}
	
	/**
	 * ��ʼ��ͨ�ò���
	 */
	public static function initParam($paramArr) {
		$options = array(
			'corpId'            => '', #��ҵ
			'token'             => '', #Token
			'signature'         => '', #΢�ż���ǩ��
			'timestamp'         => '', #ʱ���
			'encodingAesKey'    => '', #EncodingAesKey
			'nonce'             => '', #�����
			'agentId'           => '', #��ǰӦ��ID
			'debug'				=> '', #debugģʽ
            'secret'            => '', #secret
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		self::$corpId          = $corpId;
		self::$token           = $token;
		self::$signature       = $signature;
		self::$timestamp       = $timestamp;
		self::$encodingAesKey  = $encodingAesKey;
		self::$nonce           = $nonce;
		self::$agentId         = $agentId;
		self::$debug		   = $debug;
        self::$secret          = $secret;

		return true;
		
	}
	
    /**
     * ǩ������֤
     */
	public static function checkSignature($paramArr) {
		$options = array(
			'echoStr'           => '', #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
		
		$corpId         = self::$corpId;
		$token          = self::$token;
		$signature      = self::$signature;
		$timestamp      = self::$timestamp;
		$encodingAesKey = self::$encodingAesKey;
		$nonce          = self::$nonce;
		
        
		if (strlen($encodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($encodingAesKey);
		//verify msg_signature
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($token, $timestamp, $nonce, $echoStr);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		$gsignature = $array[1];
		if ($gsignature != $signature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$result = $pc->decrypt($echoStr, $corpId);
		if ($result[0] != 0) {
			return $result[0];
		}
		$sReplyEchoStr = $result[1];

		return $sReplyEchoStr;
    }
    
    
    
    /**
     * ������Ϣ
     */
	public static function receiveMsg() {
		$corpId         = self::$corpId;
		$token          = self::$token;
		$signature      = self::$signature;
		$timestamp      = self::$timestamp;
		$encodingAesKey = self::$encodingAesKey;
		$nonce          = self::$nonce;
        
        #��������
		$xmlStr= file_get_contents("php://input","r");
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $data  = array();
        $code  = $wxcpt->DecryptMsg($signature, $timestamp, $nonce, $xmlStr, $data);
        if (self::$debug){
			
		}
		return $data;
    }
    
    /**
     * �����ı���Ϣ
     */
	public static function sendMsg($paramArr) {
		
		$options = array(
			'toUserName'            => '',             #���͸�˭
			'msgContent'            => '', 		       #��Ϣ����
			'msgType'				=> 'text'   	   #��Ϣ����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
		
		$corpId         = self::$corpId;
		$token          = self::$token;
		$signature      = self::$signature;
		$timestamp      = self::$timestamp;
		$encodingAesKey = self::$encodingAesKey;
		$nonce          = self::$nonce;
		$agentId        = self::$agentId;
		
		if(empty($corpId)){ return false; }
		
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $expend= "";
        if($msgContent){
	        switch ($msgType){
	        	#�ı�
	        	case "text"  :{  $expend = "<Content><![CDATA[{$msgContent}]]></Content>"; }break;
	        	#ͼƬ
	        	case "image" :{  $expend = "<Image><MediaId><![CDATA[{$msgContent}]]></MediaId></Image>"; }break;
	        	#����
	        	case "voice" :{  $expend = "<Voice><MediaId><![CDATA[{$msgContent}]]></MediaId></Voice>";}break;
	        	#����
	        	case "news"  :{  
	        				if(!is_array($msgContent)){ break;}
	        				$expend   	 = "<Articles>";
	        				foreach ($msgContent  as  $value){			
	        						!empty($value['title']) && $expend .= "<item><Title><![CDATA[{$value['title']}]]></Title>";
	        						!empty($value['title']) && $expend .= "<Description><![CDATA[{$value['desc']}]]></Description>";
	        						!empty($value['title']) && $expend .= "<PicUrl><![CDATA[{$value['picUrl']}]]></PicUrl>";
          							!empty($value['title']) && $expend .= "<Url><![CDATA[{$value['url']}]]></Url></item>";	
	        				}
	        				$expend   	 .= "</Articles>";
	        	}break;
	        }
        }
        $sRespData = "<xml><ToUserName><![CDATA[mycreate]]></ToUserName><FromUserName><![CDATA[{$corpId}]]></FromUserName><CreateTime>".SYSTEM_TIME."</CreateTime><MsgType><![CDATA[text]]></MsgType>{$expend}</xml>";
		$sEncryptMsg = ""; //xml��ʽ������
		$errCode = $wxcpt->EncryptMsg($sRespData, $timestamp, $nonce, $sEncryptMsg);
		return $sEncryptMsg;
    }
    
    /**
     * ���AccessToken
     */
    public static function getAccessToken() {
        if (self::$accessToken) {
            return $accessToken;
        }
        #��Redis�л�ȡaccess_token
        $accessToken = API_Item_Kv_Redis::stringGet(array(
        	'serverName' => 'Default',                    #��������
        	'key'        => 'zol_weixin_qy_access_token', #������ݵ�Key
        ));
        if ($accessToken) {
            return $accessToken;
        }
        #��api�ӿ��л�ȡaccess_token
        $corpId  = self::$corpId;
        $secret  = self::$secret;
        $data    = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$corpId.'&corpsecret='.$secret,'timeout'=>3));
        if ($data) {
            $data = json_decode($data, true);
            if ($data && !empty($data['access_token'])) {
                API_Item_Kv_Redis::stringSet(array(
                	'serverName' => 'Default',                    #��������
                	'key'        => 'zol_weixin_qy_access_token', #Key
                	'value'      => $data['access_token'],        #value
                	'life'       => 7000,                         #�����ڣ��룩
                ));
                return $data['access_token'];
            }
        }
        return false;
    }
    
    /**
     * ��õ����û���Ϣ
     */
    public static function getUserInfo($paramArr) {
        $options = array(
            'userId' => '', #userId
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$userId) return false;
        
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$assessToken.'&userid='.$userId,'timeout'=>3));
        $data = api_json_decode($data);
        return $data;
    }
    
    /**
     * ��ò����û���Ϣ
     */
    public static function getUserList($paramArr) {
        $options = array(
            'departmentId' => '', #����Id
            'fetchChild'   => 0,  #�Ƿ�ݹ��ȡ�Ӳ�������ĳ�Ա
            'isDetail'     => 0,  #�Ƿ�����ϸ��
            'status'       => 0,  #0��ȡȫ��Ա����1��ȡ�ѹ�ע��Ա�б�2��ȡ���ó�Ա�б�4��ȡδ��ע��Ա�б�status�ɵ���
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$departmentId) return false;
    
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        
        if(empty($isDetail)){
             $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/user/simplelist?access_token='.$assessToken.'&department_id='.$departmentId.'&fetch_child='.$fetchChild.'&status='.$status,'timeout'=>3));
        }else{
             $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$assessToken.'&department_id='.$departmentId.'&fetch_child='.$fetchChild.'&status='.$status,'timeout'=>3));
        }
       
        $data = api_json_decode($data);
        return $data;
    }
    
    /**
     * ����code��ȡ��Ա��Ϣ
     */
    public static function getUserInfoByCode($paramArr) {
        $options = array(
            'code'    => '', #code
            'agentId' => '', #agentId
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$code || !$agentId) return false;
        
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$assessToken.'&code='.$code.'&agentid='.$agentId,'timeout'=>3));
        $data = api_json_decode($data);
        return $data;
    }
    
    /**
     * ��Ա��������Ϣ
     * 
     */
    
    public  static  function  sendUserMsg($paramArr){
        $options = array(
			'toUserArr'             => '',             #���͸�˭
            'toPartyArr'            => '',             #���͸��ĸ���ǩ
			'msgContent'            => '', 		       #��Ϣ����
			'msgType'				=> 'text',   	   #��Ϣ����
            'safe'                  => 0,
            'agentId'               =>''               #Ӧ��Id
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        
        $url   =  "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$assessToken}";
        
        $data  =  array();
        
        if(empty($toUserArr)){
            return false;
        }
        
        $toUser   = (!empty($toUserArr)  && is_array($toUserArr))  ?  implode("|", $toUserArr)   : $toUserArr;
        $toParty  = (!empty($toPartyArr) && is_array($toPartyArr)) ?  implode("|", $toPartyArr)  : $toPartyArr;
        
        !empty($toUser)  && $data['touser']   = $toUser;
        !empty($toParty) && $data['toparty']  = $toParty;
        
        $data['msgtype']  = $msgType;
        $data['agentid']  = !empty($agentId)?$agentId:self::$agentId;
        
        if(!empty($msgContent)){
                $msgContent = urlencode(iconv('GBK', 'UTF-8', $msgContent));
                switch ($msgType){
                        #�ı�
                        case "text"  :{  $data['text'] = array('content' => $msgContent);   }break;
                        #ͼƬ
                        case "image" :{  $data['image'] = array('media_id' =>$msgContent); }break;
                        #����
                        case "voice" :{  $data['voice'] = array('media_id' =>$msgContent); }break;
                        #��Ƶ
                        case "video" :{  $data['video'] = $msgContent;                     }break;
                        #��Ƶ
                        case "file" :{   $data['file']  = array('media_id' =>$msgContent); }break;
                        #����
                        case "news"  :{  
                                         $data['news']  = $msgContent;                    
                        }break;
                }
        }
        $data['safe']  = $safe;

        $jsonStr       = urldecode(json_encode($data));
        
        $state     = API_Http::curlPost(array(
                'url'           =>$url,
                'postdata'      =>$jsonStr
        ));
        return api_json_decode($state);
    }
    
    /**
     * ��ȡ�����б�
     * 
     */

    public static function getDepartmentList($paramArr) {
        $options = array(
            'id'    => '' #����id
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$id) return false;
        
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$assessToken.'&id='.$id,'timeout'=>3));
        $data = api_json_decode($data);
        return $data;
    }
    
     /**
     *   ���ĳ����ǩ������û��б� 
     *   
     */

    public static function getTagUserList($paramArr) {
        $options = array(
            'tagId'    => '' #��ǩId
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if (!$tagId) return false;
       
        $assessToken = API_Item_Open_WeixinQy::getAccessToken();
        $data = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/tag/get?access_token='.$assessToken.'&tagid='.$tagId,'timeout'=>3));
        $data = api_json_decode($data);
        return $data;
    }
    
    
    
    
}
    
    
    
    
    
    
/**
 * error code ˵��.
 * <ul>
 *    <li>-40001: ǩ����֤����</li>
 *    <li>-40002: xml����ʧ��</li>
 *    <li>-40003: sha��������ǩ��ʧ��</li>
 *    <li>-40004: encodingAesKey �Ƿ�</li>
 *    <li>-40005: corpid У�����</li>
 *    <li>-40006: aes ����ʧ��</li>
 *    <li>-40007: aes ����ʧ��</li>
 *    <li>-40008: ���ܺ�õ���buffer�Ƿ�</li>
 *    <li>-40009: base64����ʧ��</li>
 *    <li>-40010: base64����ʧ��</li>
 *    <li>-40011: ����xmlʧ��</li>
 * </ul>
 */
class ErrorCode
{
	public static $OK = 0;
	public static $ValidateSignatureError = -40001;
	public static $ParseXmlError = -40002;
	public static $ComputeSignatureError = -40003;
	public static $IllegalAesKey = -40004;
	public static $ValidateCorpidError = -40005;
	public static $EncryptAESError = -40006;
	public static $DecryptAESError = -40007;
	public static $IllegalBuffer = -40008;
	public static $EncodeBase64Error = -40009;
	public static $DecodeBase64Error = -40010;
	public static $GenReturnXmlError = -40011;
}

 /**
 * SHA1 class
 *
 * ���㹫��ƽ̨����Ϣǩ���ӿ�.
 */
class SHA1
{
	/**
	 * ��SHA1�㷨���ɰ�ȫǩ��
	 * @param string $token Ʊ��
	 * @param string $timestamp ʱ���
	 * @param string $nonce ����ַ���
	 * @param string $encrypt ������Ϣ
	 */
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
	{
		//����
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
			return array(ErrorCode::$OK, sha1($str));
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ComputeSignatureError, null);
		}
	}

}


/**
 * PKCS7Encoder class
 *
 * �ṩ����PKCS7�㷨�ļӽ��ܽӿ�.
 */
class PKCS7Encoder
{
	public static $block_size = 32;

	/**
	 * ����Ҫ���ܵ����Ľ�����䲹λ
	 * @param $text ��Ҫ������䲹λ����������
	 * @return ���������ַ���
	 */
	function encode($text)
	{
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen($text);
		//������Ҫ����λ��
		$amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		//��ò�λ���õ��ַ�
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	/**
	 * �Խ��ܺ�����Ľ��в�λɾ��
	 * @param decrypted ���ܺ������
	 * @return ɾ����䲹λ�������
	 */
	function decode($text)
	{

		$pad = ord(substr($text, -1));
		if ($pad < 1 || $pad > PKCS7Encoder::$block_size) {
			$pad = 0;
		}
		return substr($text, 0, (strlen($text) - $pad));
	}

}

/**
 * Prpcrypt class
 *
 * �ṩ���պ����͸�����ƽ̨��Ϣ�ļӽ��ܽӿ�.
 */
class Prpcrypt
{
	public $key;

	function Prpcrypt($k)
	{
		$this->key = base64_decode($k . "=");
	}

	/**
	 * �����Ľ��м���
	 * @param string $text ��Ҫ���ܵ�����
	 * @return string ���ܺ������
	 */
	public function encrypt($text, $corpid)
	{

		try {
			//���16λ����ַ�������䵽����֮ǰ
			$random = $this->getRandomStr();
			$text = $random . pack("N", strlen($text)) . $text . $corpid;
			// �����ֽ���
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			//ʹ���Զ������䷽ʽ�����Ľ��в�λ���
			$pkc_encoder = new PKCS7Encoder;
			$text = $pkc_encoder->encode($text);
			mcrypt_generic_init($module, $this->key, $iv);
			//����
			$encrypted = mcrypt_generic($module, $text);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);

			//print(base64_encode($encrypted));
			//ʹ��BASE64�Լ��ܺ���ַ������б���
			return array(ErrorCode::$OK, base64_encode($encrypted));
		} catch (Exception $e) {
			print $e;
			return array(ErrorCode::$EncryptAESError, null);
		}
	}

	/**
	 * �����Ľ��н���
	 * @param string $encrypted ��Ҫ���ܵ�����
	 * @return string ���ܵõ�������
	 */
	public function decrypt($encrypted, $corpid)
	{

		try {
			//ʹ��BASE64����Ҫ���ܵ��ַ������н���
			$ciphertext_dec = base64_decode($encrypted);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			mcrypt_generic_init($module, $this->key, $iv);

			//����
			$decrypted = mdecrypt_generic($module, $ciphertext_dec);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
			return array(ErrorCode::$DecryptAESError, null);
		}


		try {
			//ȥ����λ�ַ�
			$pkc_encoder = new PKCS7Encoder;
			$result = $pkc_encoder->decode($decrypted);
			//ȥ��16λ����ַ���,�����ֽ����AppId
			if (strlen($result) < 16)
				return "";
			$content = substr($result, 16, strlen($result));
			$len_list = unpack("N", substr($content, 0, 4));
			$xml_len = $len_list[1];
			$xml_content = substr($content, 4, $xml_len);
			$from_corpid = substr($content, $xml_len + 4);
		} catch (Exception $e) {
			print $e;
			return array(ErrorCode::$IllegalBuffer, null);
		}
		if ($from_corpid != $corpid)
			return array(ErrorCode::$ValidateCorpidError, null);
		return array(0, $xml_content);

	}


	/**
	 * �������16λ�ַ���
	 * @return string ���ɵ��ַ���
	 */
	function getRandomStr()
	{

		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}

}



/**
 * 1.�������ظ�������Ϣ������ƽ̨��
 * 2.�������յ�����ƽ̨���͵���Ϣ����֤��Ϣ�İ�ȫ�ԣ�������Ϣ���н��ܡ�
 */
class WXBizMsgCrypt
{
	private $m_sToken;
	private $m_sEncodingAesKey;
	private $m_sCorpid;

	/**
	 * ���캯��
	 * @param $token string ����ƽ̨�ϣ����������õ�token
	 * @param $encodingAesKey string ����ƽ̨�ϣ����������õ�EncodingAESKey
	 * @param $Corpid string ����ƽ̨��Corpid
	 */
	public function WXBizMsgCrypt($token, $encodingAesKey, $Corpid)
	{
		$this->m_sToken = $token;
		$this->m_sEncodingAesKey = $encodingAesKey;
		$this->m_sCorpid = $Corpid;
	}
	
    /*
	*��֤URL
    *@param sMsgSignature: ǩ��������ӦURL������msg_signature
    *@param sTimeStamp: ʱ�������ӦURL������timestamp
    *@param sNonce: ���������ӦURL������nonce
    *@param sEchoStr: ���������ӦURL������echostr
    *@param sReplyEchoStr: ����֮���echostr����return����0ʱ��Ч
    *@return���ɹ�0��ʧ�ܷ��ض�Ӧ�Ĵ�����
	*/
	public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr)
	{
		if (strlen($this->m_sEncodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($this->m_sEncodingAesKey);
		//verify msg_signature
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		$signature = $array[1];
		if ($signature != $sMsgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$result = $pc->decrypt($sEchoStr, $this->m_sCorpid);
		if ($result[0] != 0) {
			return $result[0];
		}
		$sReplyEchoStr = $result[1];

		return ErrorCode::$OK;
	}
	/**
	 * ������ƽ̨�ظ��û�����Ϣ���ܴ��.
	 * <ol>
	 *    <li>��Ҫ���͵���Ϣ����AES-CBC����</li>
	 *    <li>���ɰ�ȫǩ��</li>
	 *    <li>����Ϣ���ĺͰ�ȫǩ�������xml��ʽ</li>
	 * </ol>
	 *
	 * @param $replyMsg string ����ƽ̨���ظ��û�����Ϣ��xml��ʽ���ַ���
	 * @param $timeStamp string ʱ����������Լ����ɣ�Ҳ������URL������timestamp
	 * @param $nonce string ������������Լ����ɣ�Ҳ������URL������nonce
	 * @param &$encryptMsg string ���ܺ�Ŀ���ֱ�ӻظ��û������ģ�����msg_signature, timestamp, nonce, encrypt��xml��ʽ���ַ���,
	 *                      ��return����0ʱ��Ч
	 *
	 * @return int �ɹ�0��ʧ�ܷ��ض�Ӧ�Ĵ�����
	 */
	public function EncryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
	{
		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//����
		$array = $pc->encrypt($sReplyMsg, $this->m_sCorpid);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}

		if ($sTimeStamp == null) {
			$sTimeStamp = time();
		}
		$encrypt = $array[1];

		//���ɰ�ȫǩ��
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}
		$signature = $array[1];

		//���ɷ��͵�xml
		$xmlparse = new XMLParse;
		$sEncryptMsg = $xmlparse->generate($encrypt, $signature, $sTimeStamp, $sNonce);
		return ErrorCode::$OK;
	}


	/**
	 * ������Ϣ����ʵ�ԣ����һ�ȡ���ܺ������.
	 * <ol>
	 *    <li>�����յ����������ɰ�ȫǩ��������ǩ����֤</li>
	 *    <li>����֤ͨ��������ȡxml�еļ�����Ϣ</li>
	 *    <li>����Ϣ���н���</li>
	 * </ol>
	 *
	 * @param $msgSignature string ǩ��������ӦURL������msg_signature
	 * @param $timestamp string ʱ��� ��ӦURL������timestamp
	 * @param $nonce string ���������ӦURL������nonce
	 * @param $postData string ���ģ���ӦPOST���������
	 * @param &$msg string ���ܺ��ԭ�ģ���return����0ʱ��Ч
	 *
	 * @return int �ɹ�0��ʧ�ܷ��ض�Ӧ�Ĵ�����
	 */
	public function DecryptMsg($sMsgSignature, $sTimeStamp = null, $sNonce, $sPostData, &$data)
	{
		if (strlen($this->m_sEncodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//��ȡ����
		$xmlparse = new XMLParse;
		$array = $xmlparse->extract($sPostData);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		if ($sTimeStamp == null) {
			$sTimeStamp = time();
		}

		$encrypt = $array[1];
		$touser_name = $array[2];
		//��֤��ȫǩ��
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
		$ret = $array[0];

		if ($ret != 0) {
			return $ret;
		}

		$signature = $array[1];
		if ($signature != $sMsgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$result = $pc->decrypt($encrypt, $this->m_sCorpid);
		if ($result[0] != 0) {
			return $result[0];
		}
		$sMsg = $result[1];		
		$data = array();
        $xml           = simplexml_load_string($sMsg, 'SimpleXMLElement', LIBXML_NOCDATA);		
        $data          = api_json_decode(api_json_encode($xml),TRUE);
//        if($xml){
//			foreach ($xml as $key => $value) {
//				$data[$key] = mb_convert_encoding(strval($value),"GBK","UTF-8");;
//			}     
//        }
		
		return ErrorCode::$OK;
	}
	
	

}



/**
 * XMLParse class
 *
 * �ṩ��ȡ��Ϣ��ʽ�е����ļ����ɻظ���Ϣ��ʽ�Ľӿ�.
 */
class XMLParse
{

	/**
	 * ��ȡ��xml���ݰ��еļ�����Ϣ
	 * @param string $xmltext ����ȡ��xml�ַ���
	 * @return string ��ȡ���ļ�����Ϣ�ַ���
	 */
	public function extract($xmltext)
	{
		try {
			$xml = new DOMDocument();
			$xml->loadXML($xmltext);
			$array_e = $xml->getElementsByTagName('Encrypt');
			$array_a = $xml->getElementsByTagName('ToUserName');
			$encrypt = $array_e->item(0)->nodeValue;
			$tousername = $array_a->item(0)->nodeValue;
			return array(0, $encrypt, $tousername);
		} catch (Exception $e) {
			print $e . "\n";
			return array(ErrorCode::$ParseXmlError, null, null);
		}
	}

	/**
	 * ����xml��Ϣ
	 * @param string $encrypt ���ܺ����Ϣ����
	 * @param string $signature ��ȫǩ��
	 * @param string $timestamp ʱ���
	 * @param string $nonce ����ַ���
	 */
	public function generate($encrypt, $signature, $timestamp, $nonce)
	{
		$format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
		return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
	}

}