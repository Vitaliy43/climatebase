<?php

class MonitorController extends CController
{
	
	
	protected function beforeAction($action)
	{
		$this->layout='//layouts/monitor';
		$this->pageTitle='Климатический монитор';
		ini_set('max_execution_time','60');

		return true;

	}
}

?>