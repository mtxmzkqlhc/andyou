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
	* ��ʼ������
	*/
	public static function init()
	{
		self::$cache = ZOL_DAL_GetCacheLoader::getInstance();
	}

	/**
	* ���ػ�������
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