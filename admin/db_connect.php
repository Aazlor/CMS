<?php

ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);

date_default_timezone_set('America/Los_Angeles');

$mysqli = new MYSQLI('localhost', 'root', '', 'live_clients');

if(!function_exists('pre')){
	function pre($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}
?>