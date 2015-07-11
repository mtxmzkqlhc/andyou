<?php
/**
* �����ƴ洢OSS(Open Storage Services)�Ľӿ�
 *
* @author zhongwt
* @copyright (c) 2013-02-01
*/

//-------------------------------
//����conf.inc.php�ļ�
//-------------------------------
define('OSS_ACCESS_ID', '3xpQ9YcV9JOj6hH2');
define('OSS_ACCESS_KEY', 'zlukx7qDoxfptb40tzzFynmpcLuaZv');
define('ALI_LOG', FALSE);//�Ƿ��¼��־
define('ALI_LOG_PATH','');//�Զ�����־·�������û�����ã���ʹ��ϵͳĬ��·������./logs/
define('ALI_DISPLAY_LOG', FALSE);//�Ƿ���ʾLOG���
define('ALI_LANG', 'zh');//���԰汾����
//<<<<<<<< conf.inc.php�ļ�����

class API_Item_Open_AliOss
{
    private static $conObj = false;

    /**
     * ��ʼ�����Ӷ���
     */
    private static function init() {

        if(!self::$conObj){
            $ossSdkService = new ALIOSS();            
            $ossSdkService->set_debug_mode(FALSE);      //�����Ƿ��curl����ģʽ
            self::$conObj  = $ossSdkService;

        }
        return self::$conObj;
    }
    

	/**
	 * ��ȡbucket�б�
	 */
    public static function getBucketList() {
        self::init();
        $response = self::$conObj->list_bucket();

        #����Ĵ���
        if($response->isOk()){
            $xmlData = (array)simplexml_load_string($response->body);
            $outArr  = array();
            if(isset($xmlData['Buckets']) ){
                foreach($xmlData['Buckets']->Bucket as $o){                    
                    $outArr[] = (array)$o;
                }
            }
            return $outArr;
        }else{
            return false;
        }
        #self::_format($response);
    }

	/**
	 * ����Ŀ¼
	 */
    public static function createDir($paramArr){
		$options = array(
			'bucket'         => '',  #bucket����
			'dir'            => '',     #dir����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$dir)return false;
        self::init();

        $response  = self::$conObj->create_object_dir($bucket,$dir);
        return $response->isOk();
    }

	/**
	 * ��ȡObject�б�
	 */
    public static function getObjectList($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'num'               => 10, #�������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket)return false;
        
        self::init();

        $options = array(
            'delimiter' => '',
            'prefix'    => '',
            'max-keys'  => $num,
        );
        $response = self::$conObj->list_object($bucket,$options);
        #����Ĵ���
        if($response->isOk()){
            $xmlData = (array)simplexml_load_string($response->body);
            $outArr  = array();
            if(isset($xmlData['Contents']) ){
                foreach($xmlData['Contents'] as $o){
                    $o = (array)$o;
                    $outArr[] = array(
                        'name' => $o['Key'],
                        'tm'   => $o['LastModified']
                    );
                }
            }
            return $outArr;
        }else{
            return false;
        }

    }


	/**
	 * ͨ�������ϴ��ļ�
	 */
    public static function uploadByContent($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object����
			'content'           => '', #�ϴ�������
			'header'            =>  array(), #�ļ�������header��Ϣ
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object)return false;

        self::init();

        #�ϴ��Ĳ���
        $upload_file_options = array(
            'content' => $content,
            'length' => strlen($content),
            ALIOSS::OSS_HEADERS => $header,
        );
        #ִ���ϴ�
        $response = self::$conObj->upload_file_by_content($bucket,$object,$upload_file_options);
        return $response->isOk();
        
    }
    


	/**
	 * �ϴ��ļ�
	 */
    public static function uploadFile($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object����
			'filePath'          => '', #�ϴ����ļ�·��
			'header'            =>  array(), #�ļ�������header��Ϣ
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object || !$filePath)return false;

        self::init();

        #�ϴ��Ĳ���
        $upload_file_options = array(
            ALIOSS::OSS_HEADERS => $header,
        );
        #ִ���ϴ�
        $response = self::$conObj->upload_file_by_file($bucket,$object,$filePath,$upload_file_options);
        return $response->isOk();
    }

	/**
	 * �ж�һ��Object�Ƿ����
	 */
    public static function isObjectExist($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object)return false;
        self::init();

        $response = self::$conObj->is_object_exist($bucket,$object);
        return $response->isOk();
    }

	/**
	 * �����ͨ���ʵ�URL
	 */
    public static function getUrl($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object����
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object)return false;

        return " http://{$bucket}.oss.aliyuncs.com/{$object}";
    }

	/**
	 * ����ǩ��url,��Ҫ�û�˽��Ȩ���µķ��ʿ���
	 */
    public static function getSignUrl($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object����
			'timeout'           => 3600, #����ʱ��
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object)return false;
        self::init();

        $response = self::$conObj->get_sign_url($bucket,$object,$timeout);

        return $response;
    }

    /**
	 * ɾ��Object
	 */
    public static function deleteObject($paramArr){
		$options = array(
			'bucket'            => 0,  #bucket����
			'object'            => '', #object���ơ�����ļ���������ʽ
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$bucket || !$object)return false;
        self::init();

        if(is_array($object)){
            $response = self::$conObj->delete_objects($bucket,$objects);
        }else{
            $response = self::$conObj->delete_object($bucket,$object);
        }
        
        return $response->isOk();

    }



}




/**
 * OSS(Open Storage Services) PHP SDK v1.1.5
 */
//����Ĭ��ʱ��
date_default_timezone_set('Asia/Shanghai');

//���API·��
if(!defined('OSS_API_PATH'))define('OSS_API_PATH', dirname(__FILE__));




//-------------------------------
//������԰�
//-------------------------------
/*%*************************************************************************************%*/
//access id & access key ���
define('NOT_SET_OSS_ACCESS_ID', 'δ����OSS�����ACCESS ID');
define('NOT_SET_OSS_ACCESS_KEY', 'δ����OSS�����ACCESS KEY');
define('NOT_SET_OSS_ACCESS_ID_AND_ACCESS_KEY', 'û������ACCESS ID & ACCESS KEY');
define('OSS_ACCESS_ID_OR_ACCESS_KEY_EMPTY', 'ACCESS ID��ACCESS KEYΪ��');

/*%*************************************************************************************%*/
//OSS���԰��Լ��ļ����
define('OSS_LANG_FILE_NOT_EXIST', 'OSS���԰��ļ�������');
define('OSS_CONFIG_FILE_NOT_EXIST',OSS_API_PATH.DIRECTORY_SEPARATOR.'conf.inc.php������');
define('OSS_UTILS_FILE_NOT_EXIST',OSS_API_PATH.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'utils.php������');
define('OSS_CURL_EXTENSION_MUST_BE_LOAD','ϵͳû�а�װCURL��չ');
define('OSS_NO_ANY_EXTENSIONS_LOADED','ϵͳû�а�װ�κ���չ,����ϵͳ����');


/*%*************************************************************************************%*/
//��־�ļ����
define('OSS_WRITE_LOG_TO_FILE_FAILED','��־д��ʧ��,������־�ļ��Ƿ���ڻ�����־�ļ���Ȩ��');
define('OSS_LOG_PATH_NOT_EXIST','��־·��������');

/*%**************************************************************************************%*/
//OSS bucket�������
define('OSS_OPTIONS_MUST_BE_ARRAY', '$option����Ϊ����');
define('OSS_GET_BUCKET_LIST_SUCCESS','��ȡBucket�б�ɹ�!');
define('OSS_GET_BUCKET_LIST_FAILED', '��ȡBucket�б�ʧ��!');
define('OSS_CREATE_BUCKET_SUCCESS', '����Bucket�ɹ�');
define('OSS_CREATE_BUCKET_FAILED', '����Bucketʧ��');
define('OSS_DELETE_BUCKET_SUCCESS', 'ɾ��Bucket�ɹ�');
define('OSS_DELETE_BUCKET_FAILED', 'ɾ��Bucketʧ��');
define('OSS_BUCKET_NAME_INVALID', 'δͨ��Bucket���ƹ���У��');
define('OSS_GET_BUCKET_ACL_SUCCESS','��ȡBucket ACL�ɹ�');
define('OSS_GET_BUCKET_ACL_FAILED','��ȡBucket ACLʧ��');
define('OSS_SET_BUCKET_ACL_SUCCESS','����Bucket ACL�ɹ�');
define('OSS_SET_BUCKET_ACL_FAILED','����Bucket ACLʧ��');
define('OSS_ACL_INVALID','ACL��������Χ,Ŀǰ������(private,public-read,public-read-write����Ȩ��)');
define('OSS_BUCKET_IS_NOT_ALLOWED_EMPTY', 'Bucket������Ϊ��');

/*%****************************************************************************************%*/
//OSS object�������
define('OSS_GET_OBJECT_LIST_SUCCESS','���OBJECT�б�ɹ�');
define('OSS_GET_OBJECT_LIST_FAILED','���OBJECT�б�ʧ��');
define('OSS_CREATE_OBJECT_DIR_SUCCESS','����OBJECTĿ¼�ɹ�');
define('OSS_CREATE_OBJECT_DIR_FAILED','����OBJECTĿ¼ʧ��');
define('OSS_DELETE_OBJECT_SUCCESS','ɾ��OBJECT�ɹ�');
define('OSS_DELETE_OBJECT_FAILED','ɾ��OBJECTʧ��');
define('OSS_UPLOAD_FILE_BY_CONTENT_SUCCESS','ͨ��Http Body�ϴ��ļ��ɹ�');
define('OSS_UPLOAD_FILE_BY_CONTENT_FAILED','ͨ��Http Body�ϴ��ļ�ʧ��');
define('OSS_GET_OBJECT_META_SUCCESS','���OBJECT META�ɹ�');
define('OSS_GET_OBJECT_META_FAILED','���OBJECT METAʧ��');
define('OSS_OBJECT_NAME_INVALID','δͨ��Object���ƹ���У��');
define('OSS_OBJECT_IS_NOT_ALLOWED_EMPTY','Object������Ϊ��');
define('OSS_INVALID_HTTP_BODY_CONTENT','Http Body�����ݷǷ�');
define('OSS_GET_OBJECT_SUCCESS','���Object�ɹ�');
define('OSS_GET_OBJECT_FAILED','���Objectʧ��');
define('OSS_OBJECT_EXIST','Object����');
define('OSS_OBJECT_NOT_EXIST','Object������');
define('OSS_NOT_SET_HTTP_CONTENT','Ϊ����Http Body');
define('OSS_INVALID_CONTENT_LENGTH','�Ƿ���Content-Lengthֵ');
define('OSS_CONTENT_LENGTH_MUST_MORE_THAN_ZERO','Content-Length�������0');
define('OSS_UPLOAD_FILE_NOT_EXIST','�ϴ��ļ�������');
define('OSS_COPY_OBJECT_SUCCESS','����Object�ɹ�');
define('OSS_COPY_OBJECT_FAILED', '����Objectʧ��');
define('OSS_FILE_NOT_EXIST','�ļ�������');
define('OSS_FILE_PATH_IS_NOT_ALLOWED_EMPTY', '�ϴ��ļ�·��Ϊ��');

/*%****************************************************************************************%*/
//OSS object Group�������
define('OSS_CREATE_OBJECT_GROUP_SUCCESS','����Object Group�ɹ�');
define('OSS_CREATE_OBJECT_GROUP_FAILED','����Object Groupʧ��');
define('OSS_GET_OBJECT_GROUP_SUCCESS','��ȡObject Group�ɹ�');
define('OSS_GET_OBJECT_GROUP_FAILED','��ȡObject Groupʧ��');
define('OSS_GET_OBJECT_GROUP_INDEX_SUCCESS','��ȡObject Group Index�ɹ�');
define('OSS_GET_OBJECT_GROUP_INDEX_FAILED','��ȡObject Group Indexʧ��');
define('OSS_GET_OBJECT_GROUP_META_SUCCESS','��ȡObject Group Group Meta�ɹ�');
define('OSS_GET_OBJECT_GROUP_META_FAILED','��ȡObject Group Group Metaʧ��');
define('OSS_DELETE_OBJECT_GROUP_SUCCESS','ɾ��Object Group Group�ɹ�');
define('OSS_DELETE_OBJECT_GROUP_FAILED','ɾ��Object Group Groupʧ��');
define('OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY', 'Object Group������Ϊ��');
define('OSS_OBJECT_ARRAY_IS_EMPTY','����Object Group��Object������Ϊ��');
define('OSS_OBJECT_GROUP_TOO_MANY_OBJECT','ÿ��Object Group������1000��Object');

