<?php
/**
* 缓存管理
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
	* 缓存KEY
	*/
	private static $cacheKey;
	
	/**
	* @var ZOL_DAL_CacheModules 模块列表
	*/
	private static $moduleManager;
	
	public function __construct()
	{
		self::$moduleManager = ZOL_DAL_CacheModuleManager::getInstance();
	}
	
	/**
	* 单例模式
	*/
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new ZOL_DAL_BaseCacheManager();
		}
		return self::$instance;
	}
	
	/**
	* 获取缓存模块
	* @return ZOL_DAL_ICacheModule
	*/
	public function getCacheModuleObj($moduleName)
	{
		static $instance;
		$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		if (empty($instance[$moduleName])) {
			#检查缓存模块是否存在
			if (!self::$moduleManager->checkModuleExist($moduleName)) {
				throw new ZOL_Exception('The Module "' . $moduleName . '" does not exist!');
			}
			$instance[$moduleName] = new $moduleName();
		}
		return $instance[$moduleName];
	}
	
	/**
	* 获取缓存对象返回数组 文件缓存的话，目录结构:Cache/$moduleName/$cacheKey{0,1}/$cacheKey{2,3}/$cacheKey.php
	* @param string $moduleName 缓存模块名称
	* @param array|string|ZOL_DAL_ICacheKey $cacheParam 缓存参数
	* @param integer $num 数量
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
	* 刷新缓存对象
	* @param string $moduleName 模块名
	* @param array $param
	*/
	public function refreshCacheObject($moduleName, $param = null)
	{
		$cacheModule = $this->getCacheModuleObj($moduleName);
		if (!($cacheModule && $cacheModule instanceof ZOL_DAL_ICacheModule)) {
			return false;
		}
		
		if ($refreshHandle = $cacheModule->refresh($param)) {
			//echo '发布时间共用了' . $cacheModule->getRefreshTime();
			
		}
		return $refreshHandle;
	}
	
	/**
	* 清除缓存对象
	*/
	public function removeCacheObject($moduleName, $cacheParam = null)
	{
		
	}
	
	public function getCacheKey()
	{
		return self::$cacheKey;
	}
}