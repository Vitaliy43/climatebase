<?php

class Structure extends Model
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return 'queue';
	}
	
	public function changeIndex($old_ind,$new_ind)
	{
		$tables=Climate::$climate_data_points;
			$command = Yii::app()->db->createCommand();
		foreach($tables as $table){
			$res=$command->update($table,array('station'=>$new_ind),"station=$old_ind");
		}
		


		return $res;
	}	

}

?>