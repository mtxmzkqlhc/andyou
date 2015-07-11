<?php 
/**
 * 企业号微信公众平台接口
 * 仲伟涛
 */
class API_Item_Open_WeixinQy {
	
	public static $corpId         = '';#企业ID
	public static $token          = '';#Token
	public static $signature      = '';# #微信加密签名
	public static $timestamp      = '';#时间戳
	public static $encodingAesKey = '';#EncodingAesKey
	public static $nonce          = '';#随机数
	public static $agentId        = 0; #当前应用ID 在微信后台能看到
	public static $debug		  = 0; #debug模式
    public static $accessToken    = '';#access_token
    public static $secret         = '';#secret
	
	/**
	 * 接收处理器
	 *
	 * @param unknown_type $paramArr
	 */
	public static function msgHandlerm($paramArr) {
		$options = array(
			'locationFunc'          => false, #地域处理函数
			'clickFunc'             => false, #点击处理函数
			'viewFunc'				=> false, #点击菜单跳转函数
			'codePushFunc'		    => false, #扫码推事件函数
			'codePushWaitFunc'		=> false, #扫码推事件等待消息函数
			'sysPhone'				=> false, #拍照系统发图事件推送函数
			'sysPhoneAlbum'		    => false, #弹出拍照或者相册发图推送函数
			'selectLocation'	    => false, #地理选择器事件推送函数
			'baseFunc'				=> false, #基础函数
			'commonFunc'			=> false, #普通内容接受函数
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		#接收消息
   	    $data = API_Item_Open_WeixinQy::receiveMsg();
   	    #基础公用函数
        if($baseFunc){
            call_user_func_array($baseFunc,array($data));
        }
	    if($data){
		    $baseParam = array(
						'userName'  =>$data["FromUserName"],  #员工名称
						'corpName'  =>$data['ToUserName'],	  #企业名称
						'createTime'=>$data['CreateTime'],	  #创建时间
						'msgType'	=>$data['MsgType'],		  #消息类型
						'agentId'	=>$data['AgentID']		  #应用Id
			);
			extract($baseParam);
			if($msgType == "event"){#事件类消息
				$baseParam['eventKey']   = $data["EventKey"];     #事件key值
				$baseParam['event']      = $data["Event"];        #事件类型
				switch ($data["Event"]){
					
					case "LOCATION":#获得了坐标

						$baseParam['Latitude']   = $data["Latitude"];      #维度
						$baseParam['Longitude']  = $data["Longitude"];     #经度
						$baseParam['Precision']  = $data["Precision"];	   #精度

						if($locationFunc){							
								call_user_func_array($locationFunc,array($baseParam));
						}
						
						break;
					case "CLICK":#普通按钮的点击事件			
						if($clickFunc){							
								call_user_func_array($clickFunc,array($baseParam));
						}
						break;	
					case "VIEW":#URL的跳转	
						if($viewFunc){							
								call_user_func_array($viewFunc,array($baseParam));
						}
						break; 
					case "scancode_push":#扫码推事件
						if($codePushFunc){							
								$baseParam['eventKey']  = $data["EventKey"];      #菜单自定义key
								$baseParam['codeInfo']  = $data["ScanCodeInfo"];  #扫描信息
								$baseParam['scanType']  = $data["ScanType"];	  #扫描类型
								$baseParam['result']    = $data["ScanResult"];    #扫描结果
								call_user_func_array($codePushFunc,array($baseParam));
						}
						break; 
					case "scancode_waitmsg":#扫码推时间并弹出消息中
						if($codePushWaitFunc){			
								$baseParam['eventKey']  = $data["EventKey"];      #菜单自定义key
								$baseParam['codeInfo']  = $data["ScanCodeInfo"];  #扫描信息
								$baseParam['scanType']  = $data["ScanType"];	  #扫描类型
								$baseParam['result']    = $data["ScanResult"];    #扫描结果				
								call_user_func_array($codePushWaitFunc,array($baseParam));
						}
						break; 
					case "pic_sysphoto":#弹出拍照系统拍照发图时间推送
						if($sysPhone){							
                                $baseParam['picInfo']   = $data["SendPicsInfo"];      #发送图片的信息
                                $baseParam['count']     = $data["Count"];  			  #发送图片的数量
                                $baseParam['picList']   = $data["PicList"];	  		  #图片列表
                                $baseParam['picMd5']    = $data["PicMd5Sum"];    	  #图片Md5验证图片是否接受	
								call_user_func_array($sysPhone,array($baseParam));
						}
						break; 
					case "pic_photo_or_album":#弹出拍照系统拍照发图时间推送
						if($sysPhoneAlbum){			
                                $baseParam['picInfo']   = $data["SendPicsInfo"];      #发送图片的信息
                                $baseParam['count']     = $data["Count"];  			  #发送图片的数量
                                $baseParam['picList']   = $data["PicList"];	  		  #图片列表
                                $baseParam['picMd5']    = $data["PicMd5Sum"];    	  #图片Md5验证图片是否接受	
								call_user_func_array($sysPhoneAlbum,array($baseParam));
						}
						break; 
					case "location_select":#弹出拍照系统拍照发图时间推送
						if($selectLocation){			
                                $baseParam['locationInfo']   = $data["SendLocationInfo"];     #发送位置信息
                                $baseParam['locationX']      = $data["Location_X"];  		  #X左边信息
                                $baseParam['locationX']   	 = $data["Location_Y"];	  		  #Y坐标信息
                                $baseParam['scale']    		 = $data["Scale"];    	  		  #精度	
                                $baseParam['label']   		 = $data["Label"];      		  #地理位置字符串
                                $baseParam['poiname']     	 = $data["Poiname"];  			  #朋友圈POI的名字，可以为空
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
	 * 初始化通用参数
	 */
	public static function initParam($paramArr) {
		$options = array(
			'corpId'            => '', #企业
			'token'             => '', #Token
			'signature'         => '', #微信加密签名
			'timestamp'         => '', #时间戳
			'encodingAesKey'    => '', #EncodingAesKey
			'nonce'             => '', #随机数
			'agentId'           => '', #当前应用ID
			'debug'				=> '', #debug模式
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
     * 签名的验证
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
     * 接收消息
     */
	public static function receiveMsg() {
		$corpId         = self::$corpId;
		$token          = self::$token;
		$signature      = self::$signature;
		$timestamp      = self::$timestamp;
		$encodingAesKey = self::$encodingAesKey;
		$nonce          = self::$nonce;
        
        #接收数据
		$xmlStr= file_get_contents("php://input","r");
        $wxcpt = new WXBizMsgCrypt($token, $encodingAesKey, $corpId);
        $data  = array();
        $code  = $wxcpt->DecryptMsg($signature, $timestamp, $nonce, $xmlStr, $data);
        if (self::$debug){
			
		}
		return $data;
    }
    
    /**
     * 发送文本消息
     */
	public static function sendMsg($paramArr) {
		
		$options = array(
			'toUserName'            => '',             #发送给谁
			'msgContent'            => '', 		       #消息内容
			'msgType'				=> 'text'   	   #消息类型
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
	        	#文本
	        	case "text"  :{  $expend = "<Content><![CDATA[{$msgContent}]]></Content>"; }break;
	        	#图片
	        	case "image" :{  $expend = "<Image><MediaId><![CDATA[{$msgContent}]]></MediaId></Image>"; }break;
	        	#声音
	        	case "voice" :{  $expend = "<Voice><MediaId><![CDATA[{$msgContent}]]></MediaId></Voice>";}break;
	        	#新闻
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
		$sEncryptMsg = ""; //xml格式的密文
		$errCode = $wxcpt->EncryptMsg($sRespData, $timestamp, $nonce, $sEncryptMsg);
		return $sEncryptMsg;
    }
    
    /**
     * 获得AccessToken
     */
    public static function getAccessToken() {
        if (self::$accessToken) {
            return $accessToken;
        }
        #从Redis中获取access_token
        $accessToken = API_Item_Kv_Redis::stringGet(array(
        	'serverName' => 'Default',                    #服务器名
        	'key'        => 'zol_weixin_qy_access_token', #获得数据的Key
        ));
        if ($accessToken) {
            return $accessToken;
        }
        #从api接口中获取access_token
        $corpId  = self::$corpId;
        $secret  = self::$secret;
        $data    = API_Http::curlPage(array('url'=>'https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid='.$corpId.'&corpsecret='.$secret,'timeout'=>3));
        if ($data) {
            $data = json_decode($data, true);
            if ($data && !empty($data['access_token'])) {
                API_Item_Kv_Redis::stringSet(array(
                	'serverName' => 'Default',                    #服务器名
                	'key'        => 'zol_weixin_qy_access_token', #Key
                	'value'      => $data['access_token'],        #value
                	'life'       => 7000,                         #生命期（秒）
                ));
                return $data['access_token'];
            }
        }
        return false;
    }
    
    /**
     * 获得单个用户信息
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
     * 获得部门用户信息
     */
    public static function getUserList($paramArr) {
        $options = array(
            'departmentId' => '', #部门Id
            'fetchChild'   => 0,  #是否递归获取子部门下面的成员
            'isDetail'     => 0,  #是否获得详细的
            'status'       => 0,  #0获取全部员工，1获取已关注成员列表，2获取禁用成员列表，4获取未关注成员列表。status可叠加
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
     * 根据code获取成员信息
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
     * 给员工发送消息
     * 
     */
    
    public  static  function  sendUserMsg($paramArr){
        $options = array(
			'toUserArr'             => '',             #发送给谁
            'toPartyArr'            => '',             #发送给哪个标签
			'msgContent'            => '', 		       #消息内容
			'msgType'				=> 'text',   	   #消息类型
            'safe'                  => 0,
            'agentId'               =>''               #应用Id
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
                        #文本
                        case "text"  :{  $data['text'] = array('content' => $msgContent);   }break;
                        #图片
                        case "image" :{  $data['image'] = array('media_id' =>$msgContent); }break;
                        #声音
                        case "voice" :{  $data['voice'] = array('media_id' =>$msgContent); }break;
                        #视频
                        case "video" :{  $data['video'] = $msgContent;                     }break;
                        #视频
                        case "file" :{   $data['file']  = array('media_id' =>$msgContent); }break;
                        #新闻
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
     * 获取部门列表
     * 
     */

    public static function getDepartmentList($paramArr) {
        $options = array(
            'id'    => '' #部门id
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
     *   获得某个标签下面的用户列表 
     *   
     */

    public static function getTagUserList($paramArr) {
        $options = array(
            'tagId'    => '' #标签Id
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
 * error code 说明.
 * <ul>
 *    <li>-40001: 签名验证错误</li>
 *    <li>-40002: xml解析失败</li>
 *    <li>-40003: sha加密生成签名失败</li>
 *    <li>-40004: encodingAesKey 非法</li>
 *    <li>-40005: corpid 校验错误</li>
 *    <li>-40006: aes 加密失败</li>
 *    <li>-40007: aes 解密失败</li>
 *    <li>-40008: 解密后得到的buffer非法</li>
 *    <li>-40009: base64加密失败</li>
 *    <li>-40010: base64解密失败</li>
 *    <li>-40011: 生成xml失败</li>
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
 * 计算公众平台的消息签名接口.
 */
class SHA1
{
	/**
	 * 用SHA1算法生成安全签名
	 * @param string $token 票据
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 * @param string $encrypt 密文消息
	 */
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
	{
		//排序
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
 * 提供基于PKCS7算法的加解密接口.
 */
class PKCS7Encoder
{
	public static $block_size = 32;

	/**
	 * 对需要加密的明文进行填充补位
	 * @param $text 需要进行填充补位操作的明文
	 * @return 补齐明文字符串
	 */
	function encode($text)
	{
		$block_size = PKCS7Encoder::$block_size;
		$text_length = strlen($text);
		//计算需要填充的位数
		$amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
		if ($amount_to_pad == 0) {
			$amount_to_pad = PKCS7Encoder::block_size;
		}
		//获得补位所用的字符
		$pad_chr = chr($amount_to_pad);
		$tmp = "";
		for ($index = 0; $index < $amount_to_pad; $index++) {
			$tmp .= $pad_chr;
		}
		return $text . $tmp;
	}

	/**
	 * 对解密后的明文进行补位删除
	 * @param decrypted 解密后的明文
	 * @return 删除填充补位后的明文
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
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt
{
	public $key;

	function Prpcrypt($k)
	{
		$this->key = base64_decode($k . "=");
	}

	/**
	 * 对明文进行加密
	 * @param string $text 需要加密的明文
	 * @return string 加密后的密文
	 */
	public function encrypt($text, $corpid)
	{

		try {
			//获得16位随机字符串，填充到明文之前
			$random = $this->getRandomStr();
			$text = $random . pack("N", strlen($text)) . $text . $corpid;
			// 网络字节序
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			//使用自定义的填充方式对明文进行补位填充
			$pkc_encoder = new PKCS7Encoder;
			$text = $pkc_encoder->encode($text);
			mcrypt_generic_init($module, $this->key, $iv);
			//加密
			$encrypted = mcrypt_generic($module, $text);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);

			//print(base64_encode($encrypted));
			//使用BASE64对加密后的字符串进行编码
			return array(ErrorCode::$OK, base64_encode($encrypted));
		} catch (Exception $e) {
			print $e;
			return array(ErrorCode::$EncryptAESError, null);
		}
	}

	/**
	 * 对密文进行解密
	 * @param string $encrypted 需要解密的密文
	 * @return string 解密得到的明文
	 */
	public function decrypt($encrypted, $corpid)
	{

		try {
			//使用BASE64对需要解密的字符串进行解码
			$ciphertext_dec = base64_decode($encrypted);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->key, 0, 16);
			mcrypt_generic_init($module, $this->key, $iv);

			//解密
			$decrypted = mdecrypt_generic($module, $ciphertext_dec);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (Exception $e) {
			return array(ErrorCode::$DecryptAESError, null);
		}


		try {
			//去除补位字符
			$pkc_encoder = new PKCS7Encoder;
			$result = $pkc_encoder->decode($decrypted);
			//去除16位随机字符串,网络字节序和AppId
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
	 * 随机生成16位字符串
	 * @return string 生成的字符串
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
 * 1.第三方回复加密消息给公众平台；
 * 2.第三方收到公众平台发送的消息，验证消息的安全性，并对消息进行解密。
 */
class WXBizMsgCrypt
{
	private $m_sToken;
	private $m_sEncodingAesKey;
	private $m_sCorpid;

	/**
	 * 构造函数
	 * @param $token string 公众平台上，开发者设置的token
	 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
	 * @param $Corpid string 公众平台的Corpid
	 */
	public function WXBizMsgCrypt($token, $encodingAesKey, $Corpid)
	{
		$this->m_sToken = $token;
		$this->m_sEncodingAesKey = $encodingAesKey;
		$this->m_sCorpid = $Corpid;
	}
	
    /*
	*验证URL
    *@param sMsgSignature: 签名串，对应URL参数的msg_signature
    *@param sTimeStamp: 时间戳，对应URL参数的timestamp
    *@param sNonce: 随机串，对应URL参数的nonce
    *@param sEchoStr: 随机串，对应URL参数的echostr
    *@param sReplyEchoStr: 解密之后的echostr，当return返回0时有效
    *@return：成功0，失败返回对应的错误码
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
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
	 *                      当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function EncryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
	{
		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//加密
		$array = $pc->encrypt($sReplyMsg, $this->m_sCorpid);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}

		if ($sTimeStamp == null) {
			$sTimeStamp = time();
		}
		$encrypt = $array[1];

		//生成安全签名
		$sha1 = new SHA1;
		$array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
		$ret = $array[0];
		if ($ret != 0) {
			return $ret;
		}
		$signature = $array[1];

		//生成发送的xml
		$xmlparse = new XMLParse;
		$sEncryptMsg = $xmlparse->generate($encrypt, $signature, $sTimeStamp, $sNonce);
		return ErrorCode::$OK;
	}


	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $msgSignature string 签名串，对应URL参数的msg_signature
	 * @param $timestamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $postData string 密文，对应POST请求的数据
	 * @param &$msg string 解密后的原文，当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function DecryptMsg($sMsgSignature, $sTimeStamp = null, $sNonce, $sPostData, &$data)
	{
		if (strlen($this->m_sEncodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}

		$pc = new Prpcrypt($this->m_sEncodingAesKey);

		//提取密文
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
		//验证安全签名
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
 * 提供提取消息格式中的密文及生成回复消息格式的接口.
 */
class XMLParse
{

	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取出的加密消息字符串
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
	 * 生成xml消息
	 * @param string $encrypt 加密后的消息密文
	 * @param string $signature 安全签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
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