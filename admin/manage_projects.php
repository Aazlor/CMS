<?php

$filegallery_vars = array (
	'Type' => "File",
	'Name' => "{text}",
	'File' => "{file}",
	'{Sort}' => 'No',
);

include('header.php');

$tabs = array(
	0 => "add",
	1 => "edit",
	2 => "remove",
);

$tab_title = 'Manage Projects';
foreach($tabs as $value){
	unset($active);
	if($_GET['x'] == $value){
		$active = ' class="active"';
		$tab_title .= ' - '.ucwords($value);
	}
	$get_tabs .= '<li'.$active.'><a href="/admin/manage_projects.php?x='.$value.'">'.ucwords($value).' Project</a></li>';
}
echo'


<div class="Title">'.$tab_title.'</div>

<div id="Tabs">
	'.$get_tabs.'
	<div class="Clear"></div>
</div>
';

if($_POST["submit_add"] == "yes") {
	if(mkdir("../projects/$_POST[Project]")){
		$ourFileName = "../projects/$_POST[Project]/images.php";
		$ourFileHandle = fopen($ourFileName, 'w+') or die("can't open file");
		fclose($ourFileHandle);
		chmod($ourFileName, 0777);
		
		echo '<div class="Title">Project Added - '.$_POST[Project].'</div>';
	}
	else{
		echo 'ERROR!';
	}
}

if($_POST["submit_edit"] == "yes"){
	
}

