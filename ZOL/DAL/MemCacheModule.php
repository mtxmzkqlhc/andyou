<?php
/**
* ������
* Memcache���������ز���
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

abstract class ZOL_DAL_MemCacheModule extends ZOL_DAL_FileCacheModule
{	
	protected $_cacheParam;
	
	protected $_cacheKey;
	/**
	* ��ʼ�����������ģ����;
	*/
	public function processParam($cacheParam = null)
	{
		if ($cacheParam === null || $this->_cacheParam === $cacheParam) {
			return $this;
		}
		
		$moduleName = get_class($this);
		if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			#���������ļ���ȡĬ��KEYMAKER
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'MEM');
			$keyMaker = new $keyMakerName($moduleName, (array)$cacheParam);
		} else {
			$keyMaker = &$cacheParam;
			$keyMaker->setModuleName($moduleName);
		}
		
		$this->_cacheParam = $keyMaker->getCacheParam();
		$this->_cacheKey   = $keyMaker->getCacheKey();
		return $this;
	}
	
	/**
	* ��ȡMemCache����
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
		
		$data = ZOL_Caching_Memcache::get($this->_cacheKey);
		if ($data) {
			$this->_cachePool[$this->_cacheKey] = $data;
		} elseif ($this->_autoRefresh) {#�Զ����»���
			if ($this->refresh($this->_cacheParam)) {
				#���»�ȡ��������
				$data = $this->_content;
			}
		}
		
		return $data;
	}
	
	/**
	* ����MemCache����
	*/
	public function set($cacheParam = '', $content = '')
	{
		$this->processParam($cacheParam);
		
		$this->_content = $content ? $content : $this->_content;
		
		if (empty($this->_cacheKey) || empty($this->_content)) {
			return false;
		}
		
		$expire = $this->_isDuly 
				? ($this->_expire - (SYSTEM_TIME % $this->_expire)) 
				: $this->_expire;
		
		return ZOL_Caching_Memcache::set($this->_cacheKey, $this->_content, $expire);
	}
	
	/**
	* ɾ��MemCache����
	*/
	public function rm($cacheParam = '')
	{
		$this->processParam($cacheParam);
		
		if (empty($this->_cacheKey)) {
			return false;
		}
		return ZOL_Caching_Memcache::delete($this->_cacheKey);
	}
}
