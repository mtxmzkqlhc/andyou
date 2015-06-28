<?php

class Db_Andyou extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => '127.0.0.1',
			'database' => 'andyou',
		 ),
		 'slave' => array(
			'host' => '127.0.0.1',
			'database' => 'andyou',
		 ),
	);    	
}