/*%****************************************************************************************%*/
//OSS Multi-Part Upload���
define('OSS_INITIATE_MULTI_PART_SUCCESS', '��ʼ��Multi-Part Upload�ɹ�');
define('OSS_INITIATE_MULTI_PART_FAILED', '��ʼ��Multi-Part Uploadʧ��');

/*%*******************************************************************************************%*/
//����
define('OSS_INVALID_OPTION_HEADERS', 'OPTIONS��������');
//<<<<<<<< ���԰�����



//����������ƣ��汾�ŵ���Ϣ
define('OSS_NAME','oss-sdk-php');
define('OSS_VERSION','1.1.5');
define('OSS_BUILD','201210121010245');
define('OSS_AUTHOR', 'xiaobing.meng@alibaba-inc.com');

// EXCEPTIONS

/**
 * OSS�쳣�࣬�̳��Ի���
 */
class OSS_Exception extends Exception {}

/*
//���get_loaded_extensions�����Ƿ񱻽��á�������Щ�汾�Ѹú��������ˣ������ȼ��ú����Ƿ���ڡ�
if(function_exists('get_loaded_extensions')){
	//���curl��չ
	$extensions = get_loaded_extensions();
	if($extensions){
		if(!in_array('curl', $extensions)){
			throw new OSS_Exception(OSS_CURL_EXTENSION_MUST_BE_LOAD);
		}
	}else{
		throw new OSS_Exception(OSS_NO_ANY_EXTENSIONS_LOADED);
	}
}else{
	throw new OSS_Exception('Function get_loaded_extensions has been disabled,Pls check php config.');
}
*/

//CLASS
/**
 * OSS������
 * @author xiaobing.meng@alibaba-inc.com
 * @since 2012-05-31
 */
class ALIOSS{
	/*%******************************************************************************************%*/
	// CONSTANTS

	/**
	 * OSS�����ַ
	 */
	const DEFAULT_OSS_HOST = 'oss.aliyuncs.com';

	/**
	 * �������
	 */
	const NAME = OSS_NAME;

	/**
	 * OSS���Build ID
	 */
	const BUILD = OSS_BUILD;

	/**
	 * �汾��
	 */
	const VERSION = OSS_VERSION;

	/**
	 * ����
	 */
	const AUTHOR = OSS_AUTHOR;

	/*%******************************************************************************************%*/
	//OSS �ڲ�����

	const OSS_BUCKET = 'bucket';
	const OSS_OBJECT = 'object';
	const OSS_HEADERS = 'headers';
	const OSS_METHOD = 'method';
	const OSS_QUERY = 'query';
	const OSS_BASENAME = 'basename';
	const OSS_MAX_KEYS = 'max-keys';
	const OSS_UPLOAD_ID = 'uploadId';
	const OSS_MAX_KEYS_VALUE = 100;
	const OSS_MAX_OBJECT_GROUP_VALUE = 1000;
	const OSS_FILE_SLICE_SIZE = 8192;
	const OSS_PREFIX = 'prefix';
	const OSS_DELIMITER = 'delimiter';
	const OSS_MARKER = 'marker';
	const OSS_CONTENT_MD5 = 'Content-Md5';
	const OSS_CONTENT_TYPE = 'Content-Type';
	const OSS_CONTENT_LENGTH = 'Content-Length';
	const OSS_IF_MODIFIED_SINCE = 'If-Modified-Since';
	const OSS_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
	const OSS_IF_MATCH = 'If-Match';
	const OSS_IF_NONE_MATCH = 'If-None-Match';
	const OSS_CACHE_CONTROL = 'Cache-Control';
	const OSS_EXPIRES = 'Expires';
	const OSS_PREAUTH = 'preauth';
	const OSS_CONTENT_COING = 'Content-Coding';
	const OSS_CONTENT_DISPOSTION = 'Content-Disposition';
	const OSS_RANGE = 'Range';
	const OS_CONTENT_RANGE = 'Content-Range';
	const OSS_CONTENT = 'content';
	const OSS_BODY = 'body';
	const OSS_LENGTH = 'length';
	const OSS_HOST = 'Host';
	const OSS_DATE = 'Date';
	const OSS_AUTHORIZATION = 'Authorization';
	const OSS_FILE_DOWNLOAD = 'fileDownload';
	const OSS_FILE_UPLOAD = 'fileUpload';
	const OSS_PART_SIZE = 'partSize';
	const OSS_SEEK_TO = 'seekTo';
	const OSS_SIZE = 'size';
	const OSS_QUERY_STRING = 'query_string';
	const OSS_SUB_RESOURCE = 'sub_resource';
	const OSS_DEFAULT_PREFIX = 'x-oss-';

	/*%******************************************************************************************%*/
	//˽��URL����

	const OSS_URL_ACCESS_KEY_ID = 'OSSAccessKeyId';
	const OSS_URL_EXPIRES = 'Expires';
	const OSS_URL_SIGNATURE = 'Signature';

	/*%******************************************************************************************%*/
	//HTTP����

	const OSS_HTTP_GET = 'GET';
	const OSS_HTTP_PUT = 'PUT';
	const OSS_HTTP_HEAD = 'HEAD';
	const OSS_HTTP_POST = 'POST';
	const OSS_HTTP_DELETE = 'DELETE';


	/*%******************************************************************************************%*/
	//��������

	//x-oss
	const OSS_ACL = 'x-oss-acl';

	//OBJECT GROUP
	const OSS_OBJECT_GROUP = 'x-oss-file-group';

	//Multi Part
	const OSS_MULTI_PART = 'uploads';

	//Multi Delete
	const OSS_MULTI_DELETE = 'delete';

	//OBJECT COPY SOURCE
	const OSS_OBJECT_COPY_SOURCE = 'x-oss-copy-source';

	//private,only owner
	const OSS_ACL_TYPE_PRIVATE = 'private';

	//public reand
	const OSS_ACL_TYPE_PUBLIC_READ = 'public-read';

	//public read write
	const OSS_ACL_TYPE_PUBLIC_READ_WRITE = 'public-read-write';

	//OSS ACL����
	static $OSS_ACL_TYPES = array(
	self::OSS_ACL_TYPE_PRIVATE,
	self::OSS_ACL_TYPE_PUBLIC_READ,
	self::OSS_ACL_TYPE_PUBLIC_READ_WRITE
	);


	/*%******************************************************************************************%*/
	// PROPERTIES

	/**
	 * �Ƿ�ʹ��SSL
	 */
	protected $use_ssl = false;

	/**
	 * �Ƿ���debugģʽ
	 */
	private $debug_mode = true;

	/**
	 * ������Դ���
	 */
	private $max_retries = 3;

	/**
	 * �Ѿ����Դ���
	 */
	private   $redirects = 0;

	/**
	 * �����ַ
	 */
	private $vhost;

	/**
	 * ·�����ַ�ʽ
	 */
	private $enable_domain_style = false;

	/**
	 * ����URL
	 */
	private  $request_url;

	/**
	 * OSS API ACCESS ID
	 */
	private $access_id;

	/**
	 * OSS API ACCESS KEY
	 */
	private $access_key;

	/**
	 * hostname
	 */
	private $hostname;

	/**
	 * port number
	 */
	private $port;

	/*%******************************************************************************************************%*/
	//Constructor

	/**
	 * Ĭ�Ϲ��캯��
	 * @param string $_access_id (Optional)
	 * @param string $access_key (Optional)
	 * @param string $hostname (Optional)
	 * @throws OSS_Exception
	 * @author	xiaobing.meng@alibaba-inc.com
	 * @since	2011-11-08
	 */
	public function __construct($access_id = NULL,$access_key = NULL, $hostname = NULL  ){
		//��֤access_id,access_key
		if(!$access_id && !defined('OSS_ACCESS_ID')){
				throw new OSS_Exception(NOT_SET_OSS_ACCESS_ID);
		}

		if(!$access_key && !defined('OSS_ACCESS_KEY')){
			throw new OSS_Exception(NOT_SET_OSS_ACCESS_KEY);
		}

		if($access_id && $access_key){
			$this->access_id = $access_id;
			$this->access_key = $access_key;
		}elseif (defined('OSS_ACCESS_ID') && defined('OSS_ACCESS_KEY')){
			$this->access_id = OSS_ACCESS_ID;
			$this->access_key = OSS_ACCESS_KEY;
		}else{
			throw new OSS_Exception(NOT_SET_OSS_ACCESS_ID_AND_ACCESS_KEY);
		}

		//У��access_id&access_key
		if(empty($this->access_id) || empty($this->access_key)){
			throw new OSS_Exception(OSS_ACCESS_ID_OR_ACCESS_KEY_EMPTY);
		}

		//У��hostname
		if(NULL === $hostname){
			$this->hostname = self::DEFAULT_OSS_HOST;
		}else{
			$this->hostname = $hostname;
		}
	}


	/*%******************************************************************************************************%*/
	//����

	/**
	 * ����debugģʽ
	 * @param boolean $debug_mode (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-05-29
	 * @return void
	 */
	public function set_debug_mode($debug_mode = true){
		$this->debug_mode = $debug_mode;
	}

	/**
	 * ��������Դ���
	 * @param int $max_retries
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-05-29
	 * @return void
	 */
	public function set_max_retries($max_retries = 3){
		$this->max_retries = $max_retries;
	}

	/**
	 * ��ȡ����Դ���
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-05-29
	 * @return int
	 */
	public function get_max_retries(){
		return $this->max_retries;
	}

	/**
	 * ����host��ַ
	 * @author xiaobing.meng@alibaba-inc.com
	 * @param string $hostname host name
	 * @param int	$port int
	 * @since 2012-06-11
	 * @return void
	 */
	public function set_host_name($hostname,$port = null){
		$this->hostname = $hostname;

		if($port){
			$this->port = $port;
			$this->hostname .= ':'.$port;
		}
	}

	/**
	 * ����vhost��ַ
	 * @author xiaobing.meng@alibaba-inc.com
	 * @param string $vhost vhost
	 * @since 2012-06-11
	 * @return void
	 */
	public function set_vhost($vhost){
		$this->vhost = $vhost;
	}

	/**
	 * ����·����ʽ�����Ϊtrue,������������������bucket.oss.aliyuncs.com
	 * @author xiaobing.meng@alibaba-inc.com
	 * @param boolean $enable_domain_style
	 * @since 2012-06-11
	 * @return void
	 */
	public function set_enable_domain_style($enable_domain_style = true){
		$this->enable_domain_style = $enable_domain_style;
	}


	/*%******************************************************************************************************%*/
	//����

