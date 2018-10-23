<?php

include('../config.php');
require('../../photogallery/'.$_GET['fn'].'/'.$_GET['id'].'-images.php');	

print_r($_GET);

$photogallery_vars = 'Gallery__'.$_GET['fn'];
$photogallery_vars = $$photogallery_vars;

foreach($photogallery_vars as $key => $value){
	$get_keys[$a] = $key;
	$a++;
}

foreach($_GET['listItem'] as $position => $item){
	$keys['image_array'] .= $position.' => "'.$image_array[$item].'",';
}		

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