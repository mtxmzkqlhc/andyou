<?php

class ZOL_Caching_Memcache extends ZOL_Caching_Abstraction
{
	protected static $servers   = array(
		'10.15.184.100:11211',
		'10.15.184.101:11211',
		'10.15.184.102:11211',
		'10.15.184.103:11211',
		'10.15.184.104:11211',
		'10.15.184.105:11211',
		'10.15.184.106:11211',
		'10.15.184.107:11211',
		'10.15.184.108:11211',
		'10.15.184.109:11211',
		'10.15.184.110:11211',
	);
	
	protected static $mem;

	protected static function init()
	{
		if (empty(self::$mem))
		{
			self::$mem = new Memcache;
            defined('MEMCACHE_CONF_KEY') || define('MEMCACHE_CONF_KEY', 'Memcache');
            $memConf = ZOL_Config::get(MEMCACHE_CONF_KEY);
            if ($memConf) {
                self::$servers = $memConf;
            }
			foreach (self::$servers as $val)
			{
				$exp = explode(':', $val);
				self::$mem->addServer($exp[0], $exp[1]);
			}
		}
	}
	public static function flush()
	{
		self::init();

		return self::$mem->flush();
	}
	public static function get($key)
	{
		self::init();

		return self::$mem->get($key);
	}
	public static function delete($key)
	{
		self::init();

		return self::$mem->delete($key);
	}
	public static function set($key = '', $var = '', $expire = 3600)
	{
		self::init();

		return self::$mem->set($key, $var, 0, $expire);
	}
	public static function add($key = '', $var = '', $expire = 3600)
	{
		self::init();

		return self::$mem->add($key, $var, 0, $expire);
	}
}

