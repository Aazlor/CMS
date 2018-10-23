<?php 
require('db_connect.php');
require('config_site_info.php');

// add a table to the selected database
$result="CREATE TABLE $database (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		type varchar(255),
		name varchar(255),
		relation varchar(255),
		info LONGTEXT,
		misc varchar(255),
		sort INT
		)";
		
if ($mysqli->query($result))
{
	echo "success in table creation : $database - Site Info<br>";
}
else 
{
	echo "no table created : $database - Site Info<br>";
}


?>