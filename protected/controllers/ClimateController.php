<?php


class ClimateController extends Controller
{
	protected $exceptions_before_action=array('stationpoint');
	
	public function actionIndex()
	{
		
		$this->pageTitle=Yii::app()->name.'. Главная страница';
		$continents=GeographyObjects::model()->get(Climate::$current_table);
		if(isset($_REQUEST['type'])){
				$response['content']=$this->renderPartial('index',array('continents'=>$continents),true);
				echo CJSON::encode($response);		
		}
		else{
			$this->render('index',array('continents'=>$continents));

		}
	}
	
	public function actionStates()
	{
	
		$this->pageTitle=Yii::app()->name.'. '.Climate::$object_name.' - страны';
		if(isset($_REQUEST['type'])){
			$response['title']=$this->pageTitle;
		}
		if(empty($_REQUEST['continent']))
			$this->redirect(SITE_PATH.'error404');	

		$continent=GeographyObjects::model()->getByName($_REQUEST['continent']);		 
		$states=GeographyObjects::model()->getStates($continent->id);
		if(count($states)>0):
			if(isset($_REQUEST['type'])){
				$response['content']=$this->renderPartial('states',array('states'=>$states,'continent'=>$continent),true);
				echo CJSON::encode($response);		
					}
			else{
				$this->render('states',array('states'=>$states,'continent'=>$continent));

			}
		else:
			$this->redirect(SITE_PATH.'error404');	
		endif;
		
	}
	
	
	public function actionStations()
	{
		$this->pageTitle=Yii::app()->name.'. '.Climate::$object_name;
		if(isset($_REQUEST['type'])){
			$response['title']=$this->pageTitle;
		}
		if(empty($_REQUEST['state']))
			$this->redirect(SITE_PATH.'error404');	
		if(isset($_REQUEST['region'])){
			$name=GeographyObjects::model()->getByName($_REQUEST['region'],'region');
			$stations=GeographyObjects::model()->getStations($name[0]['region'],'region');
		}
		else{
			$name=GeographyObjects::model()->getByName($_REQUEST['state'],'state');
			$stations=GeographyObjects::model()->getStations($name[0]['state']);

		}
		
		if(count($stations)>0):
			if(isset($_REQUEST['type'])){
				$response['content']=$this->renderPartial('stations',array('stations'=>$stations,'name'=>$name),true);
				echo CJSON::encode($response);		
					}
			else{
				$this->render('stations',array('stations'=>$stations,'name'=>$name));

			}
		else:

			$this->redirect(SITE_PATH.'error404');	
		endif;
		
	}
	
	public function actionGetOld()
	{
		
		if(!defined('LOCAL'))
			$this->redirect(SITE_PATH.'error404');
		if(empty($_REQUEST['station']))
			$this->redirect(SITE_PATH.'error404');
			
		if(isset($_REQUEST['parameter']) and isset($_REQUEST['period_id']))
				$res=Climate::deleteClimate($_REQUEST['station'],$_REQUEST['period_id'],$_REQUEST['parameter']);
		elseif(isset($_REQUEST['period_id']))
			$res=Climate::deleteClimate($_REQUEST['station'],$_REQUEST['period_id']);
		else
			$res=Climate::deleteClimate($_REQUEST['station']);
		if($res)
			$response['answer']=1;
		else
			$response['answer']=0;
		if(isset($_POST['type']))
			echo json_encode($response);
		
	}
	
	public function actionDelete()
	{
		if(!defined('LOCAL'))
			$this->redirect(SITE_PATH.'error404');
		if(empty($_REQUEST['station']))
			$this->redirect(SITE_PATH.'error404');
			

			if(isset($_REQUEST['parameter']) and isset($_REQUEST['period_id']))
				$res=Climate::deleteClimate($_REQUEST['station'],$_REQUEST['period_id'],$_REQUEST['parameter']);
			elseif(isset($_REQUEST['period_id']))
				$res=Climate::deleteClimate($_REQUEST['station'],$_REQUEST['period_id']);
			else
				$res=Climate::deleteClimate($_REQUEST['station']);
			if($res)
				$response['answer']=1;
			else
				$response['answer']=0;
			if(isset($_POST['type']))
				echo json_encode($response);
			
		
		
	}
	
