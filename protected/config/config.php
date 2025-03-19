<?php

define('SITE_PATH','http://climatebase.vit/');
define('DB_HOST','localhost');
define('LOCAL',TRUE);
define('BASE_PREFIX','');

$config_for_main=array(
			'connectionString' => 'mysql:host='.DB_HOST.';dbname=new_climatebase',
			'emulatePrepare' => true,
			'username' => 'vitaliy',
			'password' => 'vitaliy43',
			'charset' => 'utf8'
		);
	

?>