<?php

class WeatherData
{
    	
	
	public $table='';
	public $view='temp_table';
	public $old_table='';
	public $sumat;
	public $q_temp;
	public $deduct;
	public $num_years;
	public $current_year;
	public $table_summary='new_climate_summary.duration_sun_hours';
	public $exceptions_years;
	public $db='archive_weather';
	public $db_speranca='climate_speranca';
	public $pogodarunet_type=false;
	public $from_year=1959;
	public $from_year_precip=1959;
	public $evaporation_not_bigger_1=false;
	public $active_t=10;
	public $end_year=2015;
	public $avg_sun_duration=false;
	public $limit_precip=0.1;
	public $precip_year;
	public $is_south_hemisphere=false;
	public $number_days_correction=false;
	public $longitude=0;
    public $connect;
	public $with_meteo=1;
	
	public $typical_landscapes=array(
	'tropical humid'=>'тропические лесные',
	'tropical monsoon'=>'тропические сезонно-влажные лесные',
	'tropical semihumid'=>'тропические лесо-саванновые',
	'tropical semiarid'=>'тропические саванновые',
	'tropical arid'=>'тропические полупустынные',
	'tropical extraarid'=>'тропические пустынные',
	'subtropical humid'=>'субтропические лесные',
	'subtropical monsoon'=>'субтропические муссонные лесные',
	'subtropical semihumid'=>'субтропические лесостепные',
	'subtropical grassland'=>'субтропические редколесные',
	'subtropical savanna-forest'=>'субтропические лесо-саванновые',
	'subtropical mediterranean'=>'субтропические средиземноморские лесные',
	'subtropical semiarid'=>'субтропические степные',
	'subtropical savanna'=>'субтропические саванновые',
	'subtropical typical-savanna'=>'субтропические типично-саванновые',
	'subtropical semiarid mediterranean'=>'субтропические саванн и редколесий',
	'subtropical arid'=>'субтропические полупустынные',
	'subtropical arid-savanna'=>'субтропические пустынно-саванновые',
	'subtropical extraarid'=>'субтропические пустынные',
	'subboreal humid'=>'суббореальные лесные',
	'subboreal monsoon'=>'суббореальные лесные',
	'subboreal semihumid'=>'суббореальные лесостепные',
	'subboreal semiarid'=>'суббореальные степные',
	'subboreal arid'=>'суббореальные полупустынные',
	'subboreal extraarid'=>'суббореальные пустынные',
	'subboreal-transitive humid'=>'подтаежные',
	'subboreal-transitive semihumid'=>'бореальные лесостепные',
	'subboreal-transitive semiarid'=>'бореальные степные',
	'subboreal-transitive arid'=>'бореальные полупустынные',
	'subboreal-transitive extraarid'=>'бореальные пустынные',
	'taiga humid'=>'таежные',
	'taiga semihumid'=>'таежные',
	'taiga semiarid'=>'бореальные лесостепные',
	'taiga arid'=>'бореальные степные',
	'taiga extraarid'=>'бореальные пустынные',
	'forest-meadow humid'=>'лесотундровые',
	'forest-meadow semihumid'=>'лесотундровые',
	'forest-meadow semiarid'=>'лесотундровые',
	'forest-meadow arid'=>'бореальные полупустынные',
	'forest-meadow extraarid'=>'бореальные пустынные',
	'subpolar humid'=>'лесотундровые',
	'subpolar semihumid'=>'лесотундровые',
	'subpolar semiarid'=>'криостепные',
	'subpolar arid'=>'криостепные',
	'subpolar extraarid'=>'криопустынные',
	'subpolar-tundra humid'=>'тундровые',
	'subpolar-tundra semihumid'=>'тундровые',
	'subpolar-tundra semiarid'=>'полярные криостепные',
	'subpolar-tundra arid'=>'полярные криостепные',
	'subpolar-tundra extraarid'=>'полярные криопустынные',
	'polar humid'=>'арктические',
	'polar semihumid'=>'арктические',
	'polar semiarid'=>'арктические',
	'polar arid'=>'арктические',
	'polar extraarid'=>'арктические'
	);
	
