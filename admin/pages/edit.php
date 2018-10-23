<?

$page_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[y]'");
$page_vars = $page_vars->fetch_array();

$page_name = $page_vars[2];
$page_url = $page_vars[3];

$page_vars = explode('{{}}', $page_vars[4]);
foreach($page_vars as $value){
	if(!strstr($value, '(())'))
		continue;
	$sort = explode('(())', $value);

	$page_vars[$sort[0]] = $sort[1];
	
	if(preg_match('/^\+[0-9]/', $sort[0])){
		$product_groupings[$sort[0]] = $sort[1];
	}
}

$page = $page_vars['template'];
$vars = $$page;

echo'
	<div class="Title">
		Edit Page - '.$page_name.'
	</div>
';

if(in_array('{gallery}', $vars, true)){
	foreach($vars as $kp => $vp){
		###	Only want to find the key that has {gallery} as a value
		if(is_array($vp))
			continue;
		if(preg_match('/{gallery}/', $vp)){
			include('pages/gallery-display.php');			
		}
	}
}
				
echo'
<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
	<div class="Label">
		Page Name
	</div>
	<div class="LabelInsert">
		'.$page_name.'
	</div>
	<div class="Label">
		URL
	</div>
	<div class="LabelInsert">
		http://'.$site_url.$page_url.'
	</div>
';

foreach($vars as $key => $value){
	$display_key = preg_replace('/_/', ' ', $key);
	if(preg_match('/{image}/', $key)){
		$key = preg_replace('/{image}/', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		if($page_vars[$key] == ''){
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
					<img src="/images/'.$page_vars[$key].'">
				</div>
				<div class="InlineBlock">
					<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current Image
					<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
					&nbsp;Change Image To: <input type="file" name='.$key.'>
					<input type="hidden" name="Current{}'.$key.'" value="'.$page_vars[$key].'">
				</div>
			</div>
			<div class="Clear"></div>
			';
		}
	}
	elseif(preg_match('/{select}/i', $key)){
		$key = preg_replace('/{select}/i', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		$options = '';
		foreach($value as $kk => $vv){
			if($page_vars[$key] == $vv){
				$options .= '<option selected="selected">'.$vv.'</option>';					
			}
			else{
				$options .= '<option>'.$vv.'</option>';
			}
		}
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'><option value=""></option>'.$options.'</select></div><div class="Clear"></div>';
	}
	elseif(preg_match('/{radio}/i', $key)){
		$options = '';
		$key = preg_replace('/{radio}/i', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		foreach($value as $kk => $vv){
			if($page_vars[$key] == $vv){
				$options .= '<div class="radio"><input type="radio" name="'.$key.'" value="'.$vv.'" checked="checked">'.$vv.'</div>';
			}
			else{
				$options .= '<div class="radio"><input type="radio" name="'.$key.'" value="'.$vv.'">'.$vv.'</div>';
			}
		}
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert">'.$options.'</div><div class="Clear"></div>';
	}
	elseif(preg_match('/{checkbox}/i', $key)){
		$options = '';
		$key = preg_replace('/{checkbox}/i', '', $key);
		$display_key = preg_replace('/_/', ' ', $key);
		foreach($value as $kk => $vv){
			if($page_vars[$key] == $vv){
				$options .= '<div class="checkbox"><input type="checkbox" name="'.$key.'[]" value="'.$vv.'" checked="checked">'.$vv.'</div>';
			}
			else{
				$options .= '<div class="checkbox"><input type="checkbox" name="'.$key.'[]" value="'.$vv.'">'.$vv.'</div>';				
			}
		}
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput Field">'.$options.'</div><div class="Clear"></div>';
	}
	elseif(preg_match('/{details}/i', $key)){
		echo $value;
	}
	elseif(preg_match('/{file}/', $value)){
		$display_key = preg_replace('/_/', ' ', $key);
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="FieldInput LabelInsert"><input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete File<input type="hidden" name="Current{}'.$key.'" value="'.$page_vars[$key].'"><br><br>Current File: <b>'.$page_vars[$key].'</b></div><div class="Clear"></div>';

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
								Current File: <a href="/images/'.$page_vars[$key].'" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/images/'.$page_vars[$key].'</a>
							</div>
							<div class="InlineBlock File">
								<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current File
								<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
								&nbsp;Change File To: <input type="file" name='.$key.'>
								<input type="hidden" name="Current{}'.$key.'" value="'.$page_vars[$key].'">
							</div>
						</div>
						<div class="Clear"></div>
						';
					}
	}
	elseif(preg_match('/{text}/', $value)){
		$text_value = preg_replace('/"/', '&quot;', $page_vars[$key]);
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$text_value.'" class="text"></div>';
	}
	elseif(preg_match('/{number}/', $value)){
		$text_value = preg_replace('/"/', '&quot;', $page_vars[$key]);
		echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$text_value.'" class="text number"></div>';
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
		<input type="hidden" name="template" value="'.$page_vars['template'].'">
		<input type="hidden" name="page_id" value="'.$_GET['y'].'"/>
		<input type="hidden" name="submit_edit" value="yes"/>
		<input type="submit" value="Save Changes" />
	</div>
</form>
';

?>