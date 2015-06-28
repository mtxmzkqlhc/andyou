<?php
/**
 * ÔÆ¶Ë·şÎñÆ÷
 */
class Db_AndyouYun extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
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
