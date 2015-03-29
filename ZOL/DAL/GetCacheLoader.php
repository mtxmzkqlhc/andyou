<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_GetCacheLoader extends ZOL_DAL_CacheLoader
{
	/**
	* @var ZOL_DAL_ICacheManager
	*/
	private static $_manager;
	private static $_loadedCacheModules = array();
	
	public function __construct()
	{
		self::$_manager = ZOL_DAL_BaseCacheManager::getInstance();
	}
	
	/**
	* ���ػ������
	* @param string $moduleName                               ����ģ������
	* @param string|ZOL_DAL_ICacheKey $cacheParam �����ֵ�ԣ�����õļ�ֵ�㷨�����ֻ�Ǽ�ֵ�ԣ�
	*                                                         ϵͳ���Զ�����Ĭ���㷨����
	* @param integer                                          ��������
	*/
	public function loadCacheObject($moduleName, $cacheParam = null, $num = 0)
	{
		$data = self::$_manager->getCacheObject($moduleName, $cacheParam, $num);
		if (empty(self::$_loadedCacheModules[$moduleName])) {
			self::$_loadedCacheModules[$moduleName]['count'] = 0;
		}
		self::$_loadedCacheModules[$moduleName]['count'] ++;
		self::$_loadedCacheModules[$moduleName]['param']  = $cacheParam;
		self::$_loadedCacheModules[$moduleName]['key'] = self::$_manager->getCacheKey();
		return $data;
	}
	
	
	/**
	* ��ȡҳ���Ѽ���ģ����Ϣ
	*/
	public function getLoadedCacheModules()
	{
		return self::$_loadedCacheModules;
	}
}
