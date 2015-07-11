<?php

/**
 * �ٶ�������(Push)�Ľӿ�
 *
 * @author lvj
 * @copyright (c) 2014-12-03
 * 
 * 1��LBS���ͽ���androƽ̨
 */
class API_Item_Open_BaiduPush {

    /**
     * ��ѯ�豸��Ӧ�á��û���ٶ�Channel�İ󶨹�ϵ
     * @param type $paramArr
     */
    public static function queryBindList($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //*�û���ʶ
            'channelId' => '', //ͨ��id
            'deviceType' => '', //1��������豸��2��PC�豸��3��Andriod�豸��4��iOS�豸��5��Windows Phone�豸��
            'star' => 0, //��ѯ��ʼҳ�룬Ĭ��Ϊ0
            'limit' => 10, //LIMIT һ�β�ѯ������Ĭ��Ϊ10
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        //�û���ʶ ����
        if (!$userId) {
            return false;
        }

        $channel = new Channel($apiKey, $secretKey);

        //$optional ��ѡ������֧�ֵĿ�ѡ����������
        $optional = array();

        if ($channelId) {
            $optional [Channel::CHANNEL_ID] = $channelId;
        }
        if ($deviceType) {
            $optional [Channel::DEVICE_TYPE] = $deviceType;
        }
        if ($star) {
            $optional [Channel::START] = $star;
        }
        if ($limit) {
            $optional [Channel::LIMIT] = $limit;
        }

        $ret = $channel->queryBindList($userId, $optional);

        return $ret;
    }

    /**
     * �ж��豸��Ӧ�á��û���Channel�İ󶨹�ϵ�Ƿ����
     * @param type $paramArr
     */
    public static function verifyBind($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //*�û���ʶ
            'channelId' => '', //ͨ��id
            'deviceType' => '', //1��������豸��2��PC�豸��3��Andriod�豸��4��iOS�豸��5��Windows Phone�豸��
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        //�û���ʶ ����
        if (!$userId) {
            return false;
        }

        $channel = new Channel($apiKey, $secretKey);

        //$optional ��ѡ������֧�ֵĿ�ѡ����������
        $optional = array();

        if ($channelId) {
            $optional [Channel::CHANNEL_ID] = $channelId;
        }
        if ($deviceType) {
            $optional [Channel::DEVICE_TYPE] = $deviceType;
        }

        $ret = $channel->verifyBind($userId, $optional);

        return $ret;
    }

    /**
     * ����android�豸��Ϣ
     * @param type $paramArr
     */
    public static function pushMessageAndroid($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'title' => '', //���͵ı���
            'description' => '', //���͵�����
            'notification_builder_id' => 0, //Android�ͻ����Զ���֪ͨ��ʽ
            'notification_basic_style' => '', //ֻ��notification_builder_idΪ0ʱ����Ч ���壺0100B=0x04 �񶯣�0010B=0x02 �������0001B=0x01
            'openType' => 2, //1: ��ʾ��Url 2: ��ʾ��Ӧ��
            'url' => '', //ֻ��open_typeΪ1ʱ����Ч��Ϊ��Ҫ�򿪵�url��ַ
            'user_confirm' => 0, //ֻ��open_typeΪ1ʱ����Ч 1: ��ʾ��url��ַʱ��Ҫ�����û����� 0��Ĭ��ֵ����ʾֱ�Ӵ�url��ַ����Ҫ�û�����
            'pushType' => '', //�������� 1��������Ϣ��user 2��������Ϣ��tag 3��ȫ��
            'userId' => '', //
            'tagName' => '', //tag����
            'channelId' => '', //ͨ��id
            'messageType' => '', //0����Ϣ 1��֪ͨ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);


        $push_type = $pushType; //���͵�����Ϣ
        //������͵�����Ϣ����Ҫָ��user
        if ($userId) {
            $optional [Channel::USER_ID] = $userId;
        }
        //�������tag��Ϣ����Ҫָ��tag_name
        if ($tagName) {
            $optional[Channel::TAG_NAME] = $tagName;
        }
        //ָ������android�豸
        $optional[Channel::DEVICE_TYPE] = 3;
        //ָ����Ϣ����Ϊ֪ͨ
        $optional[Channel::MESSAGE_TYPE] = $messageType;
        //֪ͨ���͵����ݱ��밴ָ�����ݷ��ͣ�ʾ�����£�
        $message = '{ 
			"title": "' . ZOL_String::convToU8($title) . '",
			"description": "' . ZOL_String::convToU8($description) . '",
			"notification_basic_style":7,
			"open_type":' . $openType . ',
			"url":"' . $url . '"
 		}';
        
        // TODO��ͬ��key��Ϣ�ᱻ����
        $message_key = md5($message);//"msg_key";

        $ret = $channel->pushMessage($push_type, $message, $message_key, $optional);

        return $ret;
    }

    /**
     * ����ios�豸��Ϣ
     * @param type $paramArr
     */
    public static function pushMessageIos($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'alert' => '', //���͵�����
            'pushType' => '', //�������� 1��������Ϣ��user 2��������Ϣ��tag 3��ȫ��
            'userId' => '', //
            'tagName' => '', //tag����
            'channelId' => '', //ͨ��id
            'messageType' => '', //0����Ϣ 1��֪ͨ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);

        $push_type = $pushType; //���͵�����Ϣ
        //������͵�����Ϣ����Ҫָ��user
        if ($userId) {
            $optional [Channel::USER_ID] = $userId;
        }
        //�������tag��Ϣ����Ҫָ��tag_name
        if ($tagName) {
            $optional[Channel::TAG_NAME] = $tagName;
        }
        //ָ������ios�豸
        $optional[Channel::DEVICE_TYPE] = 4;
        //ָ����Ϣ����Ϊ֪ͨ
        $optional[Channel::MESSAGE_TYPE] = $messageType;
        //���iosӦ�õ�ǰ����״̬Ϊ����״̬��ָ��DEPLOY_STATUSΪ1��Ĭ��������״̬��ֵΪ2.
        //�ɰ汾�����ò�ͬ���������ֲ���״̬����Ȼ֧�֡�
        $optional[Channel::DEPLOY_STATUS] = 1;
        //֪ͨ���͵����ݱ��밴ָ�����ݷ��ͣ�ʾ�����£�
        $message = '{ 
            "aps":{
                "alert":"' . ZOL_String::convToU8($alert) . '",
                "sound":"",
                "badge":0
            }
        }';

        $message_key = "msg_key";
        $ret = $channel->pushMessage($push_type, $message, $message_key, $optional);

        return $ret;
    }

    /**
     * ��ѯ������Ϣ�ĸ���
     * @param type $paramArr
     */
    public static function fetchMessageCount($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);
        $ret = $channel->fetchMessageCount($userId);

        return $ret;
    }

    /**
     * ��ѯ������Ϣ
     * @param type $paramArr
     */
    public static function fetchMessage($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //
            'channelId' => '', //ͨ��id
            'star' => 0, //��ѯ��ʼҳ�룬Ĭ��Ϊ0
            'limit' => 10, //LIMIT һ�β�ѯ������Ĭ��Ϊ10
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);

        //$optional ��ѡ������֧�ֵĿ�ѡ����������
        $optional = array();

        if ($channelId) {
            $optional [Channel::CHANNEL_ID] = $channelId;
        }
        if ($star) {
            $optional [Channel::START] = $star;
        }
        if ($limit) {
            $optional [Channel::LIMIT] = $limit;
        }

        $ret = $channel->fetchMessage($userId, $optional);

        return $ret;
    }

    /**
     * ɾ��������Ϣ
     */
    public static function deleteMessage($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //
            'msgIdArr' => array(), //ɾ������Ϣid
            'channelId' => '', //ͨ��id
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);

        //$optional ��ѡ������֧�ֵĿ�ѡ����������
        $optional = array();

        if ($channelId) {
            $optional [Channel::CHANNEL_ID] = $channelId;
        }

        $msgIds = json_encode($msgIdArr);

        $ret = $channel->deleteMessage($userId, $msgIds, $optional);

        return $ret;
    }

    /**
     * �������������û���ǩ
     * @param type $paramArr
     * @return type
     */
    public static function setTag($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //
            'tagName' => '', //��ǩ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);

        $optional[Channel::USER_ID] = $userId;

        $ret = $channel->setTag($tag_name, $optional);

        return $ret;
    }

    /**
     * App Server��ѯӦ�ñ�ǩ
     * @param type $paramArr
     * @return type
     */
    public static function fetchTag($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'tagName' => '', //��ǩ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);

        $optional[Channel::TAG_NAME] = $tagName;

        $ret = $channel->fetchTag($optional);

        return $ret;
    }

    /**
     * �����ɾ���û���ǩ
     * @param type $paramArr
     */
    public static function deleteTag($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'tagName' => '', //��ǩ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);
        $ret = $channel->deleteTag($tagName);

        return $ret;
    }

    /**
     * App Server��ѯ�û������ı�ǩ�б�
     * @param type $paramArr
     */
    public static function queryUserTags($paramArr) {

        $options = array(
            'apiKey' => '', //apiKey
            'secretKey' => '', //secretKey
            'userId' => '', //�û���ʶ
        );
        if (is_array($paramArr))
            $options = array_merge($options, $paramArr);
        extract($options);

        $channel = new Channel($apiKey, $secretKey);
        $ret = $channel->queryUserTags($userId);

        return $ret;
    }

}