	protected $months_lengths=array(1=>31,2=>28.25,3=>31,4=>30,5=>31,6=>30,7=>31,8=>31,9=>30,10=>31,11=>30,12=>31);
	
	
	function __construct($table,$station,$connect) {
		
		$this->table=$this->db.'.'.$table;
		$this->view=$this->db.'.'.$this->view;
		$this->current_year = date('Y');
        $this->connect=$connect;
        if(isset($_GET['from_year']))
            $this->from_year_precip=(int)$_GET['from_year'];
		//echo 'from_year_precip '.$this->from_year_precip.'<br>';
		
	}
	

	function get_t1($station,$month=null){
        
        if($month)
			$month="AND month=$month";
		else
			$month='';
		$sql = "SELECT AVG(mintemp) AS avg_min
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    year BETWEEN $this->from_year AND $this->current_year    
        ";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        return round($row['avg_min'],1);
		
	}
	
	function get_mean_maximum($station,$month=null){
		
		global $base;
		if($month)
			$month="and month=$month";
		else
			$month='';
					
		$res=$base->select_record($this->db.'.mean_maximum','avg(temperature) as avg_max',"station=$station $month",null,true);
//		var_dump($res);
		$result=round($res['avg_max'],1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_mean_minimum($station,$month=null){
		
		global $base;
		if($month)
			$month="and month=$month";
		else
			$month='';
					
		$res=$base->select_record($this->db.'.mean_minimum','avg(temperature) as avg_min',"station=$station $month",null,true);
		//var_dump($res);
		$result=round($res['avg_min'],1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_midtemp($station,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
		$sql = "SELECT AVG(midtemp) AS avg_mid
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    year BETWEEN $this->from_year AND $this->current_year    
        ";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        return round($row['avg_mid'],1);
	}
	
	function get_t2($station,$month=null){
		
        if($month)
			$month="AND month=$month";
		else
			$month='';
		$sql = "SELECT AVG(maxtemp) AS avg_max
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    year BETWEEN $this->from_year AND $this->current_year    
        ";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        return round($row['avg_max'],1);
	}
	
	function get_abs_min($station,$month=null,&$record_year=null){
        
        if($month)
			$month="AND month=$month";
		else
			$month='';
		$sql = "SELECT mintemp AS minimum, year
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    year != $this->current_year 
                ORDER BY
                    mintemp
                LIMIT 
                    1   
        ";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        if(!is_null($record_year))
			$record_year=$row['year'];
        return round($row['minimum'],1);
		
	}
	
	function get_abs_max($station,$month=null,&$record_year=null){
        
        if($month)
			$month="AND month=$month";
		else
			$month='';
		$sql = "SELECT maxtemp AS maximum, year
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    year != $this->current_year 
                ORDER BY
                    maxtemp DESC
                LIMIT 
                    1   
        ";
        $result=mysqli_query($this->connect,$sql);
        $row = $result->fetch_assoc();
        if(!is_null($record_year))
			$record_year=$row['year'];
        return round($row['maximum'],1);
		
	}
	
	function correct_precipitation($precip_in_month,$days_precip_in_month,$num_correction){
		if($days_precip_in_month/10 > 1)
			$q=1;
		else
			$q=1-(1-$days_precip_in_month/10)*$num_correction/10;
		$precip_in_month*=$q;
		return round($precip_in_month,1);
	}
	
	function get_evaporation($t_arr,$r_arr,$days_precipitation,$num_correction){
		
		$etalon_t = 18;
		$etalon_r = 60;
		$result = array();
//		$result['year'] = '';
		$count = 0;
		foreach($t_arr as $key=>$value){
			if($key == 'point' or $key == 'link' or $key == 'year' or $key == 'station' or $key == 'period_id')
				continue;
			if($value >= $etalon_t){
				$need_r = ((($value - $etalon_t)/10)+1)*$etalon_r;
			}
			else{
				$need_r = $etalon_r/((($etalon_t - $value)/10)+1);
			}
			$buffer_q=$days_precipitation[$key]/10;
			if($buffer_q>1)
				$buffer_q=1;
			$q_correct=round(1-(1-$buffer_q)*$num_correction/10,2);
			if($this->number_days_correction)
				$r_arr[$key]*=$q_correct;
			$q = $r_arr[$key]/$need_r;
//			if($this->evaporation_not_bigger_1){
				if($q>1)
					$q=1;
//			}
			
			$count += $q;
			$result[$key] = round($q,2);
		}
		$result['year'] = round($count/12,2);
		return $result;
	}
	
	function get_evaporation_sumat($evaporation,$temps){
		$count=0;
		$buffer=0;
		foreach($evaporation as $key=>$value){
			if($key == 'point' or $key == 'link' or $key == 'year' or $key == 'period_id' or $key == 'station')
				continue;
			$temp = $temps[$key];
			if($temp>=$this->active_t){
				$count++;
				$buffer+=$value;
			}
		}
		return round($buffer/$count,2);
	}
	
	function get_coppen_q($precipitation,$temps,$return_add=false){
		$arr_precips=array();
		if(!$this->is_south_hemisphere)
			$warm_months=array(4,5,6,7,8,9);
		else
			$warm_months=array(10,11,12,1,2,3);
		$months_names=array(
		'jan'=>1,
		'feb'=>2,
		'mar'=>3,
		'apr'=>4,
		'may'=>5,
		'jun'=>6,
		'jul'=>7,
		'aug'=>8,
		'sep'=>9,
		'oct'=>10,
		'nov'=>11,
		'dec'=>12
		);
		$arr_temps=array();
		$warm_precips=0;
		foreach($precipitation as $key=>$value){
			if($key == 'point' or $key == 'link' or $key == 'year' or $key=='station' or $key=='period_id')
				continue;
			if(!ctype_digit($key) && $months_names[$key])
				$key=$months_names[$key];
			$arr_precips[]=$value;
			$value=(float)$value;
//			echo 'key '.$key.'<br>';
			if(in_array($key,$warm_months))
				$warm_precips+=$value;
		}
		
		foreach($temps as $key=>$value){
			if($key == 'point' or $key == 'link' or $key == 'year')
				continue;
			$arr_temps[]=$value;
		}
//		$avg_temps=array_sum($arr_temps)/12;
		$avg_temps=$temps['year'];
		if($avg_temps<0)
			$sum_temps=0;
		$all_precips=array_sum($arr_precips);
		$q_precip=$warm_precips/$all_precips;
//		echo 'warm_precips '.$warm_precips.'<br>';
		if($q_precip<=0.3){
			$add=0;
		}
		elseif($q_precip>=0.7){
			$add=280;
		}
		else{
//			$add = 140;
			$add = (($q_precip-0.3)/0.4)*280;
		}
		if($return_add)
			return $add;
		if($add<140)
			$add=140;
		if($this->longitude)
			$add=(280/180)*$this->longitude;
		if($avg_temps<0)
			$avg_temps=0;
		$q=$all_precips/((($avg_temps*20)+$add)*2);
		$q=round($q,2);
		return $q;
	}
	
	
	function get_hydrotermic_q($precipitation,$sumat){
		$count_sumat=0;
		$q_sumat=0;
		$arr_q=array();

		foreach($precipitation as $key=>$value){
			if($key == 'point' or $key == 'link' or $key == 'year' or $key=='station' or $key=='period_id')
				continue;
			$current_sumat=$sumat[$key];
//			echo 'current_sumat '.$current_sumat.'<br>';
			if($current_sumat>=100){
				$arr_q[$key]=($value/$current_sumat)*10;
				if($arr_q[$key]>1.3)
					$arr_q[$key]=1.3;
				if($arr_q[$key]<0.4)
					$arr_q[$key]=0.4;
				$count_sumat++;
				$q_sumat+=$arr_q[$key];
				$arr_q[$key]=round($arr_q[$key],2);
			}
			else{
				$arr_q[$key]='-';
			}
			}
			$arr_q['year']=round($q_sumat/$count_sumat,2);
			return $arr_q;
		}
		
	
	
	function get_specail_coppen_q($avg_temp,$precip,$add){
		if($avg_temp<0)
			$avg_temp=0;
		$q=$precip/((($avg_temp*20)+$add)*2);
		$q=round($q,3);
		return $q;
	}
	
	function get_add_by_longitude($longitude){
		$add=($longitude/180)*280;
		return $add;
	}
	
	
	
	function get_new_precip_by_coppen($avg_temp,$longitude,$q){
//		echo 'longitude '.$longitude.'<br>';
		$add=$this->get_add_by_longitude($longitude);
//		echo 'add '.$add.'<br>';
		$precip=$q*((($avg_temp*20)+$add)*2);
		$precip=round($precip,1);
		return $precip;
	}
	
	function get_precipitation($station,$month=null){
		$month_int=$month;
        if($month)
			$month="AND month=$month";
		else
			$month='';
		if($this->with_meteo){
			  $year="year BETWEEN $this->from_year_precip AND $this->end_year";
			  $ogimet_begin=2016;
			  $union='';
		}
		else{
			$year="year BETWEEN $this->from_year_precip AND 2000";
			$ogimet_begin=2001;
		}
		
		 $union=" UNION
			 SELECT AVG(precipitation) AS avg_precip
                FROM ogimet.ogimet_data
                WHERE 1=1
                AND 
                    station=$station $month
                AND year>=$ogimet_begin
			 ";
		if($this->from_year_precip==1959) 
			$union='';

		$sql = "SELECT AVG(precip) AS avg_precip
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    $year 
				$union
        ";

		$result=mysqli_query($this->connect,$sql);
        $arr=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[]=$row['avg_precip'];
        }
		if(isset($arr[1]) && $arr[1]){
			  $avg_precip=($arr[0]+$arr[1])/2;
		}
		else{
			$avg_precip=$arr[0];
		}
//		echo 'avg_precip '.$avg_precip. '<br>';

        if(!$month){
            return round($avg_precip*365);
        }
        else{
            return round($avg_precip*$this->months_lengths[$month_int]);
        }
        
	}
	
	function get_cold_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
			if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
//		if($month and $year)
			if(!strpos($this->table,'ogimet_data'))
				$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and mintemp<0 and year != $this->current_year",null,true);
			else
				$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and mintemp<0 and year != $this->current_year",null,true);


		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function precip_for_evaporation($precip_days,$all_precip){
		$q=(($all_precip+$precip_days['year']*10)/2)/$precip_days['year'];
//		$q=($precip_days['year']*10)/$precip_days['year'];
		$arr=array();
		$arr['point']='';
		$arr['link']=$precip_days['link'];
		$arr['year']=0;
		
		foreach($precip_days as $key=>$value){
			if($key=='link' || $key=='point' || $key=='year'){
				continue;
			}
			else{
				$arr[$key]=$value*$q;
				$arr[$key]=round($arr[$key],1);
				$arr['year']+=$arr[$key];
			}
			
		}
		return $arr;
	}
	
	function get_precip_days($station,$month=null){
        
        $month_int=$month;
        if($month)
			$month="AND month=$month";
		else
			$month='';
        if($this->from_year){
            $year="year BETWEEN $this->from_year AND $this->current_year";
        }
        else{
            $year="year != $this->current_year";    
        }
		$sql = "SELECT COUNT(precip) AS num
                FROM $this->table
                WHERE 1=1
                AND
                    precip > 0
                AND 
                    station=$station $month
                AND
                    $year 
                UNION
                SELECT COUNT(precip) AS num
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    $year 
                
        ";
        $result=mysqli_query($this->connect,$sql);
        $arr=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[]=$row['num'];
        }
        $count_precip=$arr[0];
        $count_all=$arr[1];
        if(!$month){
            return round(($count_precip/$count_all)*365,1);
        }
        else{
            return round(($count_precip/$count_all)*$this->months_lengths[$month_int],1);
        }
		
	}
	
	function get_summer_days($station,$month=null){
        
        $month_int=$month;
        if($month)
			$month="AND month=$month";
		else
			$month='';
            
        $year="year != $this->current_year";    
        
		$sql = "SELECT COUNT(midtemp) AS num
                FROM $this->table
                WHERE 1=1
                AND
                    midtemp >= 15
                AND 
                    station=$station $month
                AND
                    $year 
                UNION
                SELECT COUNT(midtemp) AS num
                FROM $this->table
                WHERE 1=1
                AND 
                    station=$station $month
                AND
                    $year 
                
        ";
        $result=mysqli_query($this->connect,$sql);
        $arr=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[]=$row['num'];
        }
        $count_summer=$arr[0];
        $count_all=$arr[1];
        if(!$month){
            return round(($count_summer/$count_all)*365,1);
        }
        else{
            return round(($count_summer/$count_all)*$this->months_lengths[$month_int],1);
        }
		
	}
	
	function is_savanna($precipitation){
		$arr=array();
		foreach($precipitation as $key=>$value){
			if($key=='link' or $key=='point' or $key=='year')
				continue;
			$arr[]=$value;
		}
		$min_precip=min($arr);
		echo 'min_precip '.$min_precip.'<br>';
		if($min_precip>=60)
			return 'humid';
		elseif($min_precip>=$precipitation['year']/25)
			return 'semihumid';
		else
			return 'semiarid';
	}
	
	function get_landscapes($sumat_days,$summer_days,$average_temperature,$precipitation,$sumat,$q_coppen){
		$landscapes = '';
		if($summer_days['year']>330){
			$type='tropical';
		}
		elseif($summer_days['year']>=180 || $average_temperature['year']>=14){
			$type='subtropical';
		}
		elseif($sumat_days['year']>=150 && $summer_days['year']<180){
			$type='subboreal';
		}
		elseif($sumat_days['year']>=120 && $sumat_days['year']<150){
			$type='subboreal-transitive';
		}
		elseif($sumat_days['year']>=75 && $sumat_days['year']<120 && $summer_days['year']>=30.4){
			$type='taiga';
		}
		elseif($sumat_days['year']>=75 && $sumat_days['year']<120 && $summer_days['year']<30.4){
			$type='forest-meadow';
		}
		elseif($sumat_days['year']>=45 && $sumat_days['year']<75 && $summer_days['year']<30.4){
			$type='subpolar';
		}
		elseif($sumat_days['year']>=10 && $sumat_days['year']<45){
			$type='subpolar-tundra';
		}
		elseif($sumat_days['year']<10){
			$type='polar';
		}
		
		if($type=='forest-meadow' && $sumat['year']>=1200)
			$type='taiga';
			
		if(($type=='forest-meadow' || $type=='subpolar') && $sumat['year']<800)
			$type='subpolar-tundra';
			
		if($type=='subboreal-transitive' && $sumat['year']<1800)
			$type='taiga';
		
		if($type=='tropical' && $sumat['year']<8000)
			$type='subtropical';
			
		if(min($average_temperature)>=5 && $type=='subboreal' && $sumat['year']>=4000)
			$type='subtropical';


		$type.=' '.$this->get_type_moisture($q_coppen);
				

		if($type=='subtropical semiarid' && $sumat['year']>=8000)
			$type='subtropical savanna';

	
		return $this->typical_landscapes[$type];
		
	}
	
	function is_mediterranean($hidrotermic,$type){
		$count_humid=0;
		$count_empty=0;
			foreach($hidrotermic as $key=>$value){
				if($value=='-')
					$count_empty++;
				if($key=='year' or $key=='link' or $key=='point' or $value=='-')
					continue;
				if($value>=1.3)	
					$count_humid++;
			}
			if($type=='subtropical semihumid' && !$count_empty){
				if($count_humid>=6)
					return $type.' mediterranean';
				elseif($count_humid>=4)
					return 'subtropical semiarid mediterranean';

			}
			elseif($type=='subtropical semiarid' && !$count_empty){
				if($count_humid>=4)
					return 'subtropical semiarid mediterranean';
				else
					return $type;
			}
			else
				return $type;

	}
	
	function get_type_moisture($coppen_q){

		if($coppen_q>=1)
			return 'humid';
		elseif($coppen_q>=0.75)
			return 'semihumid';
		elseif($coppen_q>=0.5)
			return 'semiarid';
		elseif($coppen_q>=0.25)
			return 'arid';
		else
			return 'extraarid';
	}
	
	function get_sumat_days($station,$month=null){
		
		$month_int=$month;
        if($month)
			$month="AND month=$month";
		else
			$month='';
            
        
		$sql = "SELECT COUNT(new_midtemp) AS num
                FROM $this->view
                WHERE 1=1
                AND
                    new_midtemp >= 10
				$month
                UNION
                SELECT COUNT(new_midtemp) AS num
                FROM $this->view
                WHERE 1=1
                $month
                
        ";

        $result=mysqli_query($this->connect,$sql);
        $arr=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[]=$row['num'];
        }
        $count_sumat=$arr[0];
        if(count($arr)==1)
            $count_all=$count_sumat;
        else
            $count_all=$arr[1];
            
        if(!$month){
            return round(($count_sumat/$count_all)*365,1);
        }
        else{
            return round(($count_sumat/$count_all)*$this->months_lengths[$month_int],1);
        }
	}
	