	public function actionData()
	{
		Climate::setAvailablePeriods();
		$criteria=new CDbCriteria;
		if(empty($_REQUEST['station']))
			$this->redirect(SITE_PATH.'error404');
		
		if(isset($_REQUEST['period'])){
			if($_REQUEST['period']=='from2000'){
				$criteria->condition='year_begin=:year_begin';
				$criteria->params=array(':year_begin'=>2000);
			}
			elseif($_REQUEST['period'] == 'from2005'){
				$criteria->condition='year_begin=:year_begin';
				$criteria->params=array(':year_begin'=>2005);
			}
			else{
				$criteria=Climate::decodePeriod($_REQUEST['period'],$criteria);

			}
			
			$period=Periods::model()->find($criteria);
		}
		if(isset($period->id)){
			$data=Climate::getClimate($_REQUEST['station'],$period->id);
			$period_id=$period->id;
		}
		else{
			if(ClimateData::existsPeriod(7,$_REQUEST['station'])){
				$data=Climate::getClimate($_REQUEST['station'],7);
				$period_id=7;
			}
			else{
				$data=array();
				if(Climate::isNeedConsolidatedData($data)){
					$data=Climate::getClimate($_REQUEST['station']);
					$buffer_arr=array(6=>array('id'=>6));
					foreach(Climate::$available_periods as $key=>$value){
						$buffer_arr[$key]=$value;
					}
					Climate::$available_periods=array();
					Climate::$available_periods=$buffer_arr;

				}
				else
					$data=Climate::getClimate($_REQUEST['station'],6);
				
				$period_id=6;
			}
		}
		
		$common_info=CommonInfo::model()->find('station=:station',array(':station'=>$_REQUEST['station']));
		if(Climate::$language=='en')
			$this->pageTitle=$this->prefix_title.$common_info->point.', '.$common_info->state;
		else
			$this->pageTitle=$this->prefix_title.$common_info->point_russian.', '.$common_info->state_russian;
		

		
		if(isset($_REQUEST['type'])){
			$response['title']=$this->pageTitle;
		
				$response['content']=$this->renderPartial('data',array('data'=>$data,'months_names'=>Climate::$months_names,'common_info'=>$common_info,'period_id'=>$period_id,'measures'=>Climate::$measures[Climate::$language],'show_months_names'=>Climate::$show_months_names[Climate::$language],'climate_data_points'=>Climate::$climate_data_points,'period_observations'=>Climate::get_observations($_REQUEST['station'])),true);
				echo CJSON::encode($response);		
					}
		else{
				$this->render('data',array('data'=>$data,'months_names'=>Climate::$months_names,'common_info'=>$common_info,'period_id'=>$period_id,'measures'=>Climate::$measures[Climate::$language],'show_months_names'=>Climate::$show_months_names[Climate::$language],'climate_data_points'=>Climate::$climate_data_points,'period_observations'=>Climate::get_observations($_REQUEST['station'])));

			}	
	}
	
	public function actionRegions()
	{
		$this->pageTitle=Yii::app()->name.'. '.Climate::$object_name.' - регионы';
		if(isset($_REQUEST['type'])){
			$response['title']=$this->pageTitle;
		}
		if(empty($_REQUEST['state']))
			$this->redirect(SITE_PATH.'error404');	
			
		$state=GeographyObjects::model()->getByName($_REQUEST['state'],'state');	
		$regions=GeographyObjects::model()->getRegions($state[0]['state']);
		
		if(count($regions)>0):
			if(isset($_REQUEST['type'])){
				$response['content']=$this->renderPartial('regions',array('regions'=>$regions,'state'=>$state),true);
				echo CJSON::encode($response);		
					}
			else{
				$this->render('regions',array('regions'=>$regions,'state'=>$state));

			}
		else:
			$this->redirect(SITE_PATH.'error404');	
		endif;
	}
	
	public function actionStationPoint()
	{
		$stationpoint=trim($_POST['stationpoint']);
		$station=GeographyObjects::model()->getStationUrl($stationpoint);
		$url=SITE_PATH.'station/'.$station;

		if($station){
			$response['answer']=1;
			$response['url']=$url;
			$response['station']=$station;
		}
		else{
			$response['answer']=0;
		}
		echo CJSON::encode($response);		
	}
	
	protected function beforeAction($action=null)
	{
		$action=$this->getAction()->getId();
		if(!in_array($action,$this->exceptions_before_action))
			parent::beforeAction();

		return true;
	}
	
	
	
	
}

?>