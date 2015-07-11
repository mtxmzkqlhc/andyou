<?php
/*
 * Copyright ? 1998 - 2014 Tencent. All Rights Reserved. ��Ѷ��˾ ��Ȩ����
 * ACCESS ID	2200115181
 * ACCESS KEY	IDL8G8171PBP
 * SECRET KEY	5caf19abde4b49a1d791f789d5c284d0
 * add by zhanghy
 * 2015-05-25
 */
class API_Item_Open_Xinge {
	/**
	 * ��ʼ��һ��userid��account��Ӧ��ϵ	
	 * tokenΪ�豸��  һ���豸һ��token
	 * û�в�һ�����в���
	 */
	public static function setAccessToken($paramArr) {
		$options = array(
				'z_api_id'			=> '',		#ACCESS ID
				'z_api_type'		=> 'xinge',	#���ͷ�������
				'z_api_token'		=> '',		#�豸Ψһ��ʶ
				'z_api_secert'		=> '',		#SECRET KEY
				'z_api_account'		=> '',		#�����û����ɵ�Ψһ��
				'z_bind_userid'     => '', 		#�û�ID
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
	
		if(!$z_bind_userid || !$z_api_id || !$z_api_type || !$z_api_token || !$z_api_account || !$z_api_secert)return false;
	
		$db 		= API_Db_User::instance();
		$apiInfo 	= self::getUserApiId(array('z_bind_userid' => $z_bind_userid));
		#���API id
		if($apiInfo == false || empty($apiInfo)){
			$sql = "insert z_push_user_token(`z_api_id`, `z_api_type`, `z_api_token`, `z_api_secert`, `z_api_account`, `z_add_time`, `z_bind_userid`) 
					values('{$z_api_id}', '{$z_api_type}', '{$z_api_token}', '{$z_api_secert}', '{$z_api_account}', now(), '{$z_bind_userid}')";
			$res = $db -> query($sql);
		}
		
		return true;
	}
	
	/**
	 * ���ZOL�û��ĵİ���Ϣ
	 */
	public static function getUserApiId($paramArr) {
		$options = array(
				'z_bind_userid'	=> '', #�û�ID
				'num'			=> 1,
				'type'			=> 'xinge',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		if(!$z_bind_userid)return false;
	
		$db 	= API_Db_User::instance();
		$sql 	= "select * from z_push_user_token where z_bind_userid = '{$z_bind_userid}' 
					and z_api_type = '{$type}' limit {$num}";
		
		return $db->getAll($sql);
	}
	
	/**
	 * �·�IOS�����豸֪ͨ��Ϣ
	 * @param type $paramArr
	 * @return ����push_id
	 */
	public static function pushSingleDeviceNotificationIos($paramArr){
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'environment' 	=> 0, 	//environment
				'userId' 		=> '', 	//userId
				'expireTime'	=> 86400,				//��Ϣ���ߴ洢��ã���λΪ�룬��洢ʱ��3�졣
				'sound'			=> 'beep.wav',			//����֪ͨ������
				'sendTime'		=> '',					//��Ϣ��ʱ����ʱ�䣬��ʽΪyear-mon-day hour:min:sec����С�ڷ�������ǰʱ�����������͡�ѡ�Ĭ��Ϊ���ַ�����������������
				'badge'			=> 1,					//���ýǱ���ֵ
				'custom'		=> false,				//�û��Զ����key-value��
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
		
		if (!$accessId || !$secretKey || !$token || !$content) {
			return false;
		}
		
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		
		$mess->setExpireTime($expireTime);
		$mess->setSendTime($sendTime);
		$mess->setAlert($content);
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_URL);
		$action->setUrl("http://www.zol.com.cn/");
		#��url��Ҫ�û�ȷ��
		$action->setComfirmOnUrl(1);
		$mess->setBadge($badge);
		$mess->setSound($sound);
		
		//$custom = array('key1'=>'value1', 'key2'=>'value2');
		$mess->setCustom($custom);
		$acceptTime = new TimeInterval(0, 0, 23, 59);			//��ʾһ���������͵�ʱ������䣬��startHour : startMin��endHour : endMin
		$mess->addAcceptTime($acceptTime);
		$ret = $push->PushSingleDevice($token, $mess);
		return $ret;
	}
	
	//IOS�·�����˺�  ����push_id
	function pushAccountList($paramArr) {
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'environment' 	=> 2, 	//environment
				'userId' 		=> '', 	//userId
				'expireTime'	=> 86400,				//��Ϣ���ߴ洢��ã���λΪ�룬��洢ʱ��3�졣
				'sound'			=> 'beep.wav',			//����֪ͨ������
				'sendTime'		=> '',					//��Ϣ��ʱ����ʱ�䣬��ʽΪyear-mon-day hour:min:sec����С�ڷ�������ǰʱ�����������͡�ѡ�Ĭ��Ϊ���ַ�����������������
				'badge'			=> 1,					//���ýǱ���ֵ
				'custom'		=> false,				//�û��Զ����key-value��
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
		
		if (!$accessId || !$secretKey || !$token || !$content || !$userId) {
			return false;
		}
		
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		
		$mess->setExpireTime($expireTime);
		$mess->setAlert($content);
		//$mess->setAlert(array('key1'=>'value1'));
		$mess->setBadge(1);
		$mess->setSound($sound);
		$mess->setCustom($custom);
		
		$acceptTime1 = new TimeInterval(0, 0, 23, 59);
		$mess->addAcceptTime($acceptTime1);
		$ret = $push->PushAccountList(0, $userId, $mess, XingeApp::IOSENV_DEV);
		return $ret;
	}
	
	/**
	 * ʹ��Ĭ�����÷����ر�򵥵�֪ͨIOS
	 * @return ����push_id
	 */
	public static function pushSimpleMessageIos($paramArr) {
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'account'		=> '',	//account
				'userId' 		=> '', 	//userId
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
	
		if (!$accessId || !$secretKey || (!$token && !$account) || !$content) {
			return false;
		}
		
		if ($token) {
			$ret = XingeApp::PushTokenIos($accessId, $secretKey, $content, $token, XingeApp::IOSENV_PROD);
		} else {
			$ret = XingeApp::PushAccountIos($accessId, $secretKey, $content, $account, XingeApp::IOSENV_PROD);
		}
		
		return $ret;
	}
	
	/**
	 * ������Ϣ�������豸
	 * title��environment����IOS����ANDROID
	 */
	public static function pushAllMessage($paramArr) {
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'title'			=> '',	//title
				'content' 		=> '', 	//���͵�����
				'environment' 	=> 0, 	//environment
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
	
		if (!$accessId || !$secretKey || !$content) {
			return false;
		}
	
		if ($environment == 0 && $title) {
			//ANDROID
			$ret = XingeApp::PushAllAndroid($accessId, $secretKey, $title, $content);
		} else {
			//IOS
			$ret = XingeApp::PushAllIos($accessId, $secretKey, $content, $environment);
		}
	
		return $ret;
	}
	
	
	/**
	 * �·�ANDROID�����豸֪ͨ��Ϣ
	 * @param type $paramArr
	 * @return ����push_id
	 */
	public static function pushSingleDeviceNotification($paramArr){
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'title'			=> '',	//���ͱ���
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'environment' 	=> 0, 	//environment
				'userId' 		=> '', 	//userId
				'expireTime'	=> 86400,				//��Ϣ���ߴ洢��ã���λΪ�룬��洢ʱ��3�졣
				'sound'			=> 'beep.wav',			//����֪ͨ������
				'sendTime'		=> '',					//��Ϣ��ʱ����ʱ�䣬��ʽΪyear-mon-day hour:min:sec����С�ڷ�������ǰʱ�����������͡�ѡ�Ĭ��Ϊ���ַ�����������������
				'badge'			=> 1,					//���ýǱ���ֵ
				'custom'		=> false,				//�û��Զ����key-value��
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
	
		if (!$accessId || !$secretKey || !$token || !$content) {
			return false;
		}
	
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();
		
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setExpireTime($expireTime);
		
		#���壺��ʽ���0�����壬�𶯣����ɴ�֪ͨ���������Ӱ����ǰ֪ͨ
		$style 	= new Style(0,1,1,0,0);
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_URL);
		$action->setUrl("http://www.zol.com.cn/");
		#��url��Ҫ�û�ȷ��
		$action->setComfirmOnUrl(1);
		
		$mess->setStyle($style);
		$mess->setAction($action);
		$mess->setCustom($custom);
		$acceptTime1 = new TimeInterval(0, 0, 23, 59);
		$mess->addAcceptTime($acceptTime1);
		$ret = $push->PushSingleDevice($token, $mess);
		
		return $ret;
	}
	
