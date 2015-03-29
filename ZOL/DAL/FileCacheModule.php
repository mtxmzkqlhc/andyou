<?php
/**
* ������
* �ļ����������ز���
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

abstract class ZOL_DAL_FileCacheModule implements ZOL_DAL_ICacheModule
{
	/**
	* �����С ����
	*/
	const CACHE_SIZE = 1000;
	
	/**
	* ��������
	*/
	protected $_expire = 3600;
	
	/**
	* �Ƿ��������
	* 
	* @var boolean
	*/
	protected $_isDuly = false;
	
	/**
	* �Զ����»���
	*/
	protected $_autoRefresh = false;

    /**
     * ��ǰ�Ļ���ģ����
     * @var string 
     */
    protected $_moduleName;

	/**
	* ���ò���
	*/
	protected $_cacheParam;
	
	/**
	* ����·��
	*/
	protected $_cachePath;
	
	/**
	* �������ݣ���ֹ���μ���
	*/
	protected $_cachePool = array();
	
	/**
	* KEY��������
	*/
	protected $_keyMakerName;
	
	/**
	* ���õ�ģ����������ͳ��ҳ��ģ�����
	*/
	protected $_moduleNames = array();
	
	/**
	* ���ڱ��������
	*/
	protected $_content;
	
	protected $_startTime = 0;
	
	protected $_endTime   = 0;
	
	/**
	* ��������
	*/
	protected $_depend = array();
	
	protected $_cacheSaveType;
	
	public function __construct($cacheParam = null)
	{
		if ($cacheParam !== null) {
			$this->processParam($cacheParam);
		}
		
		$this->_cacheSaveType = defined('DAL_CACHE_SAVE_TYPE') 
				? DAL_CACHE_SAVE_TYPE 
				: ZOL_DAL_Config::DAL_CACHE_SAVE_TYPE;
	}
	
	/**
	* ��ʼ�����������ģ����;
	*/
	public function processParam($cacheParam = null)
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		
		#��ʼ��ʱ��
		$this->_startTime = microtime(true);
		
		$moduleName = get_class($this);
		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			static $keyMaker = null;
			if(!$keyMaker || !($keyMaker instanceof ZOL_DAL_ICacheKey)) {
				#���������ļ���ȡĬ��KEYMAKER
				$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'FILE');
				$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
			} else {
				$keyMaker->setParam((array)$cacheParam);
			}
			
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		#���û���洢����
		$keyMaker->setCacheSaveType($this->_cacheSaveType);

        $this->_moduleName = $moduleName;
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cachePath  = $keyMaker->getCacheKey();
		
		return $this;
	}
	
	/**
	* ��ȡ�ļ�����
	* �ɱ���д
	* @return mixed|false
	*/
	public function get($cacheParam = null)
	{
		$this->processParam($cacheParam);
//		var_dump($this->_cachePath);
//		var_dump($this->_cacheParam);
//		exit;
		#���ػ�������
		if (isset($this->_cachePool[$this->_cachePath])) {
			return $this->_cachePool[$this->_cachePath];
		}
		
		//����
		if ($this->_autoRefresh && $this->isExpire()) {
			$this->refresh($this->_cacheParam);
		}
		
		if (file_exists($this->_cachePath)) {
			$data = false;
			switch ($this->_cacheSaveType) {
				case 'SERIALIZE':#��ȡ�������ݽ�ѹ�����л�
					$str = file_get_contents($this->_cachePath);
					if ($str) {
						$data = unserialize(gzinflate($str));
					}
					break;
				case 'PHP':
				default:
					$data = include($this->_cachePath);
			}
			
			#�ͷ��ڴ�
			if ($this->_cachePool && count($this->_cachePool) > self::CACHE_SIZE) {
				unset($this->_cachePool);
			}
			
			#��������
			$this->_cachePool[$this->_cachePath] = $data;
			return $data;
		} else {
			return false;
		}
	}
	
	/**
	* �����ļ�����
	* @param array $cacheParam
	* @param mixed $content ����
	* @param boolen $fileSyn �Ƿ�ͬ��
	*/
	public function set($cacheParam = null, $content = null, $fileSyn = false)
	{
		$this->processParam($cacheParam);
		
//		var_dump($cacheParam);
//		var_dump($this->_cacheParam);
		$this->_content = isset($content) ? $content : $this->_content;
		
		if (empty($this->_cachePath)) {
			return false;
		}
		
		//ɾ����ǰ�ļ�
//		if (ZOL_File::exists($this->_cachePath) && empty($this->_content)) {
//			$this->rm();
//			return false;
//		} elseif (empty($this->_content)) {
//			return false;
//		}
		
		if (is_object($this->_content)) {
			$this->_content = (array)$this->_content;
		}
		
		if (is_array($this->_content)) {
			#���˿�ֵ
			$this->_content = self::arrayFilter($this->_content);
		}
		//var_dump($this->_content);
		#ת�����ݣ��Ա㱣��
		$this->_convData($this->_content);
		
		$this->_endTime = microtime(true);
		
		#var_dump($this->_cachePath);
		
		
		if (ZOL_File::exists($this->_cachePath) || !empty($this->_content)) {
			//$sourceMd5 = md5($content);
			//$desMd5    = md5(ZOL_File::get($this->_cachePath));
			//if($sourceMd5 != $desMd5){#�ж�md5�Ƿ���ͬ~~
				if($fileSyn){
					$this->fileSyn();
				}
			//}
			
//			var_dump($this->_content, $this->_cachePath);
			ZOL_File::write($this->_content, $this->_cachePath);
			unset($content, $this->_content);
			return true;
		}
		return false;
	}
	
	/**
	* ת������
	*/
	private function _convData(&$content)
	{
		if (!$content) {
			return false;
		}
		switch ($this->_cacheSaveType) {
			case 'SERIALIZE':
				$content = gzdeflate(serialize($content), 9);
				break;
			case 'PHP':
			default:
				$content = '<?php return ' . self::compressData(var_export($content, true)) . ';';
		}
	}
	
	/**
	* �ļ�ͬ��
	*/
	private function fileSyn()
	{
		$path = $this->_cachePath;
		//ͬ��д��..........
		//Libs_GlobalFunc::putSynFile($path);
		return true;
	}
	
	/**
	* ɾ���ļ�����
	*/
	public function rm($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cachePath) || !file_exists($this->_cachePath)) {
			return false;
		}
		return ZOL_File::rm($this->_cachePath);
	}
	
	/**
	* �Ƿ����
	* @return boolean true|false ����|û����
	*/
	public function isExpire($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cachePath) || !is_file($this->_cachePath)) {
			return true;
		}
		
		$expire = $this->_isDuly 
				? ($this->_expire - (SYSTEM_TIME % $this->_expire)) 
				: $this->_expire;
		
		return filemtime($this->_cachePath) + $expire < time();
	}
	
	public function getDepend()
	{
		return $this->_depend;
	}
	
	public function getExpire()
	{
		return $this->_expire;
	}
    /**
     * �����Զ����±�־
     * @param type $auto 
     */
	public function setAutoRefresh($auto=0)
    {
        $this->_autoRefresh = $auto;
    }
	/**
	* ��ȡ����KEY
	*/
	public function getCacheKey()
	{
		return $this->_cachePath;
	}
	
	/**
	* ��ȡ����ʱ��
	*/
	public function getRefreshTime()
	{
		return $this->_endTime - $this->_startTime;
	}
	
	/**
	* ѹ������ ��Ҫ�ǳ�ȥ����ո�ͻ��У������ַ�����С
	*/
	private static function compressData($data)
	{
		$data = str_replace(array("\r", "\n"), array('', ''), $data);//ȥ������
		$data = str_replace(' => ', '=>', $data);//ȥ�������ֵ���ո�
		$data = preg_replace("/( ){2,}/", ' ', $data);//�����ո��滻�ɵ�һ�ո�
		return $data;
	}
	
	/**
	* �ݹ��������ֵ
	* 
	* @param mixed $array ���������
	* @param mixed $callback �ص�����
	* @return array
	*/
	public static function arrayFilter(array $array, $callback = null)
	{
		foreach ($array as &$value) {
			if (is_array($value)) {
				$value = self::arrayFilter($value, $callback);
			}
		}
		return array_filter($array);
	}
}
