<?php

/**
* Класс для получения данных с ogimet.com
*/

class OgimetParser {
    
    protected static $begin_year;
    protected static $hour = 6;
    protected static $station;
    protected static $no_low_cloudness = false;
    protected static $use_link_station = true;
    protected static $changed_station = null;
    
    public static function getData($station,$begin_year,$hour) {
        $end_year = date('Y');
        if ($begin_year >= $end_year) {
            return false;
        }
        if (strlen($station) == 4) {
            $station = '0'.$station;
        }
        if ($station >= 70000 && $station < 72500) {
            self::$no_low_cloudness = true;
        }
        if (self::$use_link_station) {
            $sql="SELECT add_station FROM link_stations WHERE main_station=$station AND active = 1";
		    $command=Yii::app()->db->createCommand();
		    $res=Yii::app()->db->createCommand($sql)->queryRow();
            if ($res) {
                self::$changed_station = $station;
                $station = $res['add_station'];   
            }
        }
        
        $sql="SELECT begin_year FROM ogimet.ogimet_stations WHERE station=$station";
		    $command=Yii::app()->db->createCommand();
		    $res=Yii::app()->db->createCommand($sql)->queryRow();
            if ($res) {
                $begin_year = $res['begin_year'];
            }
           
//        echo 'begin_year '.$begin_year.PHP_EOL; 
//        echo 'end_year '.$end_year.PHP_EOL;
//        return;
        
        self::$station = $station;
        self::$begin_year = $begin_year;
        self::$hour = $hour;
        $days = 31;

        
        
        for($year=$begin_year;$year<$end_year;$year++) {
             for($month=1;$month<13;$month++){
                 $link="http://www.ogimet.com/cgi-bin/gsynres?lang=en&ind=$station&ndays=$days&ano=$year&mes=$month&day=$days&hora=$hour&ord=REV&Send=Send";
                 $page=@file_get_contents($link);
                 self::parsePage($page,self::$station, $year);
             }
        }
    }
    

    
    private static function isFieldName($field, $name) {
        $field = trim(strip_tags($field));
        return strstr($field,$name);
    }
    
