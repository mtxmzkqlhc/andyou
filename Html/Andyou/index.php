<?php
//��Ʒ����
define('IN_PRODUCTION', true);
define('PRODUCTION_ROOT', dirname(dirname(dirname(__FILE__))));
define('SYSTEM_VAR', PRODUCTION_ROOT . '/var/');
//Ӧ������
define('APP_NAME', 'Andyou'); // �������ĸ�ʵ��
define('APP_PATH', PRODUCTION_ROOT . '/App/' . APP_NAME); // ����ʵ����APP·��

//���ݿ�����
define('DB_USERNAME','root');
define('DB_PASSWORD','abcd9814');


//DAL����
define('DAL_CACHE_DIR', SYSTEM_VAR .'cache_data/');#����Ŀ¼
define('DAL_LOCALMEM_CACHE_DIR', DAL_CACHE_DIR .'tmpfs/');#�ڴ滺��Ŀ¼
define('DAL_CACHE_MODULES_DIR', PRODUCTION_ROOT . '/Modules');#����ģ��Ŀ¼
define('DAL_DIR', PRODUCTION_ROOT . '/DAL');#DALʵ��Ŀ¼
define('DAL_CACHE_SAVE_TYPE', 'SERIALIZE');#��������

defined('SYSTEM_HOST') || define('SYSTEM_HOST', '127.0.0.1');  #���û�ж���SYSTEM_HOST����������SYSTEM_HOST����

// ����ģʽ
define('IS_DEBUGGING' , $_SERVER['SERVER_NAME'] != SYSTEM_HOST);
// ����״̬
define('IS_PRODUCTION', $_SERVER['SERVER_NAME'] == SYSTEM_HOST);

if(IS_DEBUGGING){
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
error_reporting(0);
ini_set("display_errors", 0);
/**
 * json����ش���
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
//define('ZOL_API_ISFW', true);#Ӧ��ZOL��ܵ���Ŀ
//define('ZOL_API_STOPLOG', true);#Ӧ��ZOL��ܵ���Ŀ
//require_once('/www/zdata/Api.php'); #API
require_once(PRODUCTION_ROOT . '/init.php');

ZOL::setNameSpace(PRODUCTION_ROOT . '/Libs');#ע�����
ZOL::setNameSpace(PRODUCTION_ROOT . '/Modules');#ע�Ỻ��ģ��
ZOL::setNameSpace(APP_PATH);#ע��Ӧ��
ZOL::setNameSpace(PRODUCTION_ROOT . '/Db');#ע�����ݿ�������
ZOL::setNameSpace(PRODUCTION_ROOT . '/DAL');#ע��DALʵ��
ZOL::setNameSpace(PRODUCTION_ROOT . '/Helper');#ע��Helperʵ��
ZOL::setNameSpace(PRODUCTION_ROOT . '/App/Pro');#ע��
ZOL::setNameSpace(PRODUCTION_ROOT . '/App/Pro/Plugin');#ע��

ZOL_Controller_Front::run();