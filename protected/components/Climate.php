<?php

/*

	Основной класс для работы с климатическими данными

*/

class Climate extends CApplicationComponent
{
	public static $current_table='climatebase.continents';
	public static $transit_table='archive_weather.tutiempo_buffer';
    protected static $connect = array('login' => 'vitaliy', 'password' => 'vitaliy43');
    protected static $minimum_days=3500;
	public static $language;
	public static $object_name;
	public static $archive_base='archive_weather';
	protected static $meteo_base='meteo';
	protected static $messages=array(
	'en'=>array('from'=>'From %object y.'
		),
	'ru'=>array('from'=>'С %object г.'
	)
	);
	public static $station;
	public static $months_names=array(1=>"jan",2=>"feb",3=>"mar",4=>"apr",5=>"may",6=>"jun",7=>"jul",8=>"aug",9=>"sep",10=>"oct",11=>"nov",12=>"dec");
    public static $extensions_methods = array();
	public static $period_id;
	public static $methods=array(
	'get_t1'=>'average_min_temperature',
	'get_t2'=>'average_max_temperature',
	'get_midtemp'=>'average_temperature',
	'get_abs_min'=>'lowest_temperature',
	'get_abs_max'=>'highest_temperature',
	'get_precipitation'=>'precipitation',
	'get_precip_days'=>'days_with_precipitation',
	'get_clear_days'=>'clear_days',
	'get_cloudy_days'=>'cloudy_days',
	'get_overcast_days'=>'overcast_days',
	'get_average_cloudness'=>'average_cloudness',
	'get_average_low_cloudness'=>'average_low_cloudness',
	'get_sum_at'=>'sum_at',
	'get_sun_hours'=>'average_sun_hours'
	);
	public static $measures=array(
	'en'=>array(
		'average_temperature'=>'°C',
		'average_min_temperature'=>'°C',
		'average_max_temperature'=>'°C',
		'lowest_temperature'=>'°C',
		'highest_temperature'=>'°C',
		'sum_at'=>'°C',
		'precipitation'=>'mm',
		'days_with_precipitation'=>'days',
		'clear_days'=>'days',
		'cloudy_days'=>'days',
		'overcast_days'=>'days',
		'average_cloudness'=>'points',
		'average_low_cloudness'=>'points',
		'average_sun_hours'=>'hours'
		),
	'ru'=>array(
		'average_temperature'=>'°C',
		'average_min_temperature'=>'°C',
		'average_max_temperature'=>'°C',
		'lowest_temperature'=>'°C',
		'highest_temperature'=>'°C',
		'sum_at'=>'°C',
		'precipitation'=>'мм',
		'days_with_precipitation'=>'дней',
		'clear_days'=>'дней',
		'cloudy_days'=>'дней',
		'overcast_days'=>'дней',
		'average_cloudness'=>'балл',
		'average_low_cloudness'=>'балл',
		'average_sun_hours'=>'час'
		),
	
	);
	public static $show_months_names=array(
	'ru'=>array(
		'year'=>'Год',
		'jan'=>'янв',
		'feb'=>'фев',
		'mar'=>'мар',
		'apr'=>'апр',
		'may'=>'май',
		'jun'=>'июн',
		'jul'=>'июл',
		'aug'=>'авг',
		'sep'=>'сен',
		'oct'=>'окт',
		'nov'=>'ноя',
		'dec'=>'дек'
		),
	'en'=>array(
		'year'=>'Year',
		'jan'=>'jan',
		'feb'=>'feb',
		'mar'=>'mar',
		'apr'=>'apr',
		'may'=>'may',
		'jun'=>'jun',
		'jul'=>'jul',
		'aug'=>'aug',
		'sep'=>'sep',
		'oct'=>'oct',
		'nov'=>'nov',
		'dec'=>'dec'
		),	
	);
	
	public static $exceptions_methods=array();
	
	public static $climate_data_points=array('average_temperature','average_max_temperature','average_min_temperature','highest_temperature','lowest_temperature','precipitation','days_with_precipitation','sum_at','average_cloudness','average_low_cloudness','clear_days','cloudy_days','overcast_days','average_sun_hours');
	
	public static $counted_points=array('precipitation','days_with_precipitation');
	public static $available_periods=array();
	public static $verifiable_table='average_temperature';
	public static $exceptions_table='exceptions_tables';
	protected static $use_consolidated_data=false; 
	public static $crimea_stations=array(33983,33946,33976,33924,33990);
	public static $land;
    
    public static $data_path = '/home/vitaliy/climatebase_data';
    public static $csv_divider = ' ';
    public static $max_year = 2023;
    public static $min_year;
	
	public static function set_land(){
		self::$land=Tabgeo::tabgeo_country_v4($_SERVER['REMOTE_ADDR']);
	}
    
