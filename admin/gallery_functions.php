<?
/***** FUNCTIONS *****/
function galleryDisplay($data, $template_key, $pretty_key){
	global $Gallery;

	$html['html'] = $listitems = '';
	$items = [];
	$html['title'] = $pretty_key;

	$gallery_vars = $Gallery[$template_key];

	switch ($data['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$template_key.'.txt';
			$dir_path = '/products/'.$data['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$template_key.'/images.txt';
			$dir_path = '/gallery/'.$template_key;
			break;
	}

	if(!file_exists($file_path)){
		$file = fopen($file_path,"w");
		fwrite($file, '');
		fclose($file);
		chmod($file_path, 0777);
	}
	$gallery_array = unserialize(file_get_contents($file_path));

	if(isset($gallery_array) && !empty($gallery_array)){
		foreach($gallery_array as $g_k => $g_v){

			$val = (is_array($g_v)) ? current($g_v) : $g_v;

			if(stristr($val, '.gif') || stristr($val, '.png') || stristr($val, '.jpg') || stristr($val, '.jpeg') || stristr($val, '.bmp') || stristr($val, '.pns') || stristr($val, '.tiff')){
				$items[] = '<img src="'.$dir_path.'/thumb-'.$val.'">';
				$image = '1';
			}
			elseif(preg_match('/\.[a-z]/i', $val)){
				$items[] = '<p><a href="'.$dir_path.'/'.$val.'" target="_blank">'.$dir_path.'/'.$val.'</a></p>';
			}
			else{
				$items[] = '<p>'.$val.'</p>';
			}
		}
	}
	/***** END Displays for Gallery Manage and Sort Sections *****/

	$html['html'] .= '<div class="_toggle" data-group="'.$template_key.'" data-id="'.$data['id'].'">
		<button class="dialogue"><span class="ui-icon ui-icon-plusthick"></span> Add '.$pretty_key.'</button>';
		if(!empty($items)){
			foreach($items as $i_k => $i_v){
				$listitems .= '<li id="itemID_'.$i_k.'" class="SortItem" data-id="'.$i_k.'">
					<div class="GalleryItem">
						<div class="Contents">
							'.$i_v.'
						</div>
					</div>
				</li>';
			}
		}
		else{
			$listitems = '<p>No '.$pretty_key.' loaded.</p>';
		}
		
		$html['html'] .= '
		<div class="sortable" id="sortlist'.$gallery_vars['Type'].'" data-fn="'.$template_key.'" data-id="'.$data['id'].'">
			'. $listitems .'
			<div class="Clear"></div>
		</div>
		<div class="dialogue" data-group="'.$template_key.'"></div>
	</div>';

	return $html;
}

function galleryAdd($key, $data){
	global $Gallery;

	$field_name = $key;
	$field_name_display = preg_replace('/_/', ' ', $key);
	
	$gallery_vars = $Gallery[$key];

	$html['title'] = 'Add New '.$field_name_display;
	$html['html'] = '';

	/***** BEGIN Display Gallery Form Fields BEFORE Currently Added Items *****/
	$html['html'] .= '<form method="POST" enctype="multipart/form-data" action="post_operations.php">';
		
	foreach($gallery_vars as $key => $value){
		$display_key = str_replace('_', ' ', $key);
		$display_key = preg_replace('/{.*?}/', '', $display_key);
		if(preg_match('/{image}/', $key)){
			$key = preg_replace('/{image}/', '', $key);
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
		}
		elseif(preg_match('/{file}/', $value)){
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="Clear"></div>';
		}
		elseif(preg_match('/{text}/', $value)){
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="" class="text"></div>';
		}
		elseif(preg_match('/{textarea}/', $value)){
			$display_text = (isset($data['info'][$key])) ? $data['info'][$key] : '';
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'.$display_text.'</textarea></div>';
		}
		elseif(preg_match('/{checkbox}/', $value)){
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;"></div>';
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
			$html['html'] .= '<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><select name='.$key.'>'.$options.'</select></div><div class="Clear"></div>';
		}
		elseif(preg_match('/{details}/i', $key)){
			$html['html'] .= '<div class="Details">'.$value.'</div>';
		}
	}
	
	$html['html'] .= '
		<div class="Submit">
			<input type="hidden" name="type" value="'.$data['type'].'"/>
			<input type="hidden" name="field_name" value="'.$field_name.'"/>
			<input type="hidden" name="id" value="'.$data['id'].'"/>
			<input type="hidden" name="function" value="gallery_add"/>
		</div>
	</form>
	';

	return $html;
}
function galleryAddSubmit($postData = [], $fileData = []){
	global $Gallery;
	$gallery_vars = $Gallery[$postData['field_name']];

	switch ($postData['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$postData['id'].'/'.$postData['field_name'].'.txt';
			$dir_path = '/products/'.$postData['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$postData['field_name'].'/images.txt';
			$dir_path = '/gallery/'.$postData['field_name'];
			break;
	}
	$gallery_array = unserialize(file_get_contents($file_path));

	$files_parsed = handleFiles($fileData, $gallery_vars, $dir_path);	#Upload files, resize images, return data

	$i=0;
	if(!empty($gallery_array)){
		foreach($gallery_array as $value){
			$gallery_items[$i] = $value;
			$i++;
		}
	}
	
	foreach($gallery_vars as $key => $value){

		if(!isset($gallery_items[$i]))
			$gallery_items[$i] = [];
		
		$key_name = (preg_match('/{.*?}/', $key)) ? preg_replace('/{.*?}/', '', $key) : $key;
		if(!isset($postData[$key_name]) && !isset($fileData[$key_name]))
			continue;

		if(stristr($key, '{image}') || $value == '{file}'){
			$gallery_items[$i][$key_name] = $files_parsed[$key_name];
		}
		elseif(isset($postData[$key])){
			$content = htmlspecialchars($postData[$key_name]);
			$content = stripslashes($content);
			$gallery_items[$i][$key] = $content;
		}
		elseif(isset($fileData[$key])){
			$gallery_items[$i][$key] = $fileData[$key]['name'];
		}
	}

	$file = fopen($file_path, "w+");
	fwrite($file, serialize($gallery_items));
	fclose($file);
}
function galleryEditSubmit($postData = [], $fileData = []){
	global $Gallery;

	$gallery_vars = $Gallery[$postData['field_name']];

	switch ($postData['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$postData['id'].'/'.$postData['field_name'].'.txt';
			$dir_path = '/products/'.$postData['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$postData['field_name'].'/images.txt';
			$dir_path = '/gallery/'.$postData['field_name'];
			break;
	}
	$gallery_array = unserialize(file_get_contents($file_path));

	$i=0;
	if(!empty($gallery_array)){
		foreach($gallery_array as $value){
			$gallery_items[$i] = $value;
			$i++;
		}
	}

	if(isset($_POST['delete']) && $_POST['delete'] == 'yes'){
		unset($gallery_items[$postData['array_key']]);
	}
	else{
		$files_parsed = handleFiles($fileData, $gallery_vars, $dir_path);	#Upload files, resize images, return data

		foreach($gallery_vars as $key => $value){

			if(!isset($gallery_items[$postData['array_key']]))
				$gallery_items[$postData['array_key']] = [];
			
			$key_name = (preg_match('/{.*?}/', $key)) ? preg_replace('/{.*?}/', '', $key) : $key;
			if(!isset($postData[$key_name]) && !isset($fileData[$key_name]))
				continue;

			if(stristr($key, '{image}') || $value == '{file}'){

				if(isset($postData['Remove{}'.$key_name]) && $postData['Remove{}'.$key_name] == 'yes'){

					$del_file = $_SERVER['DOCUMENT_ROOT'].$dir_path.'/'.$postData['Current{}'.$key_name];
					if(file_exists($del_file)){
						$del_file_parts = pathinfo($del_file);

						$di = new RecursiveDirectoryIterator($del_file_parts['dirname']);

						foreach ($di as $filename => $file) {
							$file_parts = pathinfo($filename);
							if(stristr($file_parts['filename'], $del_file_parts['filename']))
								unlink($filename);
						}
					}

					unset($gallery_items[$postData['array_key']][$key_name]);
					continue;
				}

				if(isset($files_parsed[$key_name]))
					$gallery_items[$postData['array_key']][$key_name] = (!isset($files_parsed[$key_name]) || $files_parsed[$key_name] == '') ? $postData['Current{}'.$key_name] : $files_parsed[$key_name];
			}
			elseif(isset($postData[$key])){
				$content = htmlspecialchars($postData[$key_name]);
				$content = stripslashes($content);
				$gallery_items[$postData['array_key']][$key] = $content;
			}
			elseif(isset($fileData[$key])){
				$gallery_items[$postData['array_key']][$key] = $fileData[$key]['name'];
			}
		}
	}

	$gallery_items = array_map('array_filter', $gallery_items);
	$gallery_items = array_values(array_filter($gallery_items));

	$file = fopen($file_path, "w+");
	fwrite($file, serialize($gallery_items));
	fclose($file);
}
function galleryItem($template_key, $data, $item){
	global $Gallery;

	$pretty_key = str_ireplace('_', ' ', $template_key);

	$html['title'] = 'Edit '.$pretty_key;

	$gallery_vars = $Gallery[$template_key];

	switch ($data['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$template_key.'.txt';
			$dir_path = '/products/'.$data['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$template_key.'/images.txt';
			$dir_path = '/gallery/'.$template_key;
			break;
	}
	$gallery_array = unserialize(file_get_contents($file_path));

	$gallery_item = $data;
	$gallery_item['info'] = $gallery_array[$item];
	$gallery_item['info']['template'] = $template_key;

	$item_html = buildForm($gallery_item, $gallery_vars, [], false);

	$html['html'] = '<form method="POST" enctype="multipart/form-data" action="post_operations.php">
		<div class="Delete"><input type="checkbox" name="delete" value="yes" id="delete" data-group="'.$pretty_key.'"> Delete this '.$pretty_key.'</div>

		'.$item_html.'

		<div class="Submit">
			<input type="hidden" name="type" value="'.$data['type'].'"/>
			<input type="hidden" name="field_name" value="'.$template_key.'"/>
			<input type="hidden" name="array_key" value="'.$item.'"/>
			<input type="hidden" name="id" value="'.$data['id'].'"/>
			<input type="hidden" name="function" value="gallery_edit"/>
		</div>
	</form>
	';

	return $html;
}
function gallerySort($data, $template_key, $sorted){
	global $Gallery;

	$gallery_vars = $Gallery[$template_key];

	switch ($data['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$template_key.'.txt';
			$dir_path = '/products/'.$data['id'];
			break;
		
		default:	#pages
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$template_key.'/images.txt';
			$dir_path = '/gallery/'.$template_key;
			break;
	}
	$gallery_array = unserialize(file_get_contents($file_path));

	$values = [];
	parse_str($sorted, $values);

	$new_order = [];
	foreach($values['itemID'] as $v){
		$new_order[] = $gallery_array[$v];
	}

	$file = fopen($file_path, "w+");
	fwrite($file, serialize($new_order));
	fclose($file);
}