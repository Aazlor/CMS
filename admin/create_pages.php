<?php

include('header.php');
include('config.php');

function clean_url($url){
	$url = strtolower($url);
	if(!preg_match('/http:\/\//', $url)){
		$url = preg_replace('/^\//', '', $url);
		$url = preg_replace('/\.html/', '', $url);
		$url = preg_replace('/ /', '-', $url);
		$url = preg_replace('/\//', '-', $url);
		$url = preg_replace('/\\\/', '-', $url);
		$url = preg_replace('/&/', 'and', $url);
		$url = '/'.$url.'.html';
	}
	return $url;
}

if($_POST['submit_add'] == 'yes'){
	
	$url = clean_url($_POST[page_url]);
	
	foreach($_POST as $key=>$value){
		if($key != 'page_name' && $key != 'page_url' && $key != 'submit_add'){
			if(preg_match('/meta_keywords/', $key) && preg_match('/\Z,/', $value)){
				$value = preg_replace('/\Z,/', '', $value);
			}
			$info .= $key.'(())'.$value.'{{}}';
		}
	}
	
	/***** START IMAGE RESIZER *****/
	
	foreach($_FILES as $file_key=>$file_value){
		if($_FILES[$file_key]['name'] != ''){
			$imagename = $_FILES[$file_key]['name'];
			if(preg_match('/.jpeg/', $imagename)){
				$imagetype = '.jpeg';
			}
			elseif(preg_match('/.jpg/', $imagename)){
				$imagetype = '.jpg';
			}
			elseif(preg_match('/.gif/', $imagename)){
				$imagetype = '.gif';
			}
			else{
				$error = 1;
				echo '____FAIL____';
				break 1;
			}
			
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../images/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			if($image_size_key != "medium"){
				$size_var = $image_size_key.'-';
			}
			else{
				$size_var = '';
			}
			
			
			/*****	BEGIN IMAGE RESIZE *****/
			
			$save = "../images/" . $size_var . $imagepath; //This is the new file you saving
			$file = "../images/" . $imagepath; //This is the original file
			
			list($width, $height) = getimagesize($file) ;
			
			$modheight = $height;
			$modwidth = $width;
			
			$tn = imagecreatetruecolor($modwidth, $modheight) ;
			if($imagetype == '.jpg' || $imagetype == '.jpeg'){
				$image = imagecreatefromjpeg($file);
				imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
				imagejpeg($tn, $save, 100);
			}
			elseif($imagetype == '.gif'){
				$image = imagecreatefromgif($file);
				imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
				imagegif($tn, $save, 100);
			}
			
			if(preg_match('/^\+/', $file_key)){
				$groupings[$file_key] = $_FILES[$file_key]['name'];
			}
			else{
				$record_images[$file_key] = $_FILES[$file_key]['name'];
			}
		}
	}
	/***** END IMAGE RESIZER *****/
	if(isset($record_images)){
		foreach($record_images as $key => $value){
			$image_list .= '{{}}'.$key.'(())'.$value;
		}
	}
	
	$info .= $image_list;
	
	$mysqli->query("INSERT INTO $database (type, name, relation, info) VALUES ('pages', '$_POST[page_name]', '$url', '$info')");
	
	echo'
	<table align="center" id="admin_content_inner">
		<tr>
			<td class="title" align="left" style="border-bottom: 1px solid #000;" colspan=3>
			Page Added - '.$_POST[page_name].'
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td align="left" valign="top">
				'.$_POST[page_name].'
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td align="left" valign="top">
				'.$url.'
			</td>
		</tr>
	</table>	
	';
}

if(!$_POST['template']){
	
	foreach($templates as $value){
		$select_template .= '<option value="'.$value.'">'.$value.'</option>';		
	}
	echo'
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?x=add">
	<table align="center" id="admin_content_inner">
		<tr>
			<td class="title" align="left" style="border-bottom: 1px solid #000;" colspan=5>
			Add a Page - Step 1
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td>Select Template</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<select name="template">'.$select_template.'</select>
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td align="left" valign="top">
				<input type="hidden" name="submit_add_continue" value="yes"/>
				<input type="submit" value="Continue" />
			</td>
		</tr>
	</table>	
	</form>
	';
}
if($_POST['template'] != ''){
	$vars = $$_POST['template'];
		
	echo'
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
	<table align="center" id="admin_content_inner">
		<tr>
			<td class="title" align="left" style="border-bottom: 1px solid #000;" colspan=5>
			Add a Page - Step 2
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td style="white-space: nowrap;" align="left">Page Name</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<input type="text" name="page_name" value="" style="width: 200px">
			</td>
		</tr>
		<tr><td height="15"></td>
		<tr>
			<td align="left" valign="top">
			URL
			</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<input type="text" name="page_url" value="" style="width: 200px">
			</td>
			<td width="10"></td>
			<td width="150" align="left">
				<span class="sub">Example: add-a-page.html</span>
			</td>
		</tr>
		<tr><td height="15"></td>
	';
	foreach($vars as $key => $value){
		if(preg_match('/{image}/', $key)){
			$key = preg_replace('/{image}/', '', $key);
			echo '<tr><td align="left">'.ucwords($key).'</td><td width="10"></td><td align="left"><input type="file" name='.$key.'></td></tr><tr><td height="15"></td>';
		}
		elseif(preg_match('/{text}/', $value)){
			echo '<tr><td align="left">'.ucwords($key).'</td><td width="10"></td><td align="left"><input type="text" name="'.$key.'" value=""></td></tr><tr><td height="15"></td>';
		}
		elseif(preg_match('/{textarea}/', $value)){
			echo '<tr><td align="left">'.ucwords($key).'</td><td width="10"></td><td colspan="3" align="left"><textarea name="'.$key.'"></textarea></td></tr><tr><td height="15"></td>';
		}
	}
	echo'
		<tr>
			<td align="left" valign="top">
			SEO Title
			</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<input type="text" name="meta_title" value="" style="width: 200px">
			</td>
		</tr>
		<tr><td height="15"></td>
		<tr>
			<td align="left" valign="top">
			SEO Meta Description
			</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<input type="text" name="meta_description" value="" style="width: 200px;">
			</td>
			<td width="10"></td>
			<td align="left">
				<span class="sub">Write a short description about this page.</span>
			</td>
		</tr>
		<tr><td height="15"></td>
		<tr>
			<td align="left" valign="top">
			SEO Meta Keywords
			</td>
			<td width="10"></td>
			<td align="left" valign="top">
				<input type="text" name="meta_keywords" value="" style="width: 200px;">
			</td>
			<td width="10"></td>
			<td align="left">
				<span class="sub">Example: enter,words,about,your,website,separated,by,commas</span>
			</td>
		</tr>
		<tr><td height="15"></td>
		<tr>
			<td align="left" valign="top">
				<input type="hidden" name="template" value="'.$_POST['template'].'">
				<input type="hidden" name="submit_add" value="yes"/>
				<input type="submit" value="Submit" />
			</td>
		</tr>
	</table>	
	</form>
	';
}

include('footer.php');

?>
