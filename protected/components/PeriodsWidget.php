<?php

class PeriodsWidget extends CWidget
{
	public $station;
	public $period_id;
	public $verifiable_table='average_temperature';

	public function run()
	{
		$periods=Climate::$available_periods;
		$this->render('periods',array('periods'=>$periods));
	}
	
	
}

?>