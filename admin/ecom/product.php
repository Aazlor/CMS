<?

function iterate_categories($fetchCats, $compare){

	global $database, $mysqli;

	while($cat = $fetchCats->fetch_array()){
		if(in_array($cat['id'], $compare))
			$check = 'checked="checked"';
		else
			$check = '';

		$checkSubCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='$cat[0]' ORDER BY sort, name ASC");
		$count = $checkSubCats->num_rows;
		if($count > 0){
			echo '<li data-id="'.$cat['id'].'"><input type="checkbox" name="category[]" value="'.$cat['id'].'" '.$check.'>  '.$cat['name'].'<ul class="Cat">';
			iterate_categories($checkSubCats, $compare);
			echo '</ul></li>';
		}
		else{
			echo '<li data-id="'.$cat['id'].'"><input type="checkbox" name="category[]" value="'.$cat['id'].'" '.$check.'>  '.$cat['name'].'</li>';			
		}
	}
}

if(!isset($_REQUEST['product_id'])){
	$check = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY id DESC LIMIT 1");
	if($check->num_rows > 0){
		$check = $check->fetch_array();
		if($check['name'] == NULL && $check['relation'] == NULL && $check['info'] == NULL){
			$product_id = $check['id'];
		}
		else{
			if($mysqli->query("INSERT INTO $database (type) VALUES ('Product')")){
				$product_id = $mysqli->query("SELECT * FROM $database ORDER BY id DESC LIMIT 1");
				$product_id = $product_id->fetch_array();
				$product_id = $product_id['id'];
			}
			else{
				die('ERROR!');
			}
		}
	}
	elseif($mysqli->query("INSERT INTO $database (type) VALUES ('Product')")){
		$product_id = $mysqli->query("SELECT * FROM $database ORDER BY id DESC LIMIT 1");
		$product_id = $product_id->fetch_array();
		$product_id = $product_id['id'];
	}
	else{
		die('ERROR!');
	}

	$dir = $_SERVER['DOCUMENT_ROOT'].'/products/'.$product_id;
	if( is_dir($dir) === false ){
		mkdir($dir, 0777);
	}

	if(in_array('{gallery}', $vars, true)){
		foreach($vars as $key => $value){
			if(!is_array($value)){
				if(preg_match('/{gallery}/', $value)){
					$filename = $dir.'/'.$key.'.php';
					if(!file_exists($filename)){
						$file = fopen($filename,"w");
						fwrite($file, '');
						fclose($file);
					}				
				}
			}
		}
	}

}
else{
	$product_id = $_REQUEST['product_id'];
	$dir = $_SERVER['DOCUMENT_ROOT'].'/products/'.$product_id;
	if( is_dir($dir) === false ){
		if(!mkdir($dir, 0777))
			echo 'ERROR';
	}
}


$product = $mysqli->query("SELECT * FROM $database WHERE id='$product_id'");
$product = $product->fetch_array();

$product_vars = [];

if($product['info'] != ''){
	$product_vars = explode('{{}}', $product['info']);
	foreach($product_vars as $value){
		$sort = explode('(())', $value);
		$product_vars[$sort[0]] = $sort[1];
	}
}

if($product['name'] != '')
	$title = 'Product Manage - '.$product['name'];
elseif($product['name'] == '' && empty($product_vars))
	$title = 'Product Creation - Add New';
else
	$title = 'Product Creation - Continue';

?>

<div class="Title"><?= $title ?></div>

<?
if(in_array('{gallery}', $vars, true)){
	foreach($vars as $key => $value){
		echo '<div class="Wrapper-'.$key.'">';
			if(!is_array($value)){
				if(preg_match('/{gallery}/', $value)){
					$filename = $dir.'/'.$key.'.php';
					if(!file_exists($filename)){
						$file = fopen($filename,"w");
						fwrite($file, '');
						fclose($file);
					}	
					include('ecom/gallery-display.php');
				}
			}
		echo '</div>';
	}
}

