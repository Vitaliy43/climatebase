<?php

class Utility
{
	
	public static function get_slide_images()
	{
		$res=Yii::app()->db->createCommand(array(
    	'select'=>'*',
    	'from'=>'slide_images',
    	'order'=>'rand()'
	))->queryAll();
	
		return $res;
	}
	
	

	
}

?>