	/**
	 * ʹ��Ĭ�����÷����ر�򵥵�֪ͨANDROID
	 * @return ����push_id
	 */
	public static function pushSimpleMessage($paramArr) {
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'title'			=> '',	//title
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'account'		=> '',	//account
				'userId' 		=> '', 	//userId
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
	
		if (!$accessId || !$secretKey || (!$token && !$account) || !$content || !$title) {
			return false;
		}
	
		if ($token) {
			$ret = XingeApp::PushTokenAndroid($accessId, $secretKey, $title, $content, $token);
		} else {
			$ret = XingeApp::PushAccountAndroid($accessId, $secretKey, $title, $content, $account);
		}
	
		return $ret;
	}
	
	//�·������豸ANDROID  ����push_id
	function pushAllDevices($paramArr){
		$options = array(
				'accessId' 		=> '', 	//accessId
				'secretKey' 	=> '', 	//secretKey
				'title'			=> '',	//���ͱ���
				'content' 		=> '', 	//���͵�����
				'token' 		=> '', 	//token
				'environment' 	=> 0, 	//environment
				'userId' 		=> '', 	//userId
				'expireTime'	=> 86400,				//��Ϣ���ߴ洢��ã���λΪ�룬��洢ʱ��3�졣
				'sound'			=> 'beep.wav',			//����֪ͨ������
				'sendTime'		=> '',					//��Ϣ��ʱ����ʱ�䣬��ʽΪyear-mon-day hour:min:sec����С�ڷ�������ǰʱ�����������͡�ѡ�Ĭ��Ϊ���ַ�����������������
				'badge'			=> 1,					//���ýǱ���ֵ
				'custom'		=> false,				//�û��Զ����key-value��
		);
		if (is_array($paramArr))
			$options = array_merge($options, $paramArr);
		extract($options);
		
		if (!$accessId || !$secretKey || !$token || !$content) {
			return false;
		}
		
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();

		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setExpireTime($expireTime);
		$style 	= new Style(0);
		#���壺��ʽ���0�����壬�𶯣����ɴ�֪ͨ���������Ӱ����ǰ֪ͨ
		$style 	= new Style(0,1,1,0,0);
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_URL);
		$action->setUrl("http://www.zol.com.cn/");
		#��url��Ҫ�û�ȷ��
		$action->setComfirmOnUrl(1);
		$mess->setStyle($style);
		$mess->setAction($action);
	
		$ret = $push->PushAllDevices(0, $mess);
		return $ret;
	}
}

/*
 * ����
 */
class XingeApp {
	
	const DEVICE_ALL 		= 0;
	const DEVICE_BROWSER 	= 1;
	const DEVICE_PC 		= 2;
	const DEVICE_ANDROID 	= 3;
	const DEVICE_IOS 		= 4;
	const DEVICE_WINPHONE 	= 5;
	
	const IOSENV_PROD 		= 1;
	const IOSENV_DEV 		= 2;
	
	public function __construct($accessId, $secretKey)
	{
		assert(isset($accessId) && isset($secretKey));
		
		$this->accessId = $accessId;
		$this->secretKey = $secretKey;
	}
	public function __destruct(){}

