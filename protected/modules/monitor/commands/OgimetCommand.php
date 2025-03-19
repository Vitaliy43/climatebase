<?php

class OgimetCommand extends CConsoleCommand {
    
    public function actionMain() {
        $count_queue = 1;
        while ($count_queue > 0) {
            $res_count=Yii::app()->db->createCommand("SELECT COUNT(station) AS count_records FROM new_climatebase.queue")->queryAll();
            $count_queue = $res_count[0]['count_records'];
            if ($count_queue > 0) {
                $row=Stations::model()->getStationFromQueue();
                if(!$row) {
                   echo 'Очередь пуста!'; 
                   break;
                }
                Climate::$station = $row['station'];
                self::executeMain($row);
                Yii::app()->db->createCommand("DELETE FROM new_climatebase.queue WHERE station = ".Climate::$station)->execute();
            }
        }
    }
    
    protected static function executeMain($row) {
       	
        $sql = "SELECT longitude_float FROM climatebase.common_info WHERE station = ".Climate::$station;
        $res_old_climatebase = Yii::app()->db->createCommand($sql)->queryAll();
        $longitude = $res_old_climatebase[0]['longitude_float'];
        if (!$longitude) {
            echo 'Отсутствуют данные о долготе станции'.PHP_EOL;
            return false;
        }
        $sql = "SELECT MAX(year) as max_year FROM ogimet.ogimet_data WHERE station = ".Climate::$station;
        $res_ogimet = Yii::app()->db->createCommand($sql)->queryAll();
        

        if (!isset($res_ogimet[0]['max_year'])) {
            $max_year = 2000;   
        }
        else {
            $max_year = $res_ogimet[0]['max_year'] + 1;
        }
        
        $sql = "SELECT COUNT(id) as num_rows FROM ogimet.ogimet_data WHERE station = ".Climate::$station;
        $res_ogimet = Yii::app()->db->createCommand($sql)->queryRow();
        $num_before = $res_ogimet['num_rows'];
        
        if ($max_year == date('Y')) {
            echo 'Станции не требуется обновление данных'.PHP_EOL;
            return false;
        }
        if ($longitude < 70) {
            $hour = 6;
        }
        elseif ($longitude >= 70 and $longitude < 105) {
            $hour = 5;
        }
        else {
            $hour = 12;
        }
        if (Climate::$station > 80000 && Climate::$station < 90000) {
            $hour = 12;
        }
        OgimetParser::getData(Climate::$station,$max_year,$hour);
        
        $sql = "SELECT COUNT(id) as num_rows FROM ogimet.ogimet_data WHERE station = ".Climate::$station;
        $res_ogimet = Yii::app()->db->createCommand($sql)->queryRow();
        $num_after = $res_ogimet['num_rows'];
        $differ = $num_after - $num_before;
        
        echo 'По станции '.Climate::$station.' добавлено '.$differ.' записей'.PHP_EOL;  
     
    }
}

?>