echo'
<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'?section=products">
';
foreach($vars as $key => $value){
	$display_key = preg_replace('/_/', ' ', $key);

	if(is_array($value)){
		if(preg_match('/{select}/i', $key)){
			$key = preg_replace('/{select}/i', '', $key);
			$options = '';
			foreach($value as $kk => $vv){
				if($product_vars[$key] == $vv){
					$options .= '<option selected="selected">'.$vv.'</option>';					
				}
				else{
					$options .= '<option>'.$vv.'</option>';
				}
			}
			echo '
			<div class="Wrapper-'.$key.'">
				<div class="Label">'.ucwords($display_key).'</div>
				<div class="FieldInput LabelInsert">
					<select name='.$key.'>
						<option value=""></option>
						'.$options.'
					</select>
				</div>
				<div class="Clear"></div>
			</div>';
		}
		elseif(preg_match('/{radio}/i', $key)){
			$options = '';
			$key = preg_replace('/{radio}/i', '', $key);
			foreach($value as $kk => $vv){
				if($product_vars[$key] == $vv){
					$options .= '<div class="radio"><input type="radio" name="'.$key.'" value="'.$vv.'" checked="checked">'.$vv.'</div>';
				}
				else{
					$options .= '<div class="radio"><input type="radio" name="'.$key.'" value="'.$vv.'">'.$vv.'</div>';
				}
			}
			echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert">'.$options.'</div><div class="Clear"></div></div>';
		}
		elseif(preg_match('/{checkbox}/i', $key)){
			$options = '';
			$key = preg_replace('/{checkbox}/i', '', $key);
			$display_key = preg_replace('/{checkbox}/i', '', $display_key);

			if(isset($product_vars[$key]))
				$check_for_vars = explode('||', $product_vars[$key]);
			else
				$check_for_vars = [];

			foreach($value as $kk => $vv){
				if(in_array($vv, $check_for_vars)){
					$options .= '<div class="checkbox"><input type="checkbox" name="'.$key.'[]" value="'.$vv.'" checked="checked">'.$vv.'</div>';
				}
				else{
					$options .= '<div class="checkbox"><input type="checkbox" name="'.$key.'[]" value="'.$vv.'">'.$vv.'</div>';				
				}
			}
			echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="FieldInput Field">'.$options.'</div><div class="Clear"></div></div>';
		}
		elseif(preg_match('/{togglebox}/i', $key)){
			$options = '';
			$key = preg_replace('/{togglebox}/i', '', $key);
			$display_key = preg_replace('/{togglebox}/i', '', $display_key);

			$options = 'data-checked="'.$value['checked'].'" data-unchecked="'.$value['unchecked'].'"';
			$checked = (isset($product_vars[$key]) && $product_vars[$key] == 'yes') ? 'checked="checked"' : '';
			
			echo '
				<div class="Wrapper-'.$key.'">
					<div class="Label">'.ucwords($display_key).'</div>
					<div class="FieldInput Field">
						<div class="checkbox"><input type="checkbox" name="'.$key.'" value="yes" '.$options.' '.$checked.' class="togglebox">Yes</div>
					</div>
					<div class="Clear"></div>
				</div>';
		}	
	}
	// elseif($value == '{gallery}'){
	// 	$filename = $dir.'/'.$key.'.php';
	// 	if(!file_exists($filename)){
	// 		$file = fopen($filename,"w");
	// 		fwrite($file, '');
	// 		fclose($file);
	// 	}	
	// 	include('ecom/gallery-display.php');		
	// }
	elseif(strstr($key, '{image}')){
		$key = preg_replace('/{image}/', '', $key);
		if($product_vars[$key] == ''){
			echo'
			<div class="Wrapper-'.$key.'">
				<div class="Label">'.ucwords($display_key).'</div>
				<div class="FieldInput LabelInsert">
					<input type="file" name='.$key.'>
				</div>
			</div>
			';
		}
		else{
			echo '
			<div class="Wrapper-'.$key.'">
				<div class="Label">'.ucwords($display_key).'</div>
				<div class="FieldInput LabelInsert">
					
					<div class="InlineBlock">
						<img src="/images/'.$product_vars[$key].'">
					</div>
					<div class="InlineBlock">
						<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current Image
						<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
						&nbsp;Change Image To: <input type="file" name='.$key.'>
						<input type="hidden" name="Current{}'.$key.'" value="'.$product_vars[$key].'">
					</div>
				</div>
				<div class="Clear"></div>
			</div>
			';
		}
	}
	elseif(preg_match('/{file}/', $value)){
		echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="FieldInput LabelInsert"><input type="file" name='.$key.'></div><div class="FieldInput LabelInsert"><input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete File<input type="hidden" name="Current{}'.$key.'" value="'.$product_vars[$key].'"><br><br>Current File: <b>'.$product_vars[$key].'</b></div><div class="Clear"></div></div>';

		if($split[$i] == ''){
			echo'
			<div class="Wrapper-'.$key.'">
				<div class="Label">'.ucwords($display_key).'</div>
				<div class="FieldInput LabelInsert">
					<input type="file" name='.$key.'>
				</div>
				<div class="Clear"></div>
			</div>
			';
		}
		else{
			echo '
			<div class="Wrapper-'.$key.'">
				<div class="Label">'.ucwords($display_key).'</div>
				<div class="FieldInput LabelInsert">
					
					<div class="InlineBlock">
						Current File: <a href="/images/'.$product_vars[$key].'" target="_blank">http://'.$_SERVER['SERVER_NAME'].'/images/'.$product_vars[$key].'</a>
					</div>
					<div class="InlineBlock File">
						<input type="checkbox" name="Remove{}'.$key.'" value="yes">Delete Current File
						<div style="width: 200px; border-bottom: 1px dotted #ccc; margin: 10px;""></div>
						&nbsp;Change File To: <input type="file" name='.$key.'>
						<input type="hidden" name="Current{}'.$key.'" value="'.$product_vars[$key].'">
					</div>
				</div>
				<div class="Clear"></div>
			</div>
			';
		}
	}
	elseif(strstr($value, '{text}')){
		if($key == 'Name')
			$text_value = $product['name'];
		elseif(isset($product_vars[$key]))
			$text_value = preg_replace('/"/', '&quot;', $product_vars[$key]);
		echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$text_value.'" class="text"></div></div>';
	}
	elseif(strstr($value, '{textarea}')){
		$display_textarea = (isset($product_vars[$key])) ? $product_vars[$key] : '';
		echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><textarea name="'.$key.'">'. $display_textarea .'</textarea></div></div>';
	}
	elseif(strstr($value, '{number}')){
		$text_value = (isset($product_vars[$key])) ? preg_replace('/"/', '&quot;', $product_vars[$key]) : '';
		echo '<div class="Wrapper-'.$key.'"><div class="Label">'.ucwords($display_key).'</div><div class="LabelInsert"><input type="text" name="'.$key.'" value="'.$text_value.'" class="text number"></div></div>';
	}
	elseif(stristr($key, '{details}')){
		echo '<div class="Wrapper-'.$key.'"><div class="Details">'.$value.'</div></div>';
	}
	elseif(strstr($value, '{categories}')){
		$topCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='Primary' ORDER BY sort, name ASC");
		echo '<div class="Wrapper-'.$key.'">
			<div class="Label">'.ucwords($display_key).'</div><div class="FieldInput Field Cats">';
		if($product['relation'] != '')
			$compare = preg_split('/\\)\\(|\\(|\\)/', $product['relation'], -1, PREG_SPLIT_NO_EMPTY);
		else
			$compare = array();
		if($topCats){
			iterate_categories($topCats, $compare);
		}
		echo '</div>
		</div>';
	}
}