	/**
	 * ʹ��Ĭ������������Ϣ������android�豸
	 */
	public static function PushTokenAndroid($accessId, $secretKey, $title, $content, $token)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setStyle(new Style(0, 1, 1, 1, 0));
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_ACTIVITY);
		$mess->setAction($action);
		$ret = $push->PushSingleDevice($token, $mess);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ������ios�豸
	 */
	public static function PushTokenIos($accessId, $secretKey, $content, $token, $environment)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		$mess->setAlert($content);
		$ret = $push->PushSingleDevice($token, $mess, $environment);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ������android���˻�
	 */
	public static function PushAccountAndroid($accessId, $secretKey, $title, $content, $account)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setStyle(new Style(0, 1, 1, 1, 0));
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_ACTIVITY);
		$mess->setAction($action);
		$ret = $push->PushSingleAccount(0, $account, $mess);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ������ios���˻�
	 */
	public static function PushAccountIos($accessId, $secretKey, $content, $account, $environment)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		$mess->setAlert($content);
		$ret = $push->PushSingleAccount(0, $account, $mess, $environment);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ�������豸android��
	 */
	public static function PushAllAndroid($accessId, $secretKey, $title, $content)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setStyle(new Style(0, 1, 1, 1, 0));
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_ACTIVITY);
		$mess->setAction($action);
		$ret = $push->PushAllDevices(0, $mess);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ�������豸ios��
	 */
	public static function PushAllIos($accessId, $secretKey, $content, $environment)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		$mess->setAlert($content);
		$ret = $push->PushAllDevices(0, $mess, $environment);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ����ǩѡ���豸android��
	 */
	public static function PushTagAndroid($accessId, $secretKey, $title, $content, $tag)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new Message();
		$mess->setTitle($title);
		$mess->setContent($content);
		$mess->setType(Message::TYPE_NOTIFICATION);
		$mess->setStyle(new Style(0, 1, 1, 1, 0));
		$action = new ClickAction();
		$action->setActionType(ClickAction::TYPE_ACTIVITY);
		$mess->setAction($action);
		$ret = $push->PushTags(0, array(0=>$tag), 'OR', $mess);
		return $ret;
	}

	/**
	 * ʹ��Ĭ������������Ϣ����ǩѡ���豸ios��
	 */
	public static function PushTagIos($accessId, $secretKey, $content, $tag, $environment)
	{
		$push = new XingeApp($accessId, $secretKey);
		$mess = new MessageIOS();
		$mess->setAlert($content);
		$ret = $push->PushTags(0, array(0=>$tag), 'OR', $mess, $environment);
		return $ret;
	}

	/**
	 * ������Ϣ�������豸
	 */
	public function  PushSingleDevice($deviceToken, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');

		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_token'] = $deviceToken;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;
		
		return $this->callRestful(self::RESTAPI_PUSHSINGLEDEVICE, $params);
	}
	
	/**
	 * ������Ϣ�������˻�
	 */
	public function  PushSingleAccount($deviceType, $account, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1);
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5) 
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!is_string($account) || empty($account))
		{
			$ret['err_msg'] = 'account not valid';
			return $ret;
		}
		if (!($message instanceof Message) && !($message instanceof MessageIOS))
		{
			$ret['err_msg'] = 'message is not android or ios';
			return $ret;
		}
		if($message instanceof MessageIOS)
		{
			if($environment!=XingeApp::IOSENV_DEV && $environment!=XingeApp::IOSENV_PROD)
			{
				$ret['err_msg'] = "ios message environment invalid";
				return $ret;
			}
		}
		if (!$message->isValid())
		{
			$ret['err_msg'] = 'message not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message)
			$params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['account'] = $account;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;

		return $this->callRestful(self::RESTAPI_PUSHSINGLEACCOUNT, $params);
	}
	
	/**
	 * ������Ϣ������˻�
	 */
	public function  PushAccountList($deviceType, $accountList, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1);
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5)
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!is_array($accountList) || empty($accountList))
		{
			$ret['err_msg'] = 'accountList not valid';
			return $ret;
		}
		if (!($message instanceof Message) && !($message instanceof MessageIOS))
		{
			$ret['err_msg'] = 'message is not android or ios';
			return $ret;
		}
		if($message instanceof MessageIOS)
		{
			if($environment!=XingeApp::IOSENV_DEV && $environment!=XingeApp::IOSENV_PROD)
			{
				$ret['err_msg'] = "ios message environment invalid";
				return $ret;
			}
		}
		if (!$message->isValid())
		{
			$ret['err_msg'] = 'message not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		if($message instanceof Message)
			$params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['account_list'] = json_encode($accountList);
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;
	
		return $this->callRestful(self::RESTAPI_PUSHACCOUNTLIST, $params);
	}
	
	/**
	 * ������Ϣ��APP�����豸
	 */
	public function  PushAllDevices($deviceType, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5)
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}

		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;

		if (!is_null($message->getLoopInterval()) && $message->getLoopInterval() > 0 
			&& !is_null($message->getLoopTimes()) && $message->getLoopTimes() > 0) {
			$params['loop_interval'] = $message->getLoopInterval();
			$params['loop_times'] = $message->getLoopTimes();
		}
		//var_dump($params);
	
		return $this->callRestful(self::RESTAPI_PUSHALLDEVICE, $params);
	}
	
	/**
	 * ������Ϣ��ָ��tags���豸
	 * ��Ҫ���͵�tagListֻ��һ���tagsOpӦΪOR
	 */
	public function  PushTags($deviceType, $tagList, $tagsOp, $message, $environment=0)
	{
		$ret = array('ret_code'=>-1, 'err_msg'=>'message not valid');
		if (!is_int($deviceType) || $deviceType<0 || $deviceType >5) 
		{
			$ret['err_msg'] = 'deviceType not valid';
			return $ret;
		}
		if (!is_array($tagList) || empty($tagList)) 
		{
			$ret['err_msg'] = 'tagList not valid';
			return $ret;
		}
		if (!is_string($tagsOp) || ($tagsOp!='AND' && $tagsOp!='OR')) 
		{
			$ret['err_msg'] = 'tagsOp not valid';
			return $ret;
		}
		
		if (!($message instanceof Message) && !($message instanceof MessageIOS)) return $ret;
		if(!$message->isValid()) return $ret;
		
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		$params['send_time'] = $message->getSendTime();
		if($message instanceof Message) $params['multi_pkg'] = $message->getMultiPkg();
		$params['device_type'] = $deviceType;
		$params['message_type'] = $message->getType();
		$params['tags_list'] = json_encode($tagList);
		$params['tags_op'] = $tagsOp;
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;
		
		if (!is_null($message->getLoopInterval()) && $message->getLoopInterval() > 0 
			&& !is_null($message->getLoopTimes()) && $message->getLoopTimes() > 0) {
			$params['loop_interval'] = $message->getLoopInterval();
			$params['loop_times'] = $message->getLoopTimes();
		}

		return $this->callRestful(self::RESTAPI_PUSHTAGS, $params);
	}

	/**
	 * ����������������
	 */
	public function  CreateMultipush($message, $environment=0)
	{
		$ret = array('ret_code'=>-1);
		if (!($message instanceof Message) && !($message instanceof MessageIOS))
		{
			$ret['err_msg'] = 'message is not android or ios';
			return $ret;
		}
		if($message instanceof MessageIOS)
		{
			if($environment!=XingeApp::IOSENV_DEV && $environment!=XingeApp::IOSENV_PROD)
			{
				$ret['err_msg'] = "ios message environment invalid";
				return $ret;
			}
		}
		if (!$message->isValid())
		{
			$ret['err_msg'] = 'message not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['expire_time'] = $message->getExpireTime();
		if($message instanceof Message)
			$params['multi_pkg'] = $message->getMultiPkg();
		$params['message_type'] = $message->getType();
		$params['message'] = $message->toJson();
		$params['timestamp'] = time();
		$params['environment'] = $environment;
	
		return $this->callRestful(self::RESTAPI_CREATEMULTIPUSH, $params);
	}
	
	/**
	 * ���ʺŴ���������
	 */
	public function  PushAccountListMultiple($pushId, $accountList)
	{
		$pushId = intval($pushId);
		$ret = array('ret_code'=>-1);
		if ($pushId <= 0)
		{
			$ret['err_msg'] = 'pushId not valid';
			return $ret;
		}
		if (!is_array($accountList) || empty($accountList))
		{
			$ret['err_msg'] = 'accountList not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_id'] = $pushId;
		$params['account_list'] = json_encode($accountList);
		$params['timestamp'] = time();	

		return $this->callRestful(self::RESTAPI_PUSHACCOUNTLISTMULTIPLE, $params);
	}

	/**
	 * ��Token����������
	 */
	public function  PushDeviceListMultiple($pushId, $deviceList)
	{
		$pushId = intval($pushId);
		$ret = array('ret_code'=>-1);
		if ($pushId <= 0)
		{
			$ret['err_msg'] = 'pushId not valid';
			return $ret;
		}
		if (!is_array($deviceList) || empty($deviceList))
		{
			$ret['err_msg'] = 'deviceList not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_id'] = $pushId;
		$params['device_list'] = json_encode($deviceList);
		$params['timestamp'] = time();	

		return $this->callRestful(self::RESTAPI_PUSHDEVICELISTMULTIPLE, $params);
	}

	/**
	 * ��ѯ��Ϣ����״̬
	 * @param array $pushIdList pushId(string)����
	 */
	public function  QueryPushStatus($pushIdList)
	{
		$ret = array('ret_code'=>-1);
		$idList = array();
		if (!is_array($pushIdList) || empty($pushIdList)) 
		{
			$ret['err_msg'] = 'pushIdList not valid';
			return $ret;
		}
		foreach ($pushIdList as $pushId)
		{
			$idList[] = array('push_id'=>$pushId);
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_ids'] = json_encode($idList);
		$params['timestamp'] = time();

		return $this->callRestful(self::RESTAPI_QUERYPUSHSTATUS, $params);
	}
	
	/**
	 * ��ѯӦ�ø��ǵ��豸��
	 */
	public function  QueryDeviceCount()
	{
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['timestamp'] = time();

		return $this->callRestful(self::RESTAPI_QUERYDEVICECOUNT, $params);
	}
	
	/**
	 * ��ѯӦ�ñ�ǩ
	 */
	public function  QueryTags($start=0, $limit=100)
	{
		$ret = array('ret_code'=>-1);
		if (!is_int($start) || !is_int($limit))
		{
			$ret['err_msg'] = 'start or limit not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['start'] = $start;
		$params['limit'] = $limit;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTAGS, $params);
	}
	
	/**
	 * ��ѯ��ǩ��token����
	 */
	public function  QueryTagTokenNum($tag)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($tag))
		{
			$ret['err_msg'] = 'tag is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['tag'] = $tag;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTAGTOKENNUM, $params);
	}
	
	/**
	 * ��ѯtoken�ı�ǩ
	 */
	public function  QueryTokenTags($deviceToken)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($deviceToken))
		{
			$ret['err_msg'] = 'deviceToken is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['device_token'] = $deviceToken;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTOKENTAGS, $params);
	}
	
	/**
	 * ȡ����ʱ����
	 */
	public function  CancelTimingPush($pushId)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($pushId) || empty($pushId))
		{
			$ret['err_msg'] = 'pushId not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['push_id'] = $pushId;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_CANCELTIMINGPUSH, $params);
	}
	
	//jsonת��Ϊ����
	protected function json2Array($json){
		$json=stripslashes($json);
		return json_decode($json,true);
	}
	
	protected function callRestful($url, $params)
	{
		$paramsBase = new ParamsBase($params);
		$sign = $paramsBase->generateSign(RequestBase::METHOD_POST, $url,  $this->secretKey);
		$params['sign'] = $sign;
		
		$requestBase = new RequestBase();
		$ret = $this->json2Array($requestBase->exec($url, $params, RequestBase::METHOD_POST));
		
		return $ret;
	}

	private function ValidateToken($token)
	{
		if(intval($this->accessId) >= 2200000000) {
            return strlen($token) == 64;
		} else {
            return (strlen($token) == 40 || strlen($token) == 64);
		}
	}

	public function InitParams() {
		
		$params = array();
        $params['access_id'] = $this->accessId;
        $params['timestamp'] = time();

        return $params;
	}    

	public function BatchSetTag($tagTokenPairs) 
	{
		$ret = array('ret_code' => -1);

		foreach ($tagTokenPairs as $pair) {
			if (!($pair instanceof TagTokenPair)) {
				$ret['err_msg'] = 'tag-token pair type error!';
				return $ret;
			}
	        if (!$this->ValidateToken($pair->token)) {
	        	$ret['err_msg'] = sprintf("invalid token %s", $pair->token);
	        	return $ret;
	        }
	    }
	    $params = $this->InitParams();

	    $tag_token_list = array();
	    foreach ($tagTokenPairs as $pair) {
	    	array_push($tag_token_list, array($pair->tag, $pair->token));
	    }
	    $params['tag_token_list'] = json_encode($tag_token_list); 
	   
	   	return $this->callRestful(self::RESTAPI_BATCHSETTAG, $params); 
	}

	public function BatchDelTag($tagTokenPairs) 
	{
		$ret = array('ret_code' => -1);

		foreach ($tagTokenPairs as $pair) {
			if (!($pair instanceof TagTokenPair)) {
				$ret['err_msg'] = 'tag-token pair type error!';
				return $ret;
			}
	        if (!$this->ValidateToken($pair->token)) {
	        	$ret['err_msg'] = sprintf("invalid token %s", $pair->token);
	        	return $ret;
	        }
	    }
	    $params = $this->InitParams();

	    $tag_token_list = array();
	    foreach ($tagTokenPairs as $pair) 	{
	    	array_push($tag_token_list, array($pair->tag, $pair->token));
	    }
	    $params['tag_token_list'] = json_encode($tag_token_list); 
	   
	   	return $this->callRestful(self::RESTAPI_BATCHDELTAG, $params); 
	}
	public function QueryInfoOfToken($deviceToken)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($deviceToken))
		{
			$ret['err_msg'] = 'deviceToken is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['device_token'] = $deviceToken;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYINFOOFTOKEN, $params);
	}
	public function QueryTokensOfAccount($account)
	{
		$ret = array('ret_code'=>-1);
		if (!is_string($account))
		{
			$ret['err_msg'] = 'account is not valid';
			return $ret;
		}
		$params = array();
		$params['access_id'] = $this->accessId;
		$params['account'] = $account;
		$params['timestamp'] = time();
	
		return $this->callRestful(self::RESTAPI_QUERYTOKENSOFACCOUNT, $params);
	}


	public $accessId = ''; //Ӧ�õĽ���Id
	public $secretKey = ''; //Ӧ�õ�skey
	
	const RESTAPI_PUSHSINGLEDEVICE = 'http://openapi.xg.qq.com/v2/push/single_device';
	const RESTAPI_PUSHSINGLEACCOUNT = 'http://openapi.xg.qq.com/v2/push/single_account';
	const RESTAPI_PUSHACCOUNTLIST = 'http://openapi.xg.qq.com/v2/push/account_list';
	const RESTAPI_PUSHALLDEVICE = 'http://openapi.xg.qq.com/v2/push/all_device';
	const RESTAPI_PUSHTAGS = 'http://openapi.xg.qq.com/v2/push/tags_device';
	const RESTAPI_QUERYPUSHSTATUS = 'http://openapi.xg.qq.com/v2/push/get_msg_status';
	const RESTAPI_QUERYDEVICECOUNT = 'http://openapi.xg.qq.com/v2/application/get_app_device_num';
	const RESTAPI_QUERYTAGS = 'http://openapi.xg.qq.com/v2/tags/query_app_tags';
	const RESTAPI_CANCELTIMINGPUSH = 'http://openapi.xg.qq.com/v2/push/cancel_timing_task';
	const RESTAPI_BATCHSETTAG = 'http://openapi.xg.qq.com/v2/tags/batch_set';
	const RESTAPI_BATCHDELTAG = 'http://openapi.xg.qq.com/v2/tags/batch_del';
    const RESTAPI_QUERYTOKENTAGS = 'http://openapi.xg.qq.com/v2/tags/query_token_tags';
    const RESTAPI_QUERYTAGTOKENNUM = 'http://openapi.xg.qq.com/v2/tags/query_tag_token_num';
	const RESTAPI_CREATEMULTIPUSH = 'http://openapi.xg.qq.com/v2/push/create_multipush';
	const RESTAPI_PUSHACCOUNTLISTMULTIPLE = 'http://openapi.xg.qq.com/v2/push/account_list_multiple';
	const RESTAPI_PUSHDEVICELISTMULTIPLE = 'http://openapi.xg.qq.com/v2/push/device_list_multiple';
	const RESTAPI_QUERYINFOOFTOKEN = 'http://openapi.xg.qq.com/v2/application/get_app_token_info';
	const RESTAPI_QUERYTOKENSOFACCOUNT = 'http://openapi.xg.qq.com/v2/application/get_app_account_tokens';
	
}

