<?php
/**
 *  Gearman ����ϵͳ
 *  ��ΰ�� 2012-5
 */
class API_Gearman{
    
    protected static $serverArr = array("10.15.184.148:4730");
    protected static $client       = false;

    /**
     * ��ʼ������
     */
    protected static function init($snId=0){

        if (!self::$client){
            if (class_exists("GearmanClient")) {
                self::$client = new GearmanClient();
                foreach(self::$serverArr as $v){
                    self::$client->addServers($v);
                }
            } else {
                die("Gearman �ӿ�ģ�鲻����");
            }
        }

    }

    /**
     * ִ������
     */
    public static function doNormal($paramArr) {
		$options = array(
			'taskName'          => '', #������
			'taskContent'       => '', #��������
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        self::init();

        return self::$client->doNormal($taskName, $taskContent);

    }
}
