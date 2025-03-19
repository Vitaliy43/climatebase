<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.


define('SITE_NAME','Climatebase.vit');
define('AJAX_LINK',TRUE);
require_once('config.php');

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Архив климатических данных',
	'theme'=>'waterfalls',
	'charset'=>'utf-8',
	'language'=>'ru',
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',
		'application.vendors.tabgeo.Tabgeo'
	),

	'modules'=>array(
		'monitor'=>array(
			'class'=>'application.modules.monitor.MonitorModule'

		)
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'showScriptName'=>FALSE,
			'class'=>'application.components.CustomUrlManager',
				
			'rules'=>array(
				'/'=>'climate/index',
				'countries/<continent:\D+>'=>'climate/states',
				'regions/<state:\D+>'=>'climate/regions',
				'stations/<state:\D+>'=>'climate/stations',
				'stations/<state:\D+>/<region:\D+>'=>'climate/stations',
				'station/<station:\d+>'=>'climate/data',
				'station/delete/<station:\d+>'=>'climate/delete',
				'station/getold/<station:\d+>'=>'climate/getold',
				'station/<station:\d+>/<period:(from\d{4}|\d{2}_\d{2})>'=>'climate/data',
    			
			),
			
		
		),
		

		'db'=>$config_for_main,

		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);
