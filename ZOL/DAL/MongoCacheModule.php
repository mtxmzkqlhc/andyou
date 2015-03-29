<?php

/**
 * 抽象类
 * Memcache缓存类别相关操作
 * @author wiki <wu.kun@zol.com.cn>
 * @copyright (c) 2009-6-23
 */
abstract class ZOL_DAL_MongoCacheModule extends ZOL_DAL_FileCacheModule {

    /**
     * 是否散列存储
     * @var bool 
     */
    protected $_hash = false;
    protected $_expire = 0;
    protected $_mongoServerKey = 0; #mongodb服务器key
    protected $_isMongoWrite = 0; #mongodb是否是写数据
    protected $_mongoDbName  = 'Product'; #db库名称
    /**
     * 初始化处理参数和模块名;
     */

    public function processParam($cacheParam = '') {

        $moduleName = str_replace("Modules_", "",get_class($this));#为了与旧框架缓存系统兼容

        $this->_moduleName = $moduleName;
        if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'MONGO');
            $keyMaker = new $keyMakerName($this->_moduleName, (array) $cacheParam);
        } else {
            $keyMaker = &$cacheParam;
            $keyMaker->setModuleName($this->_moduleName);
        }
        $this->_cacheParam = $keyMaker->getCacheParam();
        $this->_cacheKey = $keyMaker->getCacheKey();
        return $this;
    }

    /**
     * 获取MemCache缓存
     * 可被重写
     * @return mixed
     */
    public function get($cacheParam = null) {

        if ($cacheParam !== null && $this->_cacheParam !== $cacheParam) {
            $this->processParam($cacheParam);
        }
        #返回缓存数据
        if (!empty($this->_cachePool[$this->_cacheKey])) {
            return $this->_cachePool[$this->_cacheKey];
        }
        $modName = $this->_moduleName;
        $modName .= $this->_hash ? ('.' . $this->_cacheKey[0]) : '';        
        $data = ZOL_Caching_Mongo::get($modName, $this->_cacheKey, false, $this->_mongoServerKey,0,$this->_mongoDbName);
        
		#添加日志功能
		if(IS_DEBUGGING){
			$nowTime    = date("H:i:s");
			$nowUrl     = str_replace("_check_mongo_read=", "", $_SERVER["REQUEST_URI"]);
			$logContent = "{$nowUrl} [{$nowTime}] CacheRead:{$modName} Param:".json_encode($cacheParam) . "\n";
			ZOL_Log::checkUriAndWrite(array('message'=>$logContent , 'paramName'=>'_check_mongo_read'));
		}


        if (isset($data['date']) && isset($data['exprieTime'])) {
           
            $nowTime = date("Y-m-d H:i:s");
            $expreTime = strtotime($nowTime) - strtotime($data['date']);
            if ($expreTime >= $data['exprieTime']) {
                $data = false;
            }
            
        }

        if (!$data && $this->_autoRefresh && $this->refresh($this->_cacheParam)) {#自动更新缓存
            $data = $this->_content;
        }
        
        $this->_cachePool[$this->_cacheKey] = $data;
        return $data;
    }

    /**
     * 设置MemCache缓存
     */
    public function set($cacheParam = null, $content = '') {
    
        if (isset($cacheParam) && $this->_cacheParam != $cacheParam) {
            $this->processParam($cacheParam);
        }
        //$this->getRandExpire();

        if ($content && is_array($content)) {
            $content = self::arrayFilter($content);
        }
       
        $this->_content = $content;
        
        if (empty($this->_cacheKey)) {
            return false;
        }

        if (empty($this->_content)) {
            $this->rm();
            return false;
        }
        
        $modName = $this->_moduleName;
        $modName .= $this->_hash ? ('.' . $this->_cacheKey[0]) : '';
       
        $expire = $this->_isDuly ? ($this->_expire - (SYSTEM_TIME % $this->_expire)) : $this->_expire;
        
        return ZOL_Caching_Mongo::set($modName, $this->_cacheKey, $this->_content, $expire, $this->_mongoServerKey, $this->_isMongoWrite,$this->_mongoDbName);
    }

    /**
     * 删除MemCache缓存
     */
    public function rm($cacheParam = null) {
        if (isset($cacheParam) && $this->_cacheParam != $cacheParam) {
            $this->processParam($cacheParam);
        }

        if (empty($this->_cacheKey)) {
            return false;
        }

        $modName = $this->_moduleName;
        $modName .= $this->_hash ? ('.' . $this->_cacheKey[0]) : '';
        return ZOL_Caching_Mongo::delete($modName, $this->_cacheKey,$this->_mongoServerKey,1,$this->_mongoDbName);
    }

}