	function get_winter_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
			if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
				if($this->precip_year)
					$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and midtemp<0 and year >= $this->precip_year and year < $this->current_year",null,true);
				else
					$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and midtemp<0 and year != $this->current_year",null,true);

		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function createView($station,$q_temp,$deduct){
		$sql="CREATE VIEW $this->view AS SELECT 55 - ( ( 50 - midtemp ) * $q_temp ) - $deduct AS new_midtemp,month
	          FROM $this->table
              WHERE station = $station
			  AND
                 year BETWEEN $this->from_year AND $this->current_year";
		 mysqli_query($this->connect,$sql);

	}
	
	function dropView(){
		$sql="DROP VIEW $this->view IF EXISTS";
		mysqli_query($this->connect,$sql);

	}
	
	function get_sum_at($station,$month=null){
        
        $month_int=$month;
        if($month)
			$month="AND month=$month";
		else
			$month='';

//        $year="year != $this->current_year";    
        
		$sql = "SELECT SUM(new_midtemp) AS num
                FROM $this->view
                WHERE 1=1
                AND
                    new_midtemp>= 10
				$month
                UNION
                SELECT COUNT(new_midtemp) AS num
                FROM $this->view
				WHERE 1=1
				$month
        ";

        $result=mysqli_query($this->connect,$sql);
        $arr=array();
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[]=$row['num'];
        }
        $sumat=$arr[0];