class TagTokenPair {

	public function __construct($tag, $token)
	{
		$this->tag = strval($tag);
        $this->token = strval($token);
	}
	public function __destruct(){}

	public $tag;
	public $token;
}

class Message {

	public function __construct()
	{
		$this->m_acceptTimes = array();
		$this->m_multiPkg = 0;
		$this->m_raw = "";
		$this->m_style = new Style(0);
		$this->m_action = new ClickAction();
	}
	public function __destruct(){}

	public function setTitle($title)
	{
		$this->m_title = $title;
	}
	public function setContent($content)
	{
		$this->m_content = $content;
	}
	public function setExpireTime($expireTime)
	{
		$this->m_expireTime = $expireTime;
	}
	public function getExpireTime()
	{
		return $this->m_expireTime;
	}
	public function setSendTime($sendTime)
	{
		$this->m_sendTime = $sendTime;
	}
	public function getSendTime()
	{
		return $this->m_sendTime;
	}
	public function addAcceptTime($acceptTime)
	{
		$this->m_acceptTimes[] = $acceptTime;
	}
	public function acceptTimeToJson()
	{
		$ret = array();
		foreach ($this->m_acceptTimes as $acceptTime)
		{
			$ret[] = $acceptTime->toArray();
		}
		return $ret;
	}
	/**
	 * ��Ϣ����
	 * @param int $type 1��֪ͨ 2��͸����Ϣ
	 */
	public function setType($type)
	{
		$this->m_type = $type;
	}
	public function getType()
	{
		return $this->m_type;
	}
	public function setMultiPkg($multiPkg)
	{
		$this->m_multiPkg = $multiPkg;
	}
	public function getMultiPkg()
	{
		return $this->m_multiPkg;
	}
	public function setStyle($style)
	{
		$this->m_style = $style;
	}
	public function setAction($action)
	{
		$this->m_action = $action;
	}
	public function setCustom($custom)
	{
		$this->m_custom = $custom;
	}
	public function setRaw($raw)
	{
		$this->m_raw = $raw;
	}
	public function getLoopInterval() 
	{
		return $this->m_loopInterval;
	}
	public function setLoopInterval($loopInterval) 
	{
		$this->m_loopInterval = $loopInterval;
	}
	public function getLoopTimes() 
	{
		return $this->m_loopTimes;
	}
	public function setLoopTimes($loopTimes) 
	{
		$this->m_loopTimes = $loopTimes;
	}

