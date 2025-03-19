<?php

/*

	Класс для получения климатических данных из архивов погоды

*/

class ClimateMonitor extends Climate
{
	protected static $required_queue=true;
//	public static $extensions_methods=array('get_clear_days','get_cloudy_days','get_overcast_days','get_average_cloudness','get_sun_hours');
//	public static $extensions_methods=array('get_clear_days','get_cloudy_days','get_overcast_days','get_average_cloudness');
//	public static $extensions_methods=array('get_sun_hours');
	private static $my_connection;
	public static $from_ogimetbase=true;
    
	
	public static function setClimate($period_id)
	{
		$row=Stations::model()->getStationFromQueue();
		$command = Yii::app()->db->createCommand();
		self::$my_connection=mysqli_connect('localhost',self::$connect['login'],self::$connect['password']);

		if(self::$required_queue){
			if(!$row)
				return 'Очередь пуста!';
		}
		$period=Periods::model()->findByPk($period_id);
		if($period->year_end)
			$show_period=$period->year_begin.' - '.$period->year_end.' г.г';
		else
			$show_period='с '.$period->year_begin.' г.';
		self::$period_id=$period_id;
		self::$station=$row['station'];
		$buffer_station=Stations::model()->find('station=:station',array(':station'=>self::$station));
		if(!$buffer_station){
			echo 'Станция '.self::$station.' отсутствует в списке станций!';
			Stations::model()->deleteStationFromQueue(self::$station);
			return true;
		}
		if($period_id==1 or $period_id==2){
			self::$from_ogimetbase = true;
			$archive_table='ogimet.ogimet_data';
		}
		else{
			$command->truncateTable(self::$transit_table);
			$archive_table=self::$archive_base.'.tutiempo_buffer_'.$buffer_station->table_id;

			$sql="INSERT INTO ".self::$transit_table." SELECT * FROM $archive_table WHERE station=".self::$station;
			$res_insert=mysqli_query(self::$my_connection,$sql);

			if($res_insert)
				$archive_table=self::$transit_table;
			else
				die('Ошибка вставки данных. Промежуточная таблица пуста!');
		}

		if($period_id==1 or $period_id==2){
            $weather=new Weather($archive_table,$row['station']);
			$weather->field_precip='precipitation';
		}
		else{
            $weather=new Weather($archive_table,$row['station'],false);
			$weather->field_precip='precip';
			if($period->year_begin and $period->year_end){
				$weather->period='AND year BETWEEN '.$period->year_begin.' AND '.$period->year_end;
				
			}
			elseif($period_id==6){
				$weather->is_all=true;
				$weather->begin_year = 1800;
			
			}
			elseif($period_id==7)	{
				$weather->is_all=true;
				$weather->is_tutiempo=true;
			}
	

		}
		
		$buffer_count=$weather->init(true);

		if($buffer_count<self::$minimum_days){
			echo('Недостаточно данных для выборки!');
			Stations::model()->deleteStationFromQueue(self::$station);
			return true;
		}
			
		if(empty($_REQUEST['climate_method'])):
		if(!in_array($period_id, array(1,2))){
			unset(self::$methods['get_clear_days']);
			unset(self::$methods['get_cloudy_days']);
			unset(self::$methods['get_overcast_days']);
			unset(self::$methods['get_average_cloudness']);
			unset(self::$methods['get_average_low_cloudness']);
			unset(self::$methods['get_sun_hours']);
		}


		foreach(self::$methods as $method=>$table){
			
			if(!in_array($method,self::$extensions_methods)):
			
			$command->delete($table,'station=:station AND period_id=:period_id',array(':station'=>self::$station,':period_id'=>self::$period_id));
			$arr=self::getClimateData($weather,$method);
            if ($arr)
			    $res=$command->insert($table,$arr);	
			
			endif;
					
		}
		else:
		
			$command->delete(self::$methods[$_REQUEST['climate_method']],'station=:station AND period_id=:period_id',array(':station'=>self::$station,':period_id'=>self::$period_id));
			$arr=self::getClimateData($weather,$_REQUEST['climate_method']);

			$res=$command->insert(self::$methods[$_REQUEST['climate_method']],$arr);	
		
		endif;
		
		if($weather->is_all){
			$res_observe=Climate::set_observations($archive_table,self::$station);
		}
		
		if($res)
			$message='Данные по станции '.self::$station.' за период '.$show_period.' успешно обновлены';
		else
			$message='Данные по станции '.self::$station.' за период '.$show_period.' обновить не удалось';
		
		if($res)
			Stations::model()->deleteStationFromQueue(self::$station);
		if(isset($res_observe)){
			if(!$res_observe)
				$message='Ошибка выставления периода наблюдения!';
		}
		
		return $message;
	}
    
