<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 * 
 */
 Yii::import('application.models.CommonInfo');

 
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	protected $slide_images=array();
	protected $prefix_title='Climatebase.ru - ';
	
	protected function beforeAction($action=null)
	{
		$this->slide_images=Utility::get_slide_images();
		if(!LOCAL){
			Climate::$current_table=BASE_PREFIX.'climatebase.continents';
		}
		
		Climate::set_land();
//		var_dump($_REQUEST);
//		exit;
		$action=$this->getAction()->getId();
		if($this->id=='site'):
		if($action=='error404')
			return true;
		else
			$this->redirect(SITE_PATH);
		endif;
		
		if(isset($_REQUEST['lang'])){
			Climate::$language=$_REQUEST['lang'];
			Yii::app()->language=$_REQUEST['lang'];
		}
		else{
			Climate::$language='ru';
		}
			
		if(isset($_REQUEST['state']) and strpos($_REQUEST['state'],'/')){
			$buffer=explode('/',$_REQUEST['state']);
			$_REQUEST['state']=$buffer[0];
			$_REQUEST['region']=$buffer[1];
			unset($buffer);
		}
		
		
		if(isset($_REQUEST['continent'])){
			$name=GeographyObjects::model()->getShowName($_REQUEST['continent'],'continent');
			if(!$name)
				$this->redirect(SITE_PATH.'site/error404');

			$this->breadcrumbs[$name]='/countries/'.$_REQUEST['continent'];
			Climate::$object_name=$name;
			
		}
		elseif(isset($_REQUEST['region'])){
		
			Climate::$current_table='climatebase.regions';
			$buffer=GeographyObjects::model()->getRegion($_REQUEST['region'],$_REQUEST['state']);
			if(!$buffer)
				$this->redirect(SITE_PATH.'site/error404');

			if(Climate::$language=='en')
				$region=$buffer['region'];
			else
				$region=$buffer['region_russian'];
				
			if(Climate::$language=='en')
				$state=$buffer['state'];
			else
				$state=$buffer['state_russian'];

			if(Climate::$language=='en')
				$continent_name=$buffer['continent'];
			else
				$continent_name=$buffer['continent_russian'];
			$this->breadcrumbs[$continent_name]='/countries/'.$buffer['continent'];		
			$this->breadcrumbs[$state]='/regions/'.$_REQUEST['state'];
			$this->breadcrumbs[$region]='/stations/'.$_REQUEST['state'].'/'.$buffer['region'];
			Climate::$object_name=$region;
			
		}
		elseif(isset($_REQUEST['state'])){
		
			Climate::$current_table='climatebase.states';
			$buffer=GeographyObjects::model()->getState($_REQUEST['state']);
			if(!$buffer)
				$this->redirect(SITE_PATH.'site/error404');
			if(Climate::$language=='en')
				$name=$buffer['state'];
			else
				$name=$buffer['state_russian'];

			if(Climate::$language=='en')
				$continent_name=$buffer['continent'];
			else
				$continent_name=$buffer['continent_russian'];
			$this->breadcrumbs[$continent_name]='/countries/'.$buffer['continent'];		
			$this->breadcrumbs[$name]='/stations/'.$_REQUEST['state'];
			Climate::$object_name=$name;
			
		}
		elseif(isset($_REQUEST['station'])){
			
			if($_REQUEST['station']==0)
				$this->redirect(SITE_PATH.'site/error404');
			$info=CommonInfo::model()->find('station=:station',array(':station'=>$_REQUEST['station']));
			if(!$info)
				$this->redirect(SITE_PATH.'site/error404');
			Climate::$station=(int)$_REQUEST['station'];
			if(isset($_REQUEST['period_id']))
				Climate::$period_id=$_REQUEST['period_id'];

			if(Climate::$language=='en')
				$state=$info->state;
			else
				$state=$info->state_russian;
				
			if(Climate::$language=='en')
				$point=$info->point;
			else
				$point=$info->point_russian;
			
			if($info->region):
				if(Climate::$language=='en')
					$region=$info->region;
				else
					$region=$info->region_russian;
			endif;
				
			if(Climate::$language=='en'){
				$continent_name=$info->continent;

			}
			else{
				$continent=GeographyObjects::model()->find('continent=:continent',array(':continent'=>$info->continent));
				$continent_name=$continent->continent_russian;

			}
			$this->breadcrumbs[$continent_name]='/countries/'.$info->continent;
			if(isset($region))	
				$this->breadcrumbs[$state]='/regions/'.$info->state;
			else
				$this->breadcrumbs[$state]='/stations/'.$info->state;
			if(isset($region))
				$this->breadcrumbs[$region]='/stations/'.$info->state.'/'.$info->region;
				
			$this->breadcrumbs[$point]='/station/'.$_REQUEST['station'];

				
			if(isset($_REQUEST['period'])){
				$show_period=Climate::decodePeriod($_REQUEST['period']);
				if(!$show_period)
					$this->redirect(SITE_PATH.'site/error404');
				else
					$this->breadcrumbs[$show_period]='/station/'.$_REQUEST['station'].'/'.$_REQUEST['period'];

			}		
				
		}
			
		return true;
	}
	
	
}