	public function toJson()
	{
		if(!empty($this->m_raw)) return $this->m_raw;
		$ret = array();
		if ($this->m_type == self::TYPE_NOTIFICATION)
		{
			$ret['title'] = $this->m_title;
			$ret['content'] = $this->m_content;
			$ret['accept_time'] = $this->acceptTimeToJson();
			$ret['builder_id'] = $this->m_style->getBuilderId();
			$ret['ring'] = $this->m_style->getRing();
			$ret['vibrate'] = $this->m_style->getVibrate();
			$ret['clearable'] = $this->m_style->getClearable();
			$ret['n_id'] = $this->m_style->getNId();

			if (!is_null($this->m_style->getRingRaw())) {
				$ret['ring_raw'] = $this->m_style->getRingRaw();
			}
			$ret['lights'] = $this->m_style->getLights();
			$ret['icon_type'] = $this->m_style->getIconType();
			if (!is_null($this->m_style->getIconRes())) {
				$ret['icon_res'] = $this->m_style->getIconRes();
			}
			$ret['style_id'] = $this->m_style->getStyleId();
			if (!is_null($this->m_style->getSmallIcon())) {
				$ret['small_icon'] = $this->m_style->getSmallIcon();
			}
			
			$ret['action'] = $this->m_action->toJson();

		}
		else if($this->m_type == self::TYPE_MESSAGE)
		{
			$ret['title'] = $this->m_title;
			$ret['content'] = $this->m_content;
			$ret['accept_time'] = $this->acceptTimeToJson();
		}
		$ret['custom_content'] = $this->m_custom;
		return json_encode($ret);
	}

