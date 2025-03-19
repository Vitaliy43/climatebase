<?php


/*

	Класс для работы с географическими объектами: гос-вами, регионами, континентами и т.д

*/

class GeographyObjects extends Model
{
	public $base_prefix='climatebase';
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		if(LOCAL)
			return $this->base_prefix.'.continents';
		else
			return BASE_PREFIX.'climatebase.continents';

	}
	 
		
	public function get($table)
	{
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'*',
    	'from'=>$table,
			
		))->queryAll();
		return $res;
	}
	
	
	public function getState($state)
	{
		$sql="SELECT states.*,continents.continent,continents.continent_russian FROM $this->base_prefix.`states`,$this->base_prefix.`continents` WHERE states.`state`='".$state."' AND states.continent_id=continents.id" ;
		$res=Yii::app()->db->createCommand($sql)->queryAll();
		if($res)
			return $res[0];
		return false;
		
	}
	
	public function getRegion($region,$state)
	{
		$sql="SELECT regions.*,continents.continent,continents.continent_russian FROM $this->base_prefix.`regions`,$this->base_prefix.`states`,$this->base_prefix.`continents` WHERE regions.`region`='".$region."' AND regions.`state`='".$state."' AND regions.`state`=states.`state` AND states.continent_id=continents.id" ;
		$res=Yii::app()->db->createCommand($sql)->queryAll();
		if($res)
			return $res[0];
		return false;
	}
	
	public function getShowName($name,$object,$return_arr=false)
	{
		$query_array=array(
    	'select'=>'*',
    	'from'=>Climate::$current_table,
		'where'=>$object.'=:'.$object,
		'params'=>array(':'.$object=>$name)	
		);
		 	
		$res=Yii::app()->db->createCommand($query_array)->queryAll();
		if(!$res)
			return false;
		
		if(Climate::$language=='en'){
			if($return_arr)
				return $res[0];
			else
				return $res[0][$object];
		}
		else{
			$object=$object.'_russian';
			if($return_arr)
				return $res[0];
			else
				return $res[0][$object];
		}
	}
	
	public function getByName($name,$table=null)
	{
		if($table==null){
			$res=$this->find('continent=:continent',array(':continent'=>$name));

		}
		else{
			$res=Yii::app()->db->createCommand(array(
    		'select'=>'*',
    		'from'=>$this->base_prefix.'.'.$table.'s',
			'where'=>$table.'=:'.$table,
			'params'=>array(':'.$table=>$name),
			))->queryAll();
		}
		
		return $res;
	}
	
	public function getStates($continent_id)
	{
		
		if(Climate::$language=='en')
			$order='state';
		else
			$order='state_russian';
		 
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'*',
    	'from'=>$this->base_prefix.'.states',
		'where'=>'continent_id=:id',
		'params'=>array(':id'=>$continent_id),
		'order'=>$order	
		))->queryAll();
		
		if($res){
			return $res;

		}	
		else{
		
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'*',
    	'from'=>$this->base_prefix.'.states',
		'where'=>'subcontinent_id=:id',
		'params'=>array(':id'=>$continent_id)
			
		))->queryAll();
		
		return $res;
		}
	}
	
	public function getRegions($state)
	{
		if(Climate::$language=='en')
			$order='region';
		else
			$order='region_russian';
			
		if($state=='Russia'){
			if(Climate::$land=='RU'){
				
				$res=Yii::app()->db->createCommand(array(
    			'select'=>'*',
    			'from'=>$this->base_prefix.'.regions',
				'where'=>'state=:state',
				'params'=>array(':state'=>$state),
				'order'=>$order	
				))->queryAll();

			}
			else
				$res=Yii::app()->db->createCommand(array(
    			'select'=>'*',
    			'from'=>$this->base_prefix.'.regions',
				'where'=>'state=:state AND region!=:region',
				'params'=>array(':state'=>$state,':region'=>'Crimea'),
				'order'=>$order	
				))->queryAll();
		}
		else{
			$res=Yii::app()->db->createCommand(array(
    		'select'=>'*',
    		'from'=>$this->base_prefix.'.regions',
			'where'=>'state=:state',
			'params'=>array(':state'=>$state),
			'order'=>$order	
			))->queryAll();
		}
			
				
		return $res;
	}
	
	public function getStationUrl($stationpoint)
	{
		if(Climate::$language=='en'){
			$point='point';
			$state='state';
		}
		else{
			$point='point_russian';
			$state='state_russian';
		}
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'station',
    	'from'=>$this->base_prefix.'.common_info',
		'where'=>'station=:station',
		'params'=>array(':station'=>$stationpoint)
		))->queryRow();
		if($res['station'])
			return $res['station'];
		
		$buffer=explode(',',$stationpoint);
		$point_name=trim($buffer[0]);
		$state_name=trim($buffer[1]);
		$sql="SELECT station FROM $this->base_prefix.`common_info` WHERE `$point`='".$point_name."' AND `$state`='".$state_name."'" ;
		$res=Yii::app()->db->createCommand($sql)->queryRow();
		
		if($res)
			return $res['station'];
		return false;
	}
	
	public function getStations($name,$type='state')
	{
		if(Climate::$language=='en')
			$order='point';
		else
			$order='point_russian';
		
			
		if($type=='state'):
		 	if($name=='Ukraine'){
				if(Climate::$land=='RU'){
					$res=Yii::app()->db->createCommand(array(
    				'select'=>'*',
    				'from'=>$this->base_prefix.'.common_info',
					'where'=>'state=:state AND station NOT IN(0,'.implode(',',Climate::$crimea_stations).')',
					'params'=>array(':state'=>$name),
					'order'=>$order	
					))->queryAll();
				}
				else{
					$res=Yii::app()->db->createCommand(array(
    				'select'=>'*',
    				'from'=>$this->base_prefix.'.common_info',
					'where'=>'state=:state AND station!=0',
					'params'=>array(':state'=>$name),
					'order'=>$order	
					))->queryAll();
				}
			}
			else{
				$res=Yii::app()->db->createCommand(array(
    			'select'=>'*',
    			'from'=>$this->base_prefix.'.common_info',
				'where'=>'state=:state AND station!=0',
				'params'=>array(':state'=>$name),
				'order'=>$order	
				))->queryAll();
			}
			
		
		else:
		
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'*',
    	'from'=>$this->base_prefix.'.common_info',
		'where'=>'region=:region AND station!=0',
		'params'=>array(':region'=>$name),
		'order'=>$order	
		))->queryAll();
		
		endif;
		
		return $res;
		
	}
	
}

?>