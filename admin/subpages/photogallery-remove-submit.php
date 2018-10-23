<?php
/** SubPage Remove Photogallery Submit **/
/** SubPage Remove Photogallery Submit **/
/** SubPage Remove Photogallery Submit **/
/** SubPage Remove Photogallery Submit **/
/** SubPage Remove Photogallery Submit **/

	require("../photogallery/".$_POST[field_name].'/'.$_POST[pageid]."-images.php");	
	$deletekey = $_POST['imageid'];
	$i=0;
	foreach($image_array as $key => $value){
		if($key != $deletekey){
			$keys['image_array'] .= $i.' => \''.$value.'\',';
		}
		else{
			$delete_file = explode('||', $value);
		}
		$i++;
	}

	foreach($delete_file as $value){
		if(preg_match('/\.jpg/i', $value) || preg_match('/\.jpeg/i', $value) || preg_match('/\.gif/i', $value) || preg_match('/\.png/i', $value)){
			$file_to_delete = $value;
		}
	}

	$writearray = '<?php  ';
	foreach($keys as $key => $value){
		$writearray .= '$'.$key.' = array('.$value.');  ';
	}
	if(!isset($keys)){
		$writearray .= '$image_array = array();  ';
		unset($image_array);
	}
	$writearray .= '  ?>';
	
	$file = fopen('../photogallery/'.$_POST[field_name].'/'.$_POST[pageid].'-images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);
	
	$photogallery_vars = 'Gallery__'.$_POST[field_name];
	$photogallery_vars = $$photogallery_vars;


	foreach($photogallery_vars as $key => $value){
		if(preg_match('/{image}/i', $key)){
			foreach($value as $k => $v){
				if($k == 'medium'){
					unlink('../photogallery/'.$_POST[field_name].'/'.$file_to_delete);
				}
				else{
					unlink('../photogallery/'.$_POST[field_name].'/'.$k.'-'.$file_to_delete);
				}
			}
		}
	}
	
	echo '<div class="Message"><img src="images/tick.gif"> Your photo has been removed.</div>';

?>