    protected static function getClimateData($object,$method)
	{

        
        if ($method == 'average_meteo_cloudness' || $method == 'average_meteo_low_cloudness') {
            return $object->$method(self::$station,null,$key,self::$period_id);
        }
        if ($method == 'get_sun_hours' && !$object->num_year_for_sun_hours) return false;

		$temps=array();
		foreach(self::$months_names as $key=>$value){
			if(!in_array($method,self::$extensions_methods)){
                $temps[$value]=$object->$method(self::$station,null,$key,self::$period_id);
            }
		}
		$temps['year']=$object->$method(self::$station,null,null,self::$period_id);
		return array_merge(array('station'=>self::$station,'period_id'=>self::$period_id),$temps);
	}

	public static function setAvailablePeriods()
	{
		$criteria=new CDbCriteria;
		$criteria->order='position';
		
		$all_periods=Periods::model()->findAll($criteria);
		$selected_periods=array();
		foreach($all_periods as $period){
			if(ClimateData::existsPeriod($period->id,self::$station,self::$verifiable_table))
				$selected_periods[$period->id]=$period;
		}
		self::$available_periods=$selected_periods;
	}
    
        public static function correctDataWithZero()
    {
        $station = (int)$_REQUEST['station'];
        $period_id = (int)$_REQUEST['period_id'];
        $list_months = explode(',',$_REQUEST['checked_months']);
        $command = Yii::app()->db->createCommand();
        $arr = array();
        foreach ($list_months as $item) {
            if ($item) $arr[] = $item;
        }
        self::$period_id=$period_id;
		self::$station=$station;
        $months = implode(',',$arr);
        if ($period_id == 2) {           
            Yii::app()->db->createCommand("DELETE FROM ogimet.ogimet_data WHERE station = ".$station." AND mintemp = 0 AND month IN (".$months.")")->execute();
            $archive_table='ogimet.ogimet_data';
        }
        else {
            $sql="SELECT * FROM archive_weather.stations WHERE station=$station";
		    $res=Yii::app()->db->createCommand($sql)->queryRow();
            $archive_table = 'tutiempo_buffer_'.$res['table_id'];
            Yii::app()->db->createCommand("DELETE FROM archive_weather.".$archive_table." WHERE station = ".$station." AND mintemp = 0 AND month IN (".$months.")")->execute();
        }
        $command->truncateTable(self::$transit_table);
        if ($period_id != 2) {
            $sql="INSERT INTO ".self::$transit_table." SELECT * FROM archive_weather.$archive_table WHERE station=".self::$station;
        }
        else {
            $sql="INSERT INTO ".self::$transit_table." SELECT null,station,year,month,day,mintemp,midtemp,maxtemp,precipitation,link FROM $archive_table WHERE station=".self::$station;
        }
        
        $res_insert = Yii::app()->db->createCommand($sql)->execute();
        if($res_insert)
			$archive_table=self::$transit_table;
		else
			die('Ошибка вставки данных. Промежуточная таблица пуста!');
            
        $weather = new Weather($archive_table,self::$station,false);
        if ($period_id == 2) {
            $weather->field_precip='precipitation';
        }
        else {
            $weather->field_precip='precip';
			if($period->year_begin and $period->year_end){
				$weather->period='AND year BETWEEN '.$period->year_begin.' AND '.$period->year_end;
			}
            $weather->is_all=true;
            if ($period_id == 7) {
				$weather->is_tutiempo=true;
            }
            else {
                $weather->begin_year = 1800;
            }
            $buffer_count=$weather->init(true);
		    if($buffer_count<self::$minimum_days){
			    echo('Недостаточно данных для выборки!');
			    Stations::model()->deleteStationFromQueue(self::$station);
			    return true;
		    }
        }
        
        $need_methods = array('get_t1','get_abs_min');
        
        foreach(self::$methods as $method=>$table){
            if (in_array($method, $need_methods)) {
                $command->delete($table,'station=:station AND period_id=:period_id',array(':station'=>self::$station,':period_id'=>self::$period_id));
			    $arr=self::getClimateData($weather,$method);
                if ($arr) {
                    $res=$command->insert($table,$arr);	
                }
             }
                
        }
        
        $command->truncateTable(self::$transit_table);
        echo 'Данные успешно обновлены <br>';   
        
    }
	
	protected static function setExceptionsTables(){
		$sql="SELECT list_tables FROM self::$exceptions_table WHERE station=self::$station AND period_id = self::$period_id";
		$command=Yii::app()->db->createCommand();
		$res=Yii::app()->db->createCommand($sql)->queryRow();
		self::$exceptions_methods=explode(',',$res['list_tables']);
	}
	
	public static function getClimate($station,$period_id=null)
	{	
		$climate=new ClimateData($station,$period_id);
		$data=array();
		
		foreach(self::$methods as $method=>$table){
			if($period_id)
				$buffer=$climate->getData($table);
			else
				$buffer=$climate->getData($table,true);
			if($buffer)
				$data[$table]=$buffer;
		}
		return $data;
		
	}
	
	public static function isNeedConsolidatedData(&$data){
		if(isset(self::$available_periods[6]) or isset(self::$available_periods[7]) or !self::$use_consolidated_data)
			return false;
		return true;
	
	}
	
