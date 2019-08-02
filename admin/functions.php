<?

include 'gallery_functions.php';


if(!function_exists('pre')){
	function pre($array){
		echo '<blockquote><pre><code>';
		print_r($array);
		echo '</code></pre></blockquote>';
	}
}

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
	if(!isset($value['maxwidth']))
		$value['maxwidth'] = '';
	if(!isset($value['maxheight']))
		$value['maxheight'] = '';


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

	$record_file = [];

	foreach($files as $file_key => $file_val){

		if($file_val['name'] == '')
			continue;

		$source = $files[$file_key]['tmp_name'];
		$basename = basename( $files[$file_key]['name']);
		$target = $_SERVER['DOCUMENT_ROOT'].$dir_path."/".basename( $files[$file_key]['name']);
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
						
						$save = $_SERVER['DOCUMENT_ROOT'].$dir_path.'/'.$size_var.$files[$file_key]['name']; //This is the new file you saving
						
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

function createArray($string){
	
	$a = explode('{{}}', $string);
	$x = [];

	foreach($a as $v){
		if(!strstr($v, '(())'))
			continue;
		$s = explode('(())', $v);
		$x[$s[0]] = $s[1];
	}
	return $x;
}

function throwError(){
	include 'error_page.php';
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

	$get['info']['Name'] = (isset($get['info']['Name'])) ? htmlspecialchars_decode($get['info']['Name'], ENT_QUOTES) : htmlspecialchars_decode($get['name'], ENT_QUOTES);

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

function buildForm($data, $template, $categories = [], $meta = true){
	global $Gallery;

	if(!isset($data['info']['template']) || $data['info']['template'] == '')
		$data['info']['template'] = $data['type'];

	switch ($data['type']) {
		case 'Product':
			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$data['info']['template'].'.txt';
			$dir_path = '/products/'.$data['id'];
			break;
		
		default:	#pages

			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$data['info']['template'].'/images.txt';
			$dir_path = (isset($Gallery[$data['info']['template']])) ? '/gallery/'.$data['info']['template'] : '/images';
			break;
	}

	$html = [];

	foreach($template as $template_key => $value){
		$options = '';
		$pretty_key = str_replace('_', ' ', $template_key);

		if(strstr($template_key, '{image}')){

			$template_key = str_ireplace('{image}', '', $template_key);

			$pretty_key = str_ireplace('{image}', '', $pretty_key);
			$html[$template_key]['title'] = ucwords($pretty_key);

			if(!isset($data['info'][$template_key]) || $data['info'][$template_key] == ''){
				$html[$template_key]['html'] = '<input type="file" name="'.$template_key.'">';
			}
			else{
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$dir_path.'/'.$data['info'][$template_key])){
					$img_element = '<img src="'.$dir_path.'/'.$data['info'][$template_key].'">';
					$img_form_element = '<input type="checkbox" name="Remove{}'.$template_key.'" value="yes">Delete Current Image
						<div class="DottedDivider"></div>
						&nbsp;Change Image To: <input type="file" name="'.$template_key.'">
						<input type="hidden" name="Current{}'.$template_key.'" value="'.$data['info'][$template_key].'">';
				}
				else{
					$img_element = '<span class="ui-icon ui-icon-image"></span>';
					$img_form_element = 'Add Image: <input type="file" name="'.$template_key.'">';
				}

				$html[$template_key]['html'] = '
					<div class="InlineBlock">
						'.$img_element.'
					</div>
					<div class="InlineBlock">
						'.$img_form_element.'
					</div>
				';
			}
		}
		elseif(is_array($value)){
			if(stristr($template_key, '{select}')){
				$template_key = str_ireplace('{select}', '', $template_key);
				$html[$template_key]['title'] = ucwords(str_replace('_', ' ', $template_key));

				foreach($value as $kk => $vv){
					$options .= ($data['info'][$template_key] == $vv) ? '<option selected="selected">'.$vv.'</option>' : '<option>'.$vv.'</option>';
				}

				$html[$template_key]['html'] = '<select name="'.$template_key.'"><option value=""></option>'.$options.'</select>';
			}
			elseif(stristr($template_key, '{radio}')){
				$template_key = str_ireplace('{radio}', '', $template_key);
				$html[$template_key]['title'] = ucwords($pretty_key);

				foreach($value as $kk => $vv){
					$checked = ($data['info'][$template_key] == $vv) ? 'checked="checked"' : '';
					$options .= '<div class="radio"><input type="radio" name="'.$template_key.'" value="'.$vv.'" '.$checked.'>'.$vv.'</div>';
				}
				$html[$template_key]['html'] = $options;
			}
			elseif(stristr($template_key, '{checkbox}')){
				$template_key = str_ireplace('{checkbox}', '', $template_key);
				$pretty_key = str_ireplace('{checkbox}', '', $pretty_key);
				$html[$template_key]['title'] = ucwords($pretty_key);

				$check_for_vars = (isset($data['info'][$template_key])) ? explode('||', $data['info'][$template_key]) : [];

				foreach($value as $kk => $vv){
					$checked = (in_array($vv, $check_for_vars)) ? 'checked="checked"' : '';
					$options .= '<div class="checkbox"><input type="checkbox" name="'.$template_key.'[]" value="'.$vv.'" '.$checked.'>'.$vv.'</div>';
				}
				$html[$template_key]['html'] = $options;
			}
			elseif(stristr($template_key, '{togglebox}')){
				$template_key = str_ireplace('{togglebox}', '', $template_key);
				$pretty_key = str_ireplace('{togglebox}', '', $pretty_key);
				$html[$template_key]['title'] = ucwords($pretty_key);

				$options = 'data-checked="'.$value['checked'].'" data-unchecked="'.$value['unchecked'].'"';
				$checked = (isset($data['info'][$template_key]) && $data['info'][$template_key] == 'yes') ? 'checked="checked"' : '';
				
				$html[$template_key]['html'] = '<div class="checkbox"><input type="checkbox" name="'.$template_key.'" value="yes" '.$options.' '.$checked.' class="togglebox">Yes</div>';
			}	
		}
		elseif($value == '{gallery}'){
			$html[$template_key] = galleryDisplay($data, $template_key, $pretty_key);
		}
		elseif(stristr($value, '{file}')){

			$html[$template_key]['title'] = ucwords($pretty_key);
			$html[$template_key]['html'] = (isset($data['info'][$template_key]) && $data['info'][$template_key] != '') ? '
				<div class="FileInput"><input type="file" name="'.$template_key.'"></div>
				<div class="FileRemove">
					<input type="checkbox" name="Remove{}'.$template_key.'" value="yes">Delete File
					<input type="hidden" name="Current{}'.$template_key.'" value="'.$data['info'][$template_key].'">
				</div>
				<div class="FileCurrent">
					Current File: <b>'.$data['info'][$template_key].'</b>
				</div>
				' : '<div class="FileInput"><input type="file" name="'.$template_key.'"></div>';
		}
		elseif(strstr($value, '{text}')){
			$text_value = (isset($data['info'][$template_key]) && $data['info'][$template_key] != '') ? htmlspecialchars($data['info'][$template_key]) : '';

			$html[$template_key]['title'] = ucwords($pretty_key);
			$html[$template_key]['html'] = '<input type="text" name="'.$template_key.'" value="'.$text_value.'" class="text">';
		}
		elseif(strstr($value, '{textarea}')){
			$display_textarea = (isset($data['info'][$template_key])) ? $data['info'][$template_key] : '';
			$html[$template_key]['title'] = ucwords($pretty_key);
			$html[$template_key]['html'] = '<textarea name="'.$template_key.'">'. $display_textarea .'</textarea>';
		}
		elseif(strstr($value, '{number}')){
			$text_value = (isset($data['info'][$template_key])) ? str_replace('"', '&quot;', $data['info'][$template_key]) : '';
			$html[$template_key]['title'] = ucwords($pretty_key);
			$html[$template_key]['html'] = '<input type="text" name="'.$template_key.'" value="'.$text_value.'" class="text number">';
		}
		elseif(stristr($template_key, '{details}')){
			$html[$template_key]['html'] = '<div class="Details">'.$value.'</div>';
		}
		elseif(strstr($value, '{categories}')){
			$html[$template_key]['title'] = ucwords($pretty_key);
			$html[$template_key]['html'] = '<div class="CategoryList">';

			if($data['relation'] != '')
				$compare = preg_split('/\\)\\(|\\(|\\)/', $data['relation'], -1, PREG_SPLIT_NO_EMPTY);
			else
				$compare = array();
			if($categories){
				$html[$template_key]['html'] .= iterate_categories($categories, $compare);
			}

			$html[$template_key]['html'] .= '</div>';
		}
	}

	if($meta === true){
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
	}

	$string = '';
	foreach($html as $k => $v){
		$string .= (isset($v['title']) && $v['title'] != '') ? '<div class="Label">'.$v['title'].'</div>' : '';
		$string .= '<div class="FieldInput LabelInsert">'.$v['html'].'</div>';
	}

	return $string;
}

