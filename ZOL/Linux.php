<?php
/**
 * Linux相关处理
 * @author 仲伟涛 2012-02
 */
class ZOL_Linux {

	/**
	 * 执行命令
	 */
    public static function run($cmd) {
		#return `$cmd 2>&1`;
		echo $cmd;
		system($cmd, $return);
		return $return;
    }


}