/**
 * 
 * Channel
 * 
 * Channel���ṩ�ٶ�����Ϣͨ�������PHP�汾SDK���û�����ʵ��������࣬�����Լ���apiKey��secretKey������ʹ�ðٶ�����Ϣͨ������
 * 
 * @author �ٶ�����Ϣͨ������@�ٶ��Ƽܹ���
 * 
 * @version 1.0.0.0
 */
class Channel extends BaeBase {
    /**
     * ��ѡ������KEY
     * 
     * �û���ע����
     * �ڵ���Channel���SDK����ʱ�������û��ĸ��Ի���Ҫ��������Ҫ�����ѡ����������ѡ������Ҫ���ڹ�������$optional�д��룬
     * ���ﶨ����$optional������õ�KEY
     */

    /**
     * ��������ʱ��ʱ���
     * 
     * @var int TIMESTAMP
     */
    const TIMESTAMP = 'timestamp';

    /**
     * ������ڵ�ʱ��
     * 
     * �������д��Ĭ��Ϊ10����
     * 
     * @var int EXPIRES
     */
    const EXPIRES = 'expires';

    /**
     * API�汾��
     * 
     * �û�һ�㲻��Ҫ��ע����
     * 
     * @var int VERSION
     */
    const VERSION = 'v';

    /**
     * ��Ϣͨ��ID��
     * 
     * @var int CHANNEL_ID
     */
    const CHANNEL_ID = 'channel_id';

    /**
     * �û�ID������
     * 
     * 0���ٶ��û���ʶ�ԳƼ��ܴ���1���ٶ��û���ʶ����
     * 
     * @var string USER_TYPE
     */
    const USER_TYPE = 'user_type';

    /**
     * �豸����
     * 
     * 1��������豸��2��PC�豸��3��andorid�豸
     * 
     * @var int DEVICE_TYPE
     */
    const DEVICE_TYPE = 'device_type';

    /**
     * �ڼ�ҳ
     * 
     * ������ѯʱ����Ҫָ��start��Ĭ��Ϊ��0ҳ
     * 
     * @var int START
     */
    const START = 'start';

    /**
     * ÿҳ��������¼
     * 
     * ������ѯʱ����Ҫָ��limit��Ĭ��Ϊ100��
     * 
     * @var int LIMIT
     */
    const LIMIT = 'limit';

    /**
     * ��ϢID json�ַ���
     * 
     * @var string MSG_IDS
     */
    const MSG_IDS = 'msg_ids';
    const MSG_KEYS = 'msg_keys';
    const IOS_MESSAGES = 'ios_messages';
    const WP_MESSAGES = 'wp_messages';

    /**
     * ��Ϣ����
     * 
     * ��չ�����ֶΣ�0��Ĭ������
     * 
     * @var int MESSAGE_TYPE
     */
    const MESSAGE_TYPE = 'message_type';

    /**
     * ��Ϣ��ʱʱ��
     * 
     * @var int MESSAGE_EXPIRES
     */
    const MESSAGE_EXPIRES = 'message_expires';

    /**
     * iosӦ�õĲ���״̬��ֻ���iosӦ��
     * 1Ϊ����״̬
     * 2Ϊ����״̬
     * ����ָ����Ĭ��Ϊ����״̬
     */
    const DEPLOY_STATUS = 'deploy_status';

    /**
     * ��Ϣ��ǩ����
     * 
     * @var string TAG_NAME
     */
    const TAG_NAME = 'tag';

    /**
     * ��Ϣ��ǩ����
     * 
     * @var stirng TAG_INFO
     */
    const TAG_INFO = 'info';

    /**
     * ��Ϣ��ǩid
     * 
     * @var int TAG_ID
     */
    const TAG_ID = 'tid';

    /**
     * ���ʱ��
     * 
     * @var int BANNED_TIME
     */
    const BANNED_TIME = 'banned_time';

    /**
     * �ص�����
     * 
     * @var string CALLBACK_DOMAIN
     */
    const CALLBACK_DOMAIN = 'domain';

    /**
     * �ص�uri
     * 
     * @var string CALLBACK_URI
     */
    const CALLBACK_URI = 'uri';

    /**
     * Channel����
     * 
     * �û���ע����
     */
    const APPID = 'appid';
    const ACCESS_TOKEN = 'access_token';
    const API_KEY = 'apikey';
    const SECRET_KEY = 'secret_key';
    const SIGN = 'sign';
    const METHOD = 'method';
    const HOST = 'host';
    const USER_ID = 'user_id';
    const MESSAGES = 'messages';
    const PRODUCT = 'channel';
    const HOST_DEFAULT = 'http://channel.api.duapp.com';
    const HOST_IOS_DEV = 'https://channel.iospush.api.duapp.com';
    const NAME = "name";
    const DESCRIPTION = "description";
    const CERT = "cert";
    const RELEASE_CERT = "release_cert";
    const DEV_CERT = "dev_cert";
    const PUSH_TYPE = 'push_type';

    /**
     * Channel˽�б���
     * 
     * �û���ע����
     */
    protected $_apiKey = NULL;
    protected $_secretKey = NULL;
    protected $_requestId = 0;
    protected $_curlOpts = array(
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 5
    );
    protected $_host = self::HOST_DEFAULT;

    const PUSH_TO_USER = 1;
    const PUSH_TO_TAG = 2;
    const PUSH_TO_ALL = 3;
    const PUSH_TO_DEVICE = 4;

    /**
     * Channel ������
     * 
     * �û���ע����
     */
    const CHANNEL_SDK_SYS = 1;
    const CHANNEL_SDK_INIT_FAIL = 2;
    const CHANNEL_SDK_PARAM = 3;
    const CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR = 4;
    const CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR = 5;

    /**
     * ������������ַ�����ӳ��
     * 
     * �û���ע����
     */
    protected $_arrayErrorMap = array
        (
        '0' => 'php sdk error',
        self::CHANNEL_SDK_SYS => 'php sdk error',
        self::CHANNEL_SDK_INIT_FAIL => 'php sdk init error',
        self::CHANNEL_SDK_PARAM => 'lack param',
        self::CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR => 'http status is error, and the body returned is not a json string',
        self::CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR => 'http status is ok, but the body returned is not a json string',
    );

    /**
     * 2.0��rest API���沿�ַ�����channel_id����url�У����ಿ�ַ��ڰ�����
     * ��¼��Ҫ���ڰ����еķ���
     *
     * �û���ע����
     */
    protected $_method_channel_in_body = array
        (
        'push_msg',
        'set_tag',
        'fetch_tag',
        'delete_tag',
        'query_user_tags'
    );