	public function isValid()
	{
		if (is_string($this->m_raw) && !empty($this->raw)) return true;
		if(!isset($this->m_title))
			$this->m_title = "";
		else if(!is_string($this->m_title) || empty($this->m_title)) 
			return false;
		if(!isset($this->m_content))
			$this->m_content = "";
		else if(!is_string($this->m_content) || empty($this->m_content)) 
			return false;
		if(!is_int($this->m_type) || $this->m_type<self::TYPE_NOTIFICATION || $this->m_type>self::TYPE_MESSAGE) return false;
		if(!is_int($this->m_multiPkg) || $this->m_multiPkg<0 || $this->m_multiPkg>1) return false;
		if($this->m_type == self::TYPE_NOTIFICATION)
		{
			if(!($this->m_style instanceof Style) || !($this->m_action instanceof ClickAction))
				return false;
			if(!$this->m_style->isValid() || !$this->m_action->isValid())
				return false;
		}
		if (isset($this->m_expireTime))
		{
			if(!is_int($this->m_expireTime) || $this->m_expireTime>3*24*60*60)
				return false;
		}
		else
		{
			$this->m_expireTime = 0;
		}

		if(isset($this->m_sendTime))
		{
			if(strtotime($this->m_sendTime)===false) return false;
		}
		else
		{
			$this->m_sendTime = "2013-12-19 17:49:00";
		}

		foreach ($this->m_acceptTimes as $value)
		{
			if(!($value instanceof TimeInterval) || !$value->isValid())
				return false;
		}

		if(isset($this->m_custom))
		{
			if(!is_array($this->m_custom))
				return false;
		}
		else
		{
			$this->m_custom = array();
		}

		if(isset($this->m_loopInterval)) {
			if(!(is_int($this->m_loopInterval) && $this->m_loopInterval > 0)) {
				return false;
			}	
		}

		if(isset($this->m_loopTimes)) {
			if(!(is_int($this->m_loopTimes) && $this->m_loopTimes > 0)) {
				return false;
			}	
		}

		if(isset($this->m_loopInterval) && isset($this->m_loopTimes)) {
			if(($this->m_loopTimes - 1) * $this->m_loopInterval + 1 > self::MAX_LOOP_TASK_DAYS) {
				return false;
			}
		}

		return true;
	}

	private $m_title;
	private $m_content;
	private $m_expireTime;
	private $m_sendTime;
	private $m_acceptTimes;
	private $m_type;
	private $m_multiPkg;
	private $m_style;
	private $m_action;
	private $m_custom;
	private $m_raw;
	private $m_loopInterval;
	private $m_loopTimes;
	
	const TYPE_NOTIFICATION  = 1;
	const TYPE_MESSAGE = 2;
	const MAX_LOOP_TASK_DAYS = 15;
}

class MessageIOS
{
	public function __construct()
	{
		$this->m_acceptTimes = array();
	}
	public function __destruct(){}

	public function setExpireTime($expireTime)
	{
		$this->m_expireTime = $expireTime;
	}
	public function getExpireTime()
	{
		return $this->m_expireTime;
	}
	public function setSendTime($sendTime)
	{
		$this->m_sendTime = $sendTime;
	}
	public function getSendTime()
	{
		return $this->m_sendTime;
	}
	public function addAcceptTime($acceptTime)
	{
		$this->m_acceptTimes[] = $acceptTime;
	}
	public function acceptTimeToJson()
	{
		$ret = array();
		foreach ($this->m_acceptTimes as $acceptTime)
		{
			$ret[] = $acceptTime->toArray();
		}
		return $ret;
	}
	public function setCustom($custom)
	{
		$this->m_custom = $custom;
	}
	public function setRaw($raw)
	{
		$this->m_raw = $raw;
	}
	public function setAlert($alert)
	{
		$this->m_alert = $alert;
	}
	public function setBadge($badge)
	{
		$this->m_badge = $badge;
	}
	public function setSound($sound)
	{
		$this->m_sound = $sound;
	}
	public function getType()
	{
		return 0;
	}
	public function getCategory()
	{
		return $this->m_category;
	}
	public function setCategory($category)
	{
		$this->m_category = $category;
	}
	public function getLoopInterval() 
	{
		return $this->m_loopInterval;
	}
	public function setLoopInterval($loopInterval) 
	{
		$this->m_loopInterval = $loopInterval;
	}
	public function getLoopTimes() 
	{
		return $this->m_loopTimes;
	}
	public function setLoopTimes($loopTimes) 
	{
		$this->m_loopTimes = $loopTimes;
	}

	public function toJson()
	{
		if(!empty($this->m_raw)) return $this->m_raw;
		$ret = $this->m_custom;
		$aps = array();
		$ret['accept_time'] = $this->acceptTimeToJson();
		$aps['alert'] = $this->m_alert;
		if(isset($this->m_badge)) $aps['badge'] = $this->m_badge;
		if(isset($this->m_sound))$aps['sound'] = $this->m_sound;
		if(isset($this->m_category))$aps['category'] = $this->m_category;
		$ret['aps'] = $aps;
		return json_encode($ret);
	}
	
