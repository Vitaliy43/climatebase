<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('application.components.BreadcrumbsWidget', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif;?>
<h2>
<?php if(Climate::$language=='en'):?>
Main page
<?php else:?>
Главная страница
<?php endif;?>
</h2>

<table width="98%" cellpadding="4" cellspacing="4" class="climate_table">
<?php 
for($i=0;$i<count($continents);$i=$i+2):?>
<tr>
<td>
<?php
if(Climate::$language=='en'){
	$show_name=$continents[$i]['continent'];
	$add_link='?lang=en';
}
else{
	$show_name=$continents[$i]['continent_russian'];
	$add_link='';

}
if(AJAX_LINK)
	echo '<a href="'.SITE_PATH.'countries/'.$continents[$i]['continent'].$add_link.'" onclick="ajax_link(this.href);return false;">'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'countries/'.$continents[$i]['continent'].$add_link.'">'.$show_name.'</a>';

?>
</td>
<td>

<?php 

if(isset($continents[$i+1])):

if(Climate::$language=='en'){
	$show_name=$continents[$i+1]['continent'];
}
else{
	$show_name=$continents[$i+1]['continent_russian'];
}

if(AJAX_LINK)
	echo '<a href="'.SITE_PATH.'countries/'.$continents[$i+1]['continent'].$add_link.'" onclick="ajax_link(this.href);return false;">'.$show_name.'</a>';
else
	echo '<a href="'.SITE_PATH.'countries/'.$continents[$i+1]['continent'].$add_link.'">'.$show_name.'</a>';


endif;

?>

</td>

</tr>
<?php endfor;?>

</table>
	
	
	