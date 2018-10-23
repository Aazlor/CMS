<?php
/* This is where you would inject your sql into the database 
   but we're just going to format it and send it back
*/

include('../config_site_info.php');
require('../db_connect.php');

foreach ($_GET['listItem'] as $position => $item) :
	$mysqli->query("UPDATE $database SET sort = $position WHERE id = $item");
endforeach;

#print_r ($sql);
?>