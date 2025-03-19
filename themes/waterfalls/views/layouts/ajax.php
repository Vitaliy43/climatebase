<?php
header("Pragma: no-cache");
header("Cache-Control: no-store, no-cache, max-age=0, must-revalidate");
header("Content-Type: text/html; charset=utf-8");   
if(isset($this->javascript_distributors))
	echo $this->javascript_distributors;

echo $content;

?>