<?php

class WeatherController extends MonitorController
{

	public function actionSet()
	{
		$message=ClimateMonitor::setClimate($_REQUEST['period']);		
		$this->render('set',array('result'=>$message));
	}
    
    public function actionCorrectData()
    {
        ClimateMonitor::correctDataWithZero();
    }
    
    public function actionCorrectLowest()
    {
        $list_for_changes = ClimateMonitor::getListLowestWithZero();
        $this->render('list',array('list'=>$list_for_changes));
    }
	
	public function actionSetCloudness()
	{
		$message=ClimateMonitor::setMeteoCloudness();
		$this->render('set',array('result'=>$message));
	}
	
	public function actionSunHours()
	{
		$message=ClimateMonitor::setSunHours();
		$this->render('set',array('result'=>$message));

	}
	
	protected function beforeAction($action)
	{
		parent::beforeAction($action);
		return true;
	}
}

?>