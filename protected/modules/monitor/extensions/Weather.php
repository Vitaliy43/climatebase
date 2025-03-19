<?php

class Weather {
	
	
	public $table='';
	public $sumat;
	public $q_temp;
	public $num_years;
	public $get=false;
	public $field_precip;
	public $period='';
	protected $station;
	public $is_all=false;
	public $is_tutiempo=false;
	public $is_meteo=false;
	public $begin_year=1970;
	protected $num_years_for_precip;
    protected $num_year_for_cloudness;
    protected $num_year_for_low_cloudness;
    public $num_year_for_sun_hours;
	protected $period_for_precip;
    protected $is_ogimet = false;
    protected $months_names = array(1 => 'jan',2 => 'feb',3 => 'mar',4 => 'apr',5 => 'may',6 => 'jun',7 => 'jul',8 => 'aug',9 => 'sep',10 => 'oct',11 => 'nov',12 => 'dec');
		
	function __construct($table,$station,$is_ogimet=true) {
		
		$this->table=$table;
		$this->station=$station;
        $this->is_ogimet=$is_ogimet;
		$this->init();
			
	}
	
	function init($is_all=false){
		
		
		$sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station $this->period";
		$buffer=Yii::app()->db->createCommand($sql)->queryRow();
		if($this->is_meteo)
			$this->num_years=($buffer['amt']/365.25)/8;
		else
			$this->num_years=$buffer['amt']/365.25;
            
          if ($this->is_ogimet) {
              $sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station $this->period AND cloudness IS NOT NULL";
		    $buffer_cloudness=Yii::app()->db->createCommand($sql)->queryRow();
		    if($this->is_meteo)
			    $this->num_years_for_cloudness=($buffer_cloudness['amt']/365.25)/8;
		    	else
			    $this->num_years_for_cloudness=$buffer_cloudness['amt']/365.25;
            
                $sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station $this->period AND sun_hours IS NOT NULL";
		    $buffer_cloudness=Yii::app()->db->createCommand($sql)->queryRow();
		    if($this->is_meteo)
			    $this->num_years_for_sun_hours=($buffer_cloudness['amt']/365.25)/8;
		    else
			    $this->num_years_for_sun_hours=$buffer_cloudness['amt']/365.25;
          }
        

		if($is_all):
		if($this->is_all and $this->is_tutiempo){
			$sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station AND year BETWEEN $this->begin_year AND 2000";
			$buffer_precip=Yii::app()->db->createCommand($sql)->queryRow();
			$this->num_years_for_precip=$buffer_precip['amt']/365.25;
		}
		elseif($this->is_all){
//			$sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station AND year>=$this->begin_year";
			$sql="SELECT COUNT(id) AS amt FROM $this->table WHERE station=$this->station AND year>=1960";
			$buffer_precip=Yii::app()->db->createCommand($sql)->queryRow();
			$this->num_years_for_precip=$buffer_precip['amt']/365.25;
		}
		endif;

		return $buffer['amt'];
	}
	
	
	function get_t1($station,$year=null,$month=null){
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT AVG(mintemp) AS avg_min FROM $this->table WHERE station=$station $month $year $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$result=round($res['avg_min'],1);
		if($this->get)
			$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_midtemp($station,$year=null,$month=null){
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT AVG(midtemp) AS avg_mid FROM $this->table WHERE station=$station $month $year $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$result=round($res['avg_mid'],1);
		if($this->get)
			$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_t2($station,$year=null,$month=null){
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT AVG(maxtemp) AS avg_max FROM $this->table WHERE station=$station $month $year $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$result=round($res['avg_max'],1);
		if($this->get)
			$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_abs_min($station,$year=null,$month=null,$period_id){
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		if($period_id==6 or $period_id==7)
			$sql="SELECT MIN(mintemp) AS abs_min FROM $this->table WHERE station=$station $month $year";
		else
			$sql="SELECT MIN(mintemp) AS abs_min FROM $this->table WHERE station=$station $month $year $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$result=round($res['abs_min'],1);
		if($this->get)
			$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_abs_max($station,$year=null,$month=null,$period_id){
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		if($period_id==6 or $period_id==7)
			$sql="SELECT MAX(maxtemp) AS abs_max FROM $this->table WHERE station=$station $month $year";
		else
			$sql="SELECT MAX(maxtemp) AS abs_max FROM $this->table WHERE station=$station $month $year $this->period";
	
	
		$sql="SELECT MAX(maxtemp) AS abs_max FROM $this->table WHERE station=$station $month $year $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		$result=round($res['abs_max'],1);
		if($this->get)
			$result=sprintf("%01.1f",$result);
		return $result;
	}
	
	function get_precipitation($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
			
	
			
		if($this->is_all and !$this->is_tutiempo){
//			$sql="SELECT SUM($this->field_precip) AS sum_precip FROM $this->table WHERE station=$station $month $year AND year>=$this->begin_year";
			$sql="SELECT SUM($this->field_precip) AS sum_precip FROM $this->table WHERE station=$station $month $year AND year>=1960";
		}
			
		elseif($this->is_all and $this->is_tutiempo){
			$sql="SELECT SUM($this->field_precip) AS sum_precip FROM $this->table WHERE station=$station $month $year AND year BETWEEN $this->begin_year AND 2000";
		}
		
		else{
			$sql="SELECT SUM($this->field_precip) AS sum_precip FROM $this->table WHERE station=$station $month $year $this->period";
		}
		
			
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($year==null){
			if($this->is_all)
				$result=$res['sum_precip']/$this->num_years_for_precip;
			else
				$result=$res['sum_precip']/$this->num_years;
				
		}
		else{
			$result=$res['sum_precip'];

		}
			$result=round($result,1);

		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
	}	
	
	function get_sun_hours($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
			
	
			
		if($this->is_all and !$this->is_tutiempo){
			$sql="SELECT SUM(sun_hours) AS sun_hours FROM $this->table WHERE station=$station $month $year AND year>=$this->begin_year";
		}
			
		elseif($this->is_all and $this->is_tutiempo){
			$sql="SELECT SUM(sun_hours) AS sun_hours FROM $this->table WHERE station=$station $month $year AND year BETWEEN $this->begin_year AND 2000";
		}
		
		else{
			$sql="SELECT SUM(sun_hours) AS sun_hours FROM $this->table WHERE station=$station $month $year $this->period";
		}
		
			
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($year==null){
			if($this->is_all)
				$result=$res['sun_hours']/$this->num_years_for_sun_hours;
			else
				$result=$res['sun_hours']/$this->num_years_for_sun_hours;
				
		}
		else{
			$result=$res['sun_hours'];

		}
			$result=round($result,1);

		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
	}	
	
	
	function get_precip_days($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';

			if($this->is_all and !$this->is_tutiempo)
//				$sql="SELECT COUNT(*) AS count_precip FROM $this->table WHERE station=$station $month $year AND $this->field_precip>0 AND year>=$this->begin_year";
				$sql="SELECT COUNT(*) AS count_precip FROM $this->table WHERE station=$station $month $year AND $this->field_precip>0 AND year>=1960";
			elseif($this->is_all and $this->is_tutiempo)
				$sql="SELECT COUNT(*) AS count_precip FROM $this->table WHERE station=$station $month $year AND $this->field_precip>0 AND year BETWEEN $this->begin_year AND 2000";
			
			else
				$sql="SELECT COUNT(*) AS count_precip FROM $this->table WHERE station=$station $month $year AND $this->field_precip>0 $this->period";
				
		$res=Yii::app()->db->createCommand($sql)->queryRow();
		
		if($year==null){
			if($this->is_all)
				$result=$res['count_precip']/$this->num_years_for_precip;
			else
				$result=$res['count_precip']/$this->num_years;

		}
		else{
			$result=$res['count_precip'];

		}
			$result=round($result,1);

		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
			
		
	}
	
	function get_sum_at($station,$year=null,$month=null){
		
		
		if($month)
			$month="AND month=$month";
		else
			$month='';
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT SUM(midtemp) AS count_precip FROM $this->table WHERE station=$station $month $year AND midtemp>=10 $this->period";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($year==null)
			$result=$res['count_precip']/$this->num_years;
		else
			$result=$res['count_precip'];
			$result=round($result);
		
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
		
	
	}
	
	function get_clear_days($station,$year=null,$month=null){
			
	
		if($month)
			$month="AND month=$month";
		else
			$month='';
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT COUNT(*) AS sum_precip FROM $this->table WHERE station=$station $month $year AND cloudness<2.5";
//        echo $sql.'<br>';
//        echo 'num_years '.$this->num_year_for_low_cloudness.'<br>';
		$res=Yii::app()->db->createCommand($sql)->queryRow();
//		echo 'num_years '.$this->num_years.'<br>';
		if($year==null)
			$result=$res['sum_precip']/$this->num_years_for_cloudness;
		else
			$result=$res['sum_precip'];
			$result=round($result,1);
		
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
		
	}
	
	function get_average_cloudness($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
			
		$sql="SELECT AVG(cloudness) AS sum_precip FROM $this->table WHERE station=$station $month $year";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		
		$result=round($res['sum_precip'],1);
		
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
		
	}
    
    public function average_meteo_cloudness($station){
        $sql = "SELECT AVG(jan) AS jan, AVG(feb) AS feb, AVG(mar) AS mar, AVG(apr) AS apr, AVG(may) AS may, AVG(jun) AS jun, AVG(jul) AS jul, AVG(aug) AS aug, AVG(sep) AS sep, AVG(oct) AS oct, AVG(nov) AS nov, AVG(`dec`) AS `dec` FROM archive_weather.meteo_cloudness WHERE station = $station AND type = 'common'";
        
        $res=Yii::app()->db->createCommand($sql)->queryRow();	
        $result['station'] = $station;
        $result['period_id'] = 6;
        $result['year'] = 0;
        $sum = 0;
        foreach ($this->months_names as $name) {
            $result[$name] = round($res[$name],1);
            $sum += $res[$name];
        }
        $result['year'] = round($sum/12,1);
        return $result;
    }
    
    public function average_meteo_low_cloudness($station){
        $sql = "SELECT AVG(jan) AS jan, AVG(feb) AS feb, AVG(mar) AS mar, AVG(apr) AS apr, AVG(may) AS may, AVG(jun) AS jun, AVG(jul) AS jul, AVG(aug) AS aug, AVG(sep) AS sep, AVG(oct) AS oct, AVG(nov) AS nov, AVG(`dec`) AS `dec` FROM archive_weather.meteo_cloudness WHERE station = $station AND type = 'low'";
        
        $res=Yii::app()->db->createCommand($sql)->queryRow();	
        $result['station'] = $station;
        $result['period_id'] = 6;
        $result['year'] = 0;
        $sum = 0;
        foreach ($this->months_names as $name) {
            $result[$name] = round($res[$name],1);
            $sum += $res[$name];
        }
        $result['year'] = round($sum/12,1);
        return $result;
    }
	
	function get_average_low_cloudness($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
			
		$sql="SELECT AVG(low_cloudness) AS cloudness FROM $this->table WHERE station=$station $month $year";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		
		$result=round($res['cloudness'],1);
		
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
		
	}
	
	function get_cloudy_days($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT COUNT(*) AS sum_precip FROM $this->table WHERE station=$station $month $year AND cloudness>=2.5 AND cloudness<8";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($year==null)
			$result=$res['sum_precip']/$this->num_years_for_cloudness;
		else
			$result=$res['sum_precip'];
			$result=round($result,1);
		
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
		
	}
	
	function get_overcast_days($station,$year=null,$month=null){
				
		if($month)
			$month="AND month=$month";
		else
			$month='';
			
		if($year)
			$year="AND year=$year";
		else
			$year='';
		
		$sql="SELECT COUNT(*) AS sum_precip FROM $this->table WHERE station=$station $month $year AND cloudness>=8";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($year==null)
			$result=$res['sum_precip']/$this->num_years_for_cloudness;
		else
			$result=$res['sum_precip'];
			$result=round($result,1);
		if($this->get)
			$result=sprintf("%01.1f",$result);		
		return $result;
	}
	
	
}

?>