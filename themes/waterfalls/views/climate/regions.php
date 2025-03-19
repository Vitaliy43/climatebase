<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('application.components.BreadcrumbsWidget', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif;?>
<div class="climate_info">

<h2><?php if(Climate::$language=='en') echo $state[0]['state']; else echo $state[0]['state_russian'];?></h2>

<table width="98%" cellpadding="4" cellspacing="4" class="climate_table">
<?php 
for($i=0;$i<count($regions);$i=$i+2):?>
<tr>
<td>
<?php
if(Climate::$language=='en'){
	$show_name=$regions[$i]['region'];
	$add_link='?lang=en';
}
else{
	$show_name=$regions[$i]['region_russian'];
	$add_link='';
}
if(AJAX_LINK)
	echo '<a href="'.SITE_PATH.'stations/'.$regions[$i]['state'].'/'.$regions[$i]['region'].$add_link.'" onclick="ajax_link(this.href);return false;">'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'stations/'.$regions[$i]['state'].'/'.$regions[$i]['region'].$add_link.'">'.$show_name.'</a>';
?>
</td>
<td>

<?php 

if(isset($regions[$i+1])):

if(Climate::$language=='en'){
	$show_name=$regions[$i+1]['region'];
}
else{
	$show_name=$regions[$i+1]['region_russian'];
}
if(AJAX_LINK)
	echo '<a href="'.SITE_PATH.'stations/'.$regions[$i+1]['state'].'/'.$regions[$i+1]['region'].'" onclick="ajax_link(this.href);return false;">'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'stations/'.$regions[$i+1]['state'].'/'.$regions[$i+1]['region'].'">'.$show_name.'</a>';


endif;

?>

</td>

</tr>
<?php endfor;?>

</table>


</div>
