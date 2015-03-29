<?php
/**
* ����ģ�����
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
class ZOL_DAL_CacheModuleManager
{
	private static $instance;
	private $cacheModules;
	private static $cacheModulesListFile;
	
	public function __construct()
	{
		$this->_loadCacheModules();
	}
	
	/**
	* ����ģʽ
	* @return ZOL_DAL_CacheModules
	*/
	public static function getInstance()
	{
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	* ����ģ���б�����
	*/
	public function getCacheModules()
	{
		return $this->cacheModules;
	}
	
	/**
	* ����ģ���б���
	*/
	private function _loadCacheModules()
	{
		$dalDir = defined('DAL_DIR')
				  ? DAL_DIR
				  : dirname(__FILE__);
		self::$cacheModulesListFile = $dalDir . '/modules.lst';
		if (file_exists(self::$cacheModulesListFile)) {
			$this->cacheModules = unserialize(file_get_contents(self::$cacheModulesListFile));
		} else {
			$this->cacheModules = array();
			//throw new ZOL_Exception('Cache Modules List File "' . self::$cacheModulesListFile . '" does not exist!');
		}
	}
	
	/**
	* ����ģ��
	*/
	private function saveCacheModules()
	{
		if (!$this->cacheModules) {
			throw new ZOL_Exception('The cacheModules is empty!');
		}
		$content = serialize($this->cacheModules);
		return ZOL_File::write($content, self::$cacheModulesListFile);
	}
	
	/**
	* ���ģ���Ƿ����
	* @param string $moduleName ģ������
	* @return boolean
	*/
	public function checkModuleExist($moduleName)
	{
		if (array_key_exists($moduleName, $this->cacheModules)) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	* ע��ģ��
	* @param string $moduleName ģ������
	* @param boolean $rewrite �Ƿ񸲸��Ѿ�ע�������ͬģ��
	*/
	public function registerModule($moduleName, $rewrite = false)
	{
		if (!$moduleName) {
			throw new ZOL_Exception('Modulename can not for empty!');
		}
		
		if (strpos($moduleName, ZOL_DAL_Config::CACHE_MODULES_NAMESPACE) !== 0) {
			$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		}
		
		if (!class_exists($moduleName)) {
			throw new ZOL_Exception('The moduleCacheClass does not exist!');
		}
		
		if ($this->checkModuleExist($moduleName) && !$rewrite) {
			throw new ZOL_Exception('This Module "' . $moduleName . '" has existed!');
		}

		$module = new $moduleName();
		
		if (!($module instanceof ZOL_DAL_ICacheModule)) {
			throw new ZOL_Exception('This Module "' . $moduleName . '" must be an instance of ZOL_DAL_CacheModule!');
		}
		
		$moduleInfo = array(
			'name'   => $moduleName,//ģ����
			'depend' => $module->getDepend(),//ģ������
			'expire' => $module->getExpire(),//��������
		);

		$this->cacheModules[$moduleName] = $moduleInfo;
		
		return $this->saveCacheModules();
	}
	
	/**
	* ע������ģ�� ��ʱ�������� �����޷����ݲ�ͬʵ��ע��,ֻ��ͨ��Ӧ����ڵ����ý���ע��
	*/
	public function registerAllModule()
	{
		$cacheModulesDir = defined('CACHE_MODULES_DIR')
						 ? DAL_CACHE_MODULES_DIR
						 : ZOL_DAL_Config::CACHE_MODULES_DIR;
		
		$modulesFiles = glob($cacheModulesDir . '/*.php');
		if ($modulesFiles) {
			unset($this->cacheModules);
			foreach ($modulesFiles as $module) {
				$pathInfo = pathinfo($module);
				$moduleName = substr($pathInfo['basename'], 0, strpos($pathInfo['basename'], '.'));
				$this->registerModule($moduleName, true);
			}
		}
		return true;
	}
	
	/**
	* ɾ��ģ��
	*/
	public function removeModule($moduleName)
	{
		if (!$moduleName) {
			throw new ZOL_Exception('Modulename can not for empty!');
		}
		$moduleName = ZOL_DAL_Config::CACHE_MODULES_NAMESPACE . $moduleName;
		
		if (!$this->checkModuleExist($moduleName)) {
			throw new ZOL_Exception('The Modulename does not exist!');
		}
		unset($this->cacheModules[$moduleName]);
		return $this->saveCacheModules();
	}
}
