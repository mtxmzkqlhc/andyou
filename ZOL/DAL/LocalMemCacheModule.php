<?php
/**
* �ڴ��̻���ģ�� �̳����ļ�����
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-9-27
*/

abstract class ZOL_DAL_LocalMemCacheModule extends ZOL_DAL_FileCacheModule
{
	/**
	* ��ʼ�����������ģ����;
	*/
	public function processParam($cacheParam = array())
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		$moduleName = get_class($this);
		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			#���������ļ���ȡĬ��KEYMAKER
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'LOCALMEM');
			$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		#���û���洢����
		$keyMaker->setCacheSaveType($this->_cacheSaveType);
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cachePath  = $keyMaker->getCacheKey();
		
		return $this;
	}
}