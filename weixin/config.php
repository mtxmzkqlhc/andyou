<?php

defined('ZOL_API_ISFW') || define('ZOL_API_ISFW', false);//�Ƿ�ʹ��ZOL�¿�ܣ�trueΪʹ��
$path = dirname(dirname(__FILE__)) . "/cloud/";
require($path . 'Api.php'); //����˽��������ļ�

#΢���õ�
define("TOKEN", "547381");
define("ZOLSTEPKEY", "1f7QGGgjuzZ7Ykc0u3Njy7F0jayVM4cl3HehZ29myPF");
define('AppID',"wx6e8f6c570d574f04");
define('AppSecret',"3cc19133a98b5dd657b53888f51eb713");

$db = API_Db_AndyouYun::instance();