	public function isValid()
	{
		if (is_string($this->m_raw) && !empty($this->raw)) return true;
		if (isset($this->m_expireTime))
		{
			if(!is_int($this->m_expireTime) || $this->m_expireTime>3*24*60*60)
				return false;
		}
		else
		{
			$this->m_expireTime = 0;
		}
	
		if(isset($this->m_sendTime))
		{
			if(strtotime($this->m_sendTime)===false) return false;
		}
		else
		{
			$this->m_sendTime = "2014-03-13 12:00:00";
		}
	
		foreach ($this->m_acceptTimes as $value)
		{
			if(!($value instanceof TimeInterval) || !$value->isValid())
				return false;
		}
	
		if(isset($this->m_custom))
		{
			if(!is_array($this->m_custom))
				return false;
		}
		else
		{
			$this->m_custom = array();
		}
		if(!isset($this->m_alert)) return false;
		if(!is_string($this->m_alert) && !is_array($this->m_alert))
			return false;
		if(isset($this->m_badge))
		{
			if (!is_int($this->m_badge))
				return false;
		}
		if(isset($this->m_sound))
		{
			if (!is_string($this->m_sound))
				return false;
		}
		if(isset($this->m_loopInterval)) {
			if(!(is_int($this->m_loopInterval) && $this->m_loopInterval > 0)) {
				return false;
			}	
		}
		if(isset($this->m_loopTimes)) {
			if(!(is_int($this->m_loopTimes) && $this->m_loopTimes > 0)) {
				return false;
			}	
		}
		if(isset($this->m_loopInterval) && isset($this->m_loopTimes)) {
			if(($this->m_loopTimes - 1) * $this->m_loopInterval + 1 > self::MAX_LOOP_TASK_DAYS) {
				return false;
			}
		}
	
		return true;
	}
	
	
	private $m_expireTime;
	private $m_sendTime;
	private $m_acceptTimes;
	private $m_custom;
	private $m_raw;
	private $m_alert;
	private $m_badge;
	private $m_sound;
	private $m_category;
	private $m_loopInterval;
	private $m_loopTimes;

	const MAX_LOOP_TASK_DAYS = 15;
}

class ClickAction {
	/**
	 * ��������
	 * @param int $actionType 1��activity��app����2��url��3��Intent
	 */
	public function __construct()
	{
		$this->m_atyAttrIntentFlag = 0;
		$this->m_atyAttrPendingIntentFlag = 0;
		$this->m_confirmOnPackageDownloadUrl = 1;
	}

	public function setActionType($actionType) {
		$this->m_actionType = $actionType;
	}

	public function setUrl($url) {
		$this->m_url = $url;
	}

	public function setComfirmOnUrl($comfirmOnUrl) {
		$this->m_confirmOnUrl = $comfirmOnUrl;
	}

	public function setActivity($activity) {
		$this->m_activity = $activity;
	}

	public function setIntent($intent) {
		$this->m_intent = $intent;
	}

	public function setAtyAttrIntentFlag($atyAttrIntentFlag) {
		$this->m_atyAttrIntentFlag = $atyAttrIntentFlag;
	}

	public function setAtyAttrPendingIntentFlag($atyAttrPendingIntentFlag) {
		$this->m_atyAttrPendingIntentFlag = $atyAttrPendingIntentFlag;
	}

	public function setPackageDownloadUrl($packageDownloadUrl) {
		$this->m_packageDownloadUrl = $packageDownloadUrl;
	}

	public function setConfirmOnPackageDownloadUrl($confirmOnPackageDownloadUrl) {
		$this->m_confirmOnPackageDownloadUrl = $confirmOnPackageDownloadUrl;
	}

	public function setPackageName($packageName) {
		$this->m_packageName = $packageName;
	}

	public function toJson()
	{
		$ret = array();
		$ret['action_type'] = $this->m_actionType;
		$ret['browser'] = array('url'=>$this->m_url, 'confirm'=>$this->m_confirmOnUrl);
		$ret['activity'] = $this->m_activity;
		$ret['intent'] = $this->m_intent;

		$aty_attr = array();
		if (isset($this->m_atyAttrIntentFlag)) {
			$aty_attr['if'] = $this->m_atyAttrIntentFlag;
		}
		if (isset($this->m_atyAttrPendingIntentFlag)) {
			$aty_attr['pf'] = $this->m_atyAttrPendingIntentFlag;
		}
		$ret['aty_attr'] = $aty_attr;

		return $ret;
	}

	public function isValid()
	{
		if (!isset($this->m_actionType)) $this->m_actionType = self::TYPE_ACTIVITY;
		if (!is_int($this->m_actionType)) return false;
		if ($this->m_actionType<self::TYPE_ACTIVITY || $this->m_actionType>self::TYPE_INTENT)
			return false;

		if($this->m_actionType == self::TYPE_ACTIVITY)
		{
			if (!isset($this->m_activity))
			{
				$this->m_activity = "";
				return true;
			}
			if (isset($this->m_atyAttrIntentFlag)) 
			{
				if (!is_int($this->m_atyAttrIntentFlag)) {
					return false;
				}
			}
			if (isset($this->m_atyAttrPendingIntentFlag)) 
			{
				if (!is_int($this->m_atyAttrPendingIntentFlag)) {
					return false;
				}
			}

			if (is_string($this->m_activity) && !empty($this->m_activity))
				return true;
			return false;
		}

		if($this->m_actionType == self::TYPE_URL)
		{
			if (is_string($this->m_url) && !empty($this->m_url) &&
			is_int($this->m_confirmOnUrl) &&
			$this->m_confirmOnUrl>=0 && $this->m_confirmOnUrl<=1
			)
				return true;
			return false;
		}

		if($this->m_actionType == self::TYPE_INTENT)
		{
			if (is_string($this->m_intent) && !empty($this->m_intent))
				return true;
			return false;
		}	
	}

	private $m_actionType;
	private $m_url;
	private $m_confirmOnUrl;
	private $m_activity;
	private $m_intent;
	private $m_atyAttrIntentFlag;
	private $m_atyAttrPendingIntentFlag;
	private $m_packageDownloadUrl;
	private $m_confirmOnPackageDownloadUrl;
	private $m_packageName;
	
	const TYPE_ACTIVITY = 1;
	const TYPE_URL = 2;
	const TYPE_INTENT = 3;
}

class Style {
	public function __construct($builderId, $ring=0, $vibrate=0, $clearable=1, $nId=0, $lights=1, $iconType=0, $styleId=1)
	{
		$this->m_builderId = $builderId;
		$this->m_ring = $ring;
		$this->m_vibrate = $vibrate;
		$this->m_clearable = $clearable;
		$this->m_nId = $nId;
		$this->m_lights = $lights;
		$this->m_iconType = $iconType;
		$this->m_styleId = $styleId;
	}
	public  function __destruct(){}

	public function getBuilderId()
	{
		return $this->m_builderId;
	}

	public function getRing()
	{
		return $this->m_ring;
	}

	public function getVibrate()
	{
		return $this->m_vibrate;
	}

