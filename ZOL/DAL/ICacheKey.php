<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/
interface ZOL_DAL_ICacheKey
{
	/**
	* ��ȡ�����,
	*/
	public function getCacheKey();
	public function getCacheParam();
	public function setKeyNames($moduleName);#���ò���
}
