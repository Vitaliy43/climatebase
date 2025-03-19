<?php

class MeteoCommand extends CConsoleCommand {
    // Потрошим csv с meteo.ru
    
    public function actionMain()
    {
        $count_queue = 1;
        while ($count_queue > 0) {
            $res_count=Yii::app()->db->createCommand("SELECT COUNT(station) AS count_records FROM new_climatebase.queue")->queryAll();
            $count_queue = $res_count[0]['count_records'];
            if ($count_queue > 0) {
                self::executeMain();
            }
        }
        
    }
    
    public function actionCloudness()
    {
        $file = file(Climate::$data_path . '/cloudness/archive.txt');
        foreach ($file as $row) {
            $row = str_replace('   ',' ',$row);
            $row = str_replace('  ',' ',$row);
            $columns = explode(';',$row);
            $types = array(1 => 'common', 2 => 'low');
            for ($i = 2; $i < 14; $i++) {
                if (isset($columns[$i])) {
                    $columns[$i] = trim($columns[$i]);
                }
                
            }
            $arr = array(
                'station' => $columns[0],
                'year' => $columns[1],
                'type' => $types[(int)$columns[2]],          
            );
            $months_names = array(
                1 => 'jan',
                2 => 'feb',
                3 => 'mar',
                4 => 'apr',
                5 => 'may',
                6 => 'jun',
                7 => 'jul',
                8 => 'aug',
                9 => 'sep',
                10 => 'oct',
                11 => 'nov',
                12 => 'dec'
            );
            foreach ($months_names as $key=>$value) {
                if (isset($columns[$key +2]) && $columns[$key+2]) {
                    $arr[$value] = (float)$columns[$key+2];
                }
            }
            
            $command = Yii::app()->db->createCommand();
            $command->insert(Climate::$archive_base.'.meteo_cloudness', $arr);  
        }
        $res_count=Yii::app()->db->createCommand("SELECT COUNT(id) AS count_records FROM ".Climate::$archive_base.'.meteo_cloudness')->queryAll();
        echo $res_count[0]['count_records'].' records inserted'.PHP_EOL;
    }
    
    protected static function executeMain()
    {
        $row=Stations::model()->getStationFromQueue();
       if(!$row) {
           echo 'Очередь пуста!'; 
           return false;
       }	
        Climate::$station = $row['station'];

        $buffer_station=Stations::model()->find('station=:station',array(':station'=>Climate::$station));
		if(!$buffer_station){
			echo 'Станция '.Climate::$station.' отсутствует в списке станций!'.PHP_EOL;
			Stations::model()->deleteStationFromQueue(Climate::$station);
			return false;
		}
        $sql='SELECT MAX(year) AS max_year FROM archive_weather.tutiempo_buffer_'.$buffer_station['table_id'].' WHERE station = '.Climate::$station;
		$res_data=Yii::app()->db->createCommand($sql)->queryAll();
        Yii::app()->db->createCommand("TRUNCATE ".Climate::$transit_table)->execute();
        echo 'station '.Climate::$station.PHP_EOL;
        $data = file(Climate::$data_path . '/main/' . Climate::$station . '.dat');
        foreach ($data as $row) {
            $row = str_replace('   ',' ',$row);
            $row = str_replace('  ',' ',$row);
            $columns = explode(Climate::$csv_divider,$row);
            $current_station = (int)$columns[0];
            $year = (int)$columns[1];
            $month = (int)$columns[2];
            $day = (int)$columns[3];
            $mintemp = (float)$columns[5];
            $midtemp = (float)$columns[7];
            $maxtemp = (float)$columns[9];
            $precip = (float)$columns[11];
            if ($mintemp == $midtemp && $midtemp == $maxtemp && $maxtemp == $precip) {
                continue;
            }
            if ($year == Climate::$max_year) continue;
            if ($year <= $res_data[0]['max_year']) continue;
            $link = Climate::$station . ' - ' . $year . ' - ' . $month . ' - ' . $day;
            $sql="INSERT INTO ".Climate::$transit_table."(`id`, `station`, `year`, `month`, `day`, `mintemp`, `midtemp`, `maxtemp`, `precip`, `link`) VALUES (null,$current_station,$year,$month,$day,$mintemp,$midtemp,$maxtemp,$precip,'$link')";
            Yii::app()->db->createCommand($sql)->execute();
        }
        $res_count=Yii::app()->db->createCommand("SELECT COUNT(id) AS count_records FROM ".Climate::$transit_table)->queryAll();
        echo $res_count[0]['count_records'].' records inserted'.PHP_EOL;
        $sql = "INSERT IGNORE INTO archive_weather.tutiempo_buffer_".$buffer_station['table_id']." SELECT * FROM ".Climate::$transit_table;
        Yii::app()->db->createCommand($sql)->execute();
        Yii::app()->db->createCommand("DELETE FROM new_climatebase.queue WHERE station = ".Climate::$station)->execute();
    }
}

?>