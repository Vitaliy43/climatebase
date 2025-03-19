<h2><?php echo Yii::t('common_info','quick_search');?></h2>
		<ul>
			<li style="color:#A9B683;"><?php echo Yii::t('common_info','enter_stationpoint');?></li>
			<?php if (AJAX_LINK==true):?>
					<form action="<?php echo SITE_PATH.'climate/stationpoint';?>" method="post" onsubmit="ajax_search(this);return false;" id="form_search">
				<?php else:?>
					<form action="<?php echo SITE_PATH.'climate/search';?>" method="get">
				<?php endif;?>
			<li style="white-space:nowrap;">
				
					<input type="text" name="stationpoint" id="stationpoint" size="17"/>
					<input type="hidden" name="search_url" id="search_url"/>
					<input type="submit" name="search" value="<?php echo Yii::t('common_info','search');?>"/>

				
			</li>
			
			
			
		</form>
</ul>