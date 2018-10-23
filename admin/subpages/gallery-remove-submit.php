<?php
/** SubPage Remove Gallery Submit **/
/** SubPage Remove Gallery Submit **/
/** SubPage Remove Gallery Submit **/
/** SubPage Remove Gallery Submit **/
/** SubPage Remove Gallery Submit **/

	require("../gallery/".$_POST[field_name].'/'.$_POST[pageid]."-images.php");	
	$deletekey = $_POST['imageid'];
	$i=0;
	foreach($gallery_array as $key => $value){
		if($key != $deletekey){
			$keys['gallery_array'] .= $i.' => \''.$value.'\',';
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
		$writearray .= '$gallery_array = array();  ';
		unset($gallery_array);
	}
	$writearray .= '  ?>';
	
	$file = fopen('../gallery/'.$_POST[field_name].'/'.$_POST[pageid].'-images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);
	
	$gallery_vars = 'Gallery__'.$_POST[field_name];
	$gallery_vars = $$gallery_vars;


	foreach($gallery_vars as $key => $value){
		if(preg_match('/{image}/i', $key)){
			foreach($value as $k => $v){
				if($k == 'medium'){
					unlink('../gallery/'.$_POST[field_name].'/'.$file_to_delete);
				}
				else{
					unlink('../gallery/'.$_POST[field_name].'/'.$k.'-'.$file_to_delete);
				}
			}
		}
	}
	
	echo '<div class="Message"><img src="images/tick.gif"> Your photo has been removed.</div>';

?>