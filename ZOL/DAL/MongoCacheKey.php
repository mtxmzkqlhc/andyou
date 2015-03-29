<?php
/**
* MongoCache�������ȡ��
* ��Ҫ���ܣ������������������Ϊ����
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

class ZOL_DAL_MongoCacheKey extends ZOL_DAL_FileCacheKey
{
	/**
	* ��ȡ�������
	*/
	public function getCacheKey()
	{
		if ($this->checkCacheParam($this->param)) {
			return $this->makeCacheKey();
		} else {
			return false;
		}
	}

	
	/**
	* �����ֵ˳�򣬲�����KEY
	* ע�⣡����������µ�KEY��WAP�����ã���֪ͨ����
	* @return string
	*/
	protected function makeCacheKey()
	{
		/*$key = array();
        if (!is_array($this->param)) {
            return false;
        }
		foreach (self::$paramNames as $name => $type) {
			if (!isset($this->param[$name])) {
				continue;
			}
			$key[$name] = $type($this->param[$name]);
		}
		$key['moduleName'] = $this->moduleName;
		return md5(http_build_query($key));
        */
		$key = array();
		foreach (self::$_keyNames as $name => $type) {
			if (!isset($this->param[$name])) {
				continue;
			}
			$key[$name] = $type($this->param[$name]);
		}
		$key['moduleName'] = $this->moduleName;
		return md5(http_build_query($key));
	}
}