<?php
    
ini_set('upload_max_filesize', '10M');  
ini_set('post_max_size', '10M');  
ini_set('max_input_time', 300);  
ini_set('max_execution_time', 300);  
    
include('header.php');

function clean_url($url){
	$url = strtolower($url);
	if(!preg_match('/http:\/\//', $url)){
		$url = preg_replace('/[\s]+/', '_', $url);
		$url = preg_replace('/[\W]+/', '', $url);
		$url = '/'.$url.'.html';
	}
	return $url;
}

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

function resize($width, $height, $value){
	if($value['width'] != '' && $value['height'] != ''){
		$modwidth = $value['width'];	
		$modheight = $value['height'];	
	}
	elseif($value['width'] != '' && $value['height'] == ''){
		$modwidth = $value['width'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	elseif($value['height'] != '' && $value['width'] == ''){
		$modheight = $value['height'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}

	if($value['minwidth'] != ''){
		$modwidth = $value['minwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	if($modheight < $value['minheight'] && $value['minheight'] != ''){
		$modheight = $value['minheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if($modwidth == ''){
		$modwidth = $width;
	}
	if($modheight == ''){
		$modheight = $height;
	}

	if($value['maxheight'] != '' && $value['maxheight'] < $modheight){
		$modheight = $value['maxheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if($value['maxwidth'] != '' && $value['maxwidth'] < $modwidth){
		$modwidth = $value['maxwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	
	return $modwidth.'||'.$modheight;
}


foreach($_POST as $key => $value){
	if($value != '' && (preg_match('/"/', $value) || preg_match("/'/", $value)) && $key != 'Page_Name'){
		$value = stripslashes($value);
		$value = preg_replace("/\\\/", '', $value);
		$value = preg_replace("/'/", '&#39;', $value);
#		$value = preg_replace('/"/', '&#34;', $value);
		
		$_POST[$key] = $value;
	}
}

$type = 'subpage_'.$_GET['p'];

/** Add Page Submit **/
if($_POST['submit_add'] == 'yes'){
	include('subpages/add-submit.php');
}

/** Edit Page Submit **/
if($_POST['submit_edit'] == 'yes'){
	include('subpages/edit-submit.php');
}

/** Remove Page Submit **/
if($_POST['submit_remove'] == 'yes'){
	$key = $_POST[id];
	$mysqli->query("DELETE FROM $database WHERE id='$key'");
}

/** Add Photogallery Submit **/
if($_POST['submit_add_photogallery'] == 'yes'){
	include('subpages/photogallery-add-submit.php');
}

/** Remove Photogallery Submit **/
if($_POST['submit_remove_photogallery'] == 'yes'){
	include('subpages/photogallery-remove-submit.php');
}

/** Edit Photogallery Submit **/
if($_POST['submit_edit_photogallery'] == 'yes'){
	include('subpages/photogallery-edit-submit.php');
}

/** Sort Photogallery Submit **/
if($_POST['order_photogallery'] == yes){
	echo '<div class="Message"><img src="images/tick.gif"> '.$_POST['Type'].' sorted.</div>';
}

/** Remove SubPage Submit **/
if($_POST['submit_remove'] == 'yes'){
	$key = $_POST[id];
	$mysqli->query("DELETE FROM $database WHERE id='$key'");
}

/** Add Page Form **/
if($_GET['x'] == 'add'){
	include('subpages/add.php');	
}


/** Edit Page Form **/
if($_GET['x'] == 'edit' && $_POST['id'] == '' && $_GET['y'] == ''){
	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[p]'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/ /', '', $parent_vars[2]);
	$var_list = preg_replace('/\'/', '', $var_list);
	$var_list = 'Sub_'.$var_list;
	$vars = $$var_list;

	echo'
		<div class="Title">
			'.$parent_vars[2].' - Edit Page
		</div>
		<div class="Label">Select Page</div>
	';
	
	$all = $mysqli->query("SELECT * FROM $database WHERE type='$type' ORDER BY name ASC");

	if(mysql_num_rows($all) < 1){
		echo 'There are no pages to list.';
	}
	else{
		echo'<table>';
		while($get = mysql_fetch_row($all)){
			echo'
			<tr>
				<td>'.$get[2].'</td><td width="50"></td>
				<td>
					<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=edit">
						<input type="hidden" name="id" value="'.$get[0].'"/>
						<input type="submit" value="Edit" />
					</form>
				</td>
			</tr>
			';
		}
		echo'</table>';
	}	
}
if($_GET['x'] == 'edit' && $_POST['id'] != '' || $_GET['x'] == 'edit' && $_GET['y'] != ''){
		include('subpages/edit.php');
}

/** Edit Photogallery Form **/
if($_GET['x'] == 'gallery_edit'){
	include('subpages/photogallery-edit-item.php');
}

/** Remove Page Form **/
if($_GET['x'] == 'remove'){
	
	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[p]'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/ /', '', $parent_vars[2]);
	$var_list = preg_replace('/\'/', '', $var_list);
	$var_list = 'Sub_'.$var_list;
	$vars = $$var_list;
		
	$all = $mysqli->query("SELECT * FROM $database WHERE type='$type' ORDER BY name ASC");
	echo '
		<div class="Title">'.$parent_vars[2].' - Remove Page</div>
		<div class="Label">&nbsp;</div>
	';
	if(mysql_num_rows($all) < 1){
		echo 'There are no pages to list.';
	}
	else{
		echo'<table>';
		while($get = mysql_fetch_row($all)){
			echo'
			<tr>
				<td>'.$get[2].'</td><td width="50"></td>
				<td>
					<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=remove">
						<input type="hidden" name="id" value="'.$get[0].'"/>
						<input type="hidden" name="submit_remove" value="yes"/>
						<input type="submit" value="Remove" />
					</form>
				</td>
			</tr>
			';
		}
		echo'</table>';
	}
}

/** Sort Pages Form **/
if($_GET['x'] == 'sort'){
	include('subpages/sort.php');
}



include('footer.php');
?>