	public function getClearable()
	{
		return $this->m_clearable;
	}
	
	public function getNId()
	{
		return $this->m_nId;
	}

	public function getLights()
	{
		return $this->m_lights;
	}

	public function getIconType()
	{
		return $this->m_iconType;
	}

	public function getStyleId()
	{
		return $this->m_styleId;
	}

	public function setRingRaw($ringRaw)
	{
		return $this->m_ringRaw = $ringRaw;
	}

	public function getRingRaw()
	{
		return $this->m_ringRaw;
	}

	public function setIconRes($iconRes)
	{
		return $this->m_iconRes = $iconRes;
	}

	public function getIconRes() {
		return $this->m_iconRes;
	}

	public function setSmallIcon($smallIcon)
	{
		return $this->m_smallIcon = $smallIcon;
	}	

	public function getSmallIcon()
	{
		return $this->m_smallIcon;
	}

	public function isValid()
	{
		if (!is_int($this->m_builderId) || !is_int($this->m_ring) ||
			!is_int($this->m_vibrate) || !is_int($this->m_clearable) ||
			!is_int($this->m_lights) || !is_int($this->m_iconType) ||
			!is_int($this->m_styleId)
		)
			return false;
		if ($this->m_ring<0 || $this->m_ring>1) return false;
		if ($this->m_vibrate<0 || $this->m_vibrate>1) return false;
		if ($this->m_clearable<0 || $this->m_clearable>1) return false;
		if ($this->m_lights<0 || $this->m_lights>1) return false;
		if ($this->m_iconType<0 || $this->m_iconType>1) return false;
		if ($this->m_styleId<0 || $this->m_styleId>1) return false;

		return true;
	}

	private $m_builderId;
	private $m_ring;
	private $m_vibrate;
	private $m_clearable;
	private $m_nId;
	private $m_ringRaw;
	private $m_lights;
	private $m_iconType;
	private $m_iconRes;
	private $m_styleId;
	private $m_smallIcon;
}

class TimeInterval
{
	public function __construct($startHour, $startMin, $endHour, $endMin)
	{
		$this->m_startHour 	= $startHour;
		$this->m_startMin 	= $startMin;
		$this->m_endHour 	= $endHour;
		$this->m_endMin 	= $endMin;
	}
	public  function __destruct(){}
	public function toArray()
	{
		return  array(
				'start' => array('hour'=>strval($this->m_startHour), 'min'=>strval($this->m_startMin)),
				'end'   => array('hour'=>strval($this->m_endHour), 'min'=>strval($this->m_endMin))
		);
	}
	public function isValid()
	{
		if (!is_int($this->m_startHour) || !is_int($this->m_startMin) ||
		!is_int($this->m_endHour) || !is_int($this->m_endMin)
		)
			return false;

		if ($this->m_startHour>=0 && $this->m_startHour<=23 &&
		$this->m_startMin>=0 && $this->m_startMin<=59 &&
		$this->m_endHour>=0 && $this->m_endHour<=23 &&
		$this->m_endMin>=0 && $this->m_endMin<=59
		)
			return true;
		else
			return false;
	}
	private $m_startHour;
	private $m_startMin;
	private $m_endHour;
	private $m_endMin;
}

class ParamsBase
{

	/**
	 * @var array ��ǰ����Ĳ����б�
	 */
	public $_params = array();

	/**
	 * ���캯��
	*/
	public function __construct($params)
	{
		if(!is_array($params)){
			return array();
		}
		foreach ($params as $key => $value) {
			//����ǷǷ���keyֵ����ʹ�����key
			$this->_params[$key] = $value;
		}
	}

	public function set($k, $v){
		if(!isset($k) || !isset($v)){
			return;
		}
		$this->_params[$k] = $v;
	}

	/**
	 * ����ʵ��������Ĳ�������ǩ��
	 */
	public function generateSign($method, $url, $secret_key)
	{
		//������������������
		$param_str = '';
		$method = strtoupper($method);
		$url_arr = parse_url($url);
		if(isset($url_arr['host']) && isset($url_arr['path'])){
			$url = $url_arr['host'].$url_arr['path'];
		}
		if(!empty($this->_params)){
			ksort($this->_params);
			foreach ($this->_params as $key => $value) {
				$param_str.=$key.'='.$value;
			}
		}
		//print $method.$url.$param_str.$secret_key."\n";
		return md5($method.$url.$param_str.$secret_key);
	}

}

class RequestBase{

	//get����ʽ
	const METHOD_GET  = 'get';
	//post����ʽ
	const METHOD_POST = 'post';

	/**
	 * ����һ��get��post����
	 * @param $url �����url
	 * @param int $method ����ʽ
	 * @param array $params �������
	 * @param array $extra_conf curl����, �߼����������, ��
	 * $extra_conf = array(
	 *    CURLOPT_HEADER => true,
	 *    CURLOPT_RETURNTRANSFER = false
	 * )
	 * @return bool|mixed �ɹ��������ݣ�ʧ�ܷ���false
	 * @throws Exception
	 */
	public static function exec($url,  $params = array(), $method = self::METHOD_GET, $extra_conf = array())
	{
		$params = is_array($params)? http_build_query($params): $params;
		//�����get����ֱ�ӽ���������url����
		if($method == self::METHOD_GET)
		{
			$url .= (strpos($url, '?') === false ? '?':'&') . $params;
		}

		//Ĭ������
		$curl_conf = array(
				CURLOPT_URL => $url,  //����url
				CURLOPT_HEADER => false,  //�����ͷ��Ϣ
				CURLOPT_RETURNTRANSFER => true, //�������������
				CURLOPT_CONNECTTIMEOUT => 3 // ���ӳ�ʱʱ��
		);

		//����post���������Ҫ��������
		if($method == self::METHOD_POST)
		{
			//ʹ��post��ʽ
			$curl_conf[CURLOPT_POST] = true;
			//post����
			$curl_conf[CURLOPT_POSTFIELDS] = $params;
		}

		//��Ӷ��������
		foreach($extra_conf as $k => $v)
		{
			$curl_conf[$k] = $v;
		}

		$data = false;
		try
		{
			//��ʼ��һ��curl���
			$curl_handle = curl_init();
			//����curl��������
			curl_setopt_array($curl_handle, $curl_conf);
			//��������
			$data = curl_exec($curl_handle);
			if($data === false)
			{
				throw new Exception('CURL ERROR: ' . curl_error($curl_handle));
			}
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		curl_close($curl_handle);

		return $data;
	}
}



?>