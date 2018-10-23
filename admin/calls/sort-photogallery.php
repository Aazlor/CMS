<?php

include('../config_photogallery_settings.php');
require('../../photogallery/'.$_GET['fn'].'/images.php');	

$photogallery_vars = 'Gallery__'.$_GET['fn'];
$photogallery_vars = $$photogallery_vars;

foreach($photogallery_vars as $key => $value){
	$get_keys[$a] = $key;
	$a++;
}

foreach($image_array as $k => $v){
	if($v != ''){
		$sort_list[] = $v;
	}
}

foreach($_POST[pictureId] as $position => $item){
	$item = preg_replace('/^.*=/', '', $item);
	$keys['image_array'] .= $position.' => \''.$sort_list[$item].'\',';
}		

if(empty($keys)){ die(); }

$writearray = '<?php  ';
foreach($keys as $key => $value){
	$writearray .= '$'.$key.' = array('.$value.');  ';
}	
$writearray .= '  ?>';

$file = fopen('../../photogallery/'.$_GET[fn].'/images.php', "w+");
fwrite($file, $writearray);
fclose($file);
$breakout = 1;

?>