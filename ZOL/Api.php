<?php
/**
 * ZOL�ڲ�API����ļ�
 * ��ΰ��
 * 2012-6-26
 */
if(function_exists("__autoload")){
    die("[ZOL_API]__autoload��ͻ�����޸�Ϊspl_autoload_register��ʽ��");
}

define('IN_ZOL_API', true);

//API��Ŀ¼
defined('ZOL_API_BASE') || define('ZOL_API_BASE', dirname(__FILE__) );
defined('ZOL_API_ROOT') || define('ZOL_API_ROOT', ZOL_API_BASE . '/API');
defined('ZOL_API_UTF8') || define('ZOL_API_UTF8', false);
defined('ZOL_API_DEBUG') || define('ZOL_API_DEBUG', false);
defined('ZOL_API_LOG') || define('ZOL_API_LOG', ZOL_API_ROOT . "/Log");
if(!ZOL_API_ISFW){
    defined('SYSTEM_TIME') || define('SYSTEM_TIME', isset ( $_SERVER ['REQUEST_TIME'] ) ? $_SERVER ['REQUEST_TIME'] : time ());
    defined('SYSTEM_DATE') || define('SYSTEM_DATE', date ( 'Y-m-d H:i:s', SYSTEM_TIME ));
    defined('IS_DEBUGGING') || define('IS_DEBUGGING', false);
}
//���������ļ�
require_once(ZOL_API_BASE . '/ApiConfig.php');


spl_autoload_register(array('ZOL_Api', 'autoload'));
if(!ZOL_API_ISFW ){ //�������ZOL��ܣ������Զ�����,ģ��һ�¿�ܵ�����ļ�
    // ��ZOL_Api���Զ����ذ�������
    foreach (array('Db','ZOL') as $nv) {
        ZOL_Api::setNameSpace(ZOL_API_ROOT . '/' . $nv);
    }
    
}

if (!function_exists('get_called_class')){
    function get_called_class()    {
        $bt = debug_backtrace();
        $lines = file($bt[1]['file']);
        preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/',
               $lines[$bt[1]['line']-1],
               $matches);
        return $matches[1];
    }
}
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

class ZOL_Api
{
    private static $_namespace = array();

    /**
     * �Զ�����
     */
    public static function autoload($name)
    {
        if (trim($name) == '') {
            new exception('No class or interface named for loading');
        }

        if (class_exists($name, false) || interface_exists($name, false)) {
            return;
        }

        $namespace = substr($name, 0, strpos($name, '_'));
        if(ZOL_API_ISFW && in_array($namespace, array('Db','ZOL')) ){ #��ܵ��Զ����ؼ���
            return ;
        }
        $file = '';
        if ($namespace == 'API') {
            $file = ZOL_API_BASE . '/' . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
        }
        // �Ը��Ե������ռ�������
        elseif (isset(self::$_namespace[$namespace])){
            $file = self::$_namespace[$namespace] . '/' . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
        }
        if($file){
            include $file;

            if (! class_exists($name, false) && ! interface_exists($name, false)) {
                throw new ZOL_Exception('Class or interface does not exist in loaded file');
            }
        }
    }
    
    /**
     * ʹ��namespace����ʵ��ÿ��ʵ���������ռ�ӳ��
     */
    public static function setNameSpace($path)
    {
        if (empty($path)) {
            new exception('No class or interface named for loading');
        }
        $namespace = substr(strrchr($path, '/'), 1);
        $namespacePath = substr($path, 0, strlen($path) - strlen($namespace) - 1);
        if (!isset(self::$_namespace[$namespace]) || self::$_namespace[$namespace] != $namespacePath) {
            self::$_namespace[$namespace] = $namespacePath;
        } else {
            throw new exception('Class or interface does not exist in loaded file');
        }
    }

    /**
     * ִ��API�ķ���
     */
    public static function run($method,$param=false){
        if(!$method)return false;
        
        $method = str_replace(".", "_", $method);
        $class = "API_Item_" . substr($method, 0,strrpos($method,"_"));
        $func  = substr($method, strrpos($method,"_")+1);
        $data  = call_user_func_array(array($class, $func), array($param));
        if(ZOL_API_UTF8 && $data){
            $data = self::toUTF8($data);
           # array_walk_recursive($data, "api_json_convert_encoding_g2u");
            return $data;

        }
        return $data;
    }


    /**
     * UTF8��ת��
     */
    private static function toUTF8($input){
		if(is_string($input)){
			return mb_convert_encoding($input, 'UTF-8', 'GBK');
		}elseif(is_array($input)){
			$output = array();
			foreach ($input as $k=>$v){
                $k          = self::toUTF8($k);
				$output[$k] = self::toUTF8($v);
			}
			return $output;
		}else{
            return $input;
        }
	}
}
