<?php
/**
* �������
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

class ZOL_DAL_BaseCacheManager implements ZOL_DAL_ICacheManager
{
	/**
	* @var ZOL_DAL_BaseCacheManager
	*/
	private static $instance;
	
	private $moduleName;
	
	/**
	* ����KEY
	*/
	private static $cacheKey;
	
	/**
	* @var ZOL_DAL_CacheModules ģ���б�
	*/
	private static $moduleManager;
	
	public function __construct()
	{
		self::$moduleManager = ZOL_DAL_CacheModuleManager::getInstance();
	}
	
	/**
	* ����ģʽ
	*/
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new ZOL_DAL_BaseCacheManager();
		}
		return self::$instance;
	}
	
	/**
	* ��ȡ����ģ��
	* @return ZOL_DAL_ICacheModule
	*/
	public function getCacheModuleObj($moduleName)
	{
		static $instance;
		$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		if (empty($instance[$moduleName])) {
			#��黺��ģ���Ƿ����
			if (!self::$moduleManager->checkModuleExist($moduleName)) {
				throw new ZOL_Exception('The Module "' . $moduleName . '" does not exist!');
			}
			$instance[$moduleName] = new $moduleName();
		}
		return $instance[$moduleName];
	}
	
	/**
	* ��ȡ������󷵻����� �ļ�����Ļ���Ŀ¼�ṹ:Cache/$moduleName/$cacheKey{0,1}/$cacheKey{2,3}/$cacheKey.php
	* @param string $moduleName ����ģ������
	* @param array|string|ZOL_DAL_ICacheKey $cacheParam �������
	* @param integer $num ����
	*/
	public function getCacheObject($moduleName, $cacheParam = null, $num = 0)
	{
		$cacheModule = $this->getCacheModuleObj($moduleName);
		if (!($cacheModule && $cacheModule instanceof ZOL_DAL_ICacheModule)) {
			return false;
		}
		$data = $cacheModule->get($cacheParam);
		if (is_array($data) && $num > 0) {
			$data = array_slice($data, 0, $num, true);
		}
		self::$cacheKey = $cacheModule->getCacheKey();
		return $data;
	}
	
	/**
	* ˢ�»������
	* @param string $moduleName ģ����
	* @param array $param
	*/
	public function refreshCacheObject($moduleName, $param = null)
	{
		$cacheModule = $this->getCacheModuleObj($moduleName);
		if (!($cacheModule && $cacheModule instanceof ZOL_DAL_ICacheModule)) {
			return false;
		}
		
		if ($refreshHandle = $cacheModule->refresh($param)) {
			//echo '����ʱ�乲����' . $cacheModule->getRefreshTime();
			
		}
		return $refreshHandle;
	}
	
	/**
	* ����������
	*/
	public function removeCacheObject($moduleName, $cacheParam = null)
	{
		
	}
	
	public function getCacheKey()
	{
		return self::$cacheKey;
	}
}