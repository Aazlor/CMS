<?php

$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$_GET['id'].'/'.$_GET['fn'].'.php';

require($file_path);	

$i = 0;
foreach($_POST[itemID] as $key => $value){
	$sorted[$i] = $gallery_array[$value];
	$i++;
}

if(empty($sorted)){ die(); }


foreach($sorted as $key => $value){
	$writearray .= "$key => '$value', ";
}	

if($writearray != ''){
	$writearray = '<?php $gallery_array = array('. $writearray .'); ?>';

	$file = fopen($file_path, "w+");
	fwrite($file, $writearray);
	fclose($file);
	$breakout = 1;
}

?>