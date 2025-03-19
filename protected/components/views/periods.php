<div class="period_info">
<?php
	if(Climate::$language=='en'){
		$y='y';
		$summary_data='Summary data';
		$from='From';
		$add_link='?lang=en';
	}
	else{
		$y='г';
		$summary_data='Сводные данные';
		$from='С';
		$add_link='';
	}



	foreach($periods as $period){
		if(!is_object($period)){
			$name=$summary_data;
			$link=SITE_PATH.'station/'.$this->station.$add_link;
		}
		else{
			if(!$period->year_end and !$period->year_begin){
				$name=$summary_data;
				$link=SITE_PATH.'station/'.$this->station.$add_link;
			}
			elseif(!$period->year_end and $period->source!='meteo'){
				$name="$from ".$period->year_begin.' '.$y;
				$link=SITE_PATH.'station/'.$this->station.'/from'.$period->year_begin.$add_link;
			}
			else{
				$name=$period->year_begin.'-'.$period->year_end." $y.$y.";
				$buffer_link=$period->year_begin.'_'.$period->year_end;
				$buffer_link=str_replace('19','',$buffer_link);
				$buffer_link=str_replace('20','',$buffer_link);
				$link=SITE_PATH.'station/'.$this->station.'/'.$buffer_link.$add_link;
				if($period->source=='meteo')
					$name=$summary_data;
			}
		}
		
		if(is_object($period))
			$period_id=$period->id;
		else
			$period_id=$period['id'];
		
		
		if($period_id==$this->period_id){
			echo '<span>'.$name.'</span>';

		}
		else{
			if(AJAX_LINK)
				echo '<span>'.CHtml::link($name,$link,array('onclick'=>'ajax_link_inner(this.href);return false')).'</span>';
			else
				echo '<span>'.CHtml::link($name,$link).'</span>';

		}
	}

?>
</div>