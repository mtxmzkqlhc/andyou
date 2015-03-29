<?php
/**
 * ZOL内部API配置文件
 * 仲伟涛
 * 2012-6-26
 */
if (!defined('IN_ZOL_API')) die('Hacking attempt');

#引用项目是否是框架项目
defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', true);

#数据库账户信息
defined('DB_USERNAME')  || define('DB_USERNAME', 'pro_admin');
defined('DB_PASSWORD')  || define('DB_PASSWORD', '3c2d4c41');


?>