<?php

class ClimateData
{
	
	private $station;
	private $period_id;

	
	function __construct($station,$period_id=null)
	 {
		
		$this->station=(int)$station;
		if($period_id)
			$this->period_id=(int)$period_id;	
	}
	
	public static function existsPeriod($period_id,$station,$verifiable_table='average_temperature')
	{
		$sql="SELECT * FROM $verifiable_table WHERE station=$station AND period_id=$period_id";
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if($res)
			return true;
		return false;
	}
	
	function getData($table,$consolidated=false)
	{
		if($consolidated){
			if($table=='highest_temperature')
				$sql="SELECT DISTINCT station, 6 AS period_id, MAX(year) AS year, MAX(jan) AS jan, MAX(feb) AS feb, MAX(mar) AS mar, MAX(apr) AS apr, MAX(may) AS may, MAX(jun) AS jun, MAX(jul) AS jul, MAX(aug) AS aug, MAX(sep) AS sep, MAX(oct) AS oct, MAX(nov) AS nov, MAX(`dec`) AS `dec` FROM `$table` WHERE `station` = $this->station";
			elseif($table=='lowest_temperature')
				$sql="SELECT DISTINCT station, 6 AS period_id, MIN(year) AS year, MIN(jan) AS jan, MIN(feb) AS feb, MIN(mar) AS mar, MIN(apr) AS apr, MIN(may) AS may, MIN(jun) AS jun, MIN(jul) AS jul, MIN(aug) AS aug, MIN(sep) AS sep, MIN(oct) AS oct, MIN(nov) AS nov, MIN(`dec`) AS `dec` FROM `$table` WHERE `station` = $this->station";
			else
				$sql="SELECT DISTINCT station, 6 AS period_id, ROUND(AVG(year),1) AS year, ROUND(AVG(jan),1) AS jan, ROUND(AVG(feb),1) AS feb, ROUND(AVG(mar),1) AS mar, ROUND(AVG(apr),1) AS apr, ROUND(AVG(may),1) AS may, ROUND(AVG(jun),1) AS jun, ROUND(AVG(jul),1) AS jul, ROUND(AVG(aug),1) AS aug, ROUND(AVG(sep),1) AS sep, ROUND(AVG(oct),1) AS oct, ROUND(AVG(nov),1) AS nov, ROUND(AVG(`dec`),1) AS `dec` FROM `$table` WHERE `station` = $this->station";
		}
		else{
//            $precip_period=$_GET['precip_period'];
            if($table=='precipitation'){
                if(!isset($_GET['precip_period']))
                    $period_id=3;
                else
                    $period_id=$_GET['precip_period'];
            }
            else{
                $period_id=$this->period_id;
            }
//			if(!isset($_GET['station']))
				$period_id=$this->period_id;

			$sql="SELECT * FROM $table WHERE station=$this->station AND period_id=$period_id";
		}
		$res=Yii::app()->db->createCommand($sql)->queryRow();	
		if(is_null($res['year'])){
            if($table=='precipitation'){
                $period_id=3;
                $sql="SELECT * FROM $table WHERE station=$this->station AND period_id=$period_id";
                $res2=Yii::app()->db->createCommand($sql)->queryRow();	
                if(is_null($res2['year'])){
                    $sql="SELECT * FROM $table WHERE station=$this->station AND period_id=4";
                    $res3=Yii::app()->db->createCommand($sql)->queryRow();
                    if(is_null($res3['year'])){
                        return false;
                    }	
                    else{
                        return $res3;
                    }
                }
                else{
                    return $res2;
                }
            }
            else{
                return false;
            }

        }

		return $res;
	}
}

?>