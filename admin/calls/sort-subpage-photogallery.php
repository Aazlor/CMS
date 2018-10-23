<?php

include('../config.php');
require('../../photogallery/'.$_GET[fn].'/'.$_GET['id'].'-images.php');	

$photogallery_vars = 'Gallery__'.$_GET['fn'];
$photogallery_vars = $$photogallery_vars;

foreach($photogallery_vars as $key => $value){
	$get_keys[$a] = $key;
	$a++;
}

foreach($_POST[pictureId] as $position => $item){
	$item = preg_replace('/^.*=/', '', $item);
	$item = str_replace("'", '&#039;', $item);
	$keys['image_array'] .= $position.' => \''.$image_array[$item].'\',';
}		

if(empty($keys)){ die(); }

$writearray = '<?php  ';
foreach($keys as $key => $value){
	$writearray .= '$'.$key.' = array('.$value.');  ';
}	
$writearray .= '  ?>';
	
$file = fopen('../../photogallery/'.$_GET[fn].'/'.$_GET['id'].'-images.php', "w+");
fwrite($file, $writearray);
fclose($file);
$breakout = 1;

?>