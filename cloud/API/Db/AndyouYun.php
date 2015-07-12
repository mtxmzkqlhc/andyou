<?php
/**
 * ÔÆ¶Ë·şÎñÆ÷
 */
class API_Db_AndyouYun extends API_Db_Abstract_Pdo 
{
	protected $servers   = array(
        'username' => 'luobo525',
        'password' => 'abcd9814',
		'master' => array(
			'host' => '182.92.5.73',
			'database' => 'andyou_master',
		 ),
		 'slave' => array(
			'host' => '182.92.5.73',
			'database' => 'andyou_master',
		 ),
	);    	
}
