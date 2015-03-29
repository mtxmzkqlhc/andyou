<?php
/**
*
*/
class Libs_Abstract
{
    /*
	* @var ZOL_Product_Caching_GetCacheLoader
	*/
	protected static $cache;

	/**
	* 初始化缓存
	*/
	public static function init()
	{
		self::$cache = ZOL_DAL_GetCacheLoader::getInstance();
	}

	/**
	* 加载缓存数据
	*/
	protected static function loadCache($moduleName, $param = array(), $num = 0)
	{
		self::init();
		$data = self::$cache->loadCacheObject($moduleName, $param);

		if ($num && $data && count($data) > $num) {
			$data = array_slice($data, 0, $num, true);
		}

		return $data;
	}

}
?>