	/**
	 * Authorization
	 * @param array $options (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-05-31
	 */
	public function auth($options){
		//��ʼ��¼LOG
		$msg = "---LOG START---------------------------------------------------------------------------\n";

		//��֤Bucket,list_bucketʱ����Ҫ��֤
		if(!( ('/' == $options[self::OSS_OBJECT]) && ('' == $options[self::OSS_BUCKET]) && ('GET' == $options[self::OSS_METHOD])) && !$this->validate_bucket($options[self::OSS_BUCKET])){
			throw new OSS_Exception('"'.$options[self::OSS_BUCKET].'"'.OSS_BUCKET_NAME_INVALID);
		}

		//��֤Object
		if(isset($options[self::OSS_OBJECT]) && !$this->validate_object($options[self::OSS_OBJECT])){
			throw  new OSS_Exception($options[self::OSS_OBJECT].OSS_OBJECT_NAME_INVALID);
		}

		//Object����ΪUTF-8
		if($this->is_gb2312($options[self::OSS_OBJECT])){
			$options[self::OSS_OBJECT] = iconv('GB2312', "UTF-8",$options[self::OSS_OBJECT]);
		}elseif($this->check_char($options[self::OSS_OBJECT],true)){
			$options[self::OSS_OBJECT] = iconv('GBK', "UTF-8",$options[self::OSS_OBJECT]);
		}


		//��֤ACL
		if(isset($options[self::OSS_HEADERS][self::OSS_ACL]) && !empty($options[self::OSS_HEADERS][self::OSS_ACL])){
			if(!in_array(strtolower($options[self::OSS_HEADERS][self::OSS_ACL]), self::$OSS_ACL_TYPES)){
				throw new OSS_Exception($options[self::OSS_HEADERS][self::OSS_ACL].':'.OSS_ACL_INVALID);
			}
		}


		//����scheme
		$scheme = $this->use_ssl ? 'https://' : 'http://';

		if($this->enable_domain_style){
			$hostname = $this->vhost ? $this->vhost : (($options[self::OSS_BUCKET] =='')?$this->hostname:($options[self::OSS_BUCKET].'.').$this->hostname);
		}else{
			$hostname = (isset($options[self::OSS_BUCKET]) && ''!==$options[self::OSS_BUCKET])?$this->hostname.'/'.$options[self::OSS_BUCKET]:$this->hostname;
		}


		//�������
		$resource = '';
		$sub_resource = '';
		$signable_resource = '';
		$query_string_params = array();
		$signable_query_string_params = array();
		$string_to_sign = '';

		$headers = array (
			self::OSS_CONTENT_MD5 => '',
			self::OSS_CONTENT_TYPE => isset($options[self::OSS_CONTENT_TYPE])?$options[self::OSS_CONTENT_TYPE]:'application/x-www-form-urlencoded',
			self::OSS_DATE => isset($options[self::OSS_DATE])? $options[self::OSS_DATE]: gmdate('D, d M Y H:i:s \G\M\T'),
			self::OSS_HOST => $this->enable_domain_style?$hostname:$this->hostname,
		);

		if(isset ( $options [self::OSS_OBJECT] ) && '/' !== $options [self::OSS_OBJECT]){
			$signable_resource = '/'.str_replace('%2F', '/', rawurlencode($options[self::OSS_OBJECT]));
		}

		if(isset($options[self::OSS_QUERY_STRING])){
			$query_string_params = array_merge($query_string_params,$options[self::OSS_QUERY_STRING]);
		}
		$query_string = $this->to_query_string($query_string_params);

		$signable_list = array(
			'partNumber',
			'uploadId',
		);

		foreach ($signable_list as $item){
			if(isset($options[$item])){
				$signable_query_string_params[$item] = $options[$item];
			}
		}
		$signable_query_string = $this->to_query_string($signable_query_string_params);

		//�ϲ� HTTP headers
		if (isset ( $options [self::OSS_HEADERS] )) {
			$headers = array_merge ( $headers, $options [self::OSS_HEADERS] );
		}

		//��������URL
		$conjunction = '?';

		$non_signable_resource = '';

		if (isset($options[self::OSS_SUB_RESOURCE])){
			$signable_resource .= $conjunction . $options[self::OSS_SUB_RESOURCE];
			$conjunction = '&';
		}

		if($signable_query_string !== ''){
			$signable_query_string = $conjunction.$signable_query_string;
			$conjunction = '&';
		}

		if($query_string !== ''){
			$non_signable_resource .= $conjunction . $query_string;
			$conjunction = '&';
		}

		$this->request_url = 	 $scheme . $hostname . $signable_resource . $signable_query_string . $non_signable_resource;

		$msg .= "--REQUEST URL:----------------------------------------------\n".$this->request_url."\n";

		//��������
		$request = new RequestCore($this->request_url);

		// Streaming uploads
		if (isset($options[self::OSS_FILE_UPLOAD])){
			if (is_resource($options[self::OSS_FILE_UPLOAD])){
				$length = null;

				if (isset($options[self::OSS_CONTENT_LENGTH])){
					$length = $options[self::OSS_CONTENT_LENGTH];
				}elseif (isset($options[self::OSS_SEEK_TO])){

					$stats = fstat($options[self::OSS_FILE_UPLOAD]);

					if ($stats && $stats[self::OSS_SIZE] >= 0){
						$length = $stats[self::OSS_SIZE] - (integer) $options[self::OSS_SEEK_TO];
					}
				}

				$request->set_read_stream($options[self::OSS_FILE_UPLOAD], $length);

				if ($headers[self::OSS_CONTENT_TYPE] === 'application/x-www-form-urlencoded'){
					$headers[self::OSS_CONTENT_TYPE] = 'application/octet-stream';
				}
			}else{
				$request->set_read_file($options[self::OSS_FILE_UPLOAD]);

				$length = $request->read_stream_size;

				if (isset($options[self::OSS_CONTENT_LENGTH])){
					$length = $options[self::OSS_CONTENT_LENGTH];
				}elseif (isset($options[self::OSS_SEEK_TO]) && isset($length)){
					$length -= (integer) $options[self::OSS_SEEK_TO];
				}

				$request->set_read_stream_size($length);

				if (isset($headers[self::OSS_CONTENT_TYPE]) && ($headers[self::OSS_CONTENT_TYPE] === 'application/x-www-form-urlencoded')){
					$extension = explode('.', $options[self::OSS_FILE_UPLOAD]);
					$extension = array_pop($extension);
					$mime_type = MimeTypes::get_mimetype($extension);
					$headers[self::OSS_CONTENT_TYPE] = $mime_type;
				}
			}

			$options[self::OSS_CONTENT_MD5] = '';
		}

		if (isset($options[self::OSS_SEEK_TO])){
			$request->set_seek_position((integer) $options[self::OSS_SEEK_TO]);
		}

		if (isset($options[self::OSS_FILE_DOWNLOAD])){
			if (is_resource($options[self::OSS_FILE_DOWNLOAD])){
				$request->set_write_stream($options[self::OSS_FILE_DOWNLOAD]);
			}else{
				$request->set_write_file($options[self::OSS_FILE_DOWNLOAD]);
			}
		}


		if(isset($options[self::OSS_METHOD])){
			$request->set_method($options[self::OSS_METHOD]);
			$string_to_sign .= $options[self::OSS_METHOD] . "\n";
		}

		if (isset ( $options [self::OSS_CONTENT] )) {
			$request->set_body ( $options [self::OSS_CONTENT] );
			if ($headers[self::OSS_CONTENT_TYPE] === 'application/x-www-form-urlencoded'){
				$headers[self::OSS_CONTENT_TYPE] = 'application/octet-stream';
			}

			$headers[self::OSS_CONTENT_LENGTH] = strlen($options [self::OSS_CONTENT]);
			$headers[self::OSS_CONTENT_MD5] = $this->hex_to_base64(md5($options[self::OSS_CONTENT]));
		}

		uksort($headers, 'strnatcasecmp');

		foreach ( $headers as $header_key => $header_value ) {
			$header_value = str_replace ( array ("\r", "\n" ), '', $header_value );
			if ($header_value !== '') {
				$request->add_header ( $header_key, $header_value );
			}

			if (
				strtolower($header_key) === 'content-md5' ||
				strtolower($header_key) === 'content-type' ||
				strtolower($header_key) === 'date' ||
				(isset($options['self::OSS_PREAUTH']) && (integer) $options['self::OSS_PREAUTH'] > 0)
			){
				$string_to_sign .= $header_value . "\n";
			}elseif (substr(strtolower($header_key), 0, 6) === self::OSS_DEFAULT_PREFIX){
				$string_to_sign .= strtolower($header_key) . ':' . $header_value . "\n";
			}
		}

		$string_to_sign .= '/' . $options[self::OSS_BUCKET];
		$string_to_sign .=  $this->enable_domain_style ? ($options[self::OSS_BUCKET]!=''? ($options[self::OSS_OBJECT]=='/'?'/':'') :'' ) : '';
		$string_to_sign .= rawurldecode($signable_resource) . urldecode($signable_query_string);

		$msg .= "STRING TO SIGN:----------------------------------------------\n".$string_to_sign."\n";

		$signature = base64_encode(hash_hmac('sha1', $string_to_sign, $this->access_key, true));
		$request->add_header('Authorization', 'OSS ' . $this->access_id . ':' . $signature);

		if (isset($options[self::OSS_PREAUTH]) && (integer) $options[self::OSS_PREAUTH] > 0){
			return $this->request_url . $conjunction . self::OSS_URL_ACCESS_KEY_ID.'=' . $this->access_id . '&'.self::OSS_URL_EXPIRES.'=' . $options[self::OSS_PREAUTH] . '&'.self::OSS_URL_SIGNATURE.'=' . rawurlencode($signature);
		}elseif (isset($options[self::OSS_PREAUTH])){
			return $this->request_url;
		}

		if ($this->debug_mode){
			$request->debug_mode = $this->debug_mode;
		}

		$msg .= "REQUEST HEADERS:----------------------------------------------\n".serialize($request->request_headers)."\n";

		$request->send_request();

		$response_header = $request->get_response_header();
		$response_header['x-oss-request-url'] = $this->request_url;
		$response_header['x-oss-redirects'] = $this->redirects;
		$response_header['x-oss-stringtosign'] = $string_to_sign;
		$response_header['x-oss-requestheaders'] = $request->request_headers;

		$msg .= "RESPONSE HEADERS:----------------------------------------------\n".serialize($response_header)."\n";

		$data =  new ResponseCore ( $response_header , $request->get_response_body (), $request->get_response_code () );

		if((integer)$request->get_response_code() === 400 /*Bad Request*/ || (integer)$request->get_response_code() === 500 /*Internal Error*/ || (integer)$request->get_response_code() === 503 /*Service Unavailable*/){
		   if($this->redirects <= $this->max_retries ){
		   		//��������
		   		$delay = (integer) (pow(4, $this->redirects) * 100000);
		   		usleep($delay);
		   		$this->redirects++;
		   		$data = $this->auth($options);
		   }
		}

		$msg .= "RESPONSE DATA:----------------------------------------------\n".serialize($data)."\n";
		$msg .= date('Y-m-d H:i:s').":---LOG END---------------------------------------------------------------------------\n";
		//add log
		$this->log($msg);

		$this->redirects = 0;
		return $data;
	}


	/*%******************************************************************************************************%*/
	//Service Operation

	/**
	 * Get Buket list
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function list_bucket($options = NULL) {
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		$options[self::OSS_BUCKET] = '';
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$response = $this->auth ( $options );

		return $response;
	}


	/*%******************************************************************************************************%*/
	//Bucket Operation

	/**
	 * Create Bucket
	 * @param string $bucket (Required)
	 * @param string $acl (Optional)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_bucket($bucket,$acl = self::OSS_ACL_TYPE_PRIVATE, $options = NULL){
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_HEADERS] = array(self::OSS_ACL => $acl);
		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * Delete Bucket
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_bucket($bucket,$options = NULL){
		//$options
		$this->validate_options($options);

		if (! $options) {
			$options = array ();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = '/';
		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * Get Bucket's ACL
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_bucket_acl($bucket ,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_SUB_RESOURCE] = 'acl';
		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * Set bucket'ACL
	 * @param string $bucket (Required)
	 * @param string $acl  (Required)
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function set_bucket_acl($bucket ,$acl , $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_HEADERS] = array(self::OSS_ACL => $acl);
		$response = $this->auth ( $options );

		return $response;
	}


	/*%******************************************************************************************************%*/
	//Object Operation

