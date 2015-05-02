<?php
//产品配置
define('IN_PRODUCTION', true);
define('PRODUCTION_ROOT', dirname(dirname(dirname(__FILE__))));
define('SYSTEM_VAR', PRODUCTION_ROOT . '/var/');
//应用配置
define('APP_NAME', 'Andyou'); // 配置是哪个实例
define('APP_PATH', PRODUCTION_ROOT . '/App/' . APP_NAME); // 配置实例的APP路径

//数据库配置
define('DB_USERNAME','root');
define('DB_PASSWORD','abcd9814');


//DAL配置
define('DAL_CACHE_DIR', SYSTEM_VAR .'cache_data/');#缓存目录
define('DAL_LOCALMEM_CACHE_DIR', DAL_CACHE_DIR .'tmpfs/');#内存缓存目录
define('DAL_CACHE_MODULES_DIR', PRODUCTION_ROOT . '/Modules');#数据模块目录
define('DAL_DIR', PRODUCTION_ROOT . '/DAL');#DAL实例目录
define('DAL_CACHE_SAVE_TYPE', 'SERIALIZE');#缓存类型

defined('SYSTEM_HOST') || define('SYSTEM_HOST', '127.0.0.1');  #如果没有定义SYSTEM_HOST常量，定义SYSTEM_HOST常量

// 调试模式
define('IS_DEBUGGING' , $_SERVER['SERVER_NAME'] != SYSTEM_HOST);
// 生产状态
define('IS_PRODUCTION', $_SERVER['SERVER_NAME'] == SYSTEM_HOST);

if(IS_DEBUGGING){
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
error_reporting(0);
ini_set("display_errors", 0);
/**
 * json的相关处理
 */
function api_json_encode($value){
  array_walk_recursive($value, "api_json_convert_encoding_g2u");
  return json_encode($value);
}
function api_json_decode($value, $assoc = true){
  $value = json_decode($value,$assoc);
  array_walk_recursive($value, "api_json_convert_encoding_u2g");
  return $value;
}
function api_json_convert_encoding_g2u(&$value, &$key){
  $value = mb_convert_encoding($value, "UTF-8", "GBK");
}
function api_json_convert_encoding_u2g(&$value, &$key){
  $value = mb_convert_encoding($value, "GBK", "UTF-8");
}
//define('ZOL_API_ISFW', true);#应用ZOL框架的项目
//define('ZOL_API_STOPLOG', true);#应用ZOL框架的项目
//require_once('/www/zdata/Api.php'); #API
require_once(PRODUCTION_ROOT . '/init.php');

ZOL::setNameSpace(PRODUCTION_ROOT . '/Libs');#注册类库
ZOL::setNameSpace(PRODUCTION_ROOT . '/Modules');#注册缓存模块
ZOL::setNameSpace(APP_PATH);#注册应用
ZOL::setNameSpace(PRODUCTION_ROOT . '/Db');#注册数据库链接类
ZOL::setNameSpace(PRODUCTION_ROOT . '/DAL');#注册DAL实例
ZOL::setNameSpace(PRODUCTION_ROOT . '/Helper');#注册Helper实例
ZOL::setNameSpace(PRODUCTION_ROOT . '/App/Pro');#注册
ZOL::setNameSpace(PRODUCTION_ROOT . '/App/Pro/Plugin');#注册

ZOL_Controller_Front::run();