<?php
/**
* 本地的文件缓存
* 防止数据请求的过于频繁，将数据存到api调用的服务器上
* @author 仲伟涛
* @copyright (c) 2012-7-14
*/

class API_FileCache {

    public static function getCachePath($cacheKey){
        return ZOL_API_ROOT . "/Cache/" . chunk_split(substr($cacheKey, 0, 6), 2, '/') . $cacheKey . ".apich";
    }

    /**
	* 判断文件是否过期 true：过期，false：未过期
	*/
	public static function isOld($filePath,$expire){

        if(!$expire)return true;
        if(!file_exists($filePath))return true;

        $expire = $expire +  SYSTEM_TIME % 600; #加一个随机因子，防止缓存同时失效的雪崩
		return (filemtime($filePath) + $expire) < SYSTEM_TIME;
	}

    /**
	* 写入文件缓存
	*/
	public static function set($cacheKey,$data){

        if(!$data || !$cacheKey)return false;

        #获得缓存文件的地址
        $filePath = self::getCachePath($cacheKey);
		ZOL_File::write($data, $filePath);
		return false;
	}


	/**
	* 获取文件缓存
	*/
	public static function get($cacheKey,$expire){

        #获得缓存文件的地址
        $filePath = self::getCachePath($cacheKey);
        #判断缓存文件是否失效，失效，返回空
        if(self::isOld($filePath,$expire)){
            return false;
        }
        #返回缓存文件内容
		return file_get_contents($filePath);
	}
}