function displayProducts($get_products, $isHREF = true){
	global $mysqli, $templates;

	$image_path = array_search('{gallery}', $templates['Product']);

	$product_list = [];

	$count = $get_products->num_rows;

	$productDisplay = '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

	if($count > 0){

		//$except = array("rar", "zip", "mp3", "mp4", "mp3", "mov", "flv", "wmv", "swf", "png", "gif", "jpg", "bmp", "avi");
		// $except = array("png", "gif", "jpg", "bmp", "jpeg");
		// $imp = implode('|', $except);

		$product_list = []; // .= '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

		while($product = $get_products->fetch_assoc()){
			$product_list[$product['id']] = $product;

			$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.txt';

			if(!file_exists($file_path)){
				$product_list[$product['id']]['image'] = '';
				continue;
			}

			$gallery_array = unserialize(file_get_contents($file_path));

			$item = (is_array($gallery_array)) ? current($gallery_array) : [];

			$product_list[$product['id']]['image'] = (isset($item['Image']) && $item['Image'] != '') ? '<img src="/products/'.$product['id'].'/thumb-'.$item['Image'].'">' : '';
		}
	}

	if(!empty($product_list)){
		foreach($product_list as $product){
			$productDisplay .= '
			<li class="SortItem" id="itemID_'.$product['id'].'">
				<div class="Product" data-product="'.$product['id'].'">
				';
					$productDisplay .= ($isHREF === true) ? '<a href="/admin/manage.php?id='.$product['id'].'">' : '';
						$productDisplay .= '
						<h4>Product ID: '.$product['id'].'</h4>

						<div class="img">
							'.$product['image'].'
						</div>
						
						<h3>'.$product['name'].'</h3>
						
						<div class="Delete" data-id="'.$product['id'].'" title="Delete this product" alt="Delete this product"><img src="images/cross.gif"></div>
						';
					$productDisplay .= ($isHREF === true) ? '</a>' : '';
				$productDisplay.= '					
				</div>
			</li>
			';
		}
	}
	else{
		$productDisplay = '<div class="SubTitle" style="text-align: center;">0 Results</div>';
	}

	return $productDisplay;
}

