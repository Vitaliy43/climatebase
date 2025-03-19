<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('application.components.BreadcrumbsWidget', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif;?>
<div class="climate_info">

<h2><?php if(Climate::$language=='en') echo $continent->continent; else echo $continent->continent_russian;?></h2>

<table width="98%" cellpadding="4" cellspacing="4" class="climate_table">
<?php 
for($i=0;$i<count($states);$i=$i+2):?>
<tr>
<td>
<?php
if(Climate::$language=='en'){
	$show_name=$states[$i]['state'];
	$add_link='?lang=en';
}
else{
	$show_name=$states[$i]['state_russian'];
	$add_link='';
}

if(AJAX_LINK)
	$ajax_link='onclick="ajax_link(this.href);return false;"';
else
	$ajax_link='';

if($states[$i]['have_regions'])
	echo '<a href="'.SITE_PATH.'regions/'.$states[$i]['state'].$add_link.'" '.$ajax_link.'>'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'stations/'.$states[$i]['state'].$add_link.'" '.$ajax_link.'>'.$show_name.'</a>';

?>
</td>
<td>

<?php 

if(isset($states[$i+1])):

if(Climate::$language=='en'){
	$show_name=$states[$i+1]['state'];
}
else{
	$show_name=$states[$i+1]['state_russian'];
}

if($states[$i+1]['have_regions'])
	echo '<a href="'.SITE_PATH.'regions/'.$states[$i+1]['state'].'" '.$ajax_link.'>'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'stations/'.$states[$i+1]['state'].'" '.$ajax_link.'>'.$show_name.'</a>';


endif;

?>

</td>

</tr>
<?php endfor;?>

</table>


</div>
