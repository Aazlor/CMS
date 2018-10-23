<?

		$field_name = $key;
		$field_name_display = preg_replace('/_/', ' ', $key);
		
		$gallery_vars = $Gallery[$key];

		$html[$field_name]['html'] = '<div class="Label ToggleAble" data-group="'.$field_name.'">'.$field_name_display.'<span class="Right">&#9650;</span><span class="Right hide">&#9660;</span></div>';

		/***** BEGIN Display Gallery Form Fields BEFORE Currently Added Items *****/
		$html[$field_name]['html'] .= '
		<div class="toggle" data-group="'.$field_name.'">
		<form method="POST" enctype="multipart/form-data" action="aa_gallery_submit.php">
			<fieldset>
				<legend>Add New '.$field_name_display.'</legend>
				';
			
				foreach($gallery_vars as $key => $value){
					$display_key = str_replace('_', ' ', $key);
					$display_key = preg_replace('/{.*?}/', '', $display_key);
					if(preg_match('/{image}/', $key)){
						$key = preg_replace('/{image}/', '', $key);
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
					}
					elseif(preg_match('/{file}/', $value)){
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
					}
					elseif(preg_match('/{text}/', $value)){
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="" class="text"></div>';
					}
					elseif(preg_match('/{textarea}/', $value)){
						$display_text = (isset($data['info'][$key])) ? $data['info'][$key] : '';
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$display_text.'</textarea></div>';
					}
					elseif(preg_match('/{checkbox}/', $value)){
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;"></div>';
					}
					elseif(preg_match('/{select}/i', $key)){
						$key = preg_replace('/{select}/i', '', $key);
						unset($options);
						foreach($value as $kk => $vv){
							if($data['info'][$key] == $vv){
								$options .= '<option selected="selected">'.$vv.'</option>';					
							}
							else{
								$options .= '<option>'.$vv.'</option>';
							}
						}
						$html[$field_name]['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'>'.$options.'</select></div><div class="Clear"></div>';
					}
					elseif(preg_match('/{details}/i', $key)){
						$html[$field_name]['html'] .= '<div class="Details">'.$value.'</div>';
					}
				}
				
				$html[$field_name]['html'] .= '
					<div class="Submit">
						<input type="hidden" name="type" value="'.$data['type'].'"/>
						<input type="hidden" name="field_name" value="'.$field_name.'"/>
						<input type="hidden" name="id" value="'.$data['id'].'"/>
						<input type="hidden" name="submit_add_gallery" value="yes"/>
						<input type="submit" value="Add '.$field_name_display.'" />
					</div>
			</fieldset>
		</form>
		';
		/***** END Display Gallery Form Fields BEFORE Currently Added Items *****/


		/***** Displays for Gallery Manage and Sort Sections *****/
		unset($gallery_array);
		unset($edit_buttons);
		unset($edit_block);
		unset($listitems);
		$listitems = '';
		unset($listorder);
		unset($split);

		$filename = ($data['type'] == 'Product') ? $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$field_name.'.php' : $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$field_name.'/images.php';
		require($filename);

		if(isset($gallery_array) && $gallery_array != ''){
			foreach($gallery_array as $key => $value){
				$split = explode('||', $value);
				foreach($split as $k => $v){
					if($v != ''){
						$edit_buttons[] = '
							<a href="?product_id='.$data['id'].'&field_name='.$field_name.'&array_key='.$key.'&function=gallery_edit" class="Edit">
								<img style="border: none;" src="images/options.png">
							</a>

	 						<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?product_id='.$data['id'].'" onsubmit="return confirm(\'Confirm Deletion?\n This cannot be undone.\')">
								<input type="hidden" name="array_key" value="'.$key.'"/>
								<input type="hidden" name="field_name" value="'.$field_name.'"/>
								<input type="hidden" name="product_id" value="'.$data['id'].'"/>
								<input type="hidden" name="type" value="'.$gallery_vars['Type'].'"/>
								<input type="hidden" name="submit_remove_gallery" value="yes"/>
								<input type="image" src="images/cross.gif" class="Delete" />
							</form>
						';

						if(preg_match('/\.png/i', $v) || preg_match('/\.jpg/i', $v) || preg_match('/\.gif/i', $v) || preg_match('/\.bmp/i', $v) || preg_match('/\.jpeg/i', $v) || preg_match('/\.pns/i', $v) || preg_match('/\.tiff/i', $v)){
							$edit_block[] = '<img src="../products/'.$data['id'].'/thumb-'.$v.'">';
							$image = '1';
						}
						elseif(preg_match('/\.[a-z]/i', $v)){
							$edit_block[] = '<p><a href="/products/'.$data['id'].'/'.$v.'" target="_blank">/products/'.$data['id'].'/'.$v.'</a></p>';
						}
						else{
							$edit_block[] = '<p>'.$v.'</p>';
						}

						break 1;
					}
				}
			}
		}
		/***** END Displays for Gallery Manage and Sort Sections *****/

		$html[$field_name]['html'] .= '
		<div class="Clear"></div>
			
		<fieldset>
			<legend>Manage '.$field_name_display.'</legend>

			<div class="Label"></div>';
				

				if(!empty($edit_block)){
					foreach($edit_block as $key => $value){
						$listitems .= '<li id="itemID_'.$key.'" class="SortItem">
							<div class="GalleryItem">
								'.$edit_buttons[$key].'
								<div class="Contents">
									'.$value.'
								</div>
							</div>
						</li>';
					}
				}
				
				$html[$field_name]['html'] .= '
				<div class="sortable" id="sortlist'.$gallery_vars['Type'].'" data-fn="'.$field_name.'" data-id="'.$data['id'].'">
					'. $listitems .'
					<div class="Clear"></div>
				</div>

		</fieldset>
	</div>';