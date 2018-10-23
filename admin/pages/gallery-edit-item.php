<?php

$page_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[yy]'");
$page_vars = $page_vars->fetch_array();

$page_name = $page_vars[2];
$page_url = $page_vars[3];

$field_name = $_GET['field_name'];
$field_name_display = preg_replace('/_/', ' ', $_GET['field_name']);

$gallery_vars = 'Gallery__'.$field_name;
$gallery_vars = $$gallery_vars;

echo'
	<div class="Title">
		'.$page_name.' - Edit '.$field_name_display.'
	</div>
';
				
			
require($_SERVER['DOCUMENT_ROOT'].'/gallery/'.$field_name.'/images.php');	

$gallery_item = $gallery_array[$_GET['array_key']];

if(preg_match('/\|\|/', $gallery_item)){
	$split = explode('||', $gallery_item);
}
else{
	$split[] = $gallery_item;
}


echo '<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?&y='.$_GET['yy'].'&x=edit">';

$i = '-1';

foreach($gallery_vars as $key => $value){
	$display_key = preg_replace('/_/', ' ', $key);
	if(preg_match('/{image}/i', $key)){
		$key = preg_replace('/{image}/', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		if($split[$i] == ''){
			echo'
			<div class="Label">'.ucwords($display_key).'</div>
			<div class="FieldInput LabelInsert">
				<input type="file" name='.$key.'>
			</div>
			';
		}
		else{
			echo '
			<div class="Label">'.ucwords($display_key).'</div>
			<div class="FieldInput LabelInsert">
				
				<div class="InlineBlock">
					<img src="/gallery/'.$field_name.'/'.$split[$i].'">
				</div>
				<div class="InlineBlock">
					<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current Image
					<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
					&nbsp;Change Image To: <input type="file" name='.$key.'>
					<input type="hidden" name="Current{}'.$key.'" value="'.$split[$i].'">
				</div>
			</div>
			<div class="Clear"></div>
			';
		}
	}
	elseif(preg_match('/{text}/', $value)){
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$split[$i].'" class="text"></div>';
	}
	elseif(preg_match('/{textarea}/', $value)){
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$split[$i].'</textarea></div>';
	}
	elseif(preg_match('/{select}/i', $key)){
		$key = preg_replace('/{select}/i', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		foreach($value as $kk => $vv){
			if($split[$i] == $vv){
				$options .= '<option selected="selected">'.$vv.'</option>';					
			}
			else{
				$options .= '<option>'.$vv.'</option>';
			}
		}
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'><option value=""></option>'.$options.'</select></div><div class="Clear"></div>';
	}
	elseif(preg_match('/{checkbox}/', $value)){
		unset($checked);
		if($split[$i] == 'yes'){
			$checked = 'checked="checked"';
		}
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="yes" '.$checked.'></div>';
	}
	elseif(preg_match('/{file}/', $value)){
		if($split[$i] == ''){
			echo'
			<div class="Label">'.ucwords($display_key).'</div>
			<div class="FieldInput LabelInsert">
				<input type="file" name='.$key.'>
			</div>
			<div class="Clear"></div>
			';
		}
		else{
			echo '
			<div class="Label">'.ucwords($display_key).'</div>
			<div class="FieldInput LabelInsert">
				
				<div class="InlineBlock">
					Current File: <a href="/gallery/'.$field_name.'/'.$split[$i].'" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/gallery/'.$field_name.'/'.$split[$i].'</a>
				</div>
				<div class="InlineBlock File">
					<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current File
					<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
					&nbsp;Change File To: <input type="file" name='.$key.'>
					<input type="hidden" name="Current{}'.$key.'" value="'.$split[$i].'">
				</div>
			</div>
			<div class="Clear"></div>
			';
		}
	}
	elseif(preg_match('/{details}/i', $key)){
		echo '<div class="Details">'.$value.'</div>';
		$i--;
	}
	$i++;
}

echo'		
	<div class="Submit">
		<input type="hidden" name="field_name" value="'.$field_name.'"/>
		<input type="hidden" name="array_key" value="'.$_GET['array_key'].'"/>
		<input type="hidden" name="submit_edit_gallery" value="yes"/>
		<input type="submit" value="Edit '.$field_name_display.'" />
	</div>
</form>
';

?>