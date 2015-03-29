<?php
/**
* �����ڴ滺��KEY������ �̳��� ZOL_DAL_FileCacheKey
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-9-27
*/

class ZOL_DAL_LocalMemCacheKey extends ZOL_DAL_FileCacheKey
{
	/**
	* ����KEY���컺��·��
	* @param string $key
	*/
	protected function makeCachePath($key)
	{
		if (empty($key)) {
			throw new Exception('The key is empty!');
		}
		
		$cacheDir = defined('DAL_LOCALMEM_CACHE_DIR')
				  ? DAL_LOCALMEM_CACHE_DIR 
				  : ZOL_DAL_Config::LOCALMEM_CACHE_DIR;
		
		$path = $cacheDir . $this->moduleName .
				'/' . $this->getCacheFileSubPath($key);
		return $path;
	}
}
