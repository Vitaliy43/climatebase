<?php

class CommonInfo extends Model
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return BASE_PREFIX.'climatebase.common_info';
	}
}

?>