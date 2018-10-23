<?php


require("../../banner_images/images.php");	

foreach ($_GET['listItem'] as $position => $item) :
	$show_images .= $position.' => "'.$image_array[$item].'", ';
	$show_links .= $position.' => "'.$link_array[$item].'", ';
endforeach;

$writearray = '<?php	$image_array = array('.$show_images.');	 $link_array = array('.$show_links.');  ?>';

$file = fopen('../../banner_images/images.php', "w+");
fwrite($file, $writearray);
fclose($file);
$breakout = 1;

?>