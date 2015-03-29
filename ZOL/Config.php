<?php
/**
* ��ȡ����
* @author wiki<wu.kun@zol.com.cn>
* @copyright (c) 2010-06-22
* @version v1.0
*/

class ZOL_Config
{
    /**
    * ���û���
    * @var array
    */
	private static $_cache;

	/**
	* ��ȡ
	*
	* @param mixed $key ���õ�ַ
	* @param mixed $arrKeyName �����Ӽ����ɶ�ά����.�ŷָ�
	* @param enum $type PHP|INI
	* @return array|false
	*/
	public static function get($key, $arrKeyName = '', $type = 'PHP')
	{
		if (!defined('PRODUCTION_CONFIG_PATH')) {
			define('PRODUCTION_CONFIG_PATH', PRODUCTION_ROOT . '/Config');
		}

		$path = PRODUCTION_CONFIG_PATH . '/' . str_replace('_', '/', $key) . '.' . strtolower($type);
		if (isset(self::$_cache[$path])) {
			$config = self::$_cache[$path];
		} else {
			if (!ZOL_File::exists($path)) {
				self::$_cache[$path] = false;
				return false;
			}

			switch ($type) {
				case 'INI':
					$config = parse_ini_file($path);
					break;
				default:
				case 'PHP':
					$config = include($path);
					break;
			}
			self::$_cache[$path] = $config;
		}

		if ($arrKeyName) {
			if (strpos('.', $arrKeyName)) {
				$keyArr = explode('.', $arrKeyName);
				foreach ($keyArr as $key) {
					if (!$config) {
						break;
					}

					$config = empty($config[$key]) ? false : $config[$key];
				}
			} else {
				$config = empty($config[$arrKeyName]) ? false : $config[$arrKeyName];
			}
		}
		return $config;
	}
}