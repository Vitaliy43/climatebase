<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('application.components.BreadcrumbsWidget', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif;
	if(Climate::$language=='en'){
		$point=$common_info->point;
		$state=$common_info->state;
		if($common_info->region)
			$region=$common_info->region;
	}
	else{
		$point=$common_info->point_russian;
		$state=$common_info->state_russian;
		if($common_info->region)
			$region=$common_info->region_russian;
	}
	
	?>
<div class="climate_info">
<div class="common_info">
<h3>
<?php echo $point;
if(isset($region))
	echo ', '.$region;
echo ', '.$state;
echo ' #'.$common_info->station;
?>
</h3>
<h4><?php echo Yii::t('common_info','elevation');?>: <?php echo $common_info->elevation;?> м</h4>
<h4><?php echo Yii::t('common_info','latitude');?>: <?php echo $common_info->latitude_char;?> <?php echo Yii::t('common_info','longitude');?>: <?php echo $common_info->longitude_char;?></h4>
</div>

<?php
	$this->Widget('application.components.PeriodsWidget',array('station'=>$common_info->station,'period_id'=>$period_id));
?>
<table width="98%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
	
	
	<?php foreach($climate_data_points as $row):?>
	<?php if(isset($data[$row])):?>
	<tr>
		<table width="100%" cellpadding="0" cellspacing="0" >
			<tr>
			<?php if(defined('LOCAL') and LOCAL)
				$action = '<a href="'.SITE_PATH.'station/delete/'.$common_info->station.'" onclick="delete_parameter(this,\''.$common_info->station.'\',\''.$row.'\',\''.$period_id.'\');return false;" class="delete_link" style="text-decoration:none;margin-left:10px;"><img src="/images/icon_delete.png" valign="middle" width="10" height="13"></a>';
			  else
			  	$action = '';
			
			?>
			
				<td align="left" nowrap="" id="<?php echo $row;?>"><table><tr><td><h3 style="color:#A9B683;"><?php echo Yii::t('climate_data',$row);?></h3></td><td><?php echo $action;?></td></tr></table></td>
			</tr>
			<tr>
				<td>
					<div class="table_hr"></div>
				</td>
			</tr>
		</table>
	</tr>
	<tr>
		<table width="90%" cellpadding="2" cellspacing="2">
			<tr>
				<td></td>
				<td class="climate_td"><?php echo $show_months_names['year'];?></td>
				<?php foreach($months_names as $name):
						echo '<td class="climate_td">'.$show_months_names[$name].'</td>';
					endforeach;
				?>
				
			</tr>
			<tr>
				<td style="color:#BFCE92;"><b><?php echo $measures[$row];?></b></td>
				<td class="climate_td"><b><?php echo sprintf("%01.1f",$data[$row]['year']);?></b></td>
				<?php foreach($months_names as $name):
						echo '<td class="climate_td"><b>'.sprintf("%01.1f",$data[$row][$name]).'</b></td>';
					endforeach;
				?>
			</tr>
		</table>
	</tr>
	
	<?php endif;?>
	<?php endforeach;?>

</table>
<?php if($period_observations and ($period_id==6 or $period_id==7)):?>

	<div class="common_info" style="margin-top: 5px;"><?php echo Yii::t('climate_data','period_observations').': '.$period_observations['begin'].' - '.$period_observations['end'];?></div>

<?php endif;?>
</div>