<?php

class CommonController extends MonitorController
{
	
	public function actionIndex()
	{
		$methods=ClimateMonitor::$methods;
		$methods['all']='Все';
		$buffer_methods=FormHelper::form_dropdown('parameters',$methods,'all',' onchange="set_parameter();return false;"');
		$this->render('index',array('methods'=>$buffer_methods));
		
	}
	
	public function actionChange()
	{
		if(isset($_REQUEST['type']))
			$type=$_REQUEST['type'];
		else
			$type='';
		$result='';
		if(isset($_REQUEST['update']) and isset($_REQUEST['new_ind'])){
			$res=Structure::model()->changeIndex($_REQUEST['old_ind'],$_REQUEST['new_ind']);
			if($res)
				$result='Индекс станции успешно сменен';
			else
				$result='Ошибка смены индекса';
		}
		
		$this->render('change',array('type'=>$type,'result'=>$result));
	}
	
	public function actionCopyConsolidated(){
		ClimateMonitor::copyFromOldBase();
	}	
	public function actionAutocompleteData()
	{
		$message=ClimateMonitor::createAutocompleteData();
		echo $message;
	}
	
	protected function beforeAction($action)
	{
		parent::beforeAction($action);
		return true;
	}
}

?>