<?php
/** SubPage Manage/Edit Gallery Item **/
/** SubPage Manage/Edit Gallery Item **/
/** SubPage Manage/Edit Gallery Item **/
/** SubPage Manage/Edit Gallery Item **/
/** SubPage Manage/Edit Gallery Item **/

	$page_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[yy]'");
	$page_vars = mysql_fetch_row($page_vars);
	
	$page_name = $page_vars[2];
	$page_url = $page_vars[3];
	
	$page_vars = preg_split('/{{}}/', $page_vars[4]);
	foreach($page_vars as $value){
		$sort = preg_split('/\(\(\)\)/', $value);
		$page_vars[$sort[0]] = $sort[1];
		
		if(preg_match('/^\+[0-9]/', $sort[0])){
			$product_groupings[$sort[0]] = $sort[1];
		}
	}
	
	$vars = $$page_vars['template'];
	
	echo'
		<div class="Title">
			Edit Page - '.$page_name.'
		</div>
	';
	
	require("../gallery/".$_GET['field_name']."/".$_GET['yy']."-images.php");	
				
	if(in_array('{gallery}', $vars, true)){
		foreach($vars as $kp => $vp){
			if(preg_match('{gallery}', $vp)){
				
				$field_name = $kp;
				$field_name_display = preg_replace('/_/', ' ', $kp);
				
				$gallery_vars = 'Gallery__'.$field_name;
				$gallery_vars = $$gallery_vars;
			
				unset($listitems);
				unset($listorder);
				$a=1;
				foreach($gallery_array as $key => $value){
					$split = preg_split('/\|\|/', $value);
					$listitems .= '<li id="listItem_'.$key.'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" /><img src="../gallery/'.$split[0].'"  width="75"></li>';
					$listorder .= '<div style="font-size: 14px; font-family: arial; padding-bottom: 3px; float: left; width: 170px; margin: 10px;"><img src="../gallery/'.$_GET['yy'].'-'.$split[0].'" width="150"></div>';
					$a++;
				}
				
				$split = explode('||', $gallery_array[$_GET[id]]);
				
				echo '<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&y='.$_GET[yy].'&x=edit">';
			
				$i = '-1';
				
				foreach($gallery_vars as $key => $value){
					$display_key = preg_replace('/_/', ' ', $key);
					if(preg_match('/{image}/', $key)){
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
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'>'.$options.'</select></div><div class="Clear"></div>';
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
									Current File: <a href="/gallery/'.$field_name.'/'.$split[$i].'" target="_blank">http://www.'.$_SERVER['SERVER_NAME'].'/gallery/'.$field_name.'/'.$split[$i].'</a>
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
					$i++;
				}
				
				echo'		
					<div class="Submit">
						<input type="hidden" name="field_name" value="'.$field_name.'"/>
						<input type="hidden" name="gallery_id" value="'.$_GET[id].'"/>
						<input type="hidden" name="submit_edit_gallery" value="yes"/>
						<input type="submit" value="Edit '.$gallery_vars[Type].'" />
					</div>
				</form>
				';
				
			}
		}
	}

?>