    protected static function parsePage($page, $station, $year) {
        
        if (strstr($page, 'No valid data found in database for ')) {
            return false;
        }
        
        $flag_snow = 0;
        $flag_sun = 0;
        $flag_gust = 0;
        $flag_precip = 0;
        $flag_low_cloudness = 0;
        
        
        list($noneed,$need) = explode("Daily summary at",$page);
        list($shlock,$thead) = explode("<THEAD>",$need);
	 
	    list($header,$buf) = explode('</thead>',$thead);
        
        list($shlock,$for_flags) = explode("</TR>",$thead);
        
        $indications=array('nieve'=>'снег','ventisca'=>'метель','iluvia'=>'дождь','llovizna'=>'изморось','tormenta'=>'гроза');
        
        $th_arr = explode('<TH',$shlock);
        $th = array();
        foreach($th_arr as $tag) {
            
            if (self::isFieldName($tag,'Date')) {
                $th['date'] = 1;
            }
            elseif (self::isFieldName($tag,'Temperature')) {
                $th['maxtemp'] = 2;
                $th['mintemp'] = 3;
                $th['midtemp'] = 4;
            }
            elseif (self::isFieldName($tag,'Wind')) {
                $th['wind_speed'] = 7;
                $offset = 0;
                if (strstr($tag, 'colspan="3"')) {
                    $offset = 2;
                }
                elseif (strstr($tag, 'colspan="2"')) {
                    $offset = 1;
                }
            }
            
        }
        
        foreach($th_arr as $tag) {
              if (self::isFieldName($tag,'Pres.')) {  
                  $th['pressure'] = 8 + $offset;
              }
              elseif (self::isFieldName($tag,'Prec.')) {  
                  $th['precipitation'] = 9 + $offset;
              }
              elseif (self::isFieldName($tag,'Tot') && self::isFieldName($tag,'Cl')) {
                  $th['cloudness'] = 10 + $offset;
              }
              elseif (self::isFieldName($tag,'low') && self::isFieldName($tag,'Cl')) {
                  $th['low_cloudness'] = 11 + $offset;
              }
        }
        if (!count($th)) {
            return false;
        }
        $next_ind = max($th);
        
        foreach($th_arr as $tag) {
            if (self::isFieldName($tag,'Sun')) {
                $next_ind++;
                $th['sun_hours'] = $next_ind;
            }
            elseif (self::isFieldName($tag,'Vis')) {
                $next_ind++;
                $th['visibility'] = $next_ind;
            }
            elseif (self::isFieldName($tag,'Snow')) {
                $next_ind++;
                $th['snow_cover'] = $next_ind;
            }
            
        }
        
        
        if(strstr($shlock,"Snow")<>""){
            $flag_snow=1;
        }
        if(strstr($shlock,"Sun")<>""){
            $flag_sun=1;
        }
        if(strstr($shlock,"Gust")<>""){
            $flag_gust=1;
        }
        
        list($need,$noneed) = explode("DISCLAIMER:",$need);
        list($noneed,$need) = explode("</THEAD>",$need);
        
        if(strstr($noneed,'Prec.')<>'') $flag_precip=1;
	    if(strstr($noneed,'low')<>'') $flag_low_cloudness=1;
        
        if($flag_low_cloudness)
	  	    $add_low_cloudness = 1;
	    else
	  	    $add_low_cloudness = 0;
            
       $tr_array=explode("<TR>",$need);
       
       foreach($tr_array as $tr){
           preg_match_all('#src="[^"]+#',$tr,$regs);
           $precip=0;
		   $thunder=0;
           
           foreach($regs[0] as $reg){
               if(strstr($reg,'nieve')<>'' or strstr($reg,'iluvia')<>'' or strstr($reg,'llovizna')<>'' or strstr($reg,'lluvia')<>''){
					$precip+=1;
			   }
				
			   if(strstr($reg,'ventisca')<>'' or strstr($reg,'tormenta')<>''){
					$thunder+=1;
			   }
           }
           
           $elements = explode("<TD",$tr);
           for($i=0;$i<count($elements);$i++){
               list($shlock,$data) = explode(">",strip_tags($elements[$i]));
               $info[$i]=$data;
           }
//           var_dump($info);
           
           $count_info=count($info);
           $temp=$info[count($info)-1];
           if(strstr($temp,"---")<>"" or strstr($temp,"0")<>""){
               $except=1;
           }
           else{
               $except=0;
           }
         
           
           list($month,$day) = explode("/",$info[1]);
           $month = (int)$month;
           $day = (int)$day;
           if (!$month || !$day) {
               continue;
           }
					
           
           
           
           if(!in_array('Tr',$info)){
               $last_index = count($info)-1;
           }
           
           if(stristr($header,'Snow')<>''){ 
			   $snow_cover=$info[$last_index];
			   if(stristr($header,'Sun')<>''){
					$sun_hours = $info[$last_index-2];
			   }
			   else{
					$sun_hours=null;
				}	
			}
			else{
				$snow_cover=null;
				if(stristr($header,'Sun')<>''){
					$sun_hours=$info[$last_index-1];	
				}
				else{
					$sun_hours=null;
				}			
			}
            if(strstr($snow_cover,'.')<>'') $snow_cover=0;
			if(!$precip) $precip='0';
			if(!$snow_cover) $snow_cover='0';
            
            if($no_low_cloudness)
				$spec_add=2;
			else
				$spec_add=0;
                
            $pressure = ((float)$info[9+$add_low_cloudness]);
            
            if($pressure > 600){
				$r = $info[10+$add_low_cloudness+$spec_add];
                $humidity=$info[6];
			}
			else{
				$r = $info[9+$add_low_cloudness+$spec_add];
				$humidity=$info[7];
            } 
            
					
			
            
            
            
            if($r<0)
			    $r=0;
			
                
            $command = Yii::app()->db->createCommand();
            $station = self::$station * 1;
            
           if (isset($th['cloudness'])) {
               if ($info[$th['cloudness']] == '---') {
                   $cloudness = 'null';
               }
               else {
                   $cloudness = (float)$info[$th['cloudness']];
                   $cloudness=($cloudness/8)*10;
                   if($cloudness>10)
				    $cloudness=10;
               }
               
           }
           else {
               $cloudness = 'null';
           }
            
            if (isset($th['low_cloudness'])) {
               if ($info[$th['low_cloudness']] == '---') {
                   $low_cloudness = 'null';
               }
               else {
                   $low_cloudness = (float)$info[$th['low_cloudness']];
                   $low_cloudness=($low_cloudness/8)*10;
                   if($low_cloudness>10)
				    $low_cloudness=10;
               }
               
           }
           else {
               $low_cloudness = 'null';
           }
           
           if ($cloudness != 'null' && $low_cloudness != 'null') {
               if ($low_cloudness > $cloudness) {
                   $temp = $cloudness;
                   $cloudness = $low_cloudness;
                   $low_cloudness = $temp;
               }
           }
           
           if (isset($th['sun_hours'])) {
               $sun_hours = (float)$info[$th['sun_hours']];
           }
           else {
               $sun_hours = 'null';
           }
           
           if (self::$changed_station) {
                $insert_station = self::$changed_station;
           }
            else {
                $insert_station = $station;
            }
           
           $link=(int)$insert_station." - $year - $month - $day";
            
            $arr = array(
                'station' => $insert_station,
                'year' => $year,
                'month' => $month,
                'day' => $day,
                'maxtemp' => (float)trim($info[$th['maxtemp']]),
                'mintemp' => (float)trim($info[$th['mintemp']]),
                'midtemp' => (float)trim($info[$th['midtemp']]),
                'events' => (int)trim($precip),
                'precipitation' => (float)trim($info[$th['precipitation']]),
                'cloudness' => $cloudness,
                'low_cloudness' => $low_cloudness,
                'wind_speed' => (float)trim($info[$th['wind_speed']]),
                'sun_hours' => $sun_hours,
                'snow_cover' => (int)$snow_cover,
                'humidity' => (float)$humidity,
                'pressure' => (float)$pressure,
                'link' => $link
            );
            
            if (is_null($sun_hours)) {
                $sun_hours = 'null';
            }
            if ($arr['midtemp'] == $arr['mintemp'] || $arr['midtemp'] == $arr['maxtemp']) {
                continue;
            }
            
            
            
            $sql = "INSERT IGNORE INTO `ogimet`.`ogimet_data`(`id`,`station`, `year`, `month`, `day`, `maxtemp`, `mintemp`, `midtemp`, `events`, `precipitation`, `cloudness`, `low_cloudness`, `wind_speed`, `sun_hours`, `snow_cover`, `humidity`, `pressure`, `link`) VALUES (null,$insert_station,$year,$month,$day,".$arr['maxtemp'].",".$arr['mintemp'].",".$arr['midtemp'].",".$arr['events'].",".$arr['precipitation'].",".$cloudness.",".$low_cloudness.",".$arr['wind_speed'].",".$sun_hours.",".$arr['snow_cover'].",".$arr['humidity'].",".$arr['pressure'].",'$link')";
            
            Yii::app()->db->createCommand($sql)->execute();
      }
   
   }    
       
    
}

?>