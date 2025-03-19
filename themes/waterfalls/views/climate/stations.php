<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('application.components.BreadcrumbsWidget', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif;?>
<div class="climate_info">

<h2><?php 

if(isset($_REQUEST['region']))
	$index_name='region';
else
	$index_name='state';
	
if(AJAX_LINK)
	$ajax_link='onclick="ajax_link(this.href);return false;"';
else
	$ajax_link='';

if(Climate::$language=='en'){ 
	echo $name[0][$index_name];
	$add_link='?lang=en';

}
else 
{
 	echo $name[0][$index_name.'_russian'];
 	$add_link='';
}
?></h2>

<table width="98%" cellpadding="4" cellspacing="4" class="climate_table">
<?php 
for($i=0;$i<count($stations);$i=$i+2):?>
<tr>
<td id="<?php echo $stations[$i]['station'];?>">
<?php
if(Climate::$language=='en'){
	$show_name=$stations[$i]['point'];
}
else{
	$show_name=$stations[$i]['point_russian'];
}

	echo '<a href="'.SITE_PATH.'station/'.$stations[$i]['station'].$add_link.'" '.$ajax_link.' class="link">'.$show_name.'</a>';
	echo '<font style="color: #579700;font-size:12px;" class="label"> #'.$stations[$i]['station'].'</font>';
	if(defined('LOCAL') AND LOCAL)
		echo '<a href="'.SITE_PATH.'station/delete/'.$stations[$i]['station'].$add_link.'" onclick="delete_station(this,\''.$stations[$i]['station'].'\');return false;" class="delete_link" style="text-decoration:none;margin-left:10px;"><img src="/images/icon_delete.png" width="10" height="13" valign="middle"></a>';
?>
</td>
<?php if(isset($stations[$i+1])):?>
<td id="<?php echo $stations[$i+1]['station'];?>">
<?php else:?>
<td>
<?php endif;?>

<?php 

if(isset($stations[$i+1])):

if(Climate::$language=='en'){
	$show_name=$stations[$i+1]['point'];
}
else{
	$show_name=$stations[$i+1]['point_russian'];
}

	echo '<a href="'.SITE_PATH.'station/'.$stations[$i+1]['station'].$add_link.'" '.$ajax_link.'>'.$show_name.'</a>';
	echo '<font style="color: #579700;font-size:12px;"> #'.$stations[$i+1]['station'].'</font>';
	if(defined('LOCAL') and LOCAL)
		echo '<a href="'.SITE_PATH.'station/delete/'.$stations[$i+1]['station'].'" onclick="delete_station(this,\''.$stations[$i+1]['station'].'\');return false;" class="delete_link" style="text-decoration:none;margin-left:10px;"><img src="/images/icon_delete.png" width="10" height="13" valign="middle"></a>';

endif;

?>

</td>

</tr>
<?php endfor;?>

</table>


</div>
