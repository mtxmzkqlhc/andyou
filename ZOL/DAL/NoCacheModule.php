<?php
/**
* û�л���ģ�� �̳����ļ�����
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2010-02-09
*/

abstract class ZOL_DAL_NoCacheModule extends ZOL_DAL_FileCacheModule
{	
	public function processParam($cacheParam = null)
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		
		#��ʼ��ʱ��
		$this->_startTime = microtime(true);
		
		$moduleName = get_class($this);

		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			static $keyMaker = null;
			if(!$keyMaker || !($keyMaker instanceof ZOL_DAL_ICacheKey)) {
				#���������ļ���ȡĬ��KEYMAKER
				$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'NO');
				$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
			} else {
				$keyMaker->setParam((array)$cacheParam);
			}
			
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		#���û���洢����
		$keyMaker->setCacheSaveType($this->_cacheSaveType);
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cacheKey   = $keyMaker->getCacheKey();
		return $this;
	}
	/**
	* ��ȡLuceneCache����
	* �ɱ���д
	* @return mixed
	*/
	public function get($cacheParam = null)
	{
		$this->processParam($cacheParam);
		
		#���ػ�������
		if (isset($this->_cachePool[$this->_cacheKey])) {
			return $this->_cachePool[$this->_cacheKey];
			
		}
		
		$this->refresh((array)$this->_cacheParam);
		$data = $this->_content;
		
		if ($data) {
			$this->_cachePool[$this->_cacheKey] = $data;
		}
		
		return $data;
	}
	
	/**
	* ���û���
	*/
	public function set($cacheParam = '', $content = null)
	{
		$this->_content = $content;
	}
	
	/**
	* ɾ������
	*/
	public function rm($cacheParam = '')
	{
		return ;
	}
	
	/**
	* �ļ�ͬ��
	*/
	private function fileSyn()
	{
		return ;
	}
}
