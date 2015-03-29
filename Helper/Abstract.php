<?php
/**
* ���������
* @author zhongwt
* @copyright (c) 2009-9-20
*/
abstract class Helper_Abstract
{
	/**
	* @var ZOL_Product_Caching_GetCacheLoader
	*/
	protected static $cache;	
	
	
	/**
	* ���ػ�������
	*/
	protected static function loadCache($moduleName, $param = array(), $num = 0)
	{
		if(!self::$cache) self::$cache = ZOL_DAL_RefreshCacheLoader::getInstance();
		$data = self::$cache->loadCacheObject($moduleName, $param);
		
		if ($num && $data && count($data) > $num) {
			$data = array_slice($data, 0, $num, true);
		}
		
		return $data;
	}

    /**
     * ����kv ��Ӧ����
     */ 
    public  static  function  getKv($data,$key,$main = false){  
            if(empty($data)){
                return  false;
            }
            $outData = array();
            foreach ($data  as $value){
                  if($main){
                      $outData[$value[$key]] = $value[$main];
                  }else{
                      $outData[$value[$key]] = $value;
                  }
            }
            return $outData;
    }
    

}