function savePage($id, $postData, $fileData){
	global $templates, $mysqli, $database;

	$data = getData($id);

	$template = (stristr($data['type'], 'page')) ?  $templates[$data['info']['template']] : $templates[$data['type']];

	switch ($data['type']) {
		case 'Product':
			$file_path = (isset($data['info']['template'])) ? $_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/'.$data['info']['template'].'.txt' : '';
			$dir_path = '/products/'.$data['id'];
			break;
		
		default:	#pages

			$file_path = $_SERVER['DOCUMENT_ROOT'].'/gallery/'.$data['info']['template'].'/images.txt';
			$dir_path = (isset($Gallery[$data['info']['template']])) ? '/gallery/'.$data['info']['template'] : '/images';
			break;
	}

	$files_parsed = handleFiles($fileData, $template, $dir_path);	#Upload files, resize images, return data

	foreach($postData as $pk => $pv){
		if(stristr($pk, 'Current{}') || stristr($pk, 'Remove{}')) continue;
		$key = preg_replace('/{.*?}/', '', $pk);
		$data['info'][$key] = $pv;
	}

	foreach ($fileData as $key => $value) {

		if($fileData[$key]['name'] == '' && isset($postData['Current{}'.$key])){
			$data['info'][$key] = $postData['Current{}'.$key];
		}
		if($fileData[$key]['name'] == '' && isset($postData['Remove{}'.$key])){
			$del_file = $_SERVER['DOCUMENT_ROOT'].'/images/'.$postData['Current{}'.$key];
			if(file_exists($del_file)){
				unlink($del_file);
			}
			$data['info'][$key] = '';
		}
		if($fileData[$key]['name'] != ''){

			if(isset($postData['Current{}'.$key])){
				$del_file = $_SERVER['DOCUMENT_ROOT'].'/images/'.$postData['Current{}'.$key];
				if(file_exists($del_file)){
					unlink($del_file);
				}			
			}

			$data['info'][$key] = $files_parsed[$key];
		}
	}

	$string = '';
	foreach ($data['info'] as $key => $value) {
		if(is_array($value)){
			$string .= '{{}}'.$key.'(())'.implode('||', $value);
		}
		else {
			$string .= '{{}}'.$key.'(())'.$value;
		}
	}

	$string = str_replace("'", "\'", $string);

	$mysqli->query("UPDATE $database SET info='$string' WHERE id='$id'");

	if(!isset($error)){
		$_SESSION['post_response'] = '<div class="Message"><img src="images/tick.gif"> '.$data['name'].' updated.</div>';
	}
	else{
		foreach($error_code as $ev){
			$error_msg .= '<br>'.$ev;
		}
		$_SESSION['post_response'] = '<div class="Message"><img src="images/cross.gif"> There was an error updating '.$data['name'].'.'.$error_msg.'</div>';
	}
}

function saveProduct($id, $postData){
	global $mysqli, $database;

	$info = $relation = '';

	foreach($postData as $key => $value){
		if($key == 'Name'){
			$product_name = ''.$value;
		}
		if($key == 'category'){
			foreach($value as $k => $v){
				$relation .= '('.$v.')';
			}
			if(isset($category))
				$relation .= $category;
		}
		elseif(is_array($value)){
			$info .= '{{}}'.$key.'(())'.implode('||', $value);
		}
		elseif($key == 'id' || $key == 'type' || $key == '' || $value == ''){
			continue;
		}
		else {
			$info .= '{{}}'.$key.'(())'.$value;
		}			
	}

	$info = $mysqli->real_escape_string(preg_replace('/^{{}}/', '', $info));
	$query = "UPDATE $database SET name='$product_name', relation='$relation', info='$info' WHERE id='$id'";
	$mysqli->query($query);	
}

?>