        if(!$month){
            $years=$arr[1]/365;
            return round($sumat/$years,1);
        }
        else{
            $years=$arr[1]/$this->months_lengths[$month_int];
            return round($sumat/$years,1);
        }
        $arr=array();
		
	}
	
	function get_q_growth_population_by_precip($station){
		
		$res=$base->select_record($this->table,'sum(precip) as precip',"station=$station and midtemp>=5 and year != $this->current_year",null,true);
		$new_res=$res['precip'];
		$res=$base->select_record($this->old_table,'sum(precip) as precip',"station=$station and midtemp>=5 and year != $this->current_year",null,true);
		$old_res=$res['precip'];
		return round($new_res/$old_res,2);
	}
	
	function get_q_growth_population_by_sumat(){
		
		$res=$base->select_record($this->table,'sum(midtemp) as midtemp',"station=$station and midtemp>=10 and year != $this->current_year",null,true);
		$new_res=$res['midtemp'];
		$res=$base->select_record($this->old_table,'sum(midtemp) as precip',"station=$station and midtemp>=10 and year != $this->current_year",null,true);
		$old_res=$res['midtemp'];
		return round($new_res/$old_res,2);
	}
	
	function get_clear_days_result($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
				
		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and cloudness<=3 and year != $this->current_year",null,true);

		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_clear_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
				
		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year $exception and cloudness<2.5 and year < $this->current_year",null,true);
//		echo 'num_years '.$this->num_years.'<br>';
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_sun_hours($station,$year=null,$month=null){
		
		global $base,$time;
		$month_for_time=$month;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		if($this->avg_sun_duration){
			$res=$base->select_record($this->table_summary,'avg(duration) as avg_hours',"station=$station $month $year $exception and year < $this->current_year",null,true);
//			var_dump($time->days_month);
			if($month!=null){
				$result=$res['avg_hours']*$time->days_month[$month_for_time];
			}
			else{
				$result=$res['avg_hours']*365;

			}
		}
		else{
			$res=$base->select_record($this->table_summary,'sum(duration) as sum_hours',"station=$station $month $year $exception and year < $this->current_year",null,true);
	
			if($year==null)
				$result=$res['sum_hours']/$this->num_years;
			else
				$result=$res['sum_hours'];
		}
		
		$result=round($result,1);
		
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_latitude_by_q($q){
		$new_latitude=5;
		$old_latitude=85;
		$buffer_q=$new_latitude/$old_latitude;
		while($buffer_q<$q){
			$new_latitude++;
			$old_latitude--;
			$buffer_q=$new_latitude/$old_latitude;
		}
		return $new_latitude;
	}
	
	function average_cloudness($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		$res=$base->select_record($this->table,'avg(cloudness) as count_precip',"station=$station $month $year $exception and year < $this->current_year",null,true);
		//var_dump($res);
		$result=round($res['count_precip'],1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_q_precip($cloudness){
		
		$arr=array();
		foreach($cloudness as $key=>$value){
			if($key=='year' or $key=='link' or $key=='point')
				continue;
			if($value=='9.0')
				$value='9';
			if($value=='8.0')
				$value='8';
			if($value=='7.0')
				$value='7';
			if($value=='6.0')
				$value='6';
			if($value=='5.0')
				$value='5';
			if($value=='4.0')
				$value='4';
			if($value=='3.0')
				$value='3';
			if($value=='2.0')
				$value='2';
			if($value=='1.0')
				$value='1';
			$q_sun_hours=get_sun_hours($value);
//			echo $key.'<br>';
//			echo 'q_sun_hours '.$q_sun_hours.'<br>';
			$arr[$key]=1-$q_sun_hours;
		}
		$arr['year']=array_sum($arr)/12;
		return $arr;
	}
	
	function average_low_cloudness($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		$res=$base->select_record($this->table,'avg(low_cloudness) as count_precip',"station=$station $month $year $exception and year != $this->current_year",null,true);
		//var_dump($res);
		$result=round($res['count_precip'],1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_cloudy_days_result($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=7 $exception and year != $this->current_year",null,true);
//		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=7 $exception and year != $this->current_year",null,true);
		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_cloudy_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		if($this->pogodarunet_type)
			$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=2.5 and cloudness<8 $exception and year < $this->current_year",null,true);
		else
			$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=6 and cloudness<8 $exception and year < $this->current_year",null,true);
		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	
	
	
	function get_partly_cloudy_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		
		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>4 and cloudness<6 $exception and year < $this->current_year",null,true);
//		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=2.5 and cloudness<5 $exception and year != $this->current_year",null,true);
		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_few_cloudy_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
		
		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>2 and cloudness<4 $exception and year < $this->current_year",null,true);
//		$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=2.5 and cloudness<5 $exception and year != $this->current_year",null,true);
		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_overcast_days($station,$year=null,$month=null){
		
		global $base;
		
		if($month)
			$month="and month=$month";
		else
			$month='';
			
		if($year)
			$year="and year=$year";
		else
			$year='';
		if($month and $year)
				$exception = '';
			else
				$exception = $this->exceptions_years;
//		if(!$this->pogodarunet_type)
			$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>=8 $exception and year < $this->current_year",null,true);
//		else
//			$res=$base->select_record($this->table,'count(*) as count_precip',"station=$station $month $year and cloudness>7.5 $exception and year != $this->current_year",null,true);
		//var_dump($res);
		if($year==null)
		$result=$res['count_precip']/$this->num_years;
		else
		$result=$res['count_precip'];
		$result=round($result,1);
		$result=sprintf("%01.1f",$result);
		return $result;
	}
    
}

?>