	/**
	 * List Object
	 * @param string $bucket (Required)
	 * @param array $options (Optional)
	 * ����options�еĲ�������
	 * $options = array(
	 * 		'max-keys' 	=> max-keys�����޶��˴η���object���������������趨��Ĭ��Ϊ100��max-keysȡֵ���ܴ���100��
	 * 		'prefix'	=> �޶����ص�object key������prefix��Ϊǰ׺��ע��ʹ��prefix��ѯʱ�����ص�key���Ի����prefix��
	 * 		'delimiter' => ��һ�����ڶ�Object���ֽ��з�����ַ����������ְ���ָ����ǰ׺�ҵ�һ�γ���delimiter�ַ�֮���object��Ϊһ��Ԫ��
	 * 		'marker'	=> �û��趨�����marker֮����ĸ����ĵ�һ����ʼ���ء�
	 * )
	 * ���� prefix��marker����ʵ�ַ�ҳ��ʾЧ���������ĳ��ȱ���С��256�ֽڡ�
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function list_object($bucket,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_HEADERS] = array(
		self::OSS_DELIMITER => isset($options[self::OSS_DELIMITER])?$options[self::OSS_DELIMITER]:'/',
		self::OSS_PREFIX => isset($options[self::OSS_PREFIX])?$options[self::OSS_PREFIX]:'',
		self::OSS_MAX_KEYS => isset($options[self::OSS_MAX_KEYS])?$options[self::OSS_MAX_KEYS]:self::OSS_MAX_KEYS_VALUE,
		self::OSS_MARKER => isset($options[self::OSS_MARKER])?$options[self::OSS_MARKER]:'',
		);

		$response = $this->auth ( $options );

		return $response;

	}

	/**
	 * ����Ŀ¼(Ŀ¼���ļ����������ڣ�Ŀ¼�������'/')
	 * @param string $bucket
	 * @param string $object
	 * @param array $options
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_object_dir($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $object.'/';   //����Ŀ¼��Ҫ��'/��β'
		$options[self::OSS_CONTENT_LENGTH] = array(self::OSS_CONTENT_LENGTH => 0);

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ͨ����http body������������ϴ��ļ����ʺϱȽ�С���ļ�
	 * ����apiԼ������Ҫ��http header������content-length�ֶ�
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $content (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function upload_file_by_content($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		//����У��
		$this->validate_content($options);


		$objArr = explode('/', $object);
		$basename = array_pop($objArr);
		$extension = explode ( '.', $basename );
		$extension = array_pop ( $extension );
		$content_type = MimeTypes::get_mimetype(strtolower($extension));

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $object;

		if(!isset($options[self::OSS_LENGTH])){
			$options[self::OSS_CONTENT_LENGTH] = strlen($options[self::OSS_CONTENT]);
		}else{
			$options[self::OSS_CONTENT_LENGTH] = $options[self::OSS_LENGTH];
		}

		if(!isset($options[self::OSS_CONTENT_TYPE]) && isset($content_type) && !empty($content_type) ){
			$options[self::OSS_CONTENT_TYPE] = $content_type;
		}

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * �ϴ��ļ����ʺϱȽϴ���ļ�
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $file (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-02-28
	 * @return ResponseCore
	 */
	public function upload_file_by_file($bucket,$object,$file,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		//file
		$this->is_empty($file, OSS_FILE_PATH_IS_NOT_ALLOWED_EMPTY);

		if($this->chk_chinese($file)){
			$file = iconv('utf-8','gbk',$file);
		}

		$options[self::OSS_FILE_UPLOAD] = $file;

		if(!file_exists($options[self::OSS_FILE_UPLOAD])){
			throw new OSS_Exception($options[self::OSS_FILE_UPLOAD].OSS_FILE_NOT_EXIST);
		}

		$filesize = filesize($options[self::OSS_FILE_UPLOAD]);
		$partsize = 1024 * 1024 ; //Ĭ��Ϊ 1M


		$extension = explode ( '.', $file );
		$extension = array_pop ( $extension );
		$content_type = MimeTypes::get_mimetype(strtolower($extension));

		$options[self::OSS_METHOD] = self::OSS_HTTP_PUT;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_CONTENT_TYPE] = $content_type;
		$options[self::OSS_CONTENT_LENGTH] = $filesize;

