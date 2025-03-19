<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php
		Yii::app()->getClientScript()->registerCoreScript('jquery');
		echo CHtml::scriptFile(Yii::app()->request->baseUrl.'/js/'.Yii::app()->theme->name.'/functions.js');
	?>
	</head>
<body>
<div id="wrapper" style="padding:100px;">
<?php

echo $content;

?>
</div>
</body>
</html>