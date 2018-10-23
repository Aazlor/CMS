<?php

include('../db_connect.php');
include('../config_site_info.php');




$_POST[sorted] = str_replace('itemID[]=', '', $_POST[sorted]);
$order = explode('&', $_POST[sorted]);

if($_POST[cat] == '')
	$file = 'sort_list_all.php';
else
	$file = 'sort_list_'.$_POST[cat].'.php';

file_put_contents ( './'.$file , serialize($order) );

// die();

pre($_POST);

$i = 0;
foreach($_POST[itemID] as $key => $value){
	if(!$mysqli->query("UPDATE $database SET sort='$key' WHERE id='$value'"))
		echo 'Failure: '.mysql_error();
}


?>