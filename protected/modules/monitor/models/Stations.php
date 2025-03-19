<?php

class Stations extends Model
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return 'archive_weather.stations';
	}
	
	public function getStationFromQueue()
	{
		$sql="SELECT * FROM `queue` LIMIT 1" ;
		return Yii::app()->db->createCommand($sql)->queryRow();
		
	}
	
	public function deleteStationFromQueue($station)
	{
		$command = Yii::app()->db->createCommand();
		return $command->delete('queue','station=:station',array(':station'=>$station));

	}
	
	

}


?>