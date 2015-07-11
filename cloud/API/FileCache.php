<?php
/**
* ���ص��ļ�����
* ��ֹ��������Ĺ���Ƶ���������ݴ浽api���õķ�������
* @author ��ΰ��
* @copyright (c) 2012-7-14
*/

class API_FileCache {

    public static function getCachePath($cacheKey){
        return ZOL_API_ROOT . "/Cache/" . chunk_split(substr($cacheKey, 0, 6), 2, '/') . $cacheKey . ".apich";
    }

    /**
	* �ж��ļ��Ƿ���� true�����ڣ�false��δ����
	*/
	public static function isOld($filePath,$expire){

        if(!$expire)return true;
        if(!file_exists($filePath))return true;

        $expire = $expire +  SYSTEM_TIME % 600; #��һ��������ӣ���ֹ����ͬʱʧЧ��ѩ��
		return (filemtime($filePath) + $expire) < SYSTEM_TIME;
	}

    /**
	* д���ļ�����
	*/
	public static function set($cacheKey,$data){

        if(!$data || !$cacheKey)return false;

        #��û����ļ��ĵ�ַ
        $filePath = self::getCachePath($cacheKey);
		ZOL_File::write($data, $filePath);
		return false;
	}


	/**
	* ��ȡ�ļ�����
	*/
	public static function get($cacheKey,$expire){

        #��û����ļ��ĵ�ַ
        $filePath = self::getCachePath($cacheKey);
        #�жϻ����ļ��Ƿ�ʧЧ��ʧЧ�����ؿ�
        if(self::isOld($filePath,$expire)){
            return false;
        }
        #���ػ����ļ�����
		return file_get_contents($filePath);
	}
}
