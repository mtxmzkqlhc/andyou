<?php
set_time_limit(0);
/**
* ��̨���ɻ���ģ�������
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_RefreshCacheLoader extends ZOL_DAL_CacheLoader
{
	/**
	* @var ZOL_DAL_ICacheManager
	*/
	private static $manager;
	private static $cacheKeyObj;
	public function __construct()
	{
		self::$manager = ZOL_DAL_BaseCacheManager::getInstance();
	}
	
	/**
	* ��ȡ����ģ�����
	* @return ZOL_DAL_ICacheModule
	*/
	public function getCacheModuleObj($moduleName)
	{
		return self::$manager->getCacheModuleObj($moduleName);
	}
	
	/**
	* ���ػ������
	* @param string $moduleName                               ����ģ������
	* @param string|ZOL_DAL_ICacheKey $cacheParam �����ֵ�ԣ�����õļ�ֵ�㷨�����ֻ�Ǽ�ֵ�ԣ�
	*                                                         ϵͳ���Զ�����Ĭ���㷨����
	*/
	public function loadCacheObject($moduleName, $cacheParam = array(), $num = 0)
	{
		return self::$manager->getCacheObject($moduleName, $cacheParam);
	}
	
	public function refreshCacheObject($moduleName, $param = array())
	{
		return self::$manager->refreshCacheObject($moduleName, $param);
	}
	
	public function removeCacheObject($moduleName, $cacheParam = array())
	{
		return self::$manager->removeCacheObject($moduleName, $cacheParam);
	}
}