$meta_title = (isset($product_vars['meta_title'])) ? $product_vars['meta_title'] : '';
$meta_description = (isset($product_vars['meta_description'])) ? $product_vars['meta_description'] : '';
$meta_keywords = (isset($product_vars['meta_keywords'])) ? $product_vars['meta_keywords'] : '';

echo'
	<div class="Label">
		Meta Title
	</div>
	<div class="LabelInsert">
		<input type="text" name="meta_title" value="'.$meta_title.'" onkeyup="MaxLength(this,60)" class="metatext">
		<div class="sub">Title your page. Maximum 60 characters.</div>
	</div>
	<div class="Label">
		Meta Description
	</div>
	<div class="LabelInsert">
		<input type="text" name="meta_description" value="'.$meta_description.'" onkeyup="MaxLength(this,150)" class="metatext">
		<div class="sub">Write a short description about this page. Maximum 150 characters.</div>
	</div>
	<div class="Label">
		Meta Keywords
	</div>
	<div class="LabelInsert">
		<input type="text" name="meta_keywords" value="'.$meta_keywords.'" onkeyup="MaxLength(this,250)" class="metatext">
		<div class="sub">Example: enter,words,about,your,website,separated,by,commas. Maximum 250 characters.</div>
	</div>
	
	<div class="Submit">
		<input type="hidden" name="product_id" value="'.$product_id.'"/>
		<input type="hidden" name="submit_product" value="yes"/>
		<input type="submit" value="Save Product" />
	</div>
</form>
';


?>