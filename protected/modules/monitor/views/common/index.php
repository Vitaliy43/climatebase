
<h3>Получение климатических данных</h3>
<div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=2';?>" id="2">С 2000 по <?php echo date('Y')-1;?> г. (ogimet.com)</a></span></div>
<div><a href="">С 2005 по <?php echo date('Y')-1;?> г. (rp5.ru)</a></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=3';?>" id="3">1980 - 2000 г. (meteo.ru)</a></span></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=4';?>" id="4">1960 - 1980 г. (meteo.ru)</a></span></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=5';?>" id="5">1940 - 1960 г. (meteo.ru)</a></span></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=7';?>" id="7">Общие (tutiempo.net)</a></span></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/set?period=6';?>" id="6">Общие (meteo.ru)</a></span></div>
<div><span><a href="<?php echo SITE_PATH.$this->module->id.'/weather/setcloudness';?>" id="6">Получение облачости (с 1966 г.)</a></span></div>
<div><a href="<?php echo SITE_PATH.$this->module->id.'/weather/sunhours';?>">Кол-во солнечных часов</a></div>
</div>
<h3>Коррекция климатических данных</h3>
<div>
	<div>
		<a href="<?php echo SITE_PATH.$this->module->id.'/'.$this->id.'/change?type=station';?>">Смена индекса станции</a>
	</div>
	
</div>

<h3>Дополнительные опции</h3>
<div>
	<div>
		<a href="<?php echo SITE_PATH.$this->module->id.'/'.$this->id.'/copyconsolidated';?>">Копирование сводных данных из старого Climatebase</a>
	</div>
	<div>
		<a href="<?php echo SITE_PATH.$this->module->id.'/'.$this->id.'/autocompletedata';?>">Формирование js-файлов для поиска с autocomplete</a>
	</div>
</div>
<div id="parameters" style="display:none;">
<?php echo $methods;?>
</div>
<input type="hidden" id="current_period"/>

