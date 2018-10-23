<?

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

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

function resizeImage($width, $height, $value){

	if(!isset($value['width']))
		$value['width'] = '';
	if(!isset($value['height']))
		$value['height'] = '';
	if(!isset($value['minwidth']))
		$value['minwidth'] = '';
	if(!isset($value['minheight']))
		$value['minheight'] = '';

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
	if(isset($modheight) && $modheight < $value['minheight'] && $value['minheight'] != ''){
		$modheight = $value['minheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if(!isset($modwidth) || $modwidth == ''){
		$modwidth = $width;
	}
	if(!isset($modheight) || $modheight == ''){
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
	
	$return_data = [
		'modwidth' => $modwidth,
		'modheight' => $modheight
	];
	return $return_data;
}

function handleFiles($files, $params, $dir_path){

	foreach($files as $file_key => $file_val){

		$source = $files[$file_key]['tmp_name'];
		$basename = basename( $files[$file_key]['name']);
		$target = $dir_path."/".basename( $files[$file_key]['name']);
		move_uploaded_file($source, $target);

		if(isset($params[$file_key]) && $params[$file_key] == '{file}' && isset($files[$file_key]['name']) && $files[$file_key]['name'] != ''){
			$record_file[$file_key] = $files[$file_key]['name'];
		}
		elseif($files[$file_key]['name'] != ''){
			$imagetype = trim(substr($basename, strrpos($basename, '.')));
			$imagetype = strtolower($imagetype);
			
			if($imagetype != '.jpeg' && $imagetype != '.jpg' && $imagetype != '.gif' && $imagetype != '.png'){
				$error = 1;
				$error_code[] = $imagetype.' is not a valid file type.  Convert to .jpeg, .jpg, .gif or .png and try again.';
				break 1;
			}
			
			$resize_params = $params[$file_key.'{image}'];
											
			/*****	BEGIN IMAGE RESIZE *****/
						
			list($width, $height) = getimagesize($target) ;
			
			if($width > 3000 || $height > 2000){
				$error = 1;
				$error_code[] = $basename.' is too large.  Reduce size to below 3000px Width and below 2000px Height';
				break 1;
			}
					
			$depth = countdim($resize_params);
			if($depth >= 1){
				foreach($resize_params as $key => $value){
					if(is_array($value)){
						$size_var = $save = $image = $tn = '';

						$size_var = ($key != 'medium') ? $key.'-' : '';
						
						$save = $dir_path.'/'.$size_var.$files[$file_key]['name']; //This is the new file you saving
						
						$resized = resizeImage($width, $height, $value);
						
						$tn = imagecreatetruecolor($resized['modwidth'], $resized['modheight']) ;
						imagealphablending($tn, false);
						imagesavealpha($tn, true);
						if($imagetype == '.jpg' || $imagetype == '.jpeg'){
							$image = imagecreatefromjpeg($target);
							imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
							imagejpeg($tn, $save, 100);
						}
						elseif($imagetype == '.gif'){
							$image = imagecreatefromgif($target);
							imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
							imagegif($tn, $save, 100);
						}
						elseif($imagetype == '.png'){
							$image = imagecreatefrompng($target);
							imagecopyresampled($tn, $image, 0, 0, 0, 0, $resized['modwidth'], $resized['modheight'], $width, $height);
							imagepng($tn, $save, 100);
						}
					}
				}
			}
			$record_file[$file_key] = $_FILES[$file_key]['name'];			
		}

	}

	return $record_file;
}

if(!function_exists('pre')){
	function pre($array){
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}

function createArray($string){
	$a = explode('{{}}', $string);
	foreach($a as $v){
		if(!strstr($v, '(())'))
			continue;
		$s = explode('(())', $v);
		$x[$s[0]] = $s[1];
	}
	return($x);
}

function throwError(){
	include 'aa_error_page.php';
	exit;
}

function getData($id){
	global $mysqli, $database;

	$get = $mysqli->query("SELECT * FROM $database WHERE id='$id'");

	if($get->num_rows == 0){
		throwError();
	}

	$get = $get->fetch_assoc();

	$get['info'] = createArray($get['info']);

	return $get;
}

function iterate_categories($fetchCats, $compare = array(), $manage = false){

	global $database, $mysqli;

	$html = '';

	while($cat = $fetchCats->fetch_array()){
		$check = (in_array($cat['id'], $compare)) ? 'checked="checked"' : '';
		$checkSubCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='$cat[0]' ORDER BY sort, name ASC");
		if($checkSubCats->num_rows > 0){
			$html .= ($manage === false) ? 
				'<li data-id="'.$cat['id'].'"><input type="checkbox" name="category[]" value="'.$cat['id'].'" '.$check.'>  '.$cat['name'].'<ul class="Cat">' :
				'<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span><ul class="Cat">';
			$html .= iterate_categories($checkSubCats, $compare, $manage);
			$html .= '</ul></li>';
		}
		else{
			$html .= ($manage === false) ? 
				'<li data-id="'.$cat['id'].'"><input type="checkbox" name="category[]" value="'.$cat['id'].'" '.$check.'>  '.$cat['name'].'</li>' :
				'<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span></li>';
		}
	}

	return $html;
}

function prune($branches){
	global $database, $mysqli;
	while($branch = $branches->fetch_assoc()){
		$twig = $mysqli->query("SELECT * FROM $database WHERE relation='$branch[id]'");
		if($twig->num_rows > 0){
			prune($twig);
		}
		$mysqli->query("DELETE FROM $database WHERE id='$branch[id]'");
	}
}

function buildForm($data, $template, $categories = []){

	global $Gallery;

	// pre($data);
	// pre($template);

	$html = [];

	if(in_array('{gallery}', $template, true)){
		foreach($template as $key => $value){
			if(!is_array($value)){
				if(stristr($value, '{gallery}')){
					$filename = ($data['type'] == 'Product') ? $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$key.'.php' : $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$key.'/images.php';
					// echo $filename;
					if(!file_exists($filename)){
						$file = fopen($filename,"w");
						fwrite($file, '');
						fclose($file);
						chmod($file, 0777);
					}	
					include 'aa_gallery_display.php';
				}
			}
		}
	}

	foreach($template as $key => $value){
		$options = '';
		$pretty_key = str_replace('_', ' ', $key);

		if(strstr($key, '{image}')){

			$key = str_ireplace('{image}', '', $key);
			$pretty_key = str_ireplace('{image}', '', $pretty_key);
			$html[$key]['title'] = ucwords($pretty_key);

			if(!isset($data['info'][$key]) || $data['info'][$key] == ''){
				$html[$key]['html'] = '<input type="file" name='.$key.'>';
			}
			else{
				$html[$key]['html'] = '
					<div class="InlineBlock">
						<img src="/images/'.$data['info'][$key].'">
					</div>
					<div class="InlineBlock">
						<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current Image
						<div class="DottedDivider"></div>
						&nbsp;Change Image To: <input type="file" name='.$key.'>
						<input type="hidden" name="Current{}'.$key.'" value="'.$data['info'][$key].'">
					</div>
				';
			}
		}
		elseif(is_array($value)){
			if(stristr($key, '{select}')){
				$key = str_ireplace('{select}', '', $key);
				$html[$key]['title'] = ucwords($pretty_key);

				foreach($value as $kk => $vv){
					$options .= ($data['info'][$key] == $vv) ? '<option selected="selected">'.$vv.'</option>' : '<option>'.$vv.'</option>';
				}

				$html[$key]['html'] = '<select name='.$key.'><option value=""></option>'.$options.'</select>';
			}
			elseif(stristr($key, '{radio}')){
				$key = str_ireplace('{radio}', '', $key);
				$html[$key]['title'] = ucwords($pretty_key);

				foreach($value as $kk => $vv){
					$checked = ($data['info'][$key] == $vv) ? 'checked="checked"' : '';
					$options .= '<div class="radio"><input type="radio" name="'.$key.'" value="'.$vv.'" '.$checked.'>'.$vv.'</div>';
				}
				$html[$key]['html'] = $options;
			}
			elseif(stristr($key, '{checkbox}')){
				$key = str_ireplace('{checkbox}', '', $key);
				$pretty_key = str_ireplace('{checkbox}', '', $pretty_key);
				$html[$key]['title'] = ucwords($pretty_key);

				$check_for_vars = (isset($data['info'][$key])) ? explode('||', $data['info'][$key]) : [];

				foreach($value as $kk => $vv){
					$checked = (in_array($vv, $check_for_vars)) ? 'checked="checked"' : '';
					$options .= '<div class="checkbox"><input type="checkbox" name="'.$key.'[]" value="'.$vv.'" '.$checked.'>'.$vv.'</div>';
				}
				$html[$key]['html'] = $options;
			}
			elseif(stristr($key, '{togglebox}')){
				$key = str_ireplace('{togglebox}', '', $key);
				$pretty_key = str_ireplace('{togglebox}', '', $pretty_key);
				$html[$key]['title'] = ucwords($pretty_key);

				$options = 'data-checked="'.$value['checked'].'" data-unchecked="'.$value['unchecked'].'"';
				$checked = (isset($data['info'][$key]) && $data['info'][$key] == 'yes') ? 'checked="checked"' : '';
				
				$html[$key]['html'] = '<div class="checkbox"><input type="checkbox" name="'.$key.'" value="yes" '.$options.' '.$checked.' class="togglebox">Yes</div>';
			}	
		}
		// elseif($value == '{gallery}'){
		// 	/*********************************************************/
		// 	$filename = $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$key.'.php';

		// 	if(!file_exists($filename)){
		// 		$file = fopen($filename,"w");
		// 		fwrite($file, '');
		// 		fclose($file);
		// 		chmod($file, 0777);
		// 	}	

		// 	$pretty_key = str_ireplace('_', ' ', $key);

		// 	$gallery_vars = $Gallery[$key];

		// 	pre($gallery_vars);

		// 	// $html = buildForm($data, $template);

		// }
		elseif(stristr($value, '{file}')){

			$html[$key]['title'] = ucwords($pretty_key);
			$html[$key]['html'] = (isset($data['info'][$key]) && $data['info'][$key] != '') ? '
				<div class="FileInput"><input type="file" name='.$key.'></div>
				<div class="FileRemove">
					<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete File
					<input type="hidden" name="Current{}'.$key.'" value="'.$data['info'][$key].'">
				</div>
				<div class="FileCurrent">
					Current File: <b>'.$data['info'][$key].'</b>
				</div>
				' : '<div class="FileInput"><input type="file" name='.$key.'></div>';
		}
		elseif(strstr($value, '{text}')){
			if($key == 'Name')
				$text_value = $data['name'];
			elseif(isset($data['info'][$key]))
				$text_value = str_replace('"', '&quot;', $data['info'][$key]);

			$html[$key]['title'] = ucwords($pretty_key);
			$html[$key]['html'] = '<input type="text" name="'.$key.'" value="'.$text_value.'" class="text">';
		}
		elseif(strstr($value, '{textarea}')){
			$display_textarea = (isset($data['info'][$key])) ? $data['info'][$key] : '';
			$html[$key]['title'] = ucwords($pretty_key);
			$html[$key]['html'] = '<textarea name="'.$key.'">'. $display_textarea .'</textarea>';
		}
		elseif(strstr($value, '{number}')){
			$text_value = (isset($data['info'][$key])) ? str_replace('"', '&quot;', $data['info'][$key]) : '';
			$html[$key]['title'] = ucwords($pretty_key);
			$html[$key]['html'] = '<input type="text" name="'.$key.'" value="'.$text_value.'" class="text number">';
		}
		elseif(stristr($key, '{details}')){
			$html[$key]['html'] = '<div class="Details">'.$value.'</div>';
		}
		elseif(strstr($value, '{categories}')){
			$html[$key]['title'] = ucwords($pretty_key);
			$html[$key]['html'] = '<div class="CategoryList">';

			if($data['relation'] != '')
				$compare = preg_split('/\\)\\(|\\(|\\)/', $data['relation'], -1, PREG_SPLIT_NO_EMPTY);
			else
				$compare = array();
			if($categories){
				$html[$key]['html'] .= iterate_categories($categories, $compare);
			}

			$html[$key]['html'] .= '</div>';
		}
	}

	$data['info']['meta_title'] = (!isset($data['info']['meta_title'])) ? '' : $data['info']['meta_title'];
	$data['info']['meta_description'] = (!isset($data['info']['meta_description'])) ? '' : $data['info']['meta_description'];
	$data['info']['meta_keywords'] = (!isset($data['info']['meta_keywords'])) ? '' : $data['info']['meta_keywords'];

	$html['meta_title']['title'] = 'Meta Title';
	$html['meta_title']['html'] = '<input type="text" name="meta_title" value="'.$data['info']['meta_title'].'" onkeyup="MaxLength(this,60)" class="metatext"><div class="sub">Title your page. Maximum 60 characters.</div>';

	$html['meta_description']['title'] = 'Meta Description';
	$html['meta_description']['html'] = '<input type="text" name="meta_description" value="'.$data['info']['meta_description'].'" onkeyup="MaxLength(this,150)" class="metatext"><div class="sub">Write a short description about this page. Maximum 150 characters.</div>';

	$html['meta_keywords']['title'] = 'Meta Keywords';
	$html['meta_keywords']['html'] = '<input type="text" name="meta_keywords" value="'.$data['info']['meta_keywords'].'" onkeyup="MaxLength(this,250)" class="metatext">
		<div class="sub">Example: enter,words,about,your,website,separated,by,commas. Maximum 250 characters.</div>';

	$string = '';
	foreach($html as $k => $v){
		$string .= (isset($v['title']) && $v['title'] != '')? '<div class="Label">'.$v['title'].'</div><div class="FieldInput LabelInsert">'.$v['html'].'</div>' : '<div class="FieldInput LabelInsert">'.$v['html'].'</div>';
	}

	return $string;
}

function displayProducts($get_products){
	global $mysqli, $templates;

	$image_path = array_search('{gallery}', $templates['Product']);

	$product_list = [];

	$count = $get_products->num_rows;

	$productDisplay = '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

	if($count > 0){

		//$except = array("rar", "zip", "mp3", "mp4", "mp3", "mov", "flv", "wmv", "swf", "png", "gif", "jpg", "bmp", "avi");
		$except = array("png", "gif", "jpg", "bmp", "jpeg");
		$imp = implode('|', $except);

		$product_list = []; // .= '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

		while($product = $get_products->fetch_assoc()){

			$product_list[$product['id']] = $product;

			include $_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.php';

			$background = '';
			if(!empty($gallery_array)){
				$images = preg_grep('/^.*\.('.$imp.')$/i', $gallery_array);
				if(count($images) > 0){
					$split = explode('||', reset($images));
					$image = preg_grep('/^.*\.('.$imp.')$/i', $split);
					$product_list[$product['id']]['bg'] = (count($images) > 0) ? reset($image) : '';
				}
			}
		}
	}

	if(!empty($product_list)){
		foreach($product_list as $product){
			$productDisplay .= '
			<li class="SortItem" id="itemID_'.$product['id'].'">
				<div class="Product" data-product="'.$product['id'].'">
				<h4>Product ID: '.$product['id'].'</h4>
					<div class="img">
						<img src="/products/'.$product['id'].'/thumb-'.$product['bg'].'">
					</div>
					<h3>'.$product['name'].'</h3>
				</div>
				<div class="Delete" data-id="'.$product['id'].'"><img src="images/cross.gif"></div>
			</li>
			';
		}
	}
	else{
		$productDisplay = '<div class="SubTitle" style="text-align: center;">0 Results</div>';
	}

	return $productDisplay;
}

?>