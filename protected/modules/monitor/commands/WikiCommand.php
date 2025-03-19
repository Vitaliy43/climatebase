<?php

class WikiCommand extends CConsoleCommand {
	
	protected static $execute_host = 'http://192.168.111.1:8081';
	
	public function actionDistricts() {
		 $res_count=Yii::app()->db->createCommand("SELECT COUNT(id) AS count_records FROM population.queue")->queryAll();
         $count_queue = $res_count[0]['count_records'];
         if ($count_queue > 0) {
            self::setDistricts();
         }
	}
	
	protected static function setDistricts() {
		$res = Yii::app()->db->createCommand("SELECT * FROM population.queue")->queryRow();
        if(!$res) {
            echo 'Очередь пуста!'; 
            return false;
        }
		if ($res['type'] == 'region') {
			$region_id = (int)$res['id'];
			$districts = Yii::app()->db->createCommand("SELECT * FROM population.districts WHERE region_id = ".$region_id)->queryAll();
			foreach ($districts as $district) {
				$url_execute = self::$execute_host . '/district/info?id='.$district['id'];
				$response = file_get_contents($url_execute);
				if (!strstr($response,'Success')) {
					echo 'Информация по district_id '.$district['id'].' не получена!' . PHP_EOL;
				}
			}
		} 
		$sql="DELETE FROM population.queue WHERE id = ".$res['id'];
        Yii::app()->db->createCommand($sql)->execute();		
	}
	
}