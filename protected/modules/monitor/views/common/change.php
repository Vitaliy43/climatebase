<h3>Замена индекса станции</h3>
<div>
<?php if(empty($_REQUEST['update'])):?>

	<form action="<?php echo SITE_PATH.$this->module->id.'/common/change';?>" method="POST">
		<div>Текущий индекс</div>
			<input type="text" name="old_ind"/>
		<br>
		<div>Новый индекс</div>
			<input type="text" name="new_ind"/>
		<br>
		<div>
			<input type="submit" name="update"/>
		</div>
	</form>
	<?php else:?>
		<div><?php echo $result;?></div>
	
	<?php endif;?>
</div>
<div style="margin-bottom:50px;">
	<a href="<?php echo SITE_PATH.$this->module->id.'/common';?>">Вернуться в Климатический монитор</a>

</div>