<?php

class MonitorModule extends CWebModule
{
	public $queue;

	public function init()
    {
		$this->queue = true;
        parent::init();
		if(!defined('LOCAL'))
			header('Location: '.SITE_PATH);
        $this->setImport(array(
            'monitor.models.*',
            'monitor.components.*',
            'monitor.extensions.*',
            'monitor.helpers.*'
        ));
    }
	
	public static function init_from_main()
	{
		if(!defined('LOCAL'))
			exit;
		Yii::import('application.modules.monitor.models.Stations');
		Yii::import('application.modules.monitor.extensions.Weather');

	}
}

?>