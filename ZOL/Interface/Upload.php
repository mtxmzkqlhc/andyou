<?php
/**
* 
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) �ϴ�����
* @version v1.0
*/

interface ZOL_Interface_Upload
{
	/**
	* ��ȡ�ļ�·����Ϣ
	* 
	* @param array $file �ļ���Ϣ
	* @return array($path, $thumbPath);
	*/
	public function save(array $file);
	public function rm($path);
}