		$response = $this->auth($options);
		return $response;
	}


	/**
	 * ����Object
	 * @param string $bucket (Required)
	 * @param string $from_object (Required)
	 * @param string $to_object (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-21
	 * @return ResponseCore
	 */
	public function copy_object($from_bucket,$from_object,$to_bucket,$to_object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//from bucket
		$this->is_empty($from_bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//to bucket
		$this->is_empty($to_bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//from object
		$this->is_empty($from_object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		//to object
		$this->is_empty($to_object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $to_bucket;
		$options[self::OSS_METHOD] = 'PUT';
		$options[self::OSS_OBJECT] = $to_object;
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_COPY_SOURCE => '/'.$from_bucket.'/'.$from_object);

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ���object��meta��Ϣ
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_meta($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'HEAD';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ɾ��object
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_object($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ����ɾ��objects
	 * @param string $bucket(Required)
	 * @param array $objects (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-09
	 * @return ResponseCore
	 */
	public function delete_objects($bucket,$objects,$options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//objects
		if(!is_array($objects) || !$objects){
			throw new OSS_Exception('The ' . __FUNCTION__ . ' method requires the "objects" option to be set as an array.');
		}

		$options[self::OSS_METHOD] = self::OSS_HTTP_POST;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_SUB_RESOURCE] = 'delete';
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';

		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><Delete></Delete>');

		// Quiet mode?
		if (isset($options['quiet'])){
			$quiet = 'false';
			if (is_bool($options['quiet'])) { //Boolean
				$quiet = $options['quiet'] ? 'true' : 'false';
			}elseif (is_string($options['quiet'])){ // String
				$quiet = ($options['quiet'] === 'true') ? 'true' : 'false';
			}

			$xml->addChild('Quiet', $quiet);
		}

		// Add the objects
		foreach ($objects as $object){
			$xobject = $xml->addChild('Object');
			$object = $this->s_replace($object);
			$xobject->addChild('Key', $object);
		}

		$options[self::OSS_CONTENT] = $xml->asXML();

		return $this->auth($options);
	}

	/**
	 * ���Object����
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		if(isset($options[self::OSS_FILE_DOWNLOAD]) && $this->chk_chinese($options[self::OSS_FILE_DOWNLOAD])){
			$options[self::OSS_FILE_DOWNLOAD] = iconv('utf-8','gbk',$options[self::OSS_FILE_DOWNLOAD]);
		}

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object;

		if(isset($options['lastmodified'])){
			$options[self::OSS_HEADERS][self::OSS_IF_MODIFIED_SINCE] = $options['lastmodified'];
            unset($options['lastmodified']);
		}

		if(isset($options['etag'])){
			$options[self::OSS_HEADERS][self::OSS_IF_NONE_MATCH] = $options['etag'];
            unset($options['etag']);
		}

		if(isset($options['range'])){
			$options[self::OSS_HEADERS][self::OSS_RANGE] = 'bytes=' . $options['range'];
            unset($options['range']);
		}

		return $this->auth ( $options );
	}

	/**
	 * ���Object�Ƿ����
	 * @param string $bucket(Required)
	 * @param string $object (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return boolean
	 */
	public function is_object_exist($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object;

		$response = $this->get_object_meta($bucket, $object,$options);

		return $response;
	}


	/*%******************************************************************************************************%*/
	//Multi Part��ز���

	/**
	 * �����ļ����Էֳɶ��ٸ�part���Լ�ÿ��part�ĳ����Լ���ʼλ��
	 * ���������� <upload_part()>�е���
	 *
	 * @param integer $filesize (Required) �ļ���С
	 * @param integer $part_size (Required) part��С,Ĭ��5M
	 * @return array An array ���� key-value ��ֵ��. Key Ϊ `seekTo` �� `length`.
	 */
	public function get_multipart_counts($filesize, $part_size = 5242880 ){
		$i = 0;
		$sizecount = $filesize;
		$values = array();

		if((integer)$part_size <= 5242880){
			$part_size = 5242880;	//5M
		}elseif ((integer)$part_size > 524288000){
			$part_size = 524288000; //500M
		}else{
			$part_size = 52428800; //50M
		}

		while ($sizecount > 0)
		{
			$sizecount -= $part_size;
			$values[] = array(
				self::OSS_SEEK_TO => ($part_size * $i),
				self::OSS_LENGTH => (($sizecount > 0) ? $part_size : ($sizecount + $part_size)),
			);
			$i++;
		}

		return $values;
	}

	/**
	 * ��ʼ��multi-part upload�����ҷ���uploadId
	 * @param string $bucket (Required) Bucket����
	 * @param string $object (Required) Object����
	 * @param array $options (Optional) Key-Value���飬���п��԰������µ�key
	 * @return ResponseCore
	 */
	public function initiate_multipart_upload($bucket,$object,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		// ��������
		$options[self::OSS_METHOD] = self::OSS_HTTP_POST;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_SUB_RESOURCE] = 'uploads';
		$options[self::OSS_CONTENT] = '';
		//$options[self::OSS_CONTENT_LENGTH] = 0;
		$options[self::OSS_HEADERS] = array(self::OSS_CONTENT_TYPE => 'application/octet-stream');

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * �ϴ�part
	 * @param string $bucket (Required) Bucket����
	 * @param string $object (Required) Object����
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value���飬���п��԰������µ�key
	 * @return ResponseCore
	 */
	public function upload_part($bucket, $object, $upload_id, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		if (!isset($options[self::OSS_FILE_UPLOAD]) || !isset($options['partNumber'])){
			throw new OSS_Exception('The `fileUpload` and `partNumber` options are both required in ' . __FUNCTION__ . '().');
		}

		$options[self::OSS_METHOD] = self::OSS_HTTP_PUT;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;

		if(isset($options[self::OSS_LENGTH])){
			$options[self::OSS_CONTENT_LENGTH] =  $options[self::OSS_LENGTH];
		}

		return $this->auth($options);
	}

	/**
	 * list part
	 * @param string $bucket (Required) Bucket����
	 * @param string $object (Required) Object����
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value���飬���п��԰������µ�key
	 * @return ResponseCore
	 */
	public function list_parts($bucket, $object, $upload_id, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;
		$options[self::OSS_QUERY_STRING] = array();

		foreach (array('max-parts', 'part-number-marker') as $param){
			if (isset($options[$param])){
				$options[self::OSS_QUERY_STRING][$param] = $options[$param];
				unset($options[$param]);
			}
		}

		return $this->auth($options);
	}

	/**
	 * ��ֹ�ϴ�mulit-part upload
	 * @param string $bucket (Required) Bucket����
	 * @param string $object (Required) Object����
	 * @param string $upload_id (Required) uploadId
	 * @param array $options (Optional) Key-Value���飬���п��԰������µ�key
	 * @return ResponseCore
	 */
	public function abort_multipart_upload($bucket, $object, $upload_id, $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_DELETE;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;

		return $this->auth($options);
	}

	/**
	 * ���multi-part�ϴ�
	 * @param string $bucket (Required) Bucket����
	 * @param string $object (Required) Object����
	 * @param string $upload_id (Required) uploadId
	 * @param string $parts xml��ʽ�ļ�
	 * @param array $options (Optional) Key-Value���飬���п��԰������µ�key
	 * @return ResponseCore
	 */
	public function complete_multipart_upload($bucket, $object, $upload_id, $parts, $options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_POST;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_UPLOAD_ID] = $upload_id;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';


		if(is_string($parts)){
			$options[self::OSS_CONTENT] = $parts;
		}else if($parts instanceof SimpleXMLElement){
			$options[self::OSS_CONTENT] = $parts->asXML();
		}else if((is_array($parts) || $parts instanceof ResponseCore)){
			$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><CompleteMultipartUpload></CompleteMultipartUpload>');

			if (is_array($parts)){
				//���ɹ�����xml
				foreach ($parts as $node){
					$part = $xml->addChild('Part');
					$part->addChild('PartNumber', $node['PartNumber']);
					$part->addChild('ETag', $node['ETag']);
				}
			}elseif ($parts instanceof ResponseCore){
				foreach ($parts->body->Part as $node){
					$part = $xml->addChild('Part');
					$part->addChild('PartNumber', (string) $node->PartNumber);
					$part->addChild('ETag', (string) $node->ETag);
				}
			}

			$options[self::OSS_CONTENT] = $xml->asXML();
		}

		return $this->auth($options);
	}

	/**
	 * �г�multipart�ϴ�
	 * @param string $bucket (Requeired) bucket
	 * @param array $options (Optional) ��������
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore
	 */
	public function list_multipart_uploads($bucket, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = '/';
		$options[self::OSS_SUB_RESOURCE] = 'uploads';

		foreach (array('key-marker', 'max-uploads', 'upload-id-marker') as $param){
			if (isset($options[$param])){
				$options[self::OSS_QUERY_STRING][$param] = $options[$param];
				unset($options[$param]);
			}
		}

		return $this->auth($options);
	}

	/**
	 * multipart�ϴ�ͳһ��װ���ӳ�ʼ�������multipart���Լ��������ֹ����
	 * @param unknown_type $bucket
	 * @param unknown_type $object
	 * @param unknown_type $options
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore
	 */
	public function create_mpu_object($bucket, $object, $options = null){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		if(isset($options[self::OSS_LENGTH])){
			$options[self::OSS_CONTENT_LENGTH] = $options[self::OSS_LENGTH];
			unset($options[self::OSS_LENGTH]);
		}

		if(isset($options[self::OSS_FILE_UPLOAD])){
			if($this->chk_chinese($options[self::OSS_FILE_UPLOAD])){
				$options[self::OSS_FILE_UPLOAD] = mb_convert_encoding($options[self::OSS_FILE_UPLOAD],'UTF-8');
			}
		}

		if(!isset($options[self::OSS_FILE_UPLOAD])){
			throw new OSS_Exception('The `fileUpload` option is required in ' . __FUNCTION__ . '().');
		}elseif (is_resource($options[self::OSS_FILE_UPLOAD])){
			$upload_position = isset($options[self::OSS_SEEK_TO]) ? (integer) $options[self::OSS_SEEK_TO] : ftell($options[self::OSS_FILE_UPLOAD]);
			$upload_filesize = isset($options[self::OSS_CONTENT_LENGTH]) ? (integer) $options[self::OSS_CONTENT_LENGTH] : null;

			if (!isset($upload_filesize) && $upload_position !== false){
				$stats = fstat($options[self::OSS_FILE_UPLOAD]);

				if ($stats && $stats[self::OSS_SIZE] >= 0){
					$upload_filesize = $stats[self::OSS_SIZE] - $upload_position;
				}
			}
		}else{
			$upload_position = isset($options[self::OSS_SEEK_TO]) ? (integer) $options[self::OSS_SEEK_TO] : 0;

			if (isset($options[self::OSS_CONTENT_TYPE])){
				$upload_filesize = (integer) $options[self::OSS_CONTENT_TYPE];
			}
			else{
				$upload_filesize = filesize($options[self::OSS_FILE_UPLOAD]);

				if ($upload_filesize !== false){
					$upload_filesize -= $upload_position;
				}
			}
		}

		if ($upload_position === false || !isset($upload_filesize) || $upload_filesize === false || $upload_filesize < 0){
			throw new OSS_Exception('The size of `fileUpload` cannot be determined in ' . __FUNCTION__ . '().');
		}

		// ����partSize
		if (isset($options[self::OSS_PART_SIZE])){
			// С��5M
			if ((integer) $options[self::OSS_PART_SIZE] <= 5242880){
				$options[self::OSS_PART_SIZE] = 5242880; // 5 MB
			}
			// ����500M
			elseif ((integer) $options[self::OSS_PART_SIZE] > 524288000){
				$options[self::OSS_PART_SIZE] = 524288000; // 500 MB
			}
		}
		else{
			$options[self::OSS_PART_SIZE] = 52428800; // 50 MB
		}

		// ����ϴ����ļ�С��partSize,��ֱ��ʹ����ͨ��ʽ�ϴ�
		if ($upload_filesize < $options[self::OSS_PART_SIZE] && !isset($options['uploadId'])){
			return $this->upload_file_by_file($bucket, $object, $options[self::OSS_FILE_UPLOAD]);
		}

		// ��ʼ��multipart
		if (isset($options['uploadId'])){
			$upload_id = $options['uploadId'];
		}else{
			//��ʼ��
			$upload = $this->initiate_multipart_upload($bucket, $object);

			if (!$upload->isOK()){
				throw new OSS_Exception('Init multi-part upload failed...');
			}
			$xml = new SimpleXmlIterator($upload->body);
			$uploadId = (string)$xml->UploadId;
		}

		// ��ķ�Ƭ
		$pieces = $this->get_multipart_counts($upload_filesize, (integer) $options[self::OSS_PART_SIZE]);

		$response_upload_part = array();
		foreach ($pieces as $i => $piece){
			$response_upload_part[] = $this->upload_part($bucket, $object, $uploadId, array(
				//'expect' => '100-continue',
				self::OSS_FILE_UPLOAD => $options[self::OSS_FILE_UPLOAD],
				'partNumber' => ($i + 1),
				self::OSS_SEEK_TO => $upload_position + (integer) $piece[self::OSS_SEEK_TO],
				self::OSS_LENGTH => (integer) $piece[self::OSS_LENGTH],
			));
		}

		$upload_parts = array();
		$upload_part_result = true;

		foreach ($response_upload_part as $i=>$response){
			$upload_part_result = $upload_part_result && $response->isOk();
		}

		if(!$upload_part_result){
			throw new OSS_Exception('any part upload failed...,pls try again');
		}

		foreach ($response_upload_part as $i=>$response){
			$upload_parts[] = array(
				'PartNumber' => ($i + 1),
			    'ETag' => (string) $response->header['etag']
			);
		}

		return $this->complete_multipart_upload($bucket, $object, $uploadId, $upload_parts);
	}


	/**
	 * ͨ��Multi-Part��ʽ�ϴ�����Ŀ¼�����е�objectĬ��Ϊ�ļ���
	 * @param string $bucket (Required)
	 * @param string $dir  (Required) ��ѡ
	 * @param boolean $recursive (Optional) �Ƿ�ݹ飬���Ϊtrue����ݹ��ȡ����Ŀ¼��Ĭ��Ϊ���ݹ��ȡ
	 * @param string $exclude ��Ҫ���˵��ļ�
	 * @param array $options (Optional) ��������
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return ResponseCore
	 */
	public function create_mtu_object_by_dir($bucket,$dir,$recursive = false,$exclude = ".|..|.svn",$options = null){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		if($this->chk_chinese($dir)){
			$dir = iconv('utf-8','gbk',$dir);
		}

		//�ж��Ƿ�Ŀ¼
		if(!is_dir($dir)){
			throw new OSS_Exception($dir.' is not a directory...,pls check it');
		}

		$file_list_array = $this->read_dir($dir,$exclude,$recursive);

		if(!$file_list_array){
			throw new OSS_Exception($dir.' is empty...');
		}

		$index = 1;

		foreach ($file_list_array as $item){
			$options = array(
				self::OSS_FILE_UPLOAD => $item['path'],
				self::OSS_PART_SIZE => 5242880,
			);

			echo $index++.". ";
			$response = $this->create_mpu_object($bucket, $item['file'],$options);
			if($response->isOK()){
				echo "Upload file {".$item['path']." } successful..\n";
			}else{
				echo "Upload file {".$item['path']." } failed..\n";
				continue;
			}
		}
	}

	/**
	 * ͨ��multi-part��ʽ�ϴ�Ŀ¼(�Ż���)
	 * $options = array(
	 * 		'bucket' 	=>  (Required)
	 * 		'object' 	=>  (Optional)
	 * 		'directory' =>  (Required)
	 * 		'exclude'	=>  (Optional)
	 * 		'recursive' =>  (Optional)
	 * )
	 */
	public function batch_upload_file($options = NULL){
		if((NULL == $options) || !isset($options['bucket']) || empty($options['bucket']) || !isset($options['directory']) ||empty($options['directory']) ) {
			throw new OSS_Exception('Bad Request',400);
		}

		$bucket = $options['bucket']; unset($options['bucket']);

		$directory = $options['directory']; unset($options['directory']);
		if($this->chk_chinese($directory)){
			$directory = iconv('utf-8','gbk',$directory);
		}

		//�ж��Ƿ�Ŀ¼
		if(!is_dir($directory)){
			throw new OSS_Exception($dir.' is not a directory...,pls check it');
		}

		$object = '';
		if(isset($options['object'])){
			$object = $options['object'];
			unset($options['object']);
		}

		$exclude = '.|..|.svn';
		if (isset($options['exclude']) && !empty($options['exclude'])){
			$exclude = $options['exclude'];
			unset($options['exclude']);
		}

		$recursive = false;
		if(isset($options['recursive']) && !empty($options['recursive'])){
			if(in_array($options['recursive'],array(true,false))){
				$recursive = $options['recursive'];
			}
			unset($options['recursive']);
		}

		//read directory
		$file_list_array = $this->read_dir($directory,$exclude,$recursive);

		if(!$file_list_array){
			throw new OSS_Exception($directory.' is empty...');
		}

		$index = 1;

		foreach ($file_list_array as $item){
			$options = array(
				self::OSS_FILE_UPLOAD => $item['path'],
				self::OSS_PART_SIZE => 5242880,
			);

			echo $index++.". ";
			$response = $this->create_mpu_object($bucket, (!empty($object)?$object.'/':'').$item['file'],$options);
			if($response->isOK()){
				echo "Upload file {".$item['path']." } successful..\n";
			}else{
				echo "Upload file {".$item['path']." } failed..\n";
				continue;
			}
		}
	}


	/*%******************************************************************************************************%*/
	//Object Group��ز���

	/**
	 * ����Object Group
	 * @param string $object_group (Required)  Object Group����
	 * @param string $bucket (Required) Bucket����
	 * @param array $object_arry (Required) object���飬���е�object������ͬһ��bucket��
	 * ����$object ����ĸ�ʽ����:
	 * $object = array(
	 * 		$object1,
	 * 		$object2,
	 * 		...
	 * )
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function create_object_group($bucket,$object_group  ,$object_arry,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'POST';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'txt/xml';  //����Content-Type
		$options[self::OSS_SUB_RESOURCE] = 'group';	   //����?group
		$options[self::OSS_CONTENT] = $this->make_object_group_xml($bucket,$object_arry);   //��ʽ��xml

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ��ȡObject Group
	 * @param string $object_group (Required)
	 * @param string $bucket	(Required)
	 * @param array $options	(Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object_group;
		//$options[self::OSS_OBJECT_GROUP] = true;	   //����?group
		//$options[self::OSS_CONTENT_TYPE] = 'txt/xml';  //����Content-Type
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);  //header�е�x-oss-file-group����Ϊ�գ����򷵻�ֵ����

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ��ȡObject Group ��Object List��Ϣ
	 * @param string $object_group (Required)
	 * @param string $bucket	(Required)
	 * @param array $options	(Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group_index($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_GROUP_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'GET';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';  //����Content-Type
		//$options[self::OSS_OBJECT_GROUP] = true;	   //����?group
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ���object group��meta��Ϣ
	 * @param string $bucket (Required)
	 * @param string $object_group (Required)
	 * @param string $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function get_object_group_meta($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'HEAD';
		$options[self::OSS_OBJECT] = $object_group;
		$options[self::OSS_CONTENT_TYPE] = 'application/xml';  //����Content-Type
		//$options[self::OSS_SUB_RESOURCE] = 'group';	   //����?group
		$options[self::OSS_HEADERS] = array(self::OSS_OBJECT_GROUP => self::OSS_OBJECT_GROUP);

		$response = $this->auth ( $options );

		return $response;
	}

	/**
	 * ɾ��Object Group
	 * @param string $bucket(Required)
	 * @param string $object_group (Required)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-11-14
	 * @return ResponseCore
	 */
	public function delete_object_group($bucket,$object_group,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object group
		$this->is_empty($object_group,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_METHOD] = 'DELETE';
		$options[self::OSS_OBJECT] = $object_group;

		$response = $this->auth ( $options );

		return $response;
	}


	/*%******************************************************************************************************%*/
	//��ǩ����url���

	/**
	 * ��ȡ��ǩ����url
	 * @param string $bucket (Required)
	 * @param string $object (Required)
	 * @param int	 $timeout (Optional)
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-21
	 * @return string
	 */
	public function get_sign_url($bucket,$object,$timeout = 60,$options = NULL){
		//options
		$this->validate_options($options);

		if(!$options){
			$options = array();
		}

		//bucket
		$this->is_empty($bucket,OSS_BUCKET_IS_NOT_ALLOWED_EMPTY);

		//object
		$this->is_empty($object,OSS_OBJECT_IS_NOT_ALLOWED_EMPTY);

		$options[self::OSS_BUCKET] = $bucket;
		$options[self::OSS_OBJECT] = $object;
		$options[self::OSS_METHOD] = self::OSS_HTTP_GET;
		$options[self::OSS_CONTENT_TYPE] = '';

		$timeout = time() + $timeout;
		$options[self::OSS_PREAUTH] = $timeout;
		$options[self::OSS_DATE] = $timeout;

		return $this->auth($options);
	}

	/*%******************************************************************************************************%*/
	//��־���

	/**
	 * ��¼��־
	 * @param string $msg (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function log($msg){
        return false;#
        
		if(defined('ALI_LOG_PATH') ){
			$log_path = ALI_LOG_PATH;
			if(empty($log_path) || !file_exists($log_path)){
				throw new OSS_Exception($log_path.OSS_LOG_PATH_NOT_EXIST);
			}
		}else{
			$log_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
		}

		//�����־Ŀ¼�Ƿ����
		if(!file_exists($log_path)){
			throw new OSS_Exception(OSS_LOG_PATH_NOT_EXIST);
		}

		$log_name = $log_path.'oss_sdk_php_'.date('Y-m-d').'.log';

		if(ALI_DISPLAY_LOG){
			echo $msg."\n<br/>";
		}

		if(ALI_LOG){
			if(!error_log(date('Y-m-d H:i:s')." : ".$msg."\n", 3,$log_name)){
				throw new OSS_Exception(OSS_WRITE_LOG_TO_FILE_FAILED);
			}
		}
	}


	/*%******************************************************************************************************%*/
	//���������

	/**
	 * ����query params
	 * @param array $array ��������
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-04
	 * @return string �������� key1=value1&key2=value2
	 */
	public function to_query_string($options = array()){
		$temp = array();

		foreach ($options as $key => $value){
			if (is_string($key) && !is_array($value)){
				$temp[] = rawurlencode($key) . '=' . rawurlencode($value);
			}
		}

		return implode('&', $temp);
	}

	/**
	 * ת��ʮ�����Ƶ�����Ϊbase64
	 *
	 * @param string $str (Required) Ҫת�����ַ���
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-20
	 * @return string Base64-encoded string.
	 */
	private function hex_to_base64($str){
		$result = '';

		for ($i = 0; $i < strlen($str); $i += 2){
			$result .= chr(hexdec(substr($str, $i, 2)));
		}

		return base64_encode($result);
	}

	private function s_replace($subject){
		$search = array('<','>','&','\'','"');
		$replace = array('&lt;','&gt;','&amp;','&apos;','&quot;');
		return str_replace($search, $replace, $subject);
	}

	/**
	 * ����Ƿ�������
	 * @param string $subject
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-06-06
	 * @return boolean
	 */
	private function chk_chinese($str){
		return preg_match('/[\x80-\xff]./', $str);
	}

	/**
	 * ����Ƿ�GB2312����
	 * @param string $str
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-20
	 * @return boolean false UTF-8����  TRUE GB2312����
	 */
	function is_gb2312($str)  {
	    for($i=0; $i<strlen($str); $i++) {
	        $v = ord( $str[$i] );
	        if( $v > 127) {
	            if( ($v >= 228) && ($v <= 233) ){
	                if( ($i+2) >= (strlen($str) - 1)) return true;  // not enough characters
	                $v1 = ord( $str[$i+1] );
	                $v2 = ord( $str[$i+2] );
	                if( ($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191) )
	                    return false;   //UTF-8����
	                else
	                    return true;    //GB����
	            }
	        }
	    }
	}


	/**
	 * ����Ƿ�GBK����
	 * @param string $str
	 * @param boolean $gbk
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-06-04
	 * @return boolean
	 */
	private function check_char($str,$gbk = true){
	    for($i=0; $i<strlen($str); $i++) {
	        $v = ord( $str[$i] );
	        if( $v > 127){
	            if( ($v >= 228) && ($v <= 233) ){
	                 if(($i+2)>= (strlen($str)-1)) return $gbk?true:FALSE;  // not enough characters
	                 $v1 = ord( $str[$i+1] ); $v2 = ord( $str[$i+2] );
	                 if($gbk){
	                      return (($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191))?FALSE:TRUE;//GBK
	                 }else{
	                      return (($v1 >= 128) && ($v1 <=191) && ($v2 >=128) && ($v2 <= 191))?TRUE:FALSE;
	                 }
	            }
	        }
	    }
	   return $gbk?TRUE:FALSE;
	}


	/**
	 * ��ȡĿ¼
	 * @param string $dir (Required) Ŀ¼��
	 * @param boolean $recursive (Optional) �Ƿ�ݹ飬Ĭ��Ϊfalse
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2012-03-05
	 * @return array
	 */
	private  function read_dir($dir,$exclude = ".|..|.svn",$recursive = false){
		static $file_list_array = array();

		$exclude_array = explode("|", $exclude);
		//��ȡĿ¼
		if($handle = opendir($dir)){
			while ( false !== ($file = readdir($handle))){
				if(!in_array(strtolower($file),$exclude_array)){
					$new_file = $dir.'/'.$file;
					if(is_dir($new_file) && $recursive){
						$this->read_dir($new_file,$exclude,$recursive);
					}else{
						$file_list_array[] = array(
							'path' => $new_file,
							'file' => $file,
						);
					}
				}
			}

			closedir($handle);
		}

		return $file_list_array;
	}


	/**
	 * ת��object����Ϊ�̶���xml��ʽ
	 * @param string $bucket (Required)
	 * @param array $object_array (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return string
	 */
	private function make_object_group_xml($bucket,$object_array){
		$xml = '';
		$xml .= '<CreateFileGroup>';

		if($object_array){
			if(count($object_array) > self::OSS_MAX_OBJECT_GROUP_VALUE){
				throw new OSS_Exception(OSS_OBJECT_GROUP_TOO_MANY_OBJECT, '-401');
			}
			$index = 1;
			foreach ($object_array as $key=>$value){
				$object_meta = (array)$this->get_object_meta($bucket, $value);
				if(isset($object_meta) && isset($object_meta['status']) && isset($object_meta['header']) && isset($object_meta['header']['etag']) && $object_meta['status'] == 200){
					$xml .= '<Part>';
					$xml .= '<PartNumber>'.intval($index).'</PartNumber>';
					$xml .= '<PartName>'.$value.'</PartName>';
					$xml .= '<ETag>'.$object_meta['header']['etag'].'</ETag>';
					$xml .= '</Part>';

					$index++;
				}
			}
		}else{
			throw new OSS_Exception(OSS_OBJECT_ARRAY_IS_EMPTY, '-400');
		}

		$xml .= '</CreateFileGroup>';

		return $xml;
	}

	/**
	 * ����bucket�����Ƿ�Ϸ�
	 * bucket�������淶��
	 * 1. ֻ�ܰ���Сд��ĸ������
	 * 2. ������Сд��ĸ�������ֿ�ͷ
	 * 3. ���ȱ�����3-63�ֽ�֮��
	 * @param string $bucket (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean
	 */
	private function validate_bucket($bucket){
		$pattern = '/^[a-z0-9][a-z0-9]{2,62}$/';
		if (! preg_match ( $pattern, $bucket )) {
			return false;
		}
		return true;
	}

	/**
	 * ����object�����Ƿ�Ϸ�
	 * object�����淶:
	 * 1. ���򳤶ȱ�����1-1023�ֽ�֮��
	 * 2. ʹ��UTF-8����
	 * @param string $object (Required)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean
	 */
	private function validate_object($object){
		$pattern = '/^.{1,1023}$/';
		if (empty ( $object ) || ! preg_match ( $pattern, $object )) {
			return false;
		}
		return true;
	}

	/**
	 * ����$options
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return boolean
	 */
	private function validate_options($options){
		//$options
		if ($options != NULL && ! is_array ( $options )) {
			throw new OSS_Exception ($options.':'.OSS_OPTIONS_MUST_BE_ARRAY);
		}
	}

	/**
	 * ����ϴ��ļ�������
	 * @param array $options (Optional)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since  2011-12-27
	 * @return string
	 */
	private function validate_content($options){
		if(isset($options[self::OSS_CONTENT])){
			if($options[self::OSS_CONTENT] == '' || !is_string($options[self::OSS_CONTENT])){
				throw new OSS_Exception(OSS_INVALID_HTTP_BODY_CONTENT,'-600');
			}
		}else{
			throw new OSS_Exception(OSS_NOT_SET_HTTP_CONTENT, '-601');
		}
	}

	/**
	 * ��֤�ļ�����
	 * @param array $options (Optional)
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function validate_content_length($options){
		if(isset($options[self::OSS_LENGTH]) && is_numeric($options[self::OSS_LENGTH])){
			if( ! $options[self::OSS_LENGTH] > 0){
				throw new OSS_Exception(OSS_CONTENT_LENGTH_MUST_MORE_THAN_ZERO, '-602');
			}
		}else{
			throw new OSS_Exception(OSS_INVALID_CONTENT_LENGTH, '-602');
		}
	}

	/**
	 * У��BUCKET/OBJECT/OBJECT GROUP�Ƿ�Ϊ��
	 * @param  string $name (Required)
	 * @param  string $errMsg (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @since 2011-12-27
	 * @return void
	 */
	private function is_empty($name,$errMsg){
		if(empty($name)){
			throw new OSS_Exception($errMsg);
		}
	}

	/**
	 * ����http header
	 * @param string $key (Required)
	 * @param string $value (Required)
	 * @param array $options (Required)
	 * @throws OSS_Exception
	 * @author xiaobing.meng@alibaba-inc.com
	 * @return void
	 */
	private static function set_options_header($key, $value, &$options) {
		if (isset ( $options [self::OSS_HEADERS] )) {
			if (! is_array ( $options [self::OSS_HEADERS] )) {
				throw new OSS_Exception(OSS_INVALID_OPTION_HEADERS, '-600');
			}
		} else {
			$options [self::OSS_HEADERS] = array ();
		}

		$options [self::OSS_HEADERS] [$key] = $value;
	}
}



/**
 * Handles all HTTP requests using cURL and manages the responses.
 *
 * @version 2011.06.07
 * @copyright 2006-2011 Ryan Parman
 * @copyright 2006-2010 Foleeo Inc.
 * @copyright 2010-2011 Amazon.com, Inc. or its affiliates.
 * @copyright 2008-2011 Contributors
 * @license http://opensource.org/licenses/bsd-license.php Simplified BSD License
 */
class RequestCore
{
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
	public $useragent = 'RequestCore/1.4.3';

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
	 * The location of the cacert.pem file to use.
	 */
	public $cacert_location = false;

	/**
	 * The state of SSL certificate verification.
	 */
	public $ssl_verification = true;

	/**
	 * The user-defined callback function to call when a stream is read from.
	 */
	public $registered_streaming_read_callback = null;

	/**
	 * The user-defined callback function to call when a stream is written to.
	 */
	public $registered_streaming_write_callback = null;


	/*%******************************************************************************************%*/
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


	/*%******************************************************************************************%*/
	// CONSTRUCTOR/DESTRUCTOR

	/**
	 * Constructs a new instance of this class.
	 *
	 * @param string $url (Optional) The URL to request or service endpoint to query.
	 * @param string $proxy (Optional) The faux-url to use for proxy settings. Takes the following format: `proxy://user:pass@hostname:port`
	 * @param array $helpers (Optional) An associative array of classnames to use for request, and response functionality. Gets passed in automatically by the calling class.
	 * @return $this A reference to the current instance.
	 */
	public function __construct($url = null, $proxy = null, $helpers = null)
	{
		// Set some default values.
		$this->request_url = $url;
		$this->method = self::HTTP_GET;
		$this->request_headers = array();
		$this->request_body = '';

		// Set a new Request class if one was set.
		if (isset($helpers['request']) && !empty($helpers['request']))
		{
			$this->request_class = $helpers['request'];
		}

		// Set a new Request class if one was set.
		if (isset($helpers['response']) && !empty($helpers['response']))
		{
			$this->response_class = $helpers['response'];
		}

		if ($proxy)
		{
			$this->set_proxy($proxy);
		}

		return $this;
	}

	/**
	 * Destructs the instance. Closes opened file handles.
	 *
	 * @return $this A reference to the current instance.
	 */
	public function __destruct()
	{
		if (isset($this->read_file) && isset($this->read_stream))
		{
			fclose($this->read_stream);
		}

		if (isset($this->write_file) && isset($this->write_stream))
		{
			fclose($this->write_stream);
		}

		return $this;
	}


	/*%******************************************************************************************%*/
	// REQUEST METHODS

	/**
	 * Sets the credentials to use for authentication.
	 *
	 * @param string $user (Required) The username to authenticate with.
	 * @param string $pass (Required) The password to authenticate with.
	 * @return $this A reference to the current instance.
	 */
	public function set_credentials($user, $pass)
	{
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
	public function add_header($key, $value)
	{
		$this->request_headers[$key] = $value;
		return $this;
	}

	/**
	 * Removes an HTTP header from the cURL request.
	 *
	 * @param string $key (Required) The custom HTTP header to set.
	 * @return $this A reference to the current instance.
	 */
	public function remove_header($key)
	{
		if (isset($this->request_headers[$key]))
		{
			unset($this->request_headers[$key]);
		}
		return $this;
	}

	/**
	 * Set the method type for the request.
	 *
	 * @param string $method (Required) One of the following constants: <HTTP_GET>, <HTTP_POST>, <HTTP_PUT>, <HTTP_HEAD>, <HTTP_DELETE>.
	 * @return $this A reference to the current instance.
	 */
	public function set_method($method)
	{
		$this->method = strtoupper($method);
		return $this;
	}

	/**
	 * Sets a custom useragent string for the class.
	 *
	 * @param string $ua (Required) The useragent string to use.
	 * @return $this A reference to the current instance.
	 */
	public function set_useragent($ua)
	{
		$this->useragent = $ua;
		return $this;
	}

	/**
	 * Set the body to send in the request.
	 *
	 * @param string $body (Required) The textual content to send along in the body of the request.
	 * @return $this A reference to the current instance.
	 */
	public function set_body($body)
	{
		$this->request_body = $body;
		return $this;
	}

	/**
	 * Set the URL to make the request to.
	 *
	 * @param string $url (Required) The URL to make the request to.
	 * @return $this A reference to the current instance.
	 */
	public function set_request_url($url)
	{
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
	public function set_curlopts($curlopts)
	{
		$this->curlopts = $curlopts;
		return $this;
	}

	/**
	 * Sets the length in bytes to read from the stream while streaming up.
	 *
	 * @param integer $size (Required) The length in bytes to read from the stream.
	 * @return $this A reference to the current instance.
	 */
	public function set_read_stream_size($size)
	{
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
	public function set_read_stream($resource, $size = null)
	{
		if (!isset($size) || $size < 0)
		{
			$stats = fstat($resource);

			if ($stats && $stats['size'] >= 0)
			{
				$position = ftell($resource);

				if ($position !== false && $position >= 0)
				{
					$size = $stats['size'] - $position;
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
	public function set_read_file($location)
	{
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
	public function set_write_stream($resource)
	{
		$this->write_stream = $resource;

		return $this;
	}

	/**
	 * Sets the file to write to while streaming down.
	 *
	 * @param string $location (Required) The writeable location to write to.
	 * @return $this A reference to the current instance.
	 */
	public function set_write_file($location)
	{
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
	public function set_proxy($proxy)
	{
		$proxy = parse_url($proxy);
		$proxy['user'] = isset($proxy['user']) ? $proxy['user'] : null;
		$proxy['pass'] = isset($proxy['pass']) ? $proxy['pass'] : null;
		$proxy['port'] = isset($proxy['port']) ? $proxy['port'] : null;
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 * Set the intended starting seek position.
	 *
	 * @param integer $position (Required) The byte-position of the stream to begin reading from.
	 * @return $this A reference to the current instance.
	 */
	public function set_seek_position($position)
	{
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
	 * 	<li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
	 * 	<li><code>$file_handle</code> - <code>resource</code> - Required - The file handle resource that represents the file on the local file system.</li>
	 * 	<li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
	 * </ul>
	 *
	 * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
	 * 	<li>The name of a global function to execute, passed as a string.</li>
	 * 	<li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
	 * 	<li>An anonymous function (PHP 5.3+).</li></ul>
	 * @return $this A reference to the current instance.
	 */
	public function register_streaming_read_callback($callback)
	{
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
	 * 	<li><code>$curl_handle</code> - <code>resource</code> - Required - The cURL handle resource that represents the in-progress transfer.</li>
	 * 	<li><code>$length</code> - <code>integer</code> - Required - The length in kilobytes of the data chunk that was transferred.</li>
	 * </ul>
	 *
	 * @param string|array|function $callback (Required) The callback function is called by <php:call_user_func()>, so you can pass the following values: <ul>
	 * 	<li>The name of a global function to execute, passed as a string.</li>
	 * 	<li>A method to execute, passed as <code>array('ClassName', 'MethodName')</code>.</li>
	 * 	<li>An anonymous function (PHP 5.3+).</li></ul>
	 * @return $this A reference to the current instance.
	 */
	public function register_streaming_write_callback($callback)
	{
		$this->registered_streaming_write_callback = $callback;

		return $this;
	}


	/*%******************************************************************************************%*/
	// PREPARE, SEND, AND PROCESS REQUEST

	/**
	 * A callback function that is invoked by cURL for streaming up.
	 *
	 * @param resource $curl_handle (Required) The cURL handle for the request.
	 * @param resource $file_handle (Required) The open file handle resource.
	 * @param integer $length (Required) The maximum number of bytes to read.
	 * @return binary Binary data from a stream.
	 */
	public function streaming_read_callback($curl_handle, $file_handle, $length)
	{
		// Once we've sent as much as we're supposed to send...
		if ($this->read_stream_read >= $this->read_stream_size)
		{
			// Send EOF
			return '';
		}

		// If we're at the beginning of an upload and need to seek...
		if ($this->read_stream_read == 0 && isset($this->seek_position) && $this->seek_position !== ftell($this->read_stream))
		{
			if (fseek($this->read_stream, $this->seek_position) !== 0)
			{
				throw new RequestCore_Exception('The stream does not support seeking and is either not at the requested position or the position is unknown.');
			}
		}

		$read = fread($this->read_stream, min($this->read_stream_size - $this->read_stream_read, $length)); // Remaining upload data or cURL's requested chunk size
		$this->read_stream_read += strlen($read);

		$out = $read === false ? '' : $read;

		// Execute callback function
		if ($this->registered_streaming_read_callback)
		{
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
	public function streaming_write_callback($curl_handle, $data)
	{
		$length = strlen($data);
		$written_total = 0;
		$written_last = 0;

		while ($written_total < $length)
		{
			$written_last = fwrite($this->write_stream, substr($data, $written_total));

			if ($written_last === false)
			{
				return $written_total;
			}

			$written_total += $written_last;
		}

		// Execute callback function
		if ($this->registered_streaming_write_callback)
		{
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
	public function prep_request()
	{
		$curl_handle = curl_init();

		// Set default options.
		curl_setopt($curl_handle, CURLOPT_URL, $this->request_url);
		curl_setopt($curl_handle, CURLOPT_FILETIME, true);
		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
		curl_setopt($curl_handle, CURLOPT_HEADER, true);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 5184000);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
		curl_setopt($curl_handle, CURLOPT_REFERER, $this->request_url);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($curl_handle, CURLOPT_READFUNCTION, array($this, 'streaming_read_callback'));

		// Verification of the SSL cert
		if ($this->ssl_verification)
		{
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, true);
		}
		else
		{
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
		}

		// chmod the file as 0755
		if ($this->cacert_location === true)
		{
			curl_setopt($curl_handle, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
		}
		elseif (is_string($this->cacert_location))
		{
			curl_setopt($curl_handle, CURLOPT_CAINFO, $this->cacert_location);
		}

		// Debug mode
		if ($this->debug_mode)
		{
			curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
		}

		// Handle open_basedir & safe mode
		if (!ini_get('safe_mode') && !ini_get('open_basedir'))
		{
			curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
		}

		// Enable a proxy connection if requested.
		if ($this->proxy)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, true);

			$host = $this->proxy['host'];
			$host .= ($this->proxy['port']) ? ':' . $this->proxy['port'] : '';
			curl_setopt($curl_handle, CURLOPT_PROXY, $host);

			if (isset($this->proxy['user']) && isset($this->proxy['pass']))
			{
				curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $this->proxy['user'] . ':' . $this->proxy['pass']);
			}
		}

		// Set credentials for HTTP Basic/Digest Authentication.
		if ($this->username && $this->password)
		{
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}

		// Handle the encoding if we can.
		if (extension_loaded('zlib'))
		{
			curl_setopt($curl_handle, CURLOPT_ENCODING, '');
		}

		// Process custom headers
		if (isset($this->request_headers) && count($this->request_headers))
		{
			$temp_headers = array();

			foreach ($this->request_headers as $k => $v)
			{
				$temp_headers[] = $k . ': ' . $v;
			}

			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
		}

		switch ($this->method)
		{
			case self::HTTP_PUT:
				//unset($this->read_stream);
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				if (isset($this->read_stream))
				{
					if (!isset($this->read_stream_size) || $this->read_stream_size < 0)
					{
						throw new RequestCore_Exception('The stream size for the streaming upload cannot be determined.');
					}

					curl_setopt($curl_handle, CURLOPT_INFILESIZE, $this->read_stream_size);
					curl_setopt($curl_handle, CURLOPT_UPLOAD, true);
				}
				else
				{
					curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				}
				break;

			case self::HTTP_POST:
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;

			case self::HTTP_HEAD:
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, self::HTTP_HEAD);
				curl_setopt($curl_handle, CURLOPT_NOBODY, 1);
				break;

			default: // Assumed GET
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $this->method);
				if (isset($this->write_stream))
				{
					curl_setopt($curl_handle, CURLOPT_WRITEFUNCTION, array($this, 'streaming_write_callback'));
					curl_setopt($curl_handle, CURLOPT_HEADER, false);
				}
				else
				{
					curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				}
				break;
		}

		// Merge in the CURLOPTs
		if (isset($this->curlopts) && sizeof($this->curlopts) > 0)
		{
			foreach ($this->curlopts as $k => $v)
			{
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
	public function process_response($curl_handle = null, $response = null)
	{
		// Accept a custom one if it's passed.
		if ($curl_handle && $response)
		{
			$this->curl_handle = $curl_handle;
			$this->response = $response;
		}

		// As long as this came back as a valid resource...
		if (is_resource($this->curl_handle))
		{
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
			foreach ($this->response_headers as $header)
			{
				$kv = explode(': ', $header);
				$header_assoc[strtolower($kv[0])] = isset($kv[1])?$kv[1]:'';
			}

			// Reset the headers to the appropriate property.
			$this->response_headers = $header_assoc;
			$this->response_headers['_info'] = $this->response_info;
			$this->response_headers['_info']['method'] = $this->method;

			if ($curl_handle && $response)
			{
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
	public function send_request($parse = false)
	{
		set_time_limit(0);

		$curl_handle = $this->prep_request();
		$this->response = curl_exec($curl_handle);

		if ($this->response === false)
		{
			throw new RequestCore_Exception('cURL resource: ' . (string) $curl_handle . '; cURL error: ' . curl_error($curl_handle) . ' (' . curl_errno($curl_handle) . ')');
		}

		$parsed_response = $this->process_response($curl_handle, $this->response);

		curl_close($curl_handle);

		if ($parse)
		{
			return $parsed_response;
		}

		return $this->response;
	}

	/**
	 * Sends the request using <php:curl_multi_exec()>, enabling parallel requests. Uses the "rolling" method.
	 *
	 * @param array $handles (Required) An indexed array of cURL handles to process simultaneously.
	 * @param array $opt (Optional) An associative array of parameters that can have the following keys: <ul>
	 * 	<li><code>callback</code> - <code>string|array</code> - Optional - The string name of a function to pass the response data to. If this is a method, pass an array where the <code>[0]</code> index is the class and the <code>[1]</code> index is the method name.</li>
	 * 	<li><code>limit</code> - <code>integer</code> - Optional - The number of simultaneous requests to make. This can be useful for scaling around slow server responses. Defaults to trusting cURLs judgement as to how many to use.</li></ul>
	 * @return array Post-processed cURL responses.
	 */
	public function send_multi_request($handles, $opt = null)
	{
		set_time_limit(0);

		// Skip everything if there are no handles to process.
		if (count($handles) === 0) return array();

		if (!$opt) $opt = array();

		// Initialize any missing options
		$limit = isset($opt['limit']) ? $opt['limit'] : -1;

		// Initialize
		$handle_list = $handles;
		$http = new $this->request_class();
		$multi_handle = curl_multi_init();
		$handles_post = array();
		$added = count($handles);
		$last_handle = null;
		$count = 0;
		$i = 0;

		// Loop through the cURL handles and add as many as it set by the limit parameter.
		while ($i < $added)
		{
			if ($limit > 0 && $i >= $limit) break;
			curl_multi_add_handle($multi_handle, array_shift($handles));
			$i++;
		}

		do
		{
			$active = false;

			// Start executing and wait for a response.
			while (($status = curl_multi_exec($multi_handle, $active)) === CURLM_CALL_MULTI_PERFORM)
			{
				// Start looking for possible responses immediately when we have to add more handles
				if (count($handles) > 0) break;
			}

			// Figure out which requests finished.
			$to_process = array();

			while ($done = curl_multi_info_read($multi_handle))
			{
				// Since curl_errno() isn't reliable for handles that were in multirequests, we check the 'result' of the info read, which contains the curl error number, (listed here http://curl.haxx.se/libcurl/c/libcurl-errors.html )
				if ($done['result'] > 0)
				{
					throw new RequestCore_Exception('cURL resource: ' . (string) $done['handle'] . '; cURL error: ' . curl_error($done['handle']) . ' (' . $done['result'] . ')');
				}

				// Because curl_multi_info_read() might return more than one message about a request, we check to see if this request is already in our array of completed requests
				elseif (!isset($to_process[(int) $done['handle']]))
				{
					$to_process[(int) $done['handle']] = $done;
				}
			}

			// Actually deal with the request
			foreach ($to_process as $pkey => $done)
			{
				$response = $http->process_response($done['handle'], curl_multi_getcontent($done['handle']));
				$key = array_search($done['handle'], $handle_list, true);
				$handles_post[$key] = $response;

				if (count($handles) > 0)
				{
					curl_multi_add_handle($multi_handle, array_shift($handles));
				}

				curl_multi_remove_handle($multi_handle, $done['handle']);
				curl_close($done['handle']);
			}
		}
		while ($active || count($handles_post) < $added);

		curl_multi_close($multi_handle);

		ksort($handles_post, SORT_NUMERIC);
		return $handles_post;
	}


	/*%******************************************************************************************%*/
	// RESPONSE METHODS

	/**
	 * Get the HTTP response headers from the request.
	 *
	 * @param string $header (Optional) A specific header value to return. Defaults to all headers.
	 * @return string|array All or selected header values.
	 */
	public function get_response_header($header = null)
	{
		if ($header)
		{
			return $this->response_headers[strtolower($header)];
		}
		return $this->response_headers;
	}

	/**
	 * Get the HTTP response body from the request.
	 *
	 * @return string The response body.
	 */
	public function get_response_body()
	{
		return $this->response_body;
	}

	/**
	 * Get the HTTP response code from the request.
	 *
	 * @return string The HTTP response code.
	 */
	public function get_response_code()
	{
		return $this->response_code;
	}
}


/**
 * Container for all response-related methods.
 */
class ResponseCore
{
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
	public function __construct($header, $body, $status = null)
	{
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
	public function isOK($codes = array(200, 201, 204, 206))
	{
		if (is_array($codes))
		{
			return in_array($this->status, $codes);
		}

		return $this->status === $codes;
	}
}

/**
 * Default RequestCore Exception.
 */
class RequestCore_Exception extends Exception {}



/**
 * ����ļ���mime type����
 * @author xiaobing.meng
 *
 */
class MimeTypes {
	public static $mime_types = array (
			'apk' => 'application/vnd.android.package-archive',
			'3gp' => 'video/3gpp', 'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff', 'asc' => 'text/plain',
			'atom' => 'application/atom+xml', 'au' => 'audio/basic',
			'avi' => 'video/x-msvideo', 'bcpio' => 'application/x-bcpio',
			'bin' => 'application/octet-stream', 'bmp' => 'image/bmp',
			'cdf' => 'application/x-netcdf', 'cgm' => 'image/cgm',
			'class' => 'application/octet-stream',
			'cpio' => 'application/x-cpio',
			'cpt' => 'application/mac-compactpro',
			'csh' => 'application/x-csh', 'css' => 'text/css',
			'dcr' => 'application/x-director', 'dif' => 'video/x-dv',
			'dir' => 'application/x-director', 'djv' => 'image/vnd.djvu',
			'djvu' => 'image/vnd.djvu',
			'dll' => 'application/octet-stream',
			'dmg' => 'application/octet-stream',
			'dms' => 'application/octet-stream',
			'doc' => 'application/msword', 'dtd' => 'application/xml-dtd',
			'dv' => 'video/x-dv', 'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eps' => 'application/postscript', 'etx' => 'text/x-setext',
			'exe' => 'application/octet-stream',
			'ez' => 'application/andrew-inset', 'flv' => 'video/x-flv',
			'gif' => 'image/gif', 'gram' => 'application/srgs',
			'grxml' => 'application/srgs+xml',
			'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip',
			'hdf' => 'application/x-hdf',
			'hqx' => 'application/mac-binhex40', 'htm' => 'text/html',
			'html' => 'text/html', 'ice' => 'x-conference/x-cooltalk',
			'ico' => 'image/x-icon', 'ics' => 'text/calendar',
			'ief' => 'image/ief', 'ifb' => 'text/calendar',
			'iges' => 'model/iges', 'igs' => 'model/iges',
			'jnlp' => 'application/x-java-jnlp-file', 'jp2' => 'image/jp2',
			'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg', 'js' => 'application/x-javascript',
			'kar' => 'audio/midi', 'latex' => 'application/x-latex',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'm3u' => 'audio/x-mpegurl', 'm4a' => 'audio/mp4a-latm',
			'm4p' => 'audio/mp4a-latm', 'm4u' => 'video/vnd.mpegurl',
			'm4v' => 'video/x-m4v', 'mac' => 'image/x-macpaint',
			'man' => 'application/x-troff-man',
			'mathml' => 'application/mathml+xml',
			'me' => 'application/x-troff-me', 'mesh' => 'model/mesh',
			'mid' => 'audio/midi', 'midi' => 'audio/midi',
			'mif' => 'application/vnd.mif', 'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie', 'mp2' => 'audio/mpeg',
			'mp3' => 'audio/mpeg', 'mp4' => 'video/mp4',
			'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg', 'mpga' => 'audio/mpeg',
			'ms' => 'application/x-troff-ms', 'msh' => 'model/mesh',
			'mxu' => 'video/vnd.mpegurl', 'nc' => 'application/x-netcdf',
			'oda' => 'application/oda', 'ogg' => 'application/ogg',
			'ogv' => 'video/ogv', 'pbm' => 'image/x-portable-bitmap',
			'pct' => 'image/pict', 'pdb' => 'chemical/x-pdb',
			'pdf' => 'application/pdf',
			'pgm' => 'image/x-portable-graymap',
			'pgn' => 'application/x-chess-pgn', 'pic' => 'image/pict',
			'pict' => 'image/pict', 'png' => 'image/png',
			'pnm' => 'image/x-portable-anymap',
			'pnt' => 'image/x-macpaint', 'pntg' => 'image/x-macpaint',
			'ppm' => 'image/x-portable-pixmap',
			'ppt' => 'application/vnd.ms-powerpoint',
			'ps' => 'application/postscript', 'qt' => 'video/quicktime',
			'qti' => 'image/x-quicktime', 'qtif' => 'image/x-quicktime',
			'ra' => 'audio/x-pn-realaudio',
			'ram' => 'audio/x-pn-realaudio', 'ras' => 'image/x-cmu-raster',
			'rdf' => 'application/rdf+xml', 'rgb' => 'image/x-rgb',
			'rm' => 'application/vnd.rn-realmedia',
			'roff' => 'application/x-troff', 'rtf' => 'text/rtf',
			'rtx' => 'text/richtext', 'sgm' => 'text/sgml',
			'sgml' => 'text/sgml', 'sh' => 'application/x-sh',
			'shar' => 'application/x-shar', 'silo' => 'model/mesh',
			'sit' => 'application/x-stuffit',
			'skd' => 'application/x-koan', 'skm' => 'application/x-koan',
			'skp' => 'application/x-koan', 'skt' => 'application/x-koan',
			'smi' => 'application/smil', 'smil' => 'application/smil',
			'snd' => 'audio/basic', 'so' => 'application/octet-stream',
			'spl' => 'application/x-futuresplash',
			'src' => 'application/x-wais-source',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc', 'svg' => 'image/svg+xml',
			'swf' => 'application/x-shockwave-flash',
			't' => 'application/x-troff', 'tar' => 'application/x-tar',
			'tcl' => 'application/x-tcl', 'tex' => 'application/x-tex',
			'texi' => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo', 'tif' => 'image/tiff',
			'tiff' => 'image/tiff', 'tr' => 'application/x-troff',
			'tsv' => 'text/tab-separated-values', 'txt' => 'text/plain',
			'ustar' => 'application/x-ustar',
			'vcd' => 'application/x-cdlink', 'vrml' => 'model/vrml',
			'vxml' => 'application/voicexml+xml', 'wav' => 'audio/x-wav',
			'wbmp' => 'image/vnd.wap.wbmp',
			'wbxml' => 'application/vnd.wap.wbxml', 'webm' => 'video/webm',
			'wml' => 'text/vnd.wap.wml',
			'wmlc' => 'application/vnd.wap.wmlc',
			'wmls' => 'text/vnd.wap.wmlscript',
			'wmlsc' => 'application/vnd.wap.wmlscriptc',
			'wmv' => 'video/x-ms-wmv', 'wrl' => 'model/vrml',
			'xbm' => 'image/x-xbitmap', 'xht' => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'xls' => 'application/vnd.ms-excel',
			'xml' => 'application/xml', 'xpm' => 'image/x-xpixmap',
			'xsl' => 'application/xml', 'xslt' => 'application/xslt+xml',
			'xul' => 'application/vnd.mozilla.xul+xml',
			'xwd' => 'image/x-xwindowdump', 'xyz' => 'chemical/x-xyz',
			'zip' => 'application/zip' );

	public static function get_mimetype($ext) {
		return (isset ( self::$mime_types [$ext] ) ? self::$mime_types [$ext] : 'application/octet-stream');
	}
}