if($_POST['submit_add_filegallery'] == 'yes'){
	
	foreach($_FILES as $key=>$ivalue){
		if(preg_match('/File/', $key)){
			$source = $_FILES[$key]['tmp_name'];
			$target = "../projects/$_POST[field_name]/".basename( $_FILES[$key]['name']);
			move_uploaded_file($source, $target);			
		}
	}
	
	require("../projects/$_POST[field_name]/images.php");	
	
	$a=0;
	foreach($filegallery_vars as $key => $value){
		if(!preg_match('/{.*}/', $key)){
			$get_keys[$a] = $key;
			$a++;
		}
	}
	
	$i=0;
	if(!in_array($imagename, $image_array)){
		foreach($image_array as $value){
			$keys[$i] = $value;
			$i++;
		}
		foreach($filegallery_vars as $key => $value){
			if(isset($_POST[$key])){
				$content = preg_replace('/\'/', '&#39;', $_POST[$key]);
				$content = preg_replace('/"/', '&#34;', $content);
				$content = stripslashes($content);
				$keys[$i] .= '||'.$content;
			}
			elseif(preg_match('/{select}/i', $key)){
				$key = preg_replace('/{.*}/', '', $key);
				$content = preg_replace("/'/", '&#39;', $_POST[$key]);
				$content = preg_replace("/\"/", '&#34;', $content);
				$keys[$i] .= '||'.$content;					
			}
			elseif(isset($_FILES[$key])){
				$content = preg_replace('/\'/', '&#39;', $_FILES[$key]['name']);
				$content = preg_replace('/"/', '&#34;', $content);
				$content = stripslashes($content);
				$keys[$i] .= '||'.$content;
			}
		}
	}
	
	$writearray = '<?php  $image_array = array(';
	foreach($keys as $key => $value){
		$value = preg_replace('/^\|\|/', '', $value);
		$writearray .= $key.' => \''.$value.'\',';
	}	
	$writearray .= ');  ?>';
	
	$file = fopen('../projects/'.$_POST[field_name].'/images.php', "w+");
	if(fwrite($file, $writearray)){
		echo '<div class="Message"><img src="images/tick.gif"> Your file has been added.</div>';
	}
	else{
		echo '<div class="Message"><img src="images/cross.gif"> There was a problem adding your file.</div>';
	}
	fclose($file);

}
if($_POST['submit_edit_filegallery'] == 'yes'){
			
	foreach($_FILES as $key=>$ivalue){
		if(preg_match('/File/', $key)){
			$source = $_FILES[$key]['tmp_name'];
			$target = "../projects/".$_POST[field_name]."/".$_FILES[$key]['name'];
			move_uploaded_file($source, $target);
		}
	}
	
	require("../projects/".$_POST[field_name]."/images.php");	
	
	$a=0;
	foreach($filegallery_vars as $key => $value){
		if(!preg_match('/{.*}/', $key)){
			$get_keys[$a] = $key;
			$a++;
		}
	}

	$i=0;
	foreach($image_array as $value){
		$split = preg_split('/\|\|/', $value);
		unset($find);
		if($i == $_POST[photogallery_id]){
			$keys[$i] = '';
			foreach($filegallery_vars as $key => $value){
				if(preg_match('/{image}/i', $key)){
					$image_key_name = preg_replace('/{image}/i', '', $key);
					if($record_images[$image_key_name] == ''){
						$record_images[$image_key_name]	= $_POST['Current{}'.$image_key_name];
					}
					$keys[$i] .= '||'.$record_images[$image_key_name];
				}
				elseif($value == '{file}'){
					if($record_images[$key] == ''){
						$record_images[$key]	= $_POST['Current{}'.$key];
					}
					$keys[$i] .= '||'.$record_images[$key];
				}
				elseif(preg_match('/Type/i', $key)){
					
				}
				elseif(preg_match('/{select}/i', $key)){
					$key = preg_replace('/{.*}/', '', $key);
					$content = preg_replace("/'/", '&#39;', $_POST[$key]);
					$content = preg_replace("/\"/", '&#34;', $content);
					$keys[$i] .= '||'.$content;					
				}
				else{
					$content = preg_replace("/'/", '&#39;', $_POST[$key]);
					$content = preg_replace("/\"/", '&#34;', $content);
					$keys[$i] .= '||'.$content;
				}
			}
			$keys[$i] = preg_replace('/^\|\|/', '', $keys[$i]);
			$find = 'yes';
		}
		if(!isset($find)){
			$keys[$i] = $value;
		}
		if(isset($find)){
			$add_new = 'no';
		}
		$i++;
	}
	
	$keys[$_POST[photogallery_id]] = preg_replace('/^\|\|/', '', $keys[$_POST[photogallery_id]]);
	
	$writearray = '<?php  $image_array = array(';
	foreach($keys as $key => $value){
		$writearray .= $key.' => \''.$value.'\',';
	}	
	$writearray .= ');  ?>';
	
	$file = fopen('../projects/'.$_POST[field_name].'/images.php', "w+");
	fwrite($file, $writearray);
	fclose($file);	

	echo '<div class="Message"><img src="images/tick.gif"> '.$filegallery_vars['Type'].' updated.</div>';

}
if($_POST['submit_remove_filegallery'] == 'yes'){
	
	require("../projects/$_POST[field_name]/images.php");	
	$deletekey = $_POST['id'];
	$i=0;
	foreach($image_array as $key => $value){
		if($key != $deletekey){
			$keys['image_array'] .= $i.' => "'.$value.'",';
		}
		else{
			$delete_file = preg_split('/\|\|/', $value);
		}
		$i++;
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
	
	if(unlink('../projects/'.$_POST[field_name].'/'.$delete_file[1])){
		$file = fopen('../projects/'.$_POST[field_name].'/images.php', "w+");
		fwrite($file, $writearray);
		fclose($file);

		echo '<div class="Message"><img src="images/tick.gif"> Your photo has been removed.</div>';
	}
	else{
		echo 'ERROR!';
	}
	
}

if($_POST["Remove"] == "Delete"){
	
	$dir = "../projects/$_POST[File]/";
	
	if ($dh = opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if($file != '.' && $file != '..'){
				$path = $dir.$file;
				unlink($path);
			}
		}
		closedir($dh);
	}
	
	if(rmdir($dir)){
		echo '<div class="Message"><img src="images/tick.gif"> Project '.$_POST[File].' has been removed.</div>';
	}
	else{
		echo 'ERROR!';
	}
}


if($_GET['x'] == 'add'){
	echo '
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?x=add">
		<div class="Label">Project Name <div style="font-size: 10px;">Please use alphanumeric names only.  No special characters, spaces are allowed.</div></div>
		<div class="LabelInsert">
			<input type="text" name="Project" value="" class="text">
		</div>
		
		<div class="Submit">
			<input type="hidden" name="submit_add" value="yes"/>
			<input type="submit" value="Add Project" />
		</div>
	</form>

	';	
}

