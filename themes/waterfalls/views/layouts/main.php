<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $this->pageTitle;?></title>

<?php
echo CHtml::cssFile(Yii::app()->request->baseUrl.'/css/'.Yii::app()->theme->name.'/style.css','screen,projection');
echo CHtml::cssFile(Yii::app()->request->baseUrl.'/css/'.Yii::app()->theme->name.'/jquery.alerts.css','screen,projection');
echo CHtml::cssFile(Yii::app()->request->baseUrl.'/css/'.Yii::app()->theme->name.'/admin-bar.css','screen,projection');
Yii::app()->getClientScript()->registerCssFile(Yii::app()->request->baseUrl.'/css/jquery.autocomplete.css');

Yii::app()->getClientScript()->registerCoreScript('jquery');
Yii::app()->getClientScript()->registerCoreScript('cycle');
Yii::app()->getClientScript()->registerCoreScript('scroll');
Yii::app()->getClientScript()->registerCoreScript('autocomplete');
Yii::app()->getClientScript()->registerCoreScript('alerts');
Yii::app()->getClientScript()->registerCoreScript('history');

echo CHtml::scriptFile(Yii::app()->request->baseUrl.'/js/'.Yii::app()->theme->name.'/functions.js');
echo CHtml::scriptFile(Yii::app()->request->baseUrl.'/js/data/stations.js');

if(Climate::$language=='en')
	echo CHtml::scriptFile(Yii::app()->request->baseUrl.'/js/data/points.js');
else
	echo CHtml::scriptFile(Yii::app()->request->baseUrl.'/js/data/points_russian.js');
	

?>


<script type="text/javascript">

$(document).ready(function() {

   $('#slide').cycle({
      fx: 'fade',
	  timeout: 10000,
	  speed: 2000
	  
   });
   
	var objects=merge_arrays(stations,points);

$("#stationpoint").autocomplete(objects, {
		width: 320,
		max: 4,
		highlight: false,
		multiple: true,
		multipleSeparator: " ",
		scroll: true,
		scrollHeight: 300
});


$('[name="stationpoint"]').result(function(event, data, formatted) {
	ajax_search_from_list(data);

});	

});


</script>


<style type="text/css" media="print">#wpadminbar { display:none; }</style>
<style type="text/css">
	html { margin-top: 28px !important; }
	* html body { margin-top: 28px !important; }
</style>
</head>

<body>

<div id="page">
<div id="slide">
<?php foreach($this->slide_images as $slide):?>
<div class="header" style="background:#093216 url(/images/waterfalls/slide-show/<?php echo $slide['name'];?>.jpg) no-repeat top center;">

		<h1 class="site_title" ><a href="<?php echo SITE_PATH;?>"><?php echo SITE_NAME;?></a></h1>
		<div class="subtitle" ><?php echo Yii::app()->name;?></div>
</div>
<?php endforeach;?>
</div>

<div id="wrap">
  <div id="content-container">
    <div class="content">
      
      <div class="post-container">
                        <div class="post" id="post-3">
         
		 
		
          <div class="entry">
		   
		  <?php echo $content;?>
             
			
          </div>
          <div class="postbottom">
            <div class="metainf">
              <!--a href="http://localhost/wordpress/archives/category/uncategorized" title="View all posts in Uncategorized" rel="category tag">Uncategorized</a-->            </div>
            <div class="commentinf">
              <!--a href="http://localhost/wordpress/archives/3#respond" title="Comment on Классные телки">Оставить комментарий!</a-->            </div>
          </div>
        </div>
            
			
                              </div>
	  <div style="float:left">
        
	<div id="sidebar">
	<ul>
	
	<li><h2><?php echo Yii::t('common_info','languages');?></h2>
		<ul>
			<li>
			<?php if (Climate::$language=='en'):?>
				<a href="<?php echo str_replace('?lang=en','',$_SERVER['REQUEST_URI']);?>" title="Русский">Русский</a>
			<?php else:?>
				Русский
			<?php endif;?>
			</li>
			<li>
			<?php if (Climate::$language=='en'):?>
				English
			<?php else:?>
				<a href="<?php echo $_SERVER['REQUEST_URI'].'?lang=en';?>" title="English">English</a>
			<?php endif;?>
			</li>
		</ul>
	</li>
	<li>
	<?php
		$this->Widget('application.components.SearchWidget');
	?>	
	</li>
	
					
		  <li id="linkcat-2" class="linkcat"><h2><?php echo Yii::t('common_info','links');?></h2>
	<ul>
<li><a href="http://meteoclub.ru/" target="_blank">Meteoclub.ru</a></li>
<li><a href="http://ogimet.com/" target="_blank">Ogimet.com</a></li>
<li><a href="http://rp5.ru/" target="_blank">Rp5.ru</a></li>
<li><a href="http://realclimate.org/" target="_blank">Realclimate.org</a></li>
<li><a href="http://climatechange.ru/" target="_blank">Climatechange.ru</a></li>
<li><a href="https://www.wunderground.com/" target="_blank">Wunderground.com</a></li>
	</ul>
</li>
		<!--li><h2><?php echo Yii::t('common_info','feedback');?></h2>
			<ul>
				<li><a href="/feedback/project"><?php echo Yii::t('common_info','about_project');?></a></li>
				<li><a href="/feedback/sendmail"><?php echo Yii::t('common_info','sendmail');?></a></li>
			</ul>
		</li-->
	
		
	</ul>
	</div>      </div>
    </div>
    <!--div class="bgbottom"></div-->
  </div>
</div>

<div id="footer">	
	<div class="footerlinks"> 
		
		<?php echo Yii::t('common_info','all_rights_reserved');?> &copy; 2012 <a href="/"><strong style="margin-left:10px;">Climatebase.ru</strong></a>
		<!--center>
<br><br>
<font size="1" color="#908967">
Локализация:&nbsp;<a href="http://www.seone.ru/pages/kontekstnaja-reklama/" title="Контекстная реклама - цены ниже!" style="color:#908967">Seone.ru</a>&nbsp;|&nbsp;<a href="http://www.gnomik.ru/do-1-goda/razvitie" title="Гномик.ру - развитие ребенка по месяцам" style="color:#908967">Gnomik.ru</a></font>
</center-->
	</div>
</div>
</div>


</body>
<!--BDthemepack-->
</html>

