<?php
/** SubPage Add Page Form **/
/** SubPage Add Page Form **/
/** SubPage Add Page Form **/
/** SubPage Add Page Form **/
/** SubPage Add Page Form **/

	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[p]'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/[\s]+/', '', $parent_vars[2]);
	$var_list = preg_replace('/[\W]+/', '', $var_list);
	$var_list = 'Sub_'.$var_list;

	$vars = $$var_list;
	
	echo'
		<div class="Title">
			'.$parent_vars[2].' - Add Page
		</div>
	';
					
	echo'
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=edit">
		<div class="Label">
			Page Name
		</div>
		<div class="LabelInsert">
			<input type="text" name="Page_Name" value="" class="text">
		</div>
	';
	foreach($vars as $key => $value){
		$display_key = preg_replace('/_/', ' ', $key);
		if(preg_match('/{text}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$page_vars[$key].'" class="text"></div>';
		}
		elseif(preg_match('/{textarea}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$page_vars[$key].'</textarea></div>';
		}
	}
	echo'
		<div class="Label">
			Meta Title
		</div>
		<div class="LabelInsert">
			<input type="text" name="meta_title" value="'.$page_vars['meta_title'].'" onkeyup="MaxLength(this,60)" class="metatext">
			<div class="sub">Title your page. Maximum 60 characters.</div>
		</div>
		<div class="Label">
			Meta Description
		</div>
		<div class="LabelInsert">
			<input type="text" name="meta_description" value="'.$page_vars['meta_description'].'" onkeyup="MaxLength(this,150)" class="metatext">
			<div class="sub">Write a short description about this page. Maximum 150 characters.</div>
		</div>
		<div class="Label">
			Meta Keywords
		</div>
		<div class="LabelInsert">
			<input type="text" name="meta_keywords" value="'.$page_vars['meta_keywords'].'" onkeyup="MaxLength(this,250)" class="metatext">
			<div class="sub">Example: enter,words,about,your,website,separated,by,commas. Maximum 250 characters.</div>
		</div>
		
	<div class="Submit">
		<input type="hidden" name="parent" value="'.$_GET[p].'"/>
		<input type="hidden" name="template" value="'.$var_list.'">
		';
		
		if(in_array('{photogallery}', $vars)){
			$gal = array_search('{photogallery}', $vars);
			echo '<input type="hidden" name="gallery" value="'.$gal.'">';
		}
		
		echo'
		<input type="hidden" name="submit_add" value="yes"/>
		<input type="submit" value="Submit" />
	</div>	
	</form>
	';

?>