	protected static function set_observations($archive_table,$station){
		$sql="SELECT MIN(year) AS 'begin',MAX(year) AS 'end' FROM $archive_table WHERE station=$station";
		$command = Yii::app()->db->createCommand();
		$res=Yii::app()->db->createCommand($sql)->queryRow();
		$data=array(
		'station'=>$station,
		'begin'=>$res['begin'],
		'end'=>$res['end']
		);
		if(count($res)>0){
			$command->delete('observations','station=:station',array(':station'=>$station));
			$res_insert=$command->insert('observations',$data);
			return $res_insert;
		}
		return false;
	}
	
	public static function get_observations($station){
		$sql="SELECT begin,end FROM observations WHERE station=$station";
		$res=Yii::app()->db->createCommand($sql)->queryRow();
		return $res;
	}
	
	protected static function RecountClimate($station,$parameter,$periods)
	{
		$period=Periods::model()->findByPk($periods['current']);
		if(!$period->year_end or $period->source=='ogimet')
			return false;
		$buffer_station=Stations::model()->find('station=:station',array(':station'=>$station));
		$archive_table=self::$archive_base.'.'.$period->source.'_buffer_'.$buffer_station->table_id;
		$weather=new Weather($archive_table,$station);
		$weather->begin_year=$period->year_end;
		$weather->field_precip='precip';
		$weather->is_tutiempo=true;
		$weather->is_all=true;
		$weather->init(TRUE);
		if(in_array($parameter,self::$counted_points)){
			if($parameter=='precipitation'){
				foreach(self::$months_names as $key=>$value)
					$arr[$value]=$weather->get_precipitation($station,null,$key);
				$arr['year']=$weather->get_precipitation($station);
				return array_merge(array('station'=>$station,'period_id'=>$periods['all']),$arr);
			}
				
			elseif($parameter=='days_with_precipitation'){
				foreach(self::$months_names as $key=>$value)
					$arr[$value]=$weather->get_precip_days($station,null,$key);
				$arr['year']=$weather->get_precip_days($station);
				return array_merge(array('station'=>$station,'period_id'=>$periods['all']),$arr);			}
				
		}
	}
	
	
	public static function deleteClimate($station,$period_id=null,$parameter=null)
	{			
		$command=Yii::app()->db->createCommand();
		if(!$period_id and !$parameter)
			$res=CommonInfo::model()->deleteAll('station=:station',array(':station'=>$station));
		foreach(self::$climate_data_points as $table):
			if(!$period_id){
					$res=$command->delete($table, 'station=:station', array(':station'=>$station));
			}
			else{
				if($parameter and $parameter==$table){
					$res=$command->delete($table, 'station=:station AND period_id=:period_id', array(':station'=>$station,':period_id'=>$period_id));
					self::updateClimateData($station,$parameter,$period_id);
				}
				elseif(!$parameter){
					$res=$command->delete($table, 'station=:station AND period_id=:period_id', array(':station'=>$station,':period_id'=>$period_id));
				}
				
			}
		endforeach;
//		return $res;
		return true;
	}
	
	public static function updateClimateData($station,$parameter,$period_id)
	{
		if(!defined('LOCAL') || !LOCAL)
			exit;
		Yii::import('application.modules.monitor.MonitorModule');
		MonitorModule::init_from_main();
		$command = Yii::app()->db->createCommand();
		$sql="SELECT period_id FROM $parameter WHERE station=$station AND (period_id = 6 OR period_id = 7)";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$arr=self::RecountClimate($station,$parameter,array('all'=>$res['period_id'],'current'=>$period_id));
		if(!$arr)
			return;
		
		$command->delete($parameter,'station=:station AND period_id=:period_id',array(':station'=>$station,':period_id'=>$res['period_id']));
		$res=$command->insert($parameter,$arr);	
	}
	
	
	public static function decodePeriod($period,$criteria=null)
	{
		if(strstr($period,'from')!=''){
			$buffer=explode('m',$period);
			$period=$buffer[1];
			if(empty($period) or !$period)
				return false;
			$buffer_name=self::$messages[self::$language]['from'];
			$buffer_name=str_replace('%object',$period,$buffer_name);
		}
		else{
			$buffer=explode('_',$period);
			$begin=$buffer[0];
			$end=$buffer[1];
			if(empty($begin) or !$begin)
				return false;
			if(empty($end) or !$end)
				return false;
			if($end=='00')
				$end='20'.$end;
			else
				$end='19'.$end;
				
			$begin='19'.$begin;
			$buffer_name=$begin.'-'.$end.' ';
			if($criteria){
				$criteria->condition.='year_begin=:year_begin AND year_end=:year_end';
				$criteria->params[':year_begin']=$begin;
				$criteria->params[':year_end']=$end;
				return $criteria;
			}
			if(self::$language=='en')
				$buffer_name.='y.y';
			else
				$buffer_name.='г.г';
		}
		
		return $buffer_name;
	}
	
}

?>