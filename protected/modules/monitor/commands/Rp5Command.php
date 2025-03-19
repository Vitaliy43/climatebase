<?php

class Rp5Command extends CConsoleCommand {

    protected static $main_station;

    public function actionMain() {
        $res_count=Yii::app()->db->createCommand("SELECT COUNT(station) AS count_records FROM archive_weather.rp5_queue")->queryAll();
            $count_queue = $res_count[0]['count_records'];
            if ($count_queue > 0) {
                self::executeMain();
            }
    }

    protected static function executeMain() {
        $res = Yii::app()->db->createCommand("SELECT station FROM archive_weather.rp5_queue")->queryRow();
        if(!$res) {
            echo 'Очередь пуста!'; 
            return false;
        }
        Climate::$station = $res['station'];
        Climate::$csv_divider = ';';
        $data = file(Climate::$data_path . '/rp5/' . Climate::$station . '.csv');
        $counter = 0;
        $dates = [];
        echo 'Старт процесса - ' . date('H:i:s') . PHP_EOL;

        foreach ($data as $row) {
            if (substr($row,0,1) == '#' || strpos($row,'Местное время')) {
                continue;
            }
            $columns = explode(Climate::$csv_divider,$row);
            $count_columns = count($columns);
            $date = str_replace('"','',$columns[0]);
            $temp = (float)str_replace('"','',$columns[1]);
            $pressure = (float)str_replace('"','',$columns[2]);
            $humidity = (float)str_replace('"','',$columns[5]);
            $wind_speed = (float)str_replace('"','',$columns[7]);
            $common_cloudness = str_replace('"','', $columns[10]);
            $common_cloudness = str_replace('%.','',$common_cloudness);
            if (strpos($common_cloudness, 'Облаков нет')) {
                $common_cloudness = 0;
            }
            elseif ($common_cloudness == '70 - 80') {
                $common_cloudness = 7.5;
            }
            elseif ($common_cloudness == '20 - 30') {
                $common_cloudness = 2.5;
            }
            elseif ($common_cloudness == '90  или более') {
                $common_cloudness = 9;
            }
            else {
                $common_cloudness = ((int)$common_cloudness)/10;
            }

            $mintemp = str_replace('"','',$columns[14]);
            $maxtemp = str_replace('"','',$columns[15]);
            if (preg_match('/\d+/', $mintemp)) {
                $mintemp = (float)$mintemp;
            }
            else {
                $mintemp = 'null';
            }

            if (preg_match('/\d+/', $maxtemp)) {
                $maxtemp = (float)$maxtemp;
            }
            else {
                $maxtemp = 'null';
            }

            $low_cloudness = str_replace('"','', $columns[17]);
            $low_cloudness = str_replace('%.','',$low_cloudness);
            if (strpos($low_cloudness, 'Облаков нет')) {
                $low_cloudness = 0;
            }
            elseif ($low_cloudness == '70 - 80') {
                $low_cloudness = 7.5;
            }
            elseif ($low_cloudness == '20 - 30') {
                $low_cloudness = 2.5;
            }
            elseif ($low_cloudness == '90  или более') {
                $low_cloudness = 9;
            }
            else {
                $low_cloudness = ((int)$low_cloudness)/10;
            }

            if ($count_columns == 31) {
                $precip = (float)str_replace('"','',$columns[24]);  
            }
            elseif ($count_columns == 32){
                $precip = (float)str_replace('"','',$columns[25]);  
            }
            else {
                $precip = (float)str_replace('"','',$columns[23]);  
            }

            if ($precip < 0) {
                echo 'Ошибка. Кол-во осадков не иожет быть отрицательной величиной'.PHP_EOL;
                exit;
            }

            list($date, $time) = explode(' ', $date);
            list($day, $month, $year) = explode('.', $date);
            list($hour, $minute) = explode(':', $time);
            $day = (int)$day;
            $month = (int)$month;

            if ($year == date('Y')) continue;

            $sql="INSERT INTO archive_weather.rp5_data(`id`, `station`, `year`, `month`, `day`, `hour`, `temp`, `min_temp`,`max_temp`,`common_cloudness`, `low_cloudness`, `events`, `precipitation`, `pressure`, `humidity`,`wind_speed`) VALUES (null,".Climate::$station.",$year,$month,$day,$hour,$temp,$mintemp,$maxtemp,$common_cloudness,$low_cloudness,'',$precip,$pressure,$humidity,$wind_speed)";
            Yii::app()->db->createCommand($sql)->execute();
            $dates[] = "$year-$month-$day";

        }
        echo 'Данные по станции '.Climate::$station.' добавлены в транзитную таблицу - ' . date('H:i:s') . PHP_EOL;
        $dates = array_unique($dates);
        self::setOgimet(Climate::$station, $dates);
        echo 'Данные по станции '.Climate::$station.' добавлены в основную базу - ' . date('H:i:s') . PHP_EOL;
        $sql = "DELETE FROM archive_weather.rp5_queue WHERE station = ".Climate::$station;
        Yii::app()->db->createCommand($sql)->execute();
        Yii::app()->db->createCommand("TRUNCATE archive_weather.rp5_data")->execute();
        $sql = "SELECT * FROM archive_weather.stations WHERE station = ".Climate::$station;
        $result = Yii::app()->db->createCommand($sql)->queryRow();
        if (!$result) {
            $sql="INSERT INTO archive_weather.stations(`id`, `station`, `table_id`) VALUES (null,".Climate::$station.",0)";
            Yii::app()->db->createCommand($sql)->execute();
            echo 'Добавлена запись в таблицу stations. Возможно потребуется новая запись в climatebase.common_info'.PHP_EOL;
        }
        
        if (self::$main_station) {
            $sql="INSERT IGNORE INTO new_climatebase.queue(`station`) VALUES (".self::$main_station.")";
        Yii::app()->db->createCommand($sql)->execute();
            $response = self::callSetClimate(self::$main_station);
        }
        else {
            $sql="INSERT IGNORE INTO new_climatebase.queue(`station`) VALUES (".Climate::$station.")";
        Yii::app()->db->createCommand($sql)->execute();
            $response = self::callSetClimate(Climate::$station);
        }
        
        if ($response) {
            echo trim(strip_tags($response)).PHP_EOL;
        }
        else {
            echo 'Ошибка добавления данных'.PHP_EOL;
        }
    }

