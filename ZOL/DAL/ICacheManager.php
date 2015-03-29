<?php
/**
* �������ӿ�
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-6-23
*/

interface ZOL_DAL_ICacheManager
{
	/**
	* ��ȡ����ģ�����
	*/
	public function getCacheModuleObj($moduleName);
	
	/**
	* ��ȡ�������ݶ���
	*/
	public function getCacheObject($moduleName, $cacheParam = null, $num = 0);
	
	/**
	* ˢ�»������
	*/
	public function refreshCacheObject($moduleName, $param = null);
	
	/**
	* ����������
	*/
	public function removeCacheObject($moduleName, $cacheParam = null);
}