    public static function getListLowestWithZero() {
        $sql='SELECT * FROM  lowest_temperature WHERE year = 0 ORDER BY station';
		$buffer=Yii::app()->db->createCommand($sql)->queryAll();
        $arr = array();
        foreach ($buffer as $row) {
            $arr[$row['station']][$row['period_id']] = $row;
        }
        return $arr;
    }
	
	public static function setMeteoCloudness()
	{
		set_time_limit(0);
		$row=Stations::model()->getStationFromQueue();
		$command = Yii::app()->db->createCommand();
		self::$my_connection=mysqli_connect('localhost',self::$connect['login'],self::$connect['password']);
		if(self::$required_queue){
			if(!$row)
				return 'Очередь пуста!';
		}
		self::$period_id=6;
		self::$station=$row['station'];
		$buffer_station=Stations::model()->find('station=:station',array(':station'=>self::$station));
		if(!$buffer_station){
			echo 'Станция '.self::$station.' отсутствует в списке станций!';
			Stations::model()->deleteStationFromQueue(self::$station);
			return true;
		}
		$station=$buffer_station->station;
		$archive_table=self::$archive_base.'.meteo_cloudness';
		$weather=new Weather($archive_table,$station);
		$weather->is_all=true;
		$weather->begin_year=1966;
		$weather->is_meteo=true;
		$methods=array(
		'average_cloudness'=>'average_meteo_cloudness',
		'average_low_cloudness'=>'average_meteo_low_cloudness'
		);
		foreach($methods as $key=>$value){
			$arr=self::getClimateData($weather,$value);
			$command->delete($key,'station=:station AND period_id=:period_id',array(':station'=>self::$station,':period_id'=>self::$period_id));
			$res=$command->insert($key,$arr);	
		}
		if($res)
			Stations::model()->deleteStationFromQueue(self::$station);
		if($res)
			$message='Данные по облачности по станции '.self::$station.' за период с 1966г. успешно обновлены <br>';
		else
			$message='Данные по облачности по станции '.self::$station.' за период с 1966г. обновить не удалось <br>';
			
			
		return $message;
	}
	
	
	public static function copyFromOldBase(){
		
		self::$my_connection=mysqli_connect('localhost',self::$connect['login'],self::$connect['password']);
		ini_set('max_execution_time','300');
		ini_set('set_time_limit','0');
		$table='average_max_temperature';
		
		foreach(self::$methods as $method=>$table){
			$res=mysql_query("SHOW TABLES FROM climatebase LIKE '$table'");
			if(!$res){
				continue;
			}
			$sql='SELECT cc.link,cc.station FROM climatebase.`common_info` cc WHERE cc.station > 0 AND cc.station NOT IN (SELECT station FROM new_climatebase.'.$table.' WHERE period_id = 6 OR period_id = 7)';
			$buffer_stations=Yii::app()->db->createCommand($sql)->queryAll();
			foreach($buffer_stations as $item){
				$link=$item['link'];
				$station=(int)$item['station'];
				$uniq=$station.'-7';
				$sql="INSERT IGNORE INTO new_climatebase.`$table`(`station`, `period_id`, `year`, `jan`, `feb`, `mar`, `apr`, `may`, `jun`, `jul`, `aug`, `sep`, `oct`, `nov`, `dec`, `uniq`) SELECT $station, 7, `year`, `jan`, `feb`, `mar`, `apr`, `may`, `jun`, `jul`, `aug`, `sep`, `oct`, `nov`, `dec`, '$uniq' FROM climatebase.".$table." WHERE link = '$link'";
				echo 'sql '.$sql.'<br>';
				$res_insert=mysqli_query($sql);
				if($res_insert)
					echo 'Inserted!!! <br>';
			}

		}
		
	}
	
	
		
	
	public static function setSunHours()
	{
		$row=Stations::model()->getStationFromQueue();
		if(self::$required_queue){
			if(!$row)
				return 'Очередь пуста!';
		}
		$command = Yii::app()->db->createCommand();
		self::$station=(int)$row['station'];
		$table='average_sun_hours';
		self::$period_id=2;
		$data=array(
		'station'=>self::$station,
		'period_id'=>2,
		);
		$arr=array();
		$counter=1;
		$days_in_month=array(
		1=>31,
		2=>28.25,
		3=>31,
		4=>30,
		5=>31,
		6=>30,
		7=>31,
		8=>31,
		9=>30,
		10=>31,
		11=>30,
		12=>31
		);
		
		foreach(self::$months_names as $month){
//			$sql='SELECT AVG(`'.$month.'`) AS num_hours FROM temp.buffer_sun_hours WHERE station='.self::$station.' AND `'.$month.'` IS NOT NULL';
			$sql='SELECT AVG(sun_hours) AS num_hours FROM ogimet.ogimet_data WHERE station='.self::$station.' AND month = '.$counter.' AND sun_hours IS NOT NULL';
			$buffer=Yii::app()->db->createCommand($sql)->queryRow();
			if(empty($buffer['num_hours'])or !$buffer['num_hours'])
				$buffer['num_hours']='0';
			$arr[$month]=round($buffer['num_hours']*$days_in_month[$counter]);
			$counter++;
		}
		$data['year']=array_sum($arr);
		if($data['year']==0)
			die('Ошибка получения данных');
		$data=array_merge($data,$arr);
		if(count($data)==15){
			$command->delete($table,'station=:station AND period_id=:period_id',array(':station'=>self::$station,':period_id'=>self::$period_id));
			$res=$command->insert($table,$data);
			if($res){
				Stations::model()->deleteStationFromQueue(self::$station);
				return 'Данные со станции '.self::$station.' успешно вставлены';
			}
			else{
				die('Ошибка вставки!');

			}
		}
		else{
			die('Недостаточно данных для вставки!');
			Stations::model()->deleteStationFromQueue(self::$station);

		}
		
	}
	
