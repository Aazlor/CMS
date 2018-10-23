<?

include('../db_connect.php');
include('../config_site_info.php');

$product_list = '';

function pre($stuff) {
	echo '<pre>';
	print_r($stuff);
	echo '</pre>';
}

function iterate_categories($fetchCats){
	global $database, $mysqli;

	while($cat = $fetchCats->fetch_array()){		
		$checkSubCats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='$cat[0]' ORDER BY sort ASC");
		$count = $checkSubCats->num_rows;
		if($count > 0){
			echo '<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span><ul class="Cat">';
			iterate_categories($checkSubCats);
			echo '</ul></li>';
		}
		else{
			echo '<li data-id="'.$cat['id'].'"> <span class="CatName" data-name="'.$cat['name'].'">'.$cat['name'].'</span> <span class="CatCog"><img src="/admin/images/options.png"></span><span class="AddCat">+</span></li>';			
		}
	}
}

function prune($branches){
	global $database, $mysqli;
	while($branch = $branches->fetch_array()){
		$twig = $mysqli->query("SELECT * FROM $database WHERE relation='$branch[0]'");
		$twig_count = $twig->num_rows;
		if($twig_count > 0){
			prune($twig);
		}
		$mysqli->query("DELETE FROM $database WHERE id='$branch[0]'");
	}
}

if(isset($_POST['newCat']) && $_POST['newCat'] != ''){
	if($mysqli->query("INSERT INTO $database (type, name, relation) VALUES ('Category', '$_POST[newCat]', 'Primary')")){
		echo 'success';
	}
	else{
		echo 'fail';
	}
}
elseif(isset($_POST['newName']) && $_POST['newName'] != '' && isset($_POST['id']) && $_POST['id'] != ''){
	if($mysqli->query("UPDATE $database SET name='$_POST[newName]' WHERE id='$_POST[id]'")){
		echo $_POST['newName'];
	}
	else{
		echo 'fail';
	}
}
elseif(isset($_POST['NewCatName']) && $_POST['NewCatName'] != '' && isset($_POST['parentid']) && $_POST['parentid'] != ''){
	if($mysqli->query("INSERT INTO $database (type, name, relation) VALUES ('Category', '$_POST[NewCatName]', '$_POST[parentid]')")){
		$sub_cats = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='$_POST[parentid]' ORDER BY sort, name ASC");
		iterate_categories($sub_cats);
	}
	else{
		echo 'fail';
	}
}
elseif(isset($_POST['deleteid']) && $_POST['deleteid'] != ''){
	$start_deletion = $mysqli->query("SELECT * FROM $database WHERE id='$_POST[deleteid]'");
	prune($start_deletion);
}
elseif(isset($_POST['search_val'])){
	if($_POST['search_val'] == ''){
	$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY sort ASC");
}
elseif (is_numeric($_POST['search_val'])){
	$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' && (id LIKE '%$_POST[search_val]%') ORDER BY sort ASC");
}
else{
	$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' && (name LIKE '%$_POST[search_val]%' || info LIKE '%$_POST[search_val]%') ORDER BY sort ASC");
}

$count = $get_products->num_rows;
	if($count > 0){
		//$except = array("rar", "zip", "mp3", "mp4", "mp3", "mov", "flv", "wmv", "swf", "png", "gif", "jpg", "bmp", "avi");
		$except = array("png", "gif", "jpg", "bmp");
		$imp = implode('|', $except);

		$product_list .= '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

		while($product = $get_products->fetch_array()){
			include($_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$_POST['img_path'].'.php');
			if(!empty($gallery_array)){
				foreach($gallery_array as $value){
					$split = explode('||', $value);
					foreach($split as $v){
						if(preg_match('/^.*\.('.$imp.')$/i', $v)){
							$image = '/products/'.$product['id'].'/'.$v;
							break 2;
						}
					}
				}
			}
			else{
				$image = '';
			}

			$product_list .= '
			<li class="SortItem" id="itemID_'.$product['id'].'">
			<div class="Product" data-product_id="'.$product['id'].'">
				<h4>Product ID: '.$product['id'].'</h4>
					<div class="img" style="background-image: url(\''.$image.'\');"></div>
					<h3>'.$product['name'].'</h3>
				<div class="Delete" data-id="'.$product['id'].'"><input type="image" src="images/cross.gif" class="Delete"></div>
			</div>
			</li>
			';
		}
	}
	else{
		$product_list = '<div class="SubTitle" style="text-align: center;">Search returned 0 results</div>';
	}

	echo $product_list;

}
elseif(isset($_POST['search_cat'])){
	if($_POST['search_cat'] == ''){
		if(file_exists('sort_list_all.php'))
			$sort_array = unserialize(file_get_contents('sort_list_all.php'));
		$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY sort ASC");
	}
	else{
		if(file_exists('sort_list_'.$_POST['search_cat'].'.php'))
			$sort_array = unserialize(file_get_contents('sort_list_'.$_POST['search_cat'].'.php'));
		$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' && relation LIKE '%($_POST[search_cat])%' ORDER BY sort ASC");
	}


	$count = $get_products->num_rows;
	if($count > 0){

		//$except = array("rar", "zip", "mp3", "mp4", "mp3", "mov", "flv", "wmv", "swf", "png", "gif", "jpg", "bmp", "avi");
		$except = array("png", "gif", "jpg", "bmp");
		$imp = implode('|', $except);

		$product_list .= '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

		while($product = $get_products->fetch_array()){
			include($_SERVER['DOCUMENT_ROOT'].'/products/'.$product[id].'/'.$_POST[img_path].'.php');
			if(!empty($gallery_array)){
				foreach($gallery_array as $value){
					$split = explode('||', $value);
					foreach($split as $v){
						if(preg_match('/^.*\.('.$imp.')$/i', $v)){
							$image = '/products/'.$product[id].'/'.$v;
							break 2;
						}
					}
				}
			}
			else{
				$image = '';
			}

			$product_array[$product[id]] = '
			<li class="SortItem" id="itemID_'.$product[id].'">
			<div class="Product" data-product_id="'.$product[id].'">
					<div class="img" style="background-image: url(\''.$image.'\');"></div>
					<h3>'.$product[name].'</h3>
				<div class="Delete" data-id="'.$product[id].'"><input type="image" src="images/cross.gif" class="Delete"></div>
			</div>
			</li>
			';
		}
	}
	else{
		$product_list = '<div class="SubTitle" style="text-align: center;">Search returned 0 results</div>';
	}

	if(!empty($product_array)){
		if(!empty($sort_array)){
			foreach($sort_array as $v){
				if($product_array[$v] != ''){
					$product_list .= $product_array[$v];
					unset($product_array[$v]);
				}
			}
			foreach($product_array as $v)
				$product_list .= $v;
		}
		else{
			foreach($product_array as $v)
				$product_list .= $v;
		}
	}

	echo $product_list;

}
?>