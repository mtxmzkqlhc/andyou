<?php
/**
 * Linux��ش���
 * @author ��ΰ�� 2012-02
 */
class ZOL_Linux {

	/**
	 * ִ������
	 */
    public static function run($cmd) {
		#return `$cmd 2>&1`;
		echo $cmd;
		system($cmd, $return);
		return $return;
    }


}
