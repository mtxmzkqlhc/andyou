<?php
/**
* ��������
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_Config
{
	const DAL_NAMESPACE              = 'ZOL_DAL_';
	const CACHE_MODULES_NAMESPACE    = 'Modules_';#����ģ�������ռ�
	const CACHE_MODULES_LIST_FILE    = 'modules.lst';
	const CACHE_KEYNAMES             = 'ZOL_DAL_KeyNames';#���������
	const CACHE_MODULES_DIR          = '/www/DAL/Modules';
	const CACHE_DIR                  = '/www/DAL/var/cache_data/';
	const LOCALMEM_CACHE_DIR         = '/www/DAL/var/cache_data/tmpfs/';
	const DAL_CACHE_SAVE_TYPE        = 'PHP';#PHP|SERIALIZE(phpԭ��|���л�) ����洢���� ������Ҫ�ģ�����ֻ��Ϊ��ƽ������
	
	/**
	* ����Ĭ��KEY������
	*/
	const DEFAULT_FILE_KEY_MAKER     = 'ZOL_DAL_FileCacheKey';
	const DEFAULT_MEM_KEY_MAKER      = 'ZOL_DAL_MemCacheKey';
	const DEFAULT_LOCALMEM_KEY_MAKER = 'ZOL_DAL_LocalMemCacheKey';
	const DEFAULT_LUCENE_KEY_MAKER   = 'ZOL_DAL_LuceneCacheKey';
	const DEFAULT_NO_KEY_MAKER       = 'ZOL_DAL_NoCacheKey';
	const DEFAULT_MONGO_KEY_MAKER    = 'ZOL_DAL_MongoCacheKey';
	
	
	/**
	* �������� DALʵ�������õ�ֵ
	* 
	* @var array
	*/
	private static $_baseConfig = array(
		'DEFAULT_FILE_KEY_MAKER'     => self::DEFAULT_FILE_KEY_MAKER,#Ĭ���ļ�KEY������
		'DEFAULT_MEM_KEY_MAKER'      => self::DEFAULT_MEM_KEY_MAKER,#Ĭ��memcache KEY������
		'DEFAULT_LOCALMEM_KEY_MAKER' => self::DEFAULT_LOCALMEM_KEY_MAKER,#Ĭ�ϱ����ڴ滺�� KEY������
		'DEFAULT_LUCENE_KEY_MAKER'   => self::DEFAULT_LUCENE_KEY_MAKER,#Ĭ��Lucene���� KEY������
		'DEFAULT_NO_KEY_MAKER'       => self::DEFAULT_NO_KEY_MAKER,#Ĭ��Lucene���� KEY������
		'CACHE_KEYNAMES'             => self::CACHE_KEYNAMES,#���������
		'DEFAULT_MONGO_KEY_MAKER'    => self::DEFAULT_MONGO_KEY_MAKER,#Ĭ��MongoDb���� KEY������
	);
	
	/**
	* ���û���
	* 
	* @var array
	*/
	private static $_config = null;
	
	/**
	* ��ȡģ�����
	* 
	* @param string $moduleName ģ����
	* @return array config 
	*/
	public static function getConfig($moduleName)
	{
		$configKey = substr($moduleName, 0, strrpos($moduleName, '_'));
		if (isset(self::$_config[$configKey])) {
			return self::$_config[$configKey];
		}
		
		$config = self::$_baseConfig;
		$token  = explode('_', substr($moduleName, strlen(self::CACHE_MODULES_NAMESPACE)));
		$tokenLen = count($token);
		
		$dir = array();
		for ($i = 0; $i < $tokenLen; $i++) {
			$midDir = '';
			if ($i > 0) {
				$dir[] = $token[$i - 1];
				$midDir = join('/', $dir) . '/';
			}
			
			$confFile = DAL_DIR . '/' . $midDir . 'conf.ini';
			
			if (is_file($confFile)) {
				$config = array_merge($config, parse_ini_file($confFile));
			}
		}
		self::$_config[$configKey] = $config;
		return $config;
	}
	
	/**
	* ��ȡKEYMAKER��
	* 
	* @param ģ���� $moduleName
	* @param ģ������ $type FILE|MEM|LOCALMEM|LUCENE|NO
	* @return config
	*/
	public static function getKeyMakerName($moduleName, $type = 'FILE')
	{
		$config = self::getConfig($moduleName);
		$type = strtoupper($type);
		if (isset($config['DEFAULT_' . $type . '_KEY_MAKER'])) {
			return $config['DEFAULT_' . $type . '_KEY_MAKER'];
		} else {
			return $config['DEFAULT_FILE_KEY_MAKER'];
		}
	}
}