    protected static function callSetClimate($station) {
        $sql = "SELECT period_id FROM archive_weather.rp5_stations WHERE station = ".$station;
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        $url = 'http://climatebase.vit/monitor/weather/set?period='.$row['period_id'];
        return file_get_contents($url);	
    }

    protected static function setOgimet($station,$dates) {

        $sql = "SELECT main_station FROM new_climatebase.link_stations WHERE add_station = $station";
        $row_link = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row_link) {
            $main_station = $row_link['main_station'];
        }
        
        foreach ($dates as $date) {
            list($year,$month,$day) = explode('-',$date);
            $sql = "SELECT MAX(max_temp) AS maxtemp, MIN(min_temp) AS mintemp, AVG(temp) AS midtemp, SUM(precipitation) AS precip, AVG(common_cloudness) AS cloudness, AVG(low_cloudness) AS low_cloudness, AVG(wind_speed) AS wind_speed, AVG(pressure) AS pressure, AVG(humidity) AS humidity 
            FROM archive_weather.rp5_data WHERE station = $station AND year = $year AND month = $month AND day = $day";
            $result = Yii::app()->db->createCommand($sql)->queryRow();
            if (is_null($result['mintemp']) || is_null($result['maxtemp'])) continue;
            $maxtemp = round($result['maxtemp'],1);
            $mintemp = round($result['mintemp'],1);
            $midtemp = round($result['midtemp'],1);
            if ($mintemp > $midtemp || $mintemp > $maxtemp) continue;
            $precip = round($result['precip'],1);
            $cloudness = round($result['cloudness'],1);
            $low_cloudness = round($result['low_cloudness'],1);
            $wind_speed = round($result['wind_speed'],1);
            $pressure = round($result['pressure'],1);
            $humidity = round($result['humidity'],1);
            if (isset($main_station)) {
                $link = "$main_station - $year - $month - $day";
                $sql="INSERT IGNORE INTO ogimet.ogimet_data(`id`, `station`, `year`, `month`, `day`, `maxtemp`, `mintemp`,`midtemp`,`precipitation`, `cloudness`, `low_cloudness`, `wind_speed`,`humidity`, `pressure`,`link`) VALUES (null,$main_station,$year,$month,$day,$maxtemp,$mintemp,$midtemp,$precip,$cloudness,$low_cloudness,$wind_speed,$humidity,$pressure,'$link')";
                self::$main_station = $main_station;
            }
            else {
                $link = "$station - $year - $month - $day";
                $sql="INSERT IGNORE INTO ogimet.ogimet_data(`id`, `station`, `year`, `month`, `day`, `maxtemp`, `mintemp`,`midtemp`,`precipitation`, `cloudness`, `low_cloudness`, `wind_speed`,`humidity`, `pressure`,`link`) VALUES (null,$station,$year,$month,$day,$maxtemp,$mintemp,$midtemp,$precip,$cloudness,$low_cloudness,$wind_speed,$humidity,$pressure,'$link')";
            }
            Yii::app()->db->createCommand($sql)->execute();
        }
        
        
    }

}

?>