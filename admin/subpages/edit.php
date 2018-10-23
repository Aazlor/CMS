<?php
/** Subpage Manage/Edit **/
/** Subpage Manage/Edit **/
/** Subpage Manage/Edit **/
/** Subpage Manage/Edit **/
/** Subpage Manage/Edit **/

	$id = $_POST['id'];
	if($id == ''){
		$id = $_GET['y'];
	}
	else{
		$_GET['y'] = $id;
	}
	
	$page_vars = $mysqli->query("SELECT * FROM $database WHERE id='$id'");
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
	
	$parent_vars = $mysqli->query("SELECT * FROM $database WHERE id='$_GET[p]'");
	$parent_vars = mysql_fetch_row($parent_vars);
	
	$var_list = preg_replace('/ /', '', $parent_vars[2]);
	$var_list = preg_replace('/\'/', '', $var_list);
	$var_list = 'Sub_'.$var_list;
	$vars = $$var_list;

	

	echo'
		<div class="Title">
			Edit '.$parent_vars[2].' - '.$page_name.'
		</div>
	';
	
	if(in_array('{photogallery}', $vars, true)){
		foreach($vars as $kp => $vp){
			if(preg_match('/{photogallery}/', $vp)){
				
				$field_name = $kp;
				$field_name_display = preg_replace('/_/', ' ', $kp);
				
				$photogallery_vars = 'Gallery__'.$field_name;
				$photogallery_vars = $$photogallery_vars;
				


				/***** BEGIN Display Photogallery Form Fields BEFORE Currently Added Items *****/
				echo '<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=edit&y='.$id.'">
			<fieldset>
				<legend>Add New '.$field_name_display.'</legend>
				';

				foreach($photogallery_vars as $key => $value){
					$display_key = preg_replace('/_/', ' ', $key);
					if(preg_match('/{image}/', $key)){
						$key = preg_replace('/{image}/', '', $key);
						$display_key = preg_replace('/_/', ' ', $key);
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
					}
					elseif(preg_match('/{file}/', $value)){
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
					}
					elseif(preg_match('/{text}/', $value)){
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="" class="text"></div>';
					}
					elseif(preg_match('/{textarea}/', $value)){
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$page_vars[$key].'</textarea></div>';
					}
					elseif(preg_match('/{checkbox}/', $value)){
						echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;"></div>';
					}
					elseif(preg_match('/{select}/i', $key)){
						$key = preg_replace('/{select}/i', '', $key);
						$display_key = preg_replace('/_/', ' ', $key);
						unset($options);
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
				}
				
				echo'		
					<div class="Submit">
						<input type="hidden" name="pageid" value="'.$id.'"/>
						<input type="hidden" name="field_name" value="'.$field_name.'"/>
						<input type="hidden" name="submit_add_photogallery" value="yes"/>
						<input type="submit" value="Add '.$photogallery_vars['Type'].'" />
					</div>
				</form>
			</fieldset>
				';
				/***** END Display Photogallery Form Fields BEFORE Currently Added Items *****/

								/***** Displays for Photogallery Manage and Sort Sections *****/
				unset($image_array);
				require("../photogallery/".$field_name."/".$id."-images.php");
				foreach($image_array as $key => $value){
					$split = explode('||', $value);
					foreach($split as $k => $v){
						if($v != ''){
							$edit_buttons[] = '
								<a href="?p='.$_GET[p].'&yy='.$_GET[y].'&field_name='.$field_name.'&id='.$key.'&x='.gallery_edit.'" class="Edit">
									<img style="border: none;" src="images/options.png">
								</a>

		 						<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=edit&y='.$_GET[y].'" onsubmit="return confirm(\'Confirm Deletion?\n This cannot be undone.\')">
									<input type="hidden" name="pageid" value="'.$id.'"/>
									<input type="hidden" name="imageid" value="'.$key.'"/>
									<input type="hidden" name="field_name" value="'.$field_name.'"/>
									<input type="hidden" name="type" value="'.$photogallery_vars[Type].'"/>
									<input type="hidden" name="submit_remove_photogallery" value="yes"/>
									<input type="image" src="images/cross.gif" class="Delete" />
								</form>
							';

							if(preg_match('/\.png/i', $v) || preg_match('/\.jpg/i', $v) || preg_match('/\.gif/i', $v) || preg_match('/\.bmp/i', $v) || preg_match('/\.jpeg/i', $v) || preg_match('/\.pns/i', $v) || preg_match('/\.tiff/i', $v)){
								$edit_block[] = '<img src="../photogallery/'.$field_name.'/'.$v.'">';
								$image = '1';
							}
							elseif(preg_match('/\.[a-z]/i', $v)){
								$edit_block[] = '<p><a href="../photogallery/'.$field_name.'/'.$v.'" target="_blank">../photogallery/'.$field_name.'/'.$v.'</a></p>';
							}
							else{
								$edit_block[] = '<p>'.$v.'</p>';
							}

							break(0);
						}
					}
				}
				/***** END Displays for Photogallery Manage and Sort Sections *****/

				echo '
				<fieldset>
					<legend>Manage '.$field_name_display.'</legend>
					';
					foreach($edit_block as $key => $value){
						echo '
							<div class="PhotogalleryItem">
								'.$edit_buttons[$key].'
								<div class="Contents">
									'.$value.'
								</div>
							</div>
						';					
					}
					echo '
				</fieldset>

				<div class="Clear"></div>
					
				<fieldset>
					<legend>Sort '.$field_name_display.'</legend>

					<div class="Label"></div>
						';
					
						unset($listitems);
						unset($listorder);

						foreach($edit_block as $key => $value){
							$listitems .= '<li id="pictureId_'.$key.'" class="SortItem">'.$value.'</li>';
						}
						echo'
						<div class="sortable" id="sortlist'.$photogallery_vars['Type'].'">
							'.$listitems.'
							<div class="Clear"></div>
						</div>
						<div id="activityIndicator" style="display:none; "><img src="images/load_indicator.gif" /><br>Saving image order to database </div>
						<div class="Submit">
							<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?p='.$_GET[p].'&x=edit&y='.$_GET[y].'">

								<input type="hidden" name="field_name" value="'.$field_name.'"/>
								<input type="hidden" name="Type" value="'.$photogallery_vars['Type'].'"/>
								<input type="hidden" name="order_photogallery" value="yes"/>
								
								<input type="submit" value="Update Changes" />
							</form>
						</div>


						';/***** SORT JS *****/echo'
						<script type="text/javascript">
							$(function() {
								$( "#sortlist'.$photogallery_vars['Type'].'" ).sortable({
									placeholder: "ui-state-highlight",
									opacity: 0.5,
								});
								$( "#sortlist'.$photogallery_vars['Type'].'" ).disableSelection();
							});

							$( "#sortlist'.$photogallery_vars['Type'].'" ).on( "sortupdate", function( event, ui ) {

								var sorted = $( "#sortlist'.$photogallery_vars['Type'].'" ).sortable( "serialize" );

								$.ajax({
									type: "POST",
									url: "calls/sort-photogallery.php?fn='.$field_name.'&id='.$_GET[y].'",
									datatype: "html",
									data: sorted,
									success: function(data) {
										var i = 0;
										$("#sortlist'.$photogallery_vars['Type'].' li").each(function(){
											var newid = "pictureId_" + i;
											$(this).attr("id", newid);
											i++;
										});
									}
								});

							});

						</script>
						';/***** END SORT JS *****/echo'
				</fieldset>
				';
				
			}
		}
	}
					
	echo'
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
		<div class="Label">
			Name
		</div>
		<div class="LabelInsert">
			<input type="text" name="Page_Name" value="'.$page_name.'" class="text">
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
		if(preg_match('/{image}/i', $key)){
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
		elseif(preg_match('/{file}/', $value)){
			if($page_vars[$key] == ''){
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
						Current File: <a href="/files/'.$page_vars[$key].'" target="_blank">http://www.'.$_SERVER['SERVER_NAME'].'/files/'.$page_vars[$key].'</a>
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
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$page_vars[$key].'" class="text"></div>';
		}
		elseif(preg_match('/{number}/', $value)){
			$text_value = preg_replace('/"/', '&quot;', $product_vars[$key]);
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$text_value.'" class="text number"></div>';
		}
		elseif(preg_match('/{textarea}/', $value)){
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$page_vars[$key].'</textarea></div>';
		}
		elseif(preg_match('/{checkbox}/', $value)){
			if($page_vars[$key] == 'Yes'){
				$checked = 'checked="checked"';
			}
			echo '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="Yes" '.$checked.'></div>';
			unset($checked);
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
				if(isset($page_vars[$key][$kk])){
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
			<input type="hidden" name="parent" value="'.$_GET['p'].'">
			<input type="hidden" name="page_id" value="'.$id.'"/>
			<input type="hidden" name="submit_edit" value="yes"/>
			<input type="submit" value="Save Changes" />
		</div>
	</form>
	';

?>