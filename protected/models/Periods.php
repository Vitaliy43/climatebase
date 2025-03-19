<?php

class Periods extends Model
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return 'periods';
	}
}

?>