	public static function createAutocompleteData()
	{
		$js_path=$_SERVER['DOCUMENT_ROOT'].'/js/data/';
		//////////////////////////////////////// Список станций ////////////////////////////////////////
		$fs=fopen($js_path.'stations.js','w');
		$sql='SELECT DISTINCT station FROM average_temperature';
		$js = 'var stations = [';
		$buffer=Yii::app()->db->createCommand($sql)->queryAll();
		for($i=0;$i<count($buffer);$i++){
			if($i==count($buffer)-1)
				$js.='"'.$buffer[$i]['station'].'"';
			else
				$js.='"'.$buffer[$i]['station'].'"'.', ';
		}
		
		$js.='];';
		
		if(fwrite($fs,$js))
			$message='stations.js обновлен! <br>';
		else	
			$message='Не удалось обновить stations.js <br>';
		fclose($fs);
			
		/////////////////////////////////////// Список пунктов ////////////////////////////////////////
		$fp=fopen($js_path.'points.js','w');
		$fpr=fopen($js_path.'points_russian.js','w');
		$sql='SELECT point,state,point_russian,state_russian FROM climatebase.common_info WHERE station IN (SELECT DISTINCT station FROM new_climatebase.average_temperature)';
		$js = 'var points = [';
		$jsr = 'var points = [';
		$buffer=Yii::app()->db->createCommand($sql)->queryAll();
		
		for($i=0;$i<count($buffer);$i++){
			$point = str_replace('_',' ',$buffer[$i]['point']);
			$state = str_replace('_',' ',$buffer[$i]['state']);
			$point_russian = str_replace('_',' ',$buffer[$i]['point_russian']);
			$state_russian = str_replace('_',' ',$buffer[$i]['state_russian']);
			if($i==count($buffer)-1){
				$js.='"'.$point.','.$state.'"';
				$jsr.='"'.$point_russian.', '.$state_russian.'"';

			}
			else{
				$js.='"'.$point.','.$state.'",';
				$jsr.='"'.$point_russian.', '.$state_russian.'",';
	
			}
		}
		
		$js.='];';
		$jsr.='];';
		
		if(fwrite($fp,$js))
			$message.='points.js обновлен! <br>';
		else	
			$message.='Не удалось обновить points.js <br>';
		fclose($fp);
		
		if(fwrite($fpr,$jsr))
			$message.='points_russian.js обновлен! <br>';
		else	
			$message.='Не удалось обновить points_russian.js <br>';
		fclose($fpr);
		return $message;
	}

}

?>