    /**
     * setApiKey
     * 
     * �û���ע����
     * �����෽���� ����Channel�����apiKey���ԣ�����û��ڴ���Channel����ʱ�Ѿ�ͨ������������apiKey����������ý��Ḳ����ǰ������
     * 
     * @access public
     * @param string $apiKey
     * @return �ɹ���true��ʧ�ܣ�false
     * 
     * @version 
     */
    public function setApiKey($apiKey) {
        $this->_resetErrorStatus();
        try {
            if ($this->_checkString($apiKey, 1, 64)) {
                $this->_apiKey = $apiKey;
            } else {
                throw new ChannelException("invaid apiKey ( ${apiKey} ), which must be a 1 - 64 length string", self::CHANNEL_SDK_INIT_FAIL);
            }
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
        return true;
    }

    /**
     * setSecretKey
     * 
     * �û���ע����
     * �����෽���� ����Channel�����secretKey���ԣ�����û��ڴ���Channel����ʱ�Ѿ�ͨ������������secretKey����������ý��Ḳ����ǰ������
     * 
     * @access public
     * @param string $secretKey
     * @return �ɹ���true��ʧ�ܣ�false
     * 
     * @version 
     */
    public function setSecretKey($secretKey) {
        $this->_resetErrorStatus();
        try {
            if ($this->_checkString($secretKey, 1, 64)) {
                $this->_secretKey = $secretKey;
            } else {
                throw new ChannelException("invaid secretKey ( ${secretKey} ), which must be a 1 - 64 length string", self::CHANNEL_SDK_INIT_FAIL);
            }
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
        return true;
    }

    /**
     * setCurlOpts
     * 
     * �û���ע����
     * �����෽���� ����HTTP������OPTION��ͬPHP curl�������opt����
     * 
     * @access public
     * @param array $arr_curlopt
     * @return �ɹ���true��ʧ�ܣ�false
     * @throws BcmsException
     * 
     * @version 1.2.0
     */
    public function setCurlOpts($arr_curlOpts) {
        $this->_resetErrorStatus();
        try {
            if (is_array($arr_curlOpts)) {
                $this->_curlOpts = $this->_curlOpts + $arr_curlOpts;
            } else {
                throw new ChannelException('invalid param - arr_curlOpts is not an array ['
                . print_r($arr_curlOpts, true) . ']', self::CHANNEL_SDK_INIT_FAIL);
            }
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
        return true;
    }

    /**
     * setHost
     * 
     * �û���ע����
     * �����෽���� ����Channel����ĺ��host���ԣ�����Channel����ʱ��ѡ��Ĭ�ϵ�host�������Ҫ�޸�host�����ø÷����޸ġ�
     * 
     * @access public
     * @param string $host
     * @return �ɹ���true��ʧ�ܣ�false
     * 
     * @version 
     */
    public function setHost($host) {
        $this->_resetErrorStatus();
        try {
            if ($this->_checkString($host, 1, 1024)) {
                $this->_host = $host;
            } else {
                throw new ChannelException("invaid host ( ${host} ), which must be a 1 - 1024 length string", self::CHANNEL_SDK_INIT_FAIL);
            }
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
        return true;
    }

    /**
     * getRequestId
     * 
     * �û���ע����
     * �����෽������ȡ�ϴε��õ�request_id�����SDK���������ֱ�ӷ���0
     * 
     * @access public
     * @return �ϴε��÷��������ص�request_id
     * 
     * @version 1.0.0.0
     */
    public function getRequestId() {
        return $this->_requestId;
    }

    /**
     * queryBindList
     * 
     * �û���ע����
     * 
     * ���������˸���userId[��channelId]��ѯ����Ϣ
     * 
     * @access public
     * @param string $userId �û�ID��
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ����������Channel::CHANNEL_ID��Channel::DEVICE_TYPE��Channel::START��Channel::LIMIT
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function queryBindList($userId, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs [self::METHOD] = 'query_bindlist';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * bindVerify
     * 
     * �û���ע����
     * 
     * У��userId[��channelId]�Ƿ��Ѿ���
     * 
     * @access public
     * @param string $userId �û�ID��
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ����������Channel::CHANNEL_ID��Channel::DEVICE_TYPE
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function verifyBind($userId, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs [self::METHOD] = 'verify_bind';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * fetchMessage
     * 
     * �û���ע����
     * 
     * ����userId[��channelId]��ѯ��Ϣ
     * 
     * @access public
     * @param string $userId �û�ID��
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ����������Channel::CHANNEL_ID��Channel::START��Channel::LIMIT
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function fetchMessage($userId, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs [self::METHOD] = 'fetch_msg';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * fetchMessageCount
     * 
     * �û���ע����
     * 
     * ����userId[��channelId]��ѯ��Ϣ�ĸ���
     * 
     * @access public
     * @param string $userId �û�ID��
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ����������Channel::CHANNEL_ID
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function fetchMessageCount($userId, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs [self::METHOD] = 'fetch_msgcount';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * deleteMessage
     * 
     * �û���ע����
     * 
     * ����userId��msgIds[��channelId]ɾ����Ϣ
     * 
     * @access public
     * @param string $userId �û�ID��
     * @param string $msgIds Ҫɾ����Щ��Ϣ,����������ʽ������Զ���json_encode;
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ����������Channel::CHANNEL_ID
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function deleteMessage($userId, $msgIds, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID, self::MSG_IDS), $tmpArgs);
            $arrArgs [self::METHOD] = 'delete_msg';
            if (is_array($arrArgs [self::MSG_IDS])) {
                $arrArgs [self::MSG_IDS] = json_encode($arrArgs [self::MSG_IDS]);
            }
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * pushMessage
     * �û���ע�� ��
     * ����pushType, messages, message_type, [optinal] ������Ϣ
     * @access public
     * @param int $pushType �������� ȡֵ��Χ 1-3, 1:���ˣ�2��һȺ��tag�� 3��������
     * @param string $messages Ҫ���͵���Ϣ������������ʽ������Զ���json_encode;�����json��ʽ������������$msgKeys��Ӧ����;
     * @param array $optional ��ѡ����,���$pushTypeΪ���ˣ�����ָ��Channel::USER_ID(��:$optional[Channel::USER_ID] = 'xxx'),
     * 		���$pushTypeΪtag������ָ��Channel::TAG,
     * 		������ѡ������Channel::MSG_KEYS ���͵���Ϣkey������������ʽ������Զ���json_encode��������$messages��Ӧ����;
     * 		Channel::MESSAGE_TYPE ��Ϣ���ͣ�ȡֵ��Χ 0-1, 0:��Ϣ��͸������1��֪ͨ��Ĭ��Ϊ0
     * 		����ָ��Channel::MESSAGE_EXPIRES, Channel::MESSAGE_EXPIRES, Channel::CHANNLE_ID��
     *
     * @return �ɹ���PHP���飻ʧ��:false
     * @version 2.0.0.0
     */
    public function pushMessage($pushType, $messages, $msgKeys, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::PUSH_TYPE, self::MESSAGES, self::MSG_KEYS), $tmpArgs);
            $arrArgs[self::METHOD] = 'push_msg';

            switch ($pushType) {
                case self::PUSH_TO_USER:
                    if (!array_key_exists(self::USER_ID, $arrArgs) || empty($arrArgs[self::USER_ID])) {
                        throw new ChannelException("userId should be specified in optional[] when pushType is PUSH_TO_USER", self::CHANNEL_SDK_PARAM);
                    }
                    break;

                case self::PUSH_TO_TAG:
                    if (!array_key_exists(self::TAG_NAME, $arrArgs) || empty($arrArgs[self::TAG_NAME])) {
                        throw new ChannelException("tag should be specified in optional[] when pushType is PUSH_TO_TAG", self::CHANNEL_SDK_PARAM);
                    }
                    break;

                case self::PUSH_TO_ALL:
                    break;

                default:
                    throw new ChannelException("pushType($pushType) must be in range[1,3]", self::CHANNEL_SDK_PARAM);
            }

            $arrArgs[self::PUSH_TYPE] = $pushType;
            if (is_array($arrArgs [self::MESSAGES])) {
                $arrArgs [self::MESSAGES] = json_encode($arrArgs [self::MESSAGES]);
            }
            if (is_array($arrArgs [self::MSG_KEYS])) {
                $arrArgs [self::MSG_KEYS] = json_encode($arrArgs [self::MSG_KEYS]);
            }
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * setTag: ������Ϣ��ǩ
     * 
     * �û���ע: ��
     *
     * @access public
     * @param string $tagName ��ǩ����
     * @param array $optional ��ѡ������֧�ֵĿ�ѡ�������� self::USER_ID�����ָ��user_id���������������tag�İ󶨲���
     * @return �ɹ�: array; ʧ��: false
     * 
     * @version 1.0.0.0
     */
    public function setTag($tagName, $optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::TAG_NAME), $tmpArgs);
            $arrArgs[self::METHOD] = 'set_tag';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * fetchTag: ��ѯ��Ϣ��ǩ��Ϣ
     * 
     * �û���ע: ��
     *
     * @param int $tagId ��ǩID��
     * @param array $optional����ѡ������֧�ֿ�ѡ��������self::TAG_NAME,���ָ��TAG_NAME,���ȡ�ñ�ǩ����Ϣ�������ȡ��Ӧ�õ����б�ǩ��Ϣ
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     */
    public function fetchTag($optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(null, $tmpArgs);
            $arrArgs[self::METHOD] = 'fetch_tag';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * deleteTag: ɾ����Ϣ��ǩ
     * 
     * �û���ע: ��
     *
     * @param int $tagId ��Ϣ��ǩID��
     * @param array $optional
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     */
    public function deleteTag($tagName, $optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::TAG_NAME), $tmpArgs);
            $arrArgs[self::METHOD] = 'delete_tag';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * queryUserTag: ��ѯ�û���صı�ǩ
     * 
     * �û���ע: ��
     *
     * @param string $userId �û�ID��
     * @param array $optional
     * @return �ɹ���PHP���飻ʧ�ܣ�false 
     */
    public function queryUserTags($userId, $optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::USER_ID), $tmpArgs);
            $arrArgs[self::METHOD] = 'query_user_tags';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * initAppIoscert: ��ʼ��Ӧ��ios֤��
     * 
     * �û���ע: ��
     *
     * @param string $name ֤������
     * @param string description ֤������
     * @param string $cert ֤������
     * @param array $optional
     * @return �ɹ���PHP���飻ʧ�ܣ�false  
     */
    public function initAppIoscert($name, $description, $release_cert, $dev_cert, $optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::NAME, self::DESCRIPTION, self::RELEASE_CERT, self::DEV_CERT), $tmpArgs);
            $arrArgs[self::METHOD] = "init_app_ioscert";
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * updateAppIoscert: �޸�ios֤������
     * 
     * �û���ע: ��
     *
     * @param array $optional��ѡ������֧�ֵĿ�ѡ�������� self::NAME, self::DESCRIPTION, self::CERT
     * @return �ɹ���PHP���飻ʧ�ܣ�false   
     */
    public function updateAppIoscert($optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(), $tmpArgs);
            $arrArgs[self::METHOD] = "update_app_ioscert";
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * queryAppIoscert: ��ѯios֤������
     * 
     * �û���ע: ��
     *
     * @param array $optional
     * @return �ɹ���PHP���飻ʧ�ܣ�false   
     */
    public function queryAppIoscert($optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(), $tmpArgs);
            $arrArgs[self::METHOD] = "query_app_ioscert";
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * deleteAppIoscert: ɾ��ios֤������
     * 
     * �û���ע: ��
     *
     * @param array $optional
     * @return �ɹ���PHP���飻ʧ�ܣ�false   
     */
    public function deleteAppIoscert($optional = null) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(), $tmpArgs);
            $arrArgs[self::METHOD] = "delete_app_ioscert";
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * queryDeviceType
     * 
     * �û���ע����
     * 
     * ����channelId��ѯ�豸����
     * 
     * @access public
     * @param string $channelId �û�channel��ID��
     * @return �ɹ���PHP���飻ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    public function queryDeviceType($channelId, $optional = NULL) {
        $this->_resetErrorStatus();
        try {
            $tmpArgs = func_get_args();
            $arrArgs = $this->_mergeArgs(array(self::CHANNEL_ID), $tmpArgs);
            $arrArgs [self::METHOD] = 'query_device_type';
            return $this->_commonProcess($arrArgs);
        } catch (Exception $ex) {
            $this->_channelExceptionHandler($ex);
            return false;
        }
    }

    /**
     * __construct
     * �û���ע����
     * �����췽�����û�����$apiKey��$secretKey���г�ʼ��
     * @access public
     * @param string $apiKey
     * @param string $secretKey
     * @param array $arr_curlOpts ��ѡ����
     * @throws ChannelException ����������׳��쳣���쳣����self::CHANNEL_SDK_INIT_FAIL
     */
    public function __construct($apiKey = NULL, $secretKey = NULL, $arr_curlOpts = array()) {
        if ($this->_checkString($apiKey, 1, 64)) {
            $this->_apiKey = $apiKey;
        } else {
            throw new ChannelException("invalid param - apiKey[$apiKey],"
            . "which must be a 1 - 64 length string", self::CHANNEL_SDK_INIT_FAIL);
        }

        if ($this->_checkString($secretKey, 1, 64)) {
            $this->_secretKey = $secretKey;
        } else {
            throw new ChannelException("invalid param - secretKey[$secretKey],"
            . "which must be a 1 - 64 length string", self::CHANNEL_SDK_INIT_FAIL);
        }

        if (!is_array($arr_curlOpts)) {
            throw new ChannelException('invalid param - arr_curlopt is not an array ['
            . print_r($arr_curlOpts, true) . ']', self::CHANNEL_SDK_INIT_FAIL);
        }
        $this->_curlOpts = $this->_curlOpts + $arr_curlOpts;

        $this->_resetErrorStatus();
    }

    /**
     * _checkString
     *  
     * �û���ע����
     * 
     * �������Ƿ���һ�����ڵ���$min��С�ڵ���$max���ַ���
     * 
     * @access protected
     * @param string $str Ҫ�����ַ���
     * @param int $min �ַ�����С����
     * @param int $max �ַ�����󳤶�
     * @return �ɹ���true��ʧ�ܣ�false
     * 
     * @version 1.0.0.0
     */
    protected function _checkString($str, $min, $max) {
        if (is_string($str) && strlen($str) >= $min && strlen($str) <= $max) {
            return true;
        }
        return false;
    }

    /**
     * _getKey
     * 
     * �û���ע����
     * ��ȡAK/SK/TOKEN/HOST��ͳһ���̺���
     * 
     * @access protected
     * @param array $opt ��������
     * @param string $opt_key ���������key
     * @param string $member �����Ա
     * @param string $g_key ȫ�ֱ���������
     * @param string $env_key ��������������
     * @param int $min �ַ������ֵ
     * @param int $max �ַ����ֵ
     * @throws ChannelException ����������׳�ChannelException�쳣���쳣����Ϊself::CHANNEL_SDK_PARAM
     * 
     * @version 1.0.0.0
     */
    protected function _getKey(&$opt, $opt_key, $member, $g_key, $env_key, $min, $max, $throw = true) {
        $dis = array(
            'access_token' => 'access_token',
        );
        global $$g_key;
        if (isset($opt[$opt_key])) {
            if (!$this->_checkString($opt[$opt_key], $min, $max)) {
                throw new ChannelException('invalid ' . $dis[$opt_key] . ' in $optinal ('
                . $opt[$opt_key] . '), which must be a ' . $min . '-' . $max
                . ' length string', self::CHANNEL_SDK_PARAM);
            }
            return;
        }
        if ($this->_checkString($member, $min, $max)) {
            $opt[$opt_key] = $member;
            return;
        }
        if (isset($$g_key)) {
            if (!$this->_checkString($$g_key, $min, $max)) {
                throw new ChannelException('invalid ' . $g_key . ' in global area ('
                . $$g_key . '), which must be a ' . $min . '-' . $max
                . ' length string', self::CHANNEL_SDK_PARAM);
            }
            $opt[$opt_key] = $$g_key;
            return;
        }

        if (false !== getenv($env_key)) {
            if (!$this->_checkString(getenv($env_key), $min, $max)) {
                throw new ChannelException('invalid ' . $env_key . ' in environment variable ('
                . getenv($env_key) . '), which must be a ' . $min . '-' . $max
                . ' length string', self::CHANNEL_SDK_PARAM);
            }
            $opt[$opt_key] = getenv($env_key);
            return;
        }

        if ($opt_key === self::HOST) {
            $opt[$opt_key] = self::HOST_DEFAULT;
            return;
        }
        if ($throw) {
            throw new ChannelException('no param (' . $dis[$opt_key] . ') was found', self::CHANNEL_SDK_PARAM);
        }
    }

    /**
     * _adjustOpt
     *   
     * �û���ע����
     * 
     * ������������
     * 
     * @access protected
     * @param array $opt ��������
     * @throws ChannelException ����������׳��쳣���쳣��Ϊ self::CHANNEL_SDK_PARAM
     * 
     * @version 1.0.0.0
     */
    protected function _adjustOpt(&$opt) {
        if (!isset($opt) || empty($opt) || !is_array($opt)) {
            throw new ChannelException('no params are set', self::CHANNEL_SDK_PARAM);
        }
        if (!isset($opt[self::TIMESTAMP])) {
            $opt[self::TIMESTAMP] = time();
        }
        $this->_getKey($opt, self::HOST, $this->_host, 'g_host', 'HTTP_BAE_ENV_ADDR_CHANNEL', 1, 1024, false);

        $this->_getKey($opt, self::API_KEY, $this->_apiKey, 'g_apiKey', 'HTTP_BAE_ENV_AK', 1, 64, false);

        if (isset($opt[self::SECRET_KEY])) {
            unset($opt[self::SECRET_KEY]);
        }
    }

    /**
     * _checkParams
     *   
     * �û���ע����
     * 
     * �����������Ƿ�Ϸ�
     * 
     * @access protected
     * @param array $params ��������
     * @throws ChannelException ����������׳��쳣���쳣��Ϊ self::CHANNEL_SDK_PARAM
     * 
     * @version 1.0.0.0
     */
    protected function _checkParams(&$params) {
        if (!is_array($params)) {
            throw new ChannelException('no params', self::CHANNEL_SDK_PARAM);
        }
        foreach ($params as $key => $value) {
            switch ($key) {
                case self::USER_ID:
                    if (!is_string($value)) {
                        throw new ChannelException("USER_ID($value) is not string", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::CHANNEL_ID:
                    if (!is_numeric($value)) {
                        throw new ChannelException("CHANNEL_ID($value) is not numeric", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::DEVICE_TYPE:
                    if (!is_numeric($value) || $value < 0 || $value > 5) {
                        throw new ChannelException("invalid DEVICE_TYPE($value)", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::TAG_NAME:
                    if (!is_string($value) || strlen($value) > 128) {
                        throw new ChannelException("TAG_NAME($value) must be a string and strlen <= 128", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::MESSAGE_TYPE:
                    if (!is_numeric($value) || $value < 0 || $value > 1) {
                        throw new ChannelException("invalid MESSAGE_TYPE($value) must be 0 or 1", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::NAME:
                    if (!is_string($value) || strlen($value) > 128) {
                        throw new ChannelException("IOS_CERT_NAME($value) must be a string and strlen <= 128", self::CHANNEL_SDK_PARAM);
                    }
                    break;
                case self::DESCRIPTION:
                    if (!is_string($value) || strlen($value) > 256) {
                        throw new ChannelException("IOS_CERT_DESCRIPTION($value) must be a string and strlen <= 256", self::CHANNEL_SDK_PARAM);
                    }
                    break;
            }
        }
    }

    /**
     * _genSign
     *
     * �û���ע�� ��
     *
     * ����method, url, �������� ����ǩ��
     */
    protected function _genSign($method, $url, $arrContent) {
        //$secret_key = $this->_secretKey;
        $opt = array();
        $this->_getKey($opt, self::SECRET_KEY, $this->_secretKey, 'g_secretKey', 'HTTP_BAE_ENV_SK', 1, 64, false);
        $secret_key = $opt[self::SECRET_KEY];

        $gather = $method . $url;
        ksort($arrContent);
        foreach ($arrContent as $key => $value) {
            $gather .= $key . '=' . $value;
        }
        $gather .= $secret_key;
        $sign = md5(urlencode($gather));
        return $sign;
    }

    /**
     * _baseControl
     *   
     * �û���ע����
     * 
     * ���罻������
     * 
     * @access protected
     * @param array $opt ��������
     * @throws ChannelException ����������׳��쳣�������Ϊself::CHANNEL_SDK_SYS
     * 
     * @version 1.0.0.0
     */
    protected function _baseControl($opt) {
        $content = '';
        $resource = 'channel';
        if (isset($opt[self::CHANNEL_ID]) && !is_null($opt[self::CHANNEL_ID]) && !in_array($opt[self::METHOD], $this->_method_channel_in_body)) {
            $resource = $opt[self::CHANNEL_ID];
            unset($opt[self::CHANNEL_ID]);
        }
        $host = $opt[self::HOST];
        unset($opt[self::HOST]);

        $url = $host . '/rest/2.0/' . self::PRODUCT . '/';
        $url .= $resource;
        $http_method = 'POST';
        $opt[self::SIGN] = $this->_genSign($http_method, $url, $opt);
        foreach ($opt as $k => $v) {
            $k = urlencode($k);
            $v = urlencode($v);
            $content .= $k . '=' . $v . '&';
        }
        $content = substr($content, 0, strlen($content) - 1);

        $request = new RequestCore($url);
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $headers['User-Agent'] = 'Baidu Channel Service Phpsdk Client';
        foreach ($headers as $headerKey => $headerValue) {
            $headerValue = str_replace(array("\r", "\n"), '', $headerValue);
            if ($headerValue !== '') {
                $request->add_header($headerKey, $headerValue);
            }
        }
        $request->set_method($http_method);
        $request->set_body($content);
        if (is_array($this->_curlOpts)) {
            $request->set_curlopts($this->_curlOpts);
        }
        $request->send_request();
        return new ResponseCore($request->get_response_header(), $request->get_response_body(), $request->get_response_code());
    }

    /**
     * _channelExceptionHandler
     *   
     * �û���ע����
     * 
     * �쳣������
     * 
     * @access protected
     * @param Excetpion $ex �쳣����������Ҫ�����Channel����Ĵ���״̬��Ϣ
     * 
     * @version 1.0.0.0
     */
    protected function _channelExceptionHandler($ex) {
        $tmpCode = $ex->getCode();
        if (0 === $tmpCode) {
            $tmpCode = self::CHANNEL_SDK_SYS;
        }

        $this->errcode = $tmpCode;
        if ($this->errcode >= 30000) {
            $this->errmsg = $ex->getMessage();
        } else {
            $this->errmsg = $this->_arrayErrorMap[$this->errcode] . ',detail info['
                    . $ex->getMessage() . ',break point:' . $ex->getFile() . ':'
                    . $ex->getLine() . '].';
        }
    }

    /**
     * _commonProcess
     *   
     * �û���ע����
     * 
     * ���з�����SDK������ͨ�ù���
     * 
     * @access protected
     * @param array $paramOpt ��������
     * @param array $arrNeed ����Ĳ���KEY
     * @throws ChannelException ����������׳��쳣
     * 
     * @version 1.0.0.0
     */
    protected function _commonProcess($paramOpt = NULL) {
        $this->_adjustOpt($paramOpt);
        $this->_checkParams($paramOpt);
        $ret = $this->_baseControl($paramOpt);
        if (empty($ret)) {
            throw new ChannelException('base control returned empty object', self::CHANNEL_SDK_SYS);
        }
        if ($ret->isOK()) {
            $result = json_decode($ret->body, true);
            if (is_null($result)) {
                throw new ChannelException($ret->body, self::CHANNEL_SDK_HTTP_STATUS_OK_BUT_RESULT_ERROR);
            }
            $this->_requestId = $result['request_id'];
            return $result;
        }
        $result = json_decode($ret->body, true);
        if (is_null($result)) {
            throw new ChannelException('ret body:' . $ret->body, self::CHANNEL_SDK_HTTP_STATUS_ERROR_AND_RESULT_ERROR);
        }
        $this->_requestId = $result['request_id'];
        throw new ChannelException($result['error_msg'], $result['error_code']);
    }

    /**
     * _mergeArgs
     *   
     * �û���ע����
     * 
     * �ϲ�����Ĳ�����һ�������У����ں�������
     * 
     * @access protected
     * @param array $arrNeed ����Ĳ���KEY
     * @param array $tmpArgs ��������
     * @throws ChannelException ����������׳��쳣���쳣��Ϊself::Channel_SDK_PARAM 
     * 
     * @version 1.0.0.0
     */
    protected function _mergeArgs($arrNeed, $tmpArgs) {
        $arrArgs = array();
        if (0 == count($arrNeed) && 0 == count($tmpArgs)) {
            return $arrArgs;
        }
        if (count($tmpArgs) - 1 != count($arrNeed) && count($tmpArgs) != count($arrNeed)) {
            $keys = '(';
            foreach ($arrNeed as $key) {
                $keys .= $key .= ',';
            }
            if ($keys[strlen($keys) - 1] === '' && ',' === $keys[strlen($keys) - 2]) {
                $keys = substr($keys, 0, strlen($keys) - 2);
            }
            $keys .= ')';
            throw new Exception('invalid sdk params, params' . $keys . 'are needed', self::CHANNEL_SDK_PARAM);
        }
        if (empty($tmpArgs[count($tmpArgs) - 1])) {
            $tmpArgs[count($tmpArgs) - 1] = array();
        }
        if (count($tmpArgs) - 1 == count($arrNeed) && !is_array($tmpArgs[count($tmpArgs) - 1])) {
            throw new Exception('invalid sdk params, optional param must be an array', self::CHANNEL_SDK_PARAM);
        }

        $idx = 0;
        if (!is_null($arrNeed)) {
            foreach ($arrNeed as $key) {
                if (!is_integer($tmpArgs[$idx]) && empty($tmpArgs[$idx])) {
                    throw new Exception("lack param (${key})", self::CHANNEL_SDK_PARAM);
                }
                $arrArgs[$key] = $tmpArgs[$idx];
                $idx += 1;
            }
        }
        if (isset($tmpArgs[$idx])) {
            foreach ($tmpArgs[$idx] as $key => $value) {
                if (!array_key_exists($key, $arrArgs) && (is_integer($value) || !empty($value))) {
                    $arrArgs[$key] = $value;
                }
            }
        }
        if (isset($arrArgs[self::CHANNEL_ID])) {
            $arrArgs[self::CHANNEL_ID] = urlencode($arrArgs[self::CHANNEL_ID]);
        }
        return $arrArgs;
    }

    /**
     * _resetErrorStatus
     *   
     * �û���ע����
     * 
     * �ָ�����Ĵ���״̬��ÿ�ε��÷����෽��ʱ���ɷ����෽���Զ����ø÷���
     * 
     * @access protected
     * 
     * @version 1.0.0.0
     */
    protected function _resetErrorStatus() {
        $this->errcode = 0;
        $this->errmsg = $this->_arrayErrorMap[$this->errcode];
        $this->_requestId = 0;
    }

}

class ChannelException extends Exception {
    //do nothing
}

/**
 * ���ļ��ٶ��Ʒ���PHP�汾SDK�Ĺ������罻������
 * 
 * @author �ٶ��ƶ�.����ҵ��
 * @copyright Copyright (c) 2012-2020 �ٶ��������缼��(����)���޹�˾
 * @version 2.0.0
 * @package
 */
class RequestCore {

    /**
     * The URL being requested.
     */
    public $request_url;

    /**
     * The headers being sent in the request.
     */
    public $request_headers;

    /**
     * The body being sent in the request.
     */
    public $request_body;

    /**
     * The response returned by the request.
     */
    public $response;

    /**
     * The headers returned by the request.
     */
    public $response_headers;

    /**
     * The body returned by the request.
     */
    public $response_body;

    /**
     * The HTTP status code returned by the request.
     */
    public $response_code;

    /**
     * Additional response data.
     */
    public $response_info;

    /**
     * The handle for the cURL object.
     */
    public $curl_handle;

    /**
     * The method by which the request is being made.
     */
    public $method;

    /**
     * Stores the proxy settings to use for the request.
     */
    public $proxy = null;

    /**
     * The username to use for the request.
     */
    public $username = null;

    /**
     * The password to use for the request.
     */
    public $password = null;

    /**
     * Custom CURLOPT settings.
     */
    public $curlopts = null;

    /**
     * The state of debug mode.
     */
    public $debug_mode = false;

    /**
     * The default class to use for HTTP Requests (defaults to <RequestCore>).
     */
    public $request_class = 'RequestCore';

    /**
     * The default class to use for HTTP Responses (defaults to <ResponseCore>).
     */
    public $response_class = 'ResponseCore';

    /**
     * Default useragent string to use.
     */
    public $useragent = 'RequestCore/1.4.2';

    /**
     * File to read from while streaming up.
     */
    public $read_file = null;

    /**
     * The resource to read from while streaming up.
     */
    public $read_stream = null;

    /**
     * The size of the stream to read from.
     */
    public $read_stream_size = null;

    /**
     * The length already read from the stream.
     */
    public $read_stream_read = 0;

    /**
     * File to write to while streaming down.
     */
    public $write_file = null;

    /**
     * The resource to write to while streaming down.
     */
    public $write_stream = null;

    /**
     * Stores the intended starting seek position.
     */
    public $seek_position = null;

    /**
     * The user-defined callback function to call when a stream is read from.
     */
    public $registered_streaming_read_callback = null;

    /**
     * The user-defined callback function to call when a stream is written to.
     */
    public $registered_streaming_write_callback = null;

    /* %******************************************************************************************% */

    // CONSTANTS
    /**
     * GET HTTP Method
     */
    const HTTP_GET = 'GET';

    /**
     * POST HTTP Method
     */
    const HTTP_POST = 'POST';

    /**
     * PUT HTTP Method
     */
    const HTTP_PUT = 'PUT';

    /**
     * DELETE HTTP Method
     */
    const HTTP_DELETE = 'DELETE';

    /**
     * HEAD HTTP Method
     */
    const HTTP_HEAD = 'HEAD';

    /* %******************************************************************************************% */

    // CONSTRUCTOR/DESTRUCTOR
    /**
     * Constructs a new instance of this class.
     *
     * @param string $url (Optional) The URL to request or service endpoint to query.
     * @param string $proxy (Optional) The faux-url to use for proxy settings. Takes the following format: `proxy://user:pass@hostname:port`
     * @param array $helpers (Optional) An associative array of classnames to use for request, and response functionality. Gets passed in automatically by the calling class.
     * @return $this A reference to the current instance.
     */
    public function __construct($url = null, $proxy = null, $helpers = null) {
        // Set some default values.
        $this->request_url = $url;
        $this->method = self::HTTP_GET;
        $this->request_headers = array();
        $this->request_body = '';
        // Set a new Request class if one was set.
        if (isset($helpers ['request']) && !empty($helpers ['request'])) {
            $this->request_class = $helpers ['request'];
        }
        // Set a new Request class if one was set.
        if (isset($helpers ['response']) && !empty($helpers ['response'])) {
            $this->response_class = $helpers ['response'];
        }
        if ($proxy) {
            $this->set_proxy($proxy);
        }
        return $this;
    }

    /**
     * Destructs the instance. Closes opened file handles.
     *
     * @return $this A reference to the current instance.
     */
    public function __destruct() {
        if (isset($this->read_file) && isset($this->read_stream)) {
            fclose($this->read_stream);
        }
        if (isset($this->write_file) && isset($this->write_stream)) {
            fclose($this->write_stream);
        }
        return $this;
    }

    /* %******************************************************************************************% */

    // REQUEST METHODS
    /**
     * Sets the credentials to use for authentication.
     *
     * @param string $user (Required) The username to authenticate with.
     * @param string $pass (Required) The password to authenticate with.
     * @return $this A reference to the current instance.
     */
    public function set_credentials($user, $pass) {
        $this->username = $user;
        $this->password = $pass;
        return $this;
    }

    /**
     * Adds a custom HTTP header to the cURL request.
     *
     * @param string $key (Required) The custom HTTP header to set.
     * @param mixed $value (Required) The value to assign to the custom HTTP header.
     * @return $this A reference to the current instance.
     */
    public function add_header($key, $value) {
        $this->request_headers [$key] = $value;
        return $this;
    }

    /**
     * Removes an HTTP header from the cURL request.
     *
     * @param string $key (Required) The custom HTTP header to set.
     * @return $this A reference to the current instance.
     */
    public function remove_header($key) {
        if (isset($this->request_headers [$key])) {
            unset($this->request_headers [$key]);
        }
        return $this;
    }

    /**
     * Set the method type for the request.
     *
     * @param string $method (Required) One of the following constants: <HTTP_GET>, <HTTP_POST>, <HTTP_PUT>, <HTTP_HEAD>, <HTTP_DELETE>.
     * @return $this A reference to the current instance.
     */
    public function set_method($method) {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Sets a custom useragent string for the class.
     *
     * @param string $ua (Required) The useragent string to use.
     * @return $this A reference to the current instance.
     */
    public function set_useragent($ua) {
        $this->useragent = $ua;
        return $this;
    }

    /**
     * Set the body to send in the request.
     *
     * @param string $body (Required) The textual content to send along in the body of the request.
     * @return $this A reference to the current instance.
     */
    public function set_body($body) {
        $this->request_body = $body;
        return $this;
    }

    /**
     * Set the URL to make the request to.
     *
     * @param string $url (Required) The URL to make the request to.
     * @return $this A reference to the current instance.
     */
    public function set_request_url($url) {
        $this->request_url = $url;
        return $this;
    }

    /**
     * Set additional CURLOPT settings. These will merge with the default settings, and override if
     * there is a duplicate.
     *
     * @param array $curlopts (Optional) A set of key-value pairs that set `CURLOPT` options. These will merge with the existing CURLOPTs, and ones passed here will override the defaults. Keys should be the `CURLOPT_*` constants, not strings.
     * @return $this A reference to the current instance.
     */
    public function set_curlopts($curlopts) {
        $this->curlopts = $curlopts;
        return $this;
    }

    /**
     * Sets the length in bytes to read from the stream while streaming up.
     *
     * @param integer $size (Required) The length in bytes to read from the stream.
     * @return $this A reference to the current instance.
     */
    public function set_read_stream_size($size) {
        $this->read_stream_size = $size;
        return $this;
    }

    /**
     * Sets the resource to read from while streaming up. Reads the stream from its current position until
     * EOF or `$size` bytes have been read. If `$size` is not given it will be determined by <php:fstat()> and
     * <php:ftell()>.
     *
     * @param resource $resource (Required) The readable resource to read from.
     * @param integer $size (Optional) The size of the stream to read.
     * @return $this A reference to the current instance.
     */
    public function set_read_stream($resource, $size = null) {
        if (!isset($size) || $size < 0) {
            $stats = fstat($resource);
            if ($stats && $stats ['size'] >= 0) {
                $position = ftell($resource);
                if ($position !== false && $position >= 0) {
                    $size = $stats ['size'] - $position;
                }
            }
        }
        $this->read_stream = $resource;
        return $this->set_read_stream_size($size);
    }

    /**
     * Sets the file to read from while streaming up.
     *
     * @param string $location (Required) The readable location to read from.
     * @return $this A reference to the current instance.
     */
    public function set_read_file($location) {
        $this->read_file = $location;
        $read_file_handle = fopen($location, 'r');
        return $this->set_read_stream($read_file_handle);
    }

    /**
     * Sets the resource to write to while streaming down.
     *
     * @param resource $resource (Required) The writeable resource to write to.
     * @return $this A reference to the current instance.
     */
    public function set_write_stream($resource) {
        $this->write_stream = $resource;
        return $this;
    }

    /**
     * Sets the file to write to while streaming down.
     *
     * @param string $location (Required) The writeable location to write to.
     * @return $this A reference to the current instance.
     */
    public function set_write_file($location) {
        $this->write_file = $location;
        $write_file_handle = fopen($location, 'w');
        return $this->set_write_stream($write_file_handle);
    }

    /**
     * Set the proxy to use for making requests.
     *
     * @param string $proxy (Required) The faux-url to use for proxy settings. Takes the following format: `proxy://user:pass@hostname:port`
     * @return $this A reference to the current instance.
     */
    public function set_proxy($proxy) {
        $proxy = parse_url($proxy);
        $proxy ['user'] = isset($proxy ['user']) ? $proxy ['user'] : null;
        $proxy ['pass'] = isset($proxy ['pass']) ? $proxy ['pass'] : null;
        $proxy ['port'] = isset($proxy ['port']) ? $proxy ['port'] : null;
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * Set the intended starting seek position.
     *
     * @param integer $position (Required) The byte-position of the stream to begin reading from.
     * @return $this A reference to the current instance.
     */
    public function set_seek_position($position) {
        $this->seek_position = isset($position) ? (integer) $position : null;
        return $this;
    }

    /**
     * Register a callback function to execute whenever a data stream is read from using
     * <CFRequest::streaming_read_callback()>.
     *
     * The user-defined callback function should accept three arguments:
     *
     * <ul>
     * <li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
     * <li><code>$file_handle</code> - <code>resource</code> - Required - The file handle resource that represents the file on the local file system.</li>
     * <li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
     * </ul>
     *
     * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
     * <li>The name of a global function to execute, passed as a string.</li>
     * <li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
     * <li>An anonymous function (PHP 5.3+).</li></ul>
     * @return $this A reference to the current instance.
     */
    public function register_streaming_read_callback($callback) {
        $this->registered_streaming_read_callback = $callback;
        return $this;
    }

    /**
     * Register a callback function to execute whenever a data stream is written to using
     * <CFRequest::streaming_write_callback()>.
     *
     * The user-defined callback function should accept two arguments:
     *
     * <ul>
     * <li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
     * <li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
     * </ul>
     *
     * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
     * <li>The name of a global function to execute, passed as a string.</li>
     * <li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
     * <li>An anonymous function (PHP 5.3+).</li></ul>
     * @return $this A reference to the current instance.
     */
    public function register_streaming_write_callback($callback) {
        $this->registered_streaming_write_callback = $callback;
        return $this;
    }

    /* %******************************************************************************************% */

    // PREPARE, SEND, AND PROCESS REQUEST
    /**
     * A callback function that is invoked by cURL for streaming up.
     *
     * @param resource $curl_handle (Required) The cURL handle for the request.
     * @param resource $file_handle (Required) The open file handle resource.
     * @param integer $length (Required) The maximum number of bytes to read.
     * @return binary Binary data from a stream.
     */
    public function streaming_read_callback($curl_handle, $file_handle, $length) {
        // Once we've sent as much as we're supposed to send...
        if ($this->read_stream_read >= $this->read_stream_size) {
            // Send EOF
            return '';
        }
        // If we're at the beginning of an upload and need to seek...
        if ($this->read_stream_read == 0 && isset($this->seek_position) && $this->seek_position !== ftell($this->read_stream)) {
            if (fseek($this->read_stream, $this->seek_position) !== 0) {
                throw new RequestCore_Exception('The stream does not support seeking and is either not at the requested position or the position is unknown.');
            }
        }
        $read = fread($this->read_stream, min($this->read_stream_size - $this->read_stream_read, $length)); // Remaining upload data or cURL's requested chunk size
        $this->read_stream_read += strlen($read);
        $out = $read === false ? '' : $read;
        // Execute callback function
        if ($this->registered_streaming_read_callback) {
            call_user_func($this->registered_streaming_read_callback, $curl_handle, $file_handle, $out);
        }
        return $out;
    }

    /**
     * A callback function that is invoked by cURL for streaming down.
     *
     * @param resource $curl_handle (Required) The cURL handle for the request.
     * @param binary $data (Required) The data to write.
     * @return integer The number of bytes written.
     */
    public function streaming_write_callback($curl_handle, $data) {
        $length = strlen($data);
        $written_total = 0;
        $written_last = 0;
        while ($written_total < $length) {
            $written_last = fwrite($this->write_stream, substr($data, $written_total));
            if ($written_last === false) {
                return $written_total;
            }
            $written_total += $written_last;
        }
        // Execute callback function
        if ($this->registered_streaming_write_callback) {
            call_user_func($this->registered_streaming_write_callback, $curl_handle, $written_total);
        }
        return $written_total;
    }

    /**
     * Prepares and adds the details of the cURL request. This can be passed along to a <php:curl_multi_exec()>
     * function.
     *
     * @return resource The handle for the cURL object.
     */
    public function prep_request() {
        $curl_handle = curl_init();
        // Set default options.
        curl_setopt($curl_handle, CURLOPT_URL, $this->request_url);
        curl_setopt($curl_handle, CURLOPT_FILETIME, true);
        curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, true);
        curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
        curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
        curl_setopt($curl_handle, CURLOPT_HEADER, true);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
        curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
        curl_setopt($curl_handle, CURLOPT_REFERER, $this->request_url);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->useragent);
        curl_setopt($curl_handle, CURLOPT_READFUNCTION, array(
            $this, 'streaming_read_callback'));
        if ($this->debug_mode) {
            curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
        }
        if (!ini_get('safe_mode')) {
            //modify by zhengkan
            //curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        }
        // Enable a proxy connection if requested.
        if ($this->proxy) {
            curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, true);
            $host = $this->proxy ['host'];
            $host .= ($this->proxy ['port']) ? ':' . $this->proxy ['port'] : '';
            curl_setopt($curl_handle, CURLOPT_PROXY, $host);
            if (isset($this->proxy ['user']) && isset($this->proxy ['pass'])) {
                curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $this->proxy ['user'] . ':' . $this->proxy ['pass']);
            }
        }
        // Set credentials for HTTP Basic/Digest Authentication.
        if ($this->username && $this->password) {
            curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }
        // Handle the encoding if we can.
        if (extension_loaded('zlib')) {
            curl_setopt($curl_handle, CURLOPT_ENCODING, '');
        }
        // Process custom headers
        if (isset($this->request_headers) && count($this->request_headers)) {
            $temp_headers = array();
            foreach ($this->request_headers as $k => $v) {
                $temp_headers [] = $k . ': ' . $v;
            }
            curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
        }
        switch ($this->method) {
            case self::HTTP_PUT :
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (isset($this->read_stream)) {
                    if (!isset($this->read_stream_size) || $this->read_stream_size < 0) {
                        throw new RequestCore_Exception('The stream size for the streaming upload cannot be determined.');
                    }
                    curl_setopt($curl_handle, CURLOPT_INFILESIZE, $this->read_stream_size);
                    curl_setopt($curl_handle, CURLOPT_UPLOAD, true);
                } else {
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
                }
                break;
            case self::HTTP_POST :
                curl_setopt($curl_handle, CURLOPT_POST, true);
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
                break;
            case self::HTTP_HEAD :
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, self::HTTP_HEAD);
                curl_setopt($curl_handle, CURLOPT_NOBODY, 1);
                break;
            default : // Assumed GET
                curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $this->method);
                if (isset($this->write_stream)) {
                    curl_setopt($curl_handle, CURLOPT_WRITEFUNCTION, array(
                        $this, 'streaming_write_callback'));
                    curl_setopt($curl_handle, CURLOPT_HEADER, false);
                } else {
                    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
                }
                break;
        }
        // Merge in the CURLOPTs
        if (isset($this->curlopts) && sizeof($this->curlopts) > 0) {
            foreach ($this->curlopts as $k => $v) {
                curl_setopt($curl_handle, $k, $v);
            }
        }
        return $curl_handle;
    }

    /**
     * Take the post-processed cURL data and break it down into useful header/body/info chunks. Uses the
     * data stored in the `curl_handle` and `response` properties unless replacement data is passed in via
     * parameters.
     *
     * @param resource $curl_handle (Optional) The reference to the already executed cURL request.
     * @param string $response (Optional) The actual response content itself that needs to be parsed.
     * @return ResponseCore A <ResponseCore> object containing a parsed HTTP response.
     */
    public function process_response($curl_handle = null, $response = null) {
        // Accept a custom one if it's passed.
        if ($curl_handle && $response) {
            $this->curl_handle = $curl_handle;
            $this->response = $response;
        }
        // As long as this came back as a valid resource...
        if (is_resource($this->curl_handle)) {
            // Determine what's what.
            $header_size = curl_getinfo($this->curl_handle, CURLINFO_HEADER_SIZE);
            $this->response_headers = substr($this->response, 0, $header_size);
            $this->response_body = substr($this->response, $header_size);
            $this->response_code = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
            $this->response_info = curl_getinfo($this->curl_handle);
            // Parse out the headers
            $this->response_headers = explode("\r\n\r\n", trim($this->response_headers));
            $this->response_headers = array_pop($this->response_headers);
            $this->response_headers = explode("\r\n", $this->response_headers);
            array_shift($this->response_headers);
            // Loop through and split up the headers.
            $header_assoc = array();
            foreach ($this->response_headers as $header) {
                $kv = explode(': ', $header);
                //$header_assoc [strtolower ( $kv [0] )] = $kv [1];
                $header_assoc [$kv [0]] = $kv [1];
            }
            // Reset the headers to the appropriate property.
            $this->response_headers = $header_assoc;
            $this->response_headers ['_info'] = $this->response_info;
            $this->response_headers ['_info'] ['method'] = $this->method;
            if ($curl_handle && $response) {
                return new $this->response_class($this->response_headers, $this->response_body, $this->response_code, $this->curl_handle);
            }
        }
        // Return false
        return false;
    }

    /**
     * Sends the request, calling necessary utility functions to update built-in properties.
     *
     * @param boolean $parse (Optional) Whether to parse the response with ResponseCore or not.
     * @return string The resulting unparsed data from the request.
     */
    public function send_request($parse = false) {
        $curl_handle = $this->prep_request();
        $this->response = curl_exec($curl_handle);
        if ($this->response === false) {
            throw new RequestCore_Exception('cURL resource: ' . (string) $curl_handle . '; cURL error: ' . curl_error($curl_handle) . ' (' . curl_errno($curl_handle) . ')');
        }
        $parsed_response = $this->process_response($curl_handle, $this->response);
        curl_close($curl_handle);
        if ($parse) {
            return $parsed_response;
        }
        return $this->response;
    }

    /**
     * Sends the request using <php:curl_multi_exec()>, enabling parallel requests. Uses the "rolling" method.
     *
     * @param array $handles (Required) An indexed array of cURL handles to process simultaneously.
     * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
     * <li><code>callback</code> - <code>string|array</code> - Optional - The string name of a function to pass the response data to. If this is a method, pass an array where the <code>[0]</code> index is the class and the <code>[1]</code> index is the method name.</li>
     * <li><code>limit</code> - <code>integer</code> - Optional - The number of simultaneous requests to make. This can be useful for scaling around slow server responses. Defaults to trusting cURLs judgement as to how many to use.</li></ul>
     * @return array Post-processed cURL responses.
     */
    public function send_multi_request($handles, $opt = null) {
        // Skip everything if there are no handles to process.
        if (count($handles) === 0)
            return array();
        if (!$opt)
            $opt = array();

        // Initialize any missing options
        $limit = isset($opt ['limit']) ? $opt ['limit'] : - 1;
        // Initialize
        $handle_list = $handles;
        $http = new $this->request_class ();
        $multi_handle = curl_multi_init();
        $handles_post = array();
        $added = count($handles);
        $last_handle = null;
        $count = 0;
        $i = 0;
        // Loop through the cURL handles and add as many as it set by the limit parameter.
        while ($i < $added) {
            if ($limit > 0 && $i >= $limit)
                break;
            curl_multi_add_handle($multi_handle, array_shift($handles));
            $i ++;
        }
        do {
            $active = false;
            // Start executing and wait for a response.
            while (($status = curl_multi_exec($multi_handle, $active)) === CURLM_CALL_MULTI_PERFORM) {
                // Start looking for possible responses immediately when we have to add more handles
                if (count($handles) > 0)
                    break;
            }
            // Figure out which requests finished.
            $to_process = array();
            while ($done = curl_multi_info_read($multi_handle)) {
                // Since curl_errno() isn't reliable for handles that were in multirequests, we check the 'result' of the info read, which contains the curl error number, (listed here http://curl.haxx.se/libcurl/c/libcurl-errors.html )
                if ($done ['result'] > 0) {
                    throw new RequestCore_Exception('cURL resource: ' . (string) $done ['handle'] . '; cURL error: ' . curl_error($done ['handle']) . ' (' . $done ['result'] . ')');
                } // Because curl_multi_info_read() might return more than one message about a request, we check to see if this request is already in our array of completed requests
                elseif (!isset($to_process [(int) $done ['handle']])) {
                    $to_process [(int) $done ['handle']] = $done;
                }
            }
            // Actually deal with the request
            foreach ($to_process as $pkey => $done) {
                $response = $http->process_response($done ['handle'], curl_multi_getcontent($done ['handle']));
                $key = array_search($done ['handle'], $handle_list, true);
                $handles_post [$key] = $response;
                if (count($handles) > 0) {
                    curl_multi_add_handle($multi_handle, array_shift($handles));
                }
                curl_multi_remove_handle($multi_handle, $done ['handle']);
                curl_close($done ['handle']);
            }
        } while ($active || count($handles_post) < $added);
        curl_multi_close($multi_handle);
        ksort($handles_post, SORT_NUMERIC);
        return $handles_post;
    }

    /* %******************************************************************************************% */

    // RESPONSE METHODS
    /**
     * Get the HTTP response headers from the request.
     *
     * @param string $header (Optional) A specific header value to return. Defaults to all headers.
     * @return string|array All or selected header values.
     */
    public function get_response_header($header = null) {
        if ($header) {
            //			return $this->response_headers [strtolower ( $header )];
            return $this->response_headers [$header];
        }
        return $this->response_headers;
    }

    /**
     * Get the HTTP response body from the request.
     *
     * @return string The response body.
     */
    public function get_response_body() {
        return $this->response_body;
    }

    /**
     * Get the HTTP response code from the request.
     *
     * @return string The HTTP response code.
     */
    public function get_response_code() {
        return $this->response_code;
    }

}

/**
 * Container for all response-related methods.
 */
class ResponseCore {

    /**
     * Stores the HTTP header information.
     */
    public $header;

    /**
     * Stores the SimpleXML response.
     */
    public $body;

    /**
     * Stores the HTTP response code.
     */
    public $status;

    /**
     * Constructs a new instance of this class.
     *
     * @param array $header (Required) Associative array of HTTP headers (typically returned by <RequestCore::get_response_header()>).
     * @param string $body (Required) XML-formatted response from AWS.
     * @param integer $status (Optional) HTTP response status code from the request.
     * @return object Contains an <php:array> `header` property (HTTP headers as an associative array), a <php:SimpleXMLElement> or <php:string> `body` property, and an <php:integer> `status` code.
     */
    public function __construct($header, $body, $status = null) {
        $this->header = $header;
        $this->body = $body;
        $this->status = $status;
        return $this;
    }

    /**
     * Did we receive the status code we expected?
     *
     * @param integer|array $codes (Optional) The status code(s) to expect. Pass an <php:integer> for a single acceptable value, or an <php:array> of integers for multiple acceptable values.
     * @return boolean Whether we received the expected status code or not.
     */
    public function isOK($codes = array(200, 201, 204, 206)) {
        if (is_array($codes)) {
            return in_array($this->status, $codes);
        }
        return $this->status === $codes;
    }

}

/**
 * Default RequestCore Exception.
 */
class RequestCore_Exception extends Exception {
    
}

/* * ************************************************************************
 *
 * Copyright (c) 2011 Baidu.com, Inc. All Rights Reserved
 *
 * *********************************************************************** */

/**
 * Exception class for the Bae Open API.
 * 
 * @package   BaeOpenAPI
 * @author	  yexw(yexinwei@baidu.com)
 * @version   $Revision: 1.10 $
 * */
class BaeException extends Exception {
    
}

/**
 * Base class for the Bae Open API.
 * 
 * @package   BaeOpenAPI
 * @author	  yexw(yexinwei@baidu.com)
 * @version   $Revision: 1.10 $
 * */
class BaeBase {

    public $errcode;
    public $errmsg;
    protected $_handle = null;

    /**
     * Class constructor
     * 
     */
    public function __construct() {
        //todo
    }

    /**
     * @brief Generates a user-level error/warning/notice message
     * 
     * @param string $error_msg	 The designated error message for this error. 
     * @param string $error_type The designated error type for this error.It  
     * only works with the E_USER family of constants.
     */
    public function error($error_msg, $error_type = E_USER_ERROR) {
        echo '<pre>';
        debug_print_backtrace();
        echo '</pre>';
        trigger_error($error_msg, $error_type);
    }

    /**
     * @brief return the handle
     * 
     */
    public function getHandle() {
        return $this->_handle;
    }

    /**
     * @brief return the error message
     * 
     */
    public function errmsg() {
        return $this->errmsg;
    }

    /**
     * @brief return the error code
     * 
     */
    public function errno() {
        return $this->errcode;
    }

}
