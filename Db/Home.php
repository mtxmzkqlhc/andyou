<?php

class Db_Home extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => '127.0.0.1',
			'database' => 'home',
		 ),
		 'slave' => array(
			'host' => '127.0.0.1',
			'database' => 'home',
		 ),
	);    	
}
