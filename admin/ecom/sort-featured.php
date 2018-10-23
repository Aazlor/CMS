<?php

include('../db_connect.php');
include('../config_site_info.php');




$_POST[sorted] = str_replace('itemID[]=', '', $_POST[sorted]);
$order = explode('&', $_POST[sorted]);

$file = 'featured_list.php';

file_put_contents ( './'.$file , serialize($order) );

die();


$i = 0;
foreach($_POST[itemID] as $key => $value){
	if(!$mysqli->query("UPDATE $database SET sort='$key' WHERE id='$value'"))
		echo 'Failure: '.mysql_error();
}


?>