if($_GET['x'] == 'edit'){
	if($_GET['y'] == ''){
		$dir = "../projects/";
		
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					if($file != '.' && $file != '..'){
						$file_url = preg_replace('/ /', '%20', $file);
						echo '
						<div>
							<div style="float: left;">'.$file.'</div>
							<div style="float: right;">
								<a href="?x=edit&y='.$file_url.'">Edit</a>
							</div>
							<div style="clear:both;"></div>
							<hr>
						</div>
						';				
					}
				}
				closedir($dh);
			}
		}
	}
	elseif($_GET['y'] != ''){
		
		$file_name = $_GET[y];
		$file_name = preg_replace('/%20/', ' ', $file_name);
				
		echo '
		<div class="Label">'.$file_name.'</div>
		<table class="LabelInsert" width="720" cellspacing=0 cellpadding=0 align="left">
			<tr>
		';
		
		unset($image_array);
		require("../projects/$file_name/images.php");	
		
		foreach($filegallery_vars as $key => $value){
			if($key == 'Type' || $key == '{Sort}'){}
			else{
				$key = preg_replace('/{.*}/', '', $key);
				echo '<th width="360" align="center">'.ucwords($key).'</th>';
			}
		}
		echo '
				<th width="75" align="center">Delete</th>
				<th width="75" align="center">Edit</th>
			</tr>
		';
		
		$a = 0;
		foreach($image_array as $key => $value){
			$value = preg_replace('/^\|\|/', '', $value);
			$split = preg_split('/\|\|/', $value);
			foreach($split as $k => $v){
				echo '<td align="center">'.substr($v, 0, 200).'</td>';
			}
			echo'
				<td align="center">
					<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?x=edit&y='.$_GET[y].'">
						<input type="hidden" name="id" value="'.$key.'"/>
						<input type="hidden" name="field_name" value="'.$_GET[y].'"/>
						<input type="hidden" name="submit_remove_filegallery" value="yes"/>
						<input type="image" src="images/cross.gif" />
					</form>
				</td>
				<td align="center">
					<a href="?yy='.$_GET[y].'&field_name='.$field_name.'&id='.$key.'&x='.filegallery_edit.'"><input type="image" src="images/edit.jpg" /></a>
				</td>
			</tr>
			';
		}
		echo '</table>
		
		<div class="Clear"></div>
		';
		
		if($filegallery_vars['{Sort}'] != 'No'){
			
		echo'
		<div class="Label">Sort '.$filegallery_vars['Type'].'</div>
		';
	
		unset($listitems);
		unset($listorder);
		$a=1;
		foreach($image_array as $key => $value){
			$split = preg_split('/\|\|/', $value);
			$listitems .= '<li id="listItem_'.$key.'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" />'.$split[0].'</li>';
			$listorder .= '<div style="font-size: 14px; font-family: arial; padding-bottom: 3px; float: left; width: 170px; margin: 10px;">'.$split[0].'</div>';
			$a++;
		}
		echo'
			<script type="text/javascript" src="images/jquery-1.3.2.min.js"></script>
			<script type="text/javascript" src="images/jquery-ui-1.7.1.custom.min.js"></script>
			<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />
			<script type="text/javascript">
			  // When the document is ready set up our sortable with it\'s inherant function(s)
			  $(document).ready(function() {
			    $("#test-list'.$field_name.'").sortable({
			      handle : \'.handle\',
			      update : function () {
					  var order = $(\'#test-list'.$field_name.'\').sortable(\'serialize\');
			  		$("#info").load("calls/sort-photogallery.php?fn='.$field_name.'&"+order);
			      }
			    });
			});
			</script>
	
					';/*
					<pre>
					<div id="info">Waiting for update</div>
					</pre>
					*/echo'
					<div class="LabelInsert">
						<ul id="test-list'.$field_name.'" class="test-list">
						  '.$listitems.'
						</ul>
					</div>
					
					<div class="Submit">
						<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER['PHP_SELF'].'?y='.$_GET[y].'">
						<input type="hidden" name="field_name" value="'.$field_name.'"/>
						<input type="hidden" name="order_photogallery" value="yes"/>
						<input type="submit" value="Save Order" />
						</form>
					</div>
		';
		}
		
		echo '<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?x=edit&y='.$_GET[y].'">';
	
		foreach($filegallery_vars as $key => $value){
			$display_key = preg_replace('/_/', ' ', $key);
			if(preg_match('/{file}/', $value)){
				echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
			}
			elseif(preg_match('/{text}/', $value)){
				echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="" class="text"></div>';
			}
			elseif(preg_match('/{textarea}/', $value)){
				echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$page_vars[$key].'</textarea></div>';
			}
			elseif(preg_match('/{select}/i', $key)){
				$key = preg_replace('/{select}/i', '', $key);
				$display_key = preg_replace('/_/', ' ', $key);
				foreach($value as $kk => $vv){
					if($page_vars[$key] == $vv){
						$options .= '<option selected="selected">'.$vv.'</option>';					
					}
					else{
						$options .= '<option>'.$vv.'</option>';
					}
				}
				echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'>'.$options.'</select></div><div class="Clear"></div>';
			}
		}
		
		echo'		
			<div class="Submit">
				<input type="hidden" name="field_name" value="'.$file_name.'"/>
				<input type="hidden" name="submit_add_filegallery" value="yes"/>
				<input type="submit" value="Add '.$filegallery_vars['Type'].'" />
			</div>
		</form>
		';	
	}
}
if($_GET['x'] == 'filegallery_edit'){
	
	require("../projects/".$_GET['yy']."/images.php");	
	
	$split = preg_split('/\|\|/', $image_array[$_GET[id]]);
	
	echo'
		<div class="Title">
			Edit File - '.$split[0].'
		</div>
	';	
				

	$a = 0;
	foreach($image_array as $key => $value){
		$split = preg_split('/\|\|/', $value);
		$listitems .= '<li id="listItem_'.$key.'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" /><img src="../projects/'.$split[0].'"  width="75"></li>';
		$listorder .= '<div style="font-size: 14px; font-family: arial; padding-bottom: 3px; float: left; width: 170px; margin: 10px;"><img src="../projects/'.$_GET['yy'].'-'.$split[0].'" width="150"></div>';
		$a++;
	}
	
	$split = preg_split('/\|\|/', $image_array[$_GET[id]]);
	
	echo '<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?&y='.$_GET[yy].'&x=edit">';

	$i = '-1';
	
	foreach($filegallery_vars as $key => $value){
		$display_key = preg_replace('/_/', ' ', $key);
		if(preg_match('/{file}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="hidden" name="Current{}'.$key.'" value="'.$split[$i].'"><input type="file" name='.$key.' value=""><br>Current File: <b>'.$split[$i].'</b></div><div class="Clear"></div>';
		}
		elseif(preg_match('/{text}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$split[$i].'" class="text"></div>';
		}
		elseif(preg_match('/{textarea}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$split[$i].'</textarea></div>';
		}
		$i++;
	}
	
	echo'		
		<div class="Submit">
			<input type="hidden" name="field_name" value="'.$_GET[yy].'"/>
			<input type="hidden" name="photogallery_id" value="'.$_GET[id].'"/>
			<input type="hidden" name="submit_edit_filegallery" value="yes"/>
			<input type="submit" value="Edit '.$filegallery_vars[Type].'" />
		</div>
	</form>
	';
}
if($_GET['x'] == 'remove'){
	$dir = "../projects/";
	
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if($file != '.' && $file != '..'){
					$file_url = preg_replace('/ /', '%20', $file);
					echo '
					<div>
						<div style="float: left;">'.$file.'</div>
						<div style="float: right;">
							<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove">
								<input type="submit" name="Remove" value="Delete" onClick="return confirm(\'Are you sure you want to delete the project '.$file.'?\')">
								<input type="hidden" name="File" value="'.$file.'">
							</form>
						</div>
						<div style="clear:both;"></div>
						<hr>
					</div>
					';				
				}
			}
			closedir($dh);
		}
	}
}


include('footer.php');

?>