<?php

require($_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_GET['fn'].'/images.php');	

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


	$file = fopen($_SERVER['DOCUMENT_ROOT'].'/gallery/'.$_GET[fn].'/images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);
	$breakout = 1;
}

?>