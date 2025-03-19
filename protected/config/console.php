<?php
$db_host = '127.0.0.1';


// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',
	// application components
	'components'=>array(
    /*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=new_climatebase',
		),
        */
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host='.$db_host.';dbname=new_climatebase',
			'emulatePrepare' => true,
			'username' => 'vitaliy',
			'password' => 'vitaliy43',
			'charset' => 'utf8',
		),
		
	),
    
    'modules'=>array(
		'monitor'=>array(
			'class'=>'application.modules.monitor.MonitorModule'

		)
	),
    // autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.helpers.*',
		'application.extensions.*',
        'application.modules.monitor.models.*',
		'application.modules.monitor.components.*',
		'application.modules.monitor.extnsions.*',
	),
    'commandMap' => array(
        'meteo' => array(
            'class' => 'application.modules.monitor.commands.MeteoCommand',
        ),
        'ogimet' => array(
            'class' => 'application.modules.monitor.commands.OgimetCommand',
        ),
	'rp5' => array(
            'class' => 'application.modules.monitor.commands.Rp5Command',
        ),
	'wiki' => array(
            'class' => 'application.modules.monitor.commands.WikiCommand',
        ),
		
    ),
);