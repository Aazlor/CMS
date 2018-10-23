<?

	$info = $relation = '';

	$product_id = $_POST['product_id'];

	foreach($_POST as $key => $value){
		if($key == 'category'){
			foreach($value as $k => $v){
				$relation .= '('.$v.')';
			}
			if(isset($category))
				$relation .= $category;
		}
		elseif($key == 'Name'){
			$product_name = ''.$value;
		}
		elseif(is_array($value)){
			$info .= '{{}}'.$key.'(())'.implode('||', $value);
		}
		elseif($key == 'product_id' || $key == 'submit_product' || $key == '' || $value == ''){
			continue;
		}
		else {
			$info .= '{{}}'.$key.'(())'.$value;
		}			
	}
	
	$info = preg_replace('/^{{}}/', '', $info);
	
	$mysqli->query("UPDATE $database SET name='$product_name', relation='$relation', info='$info' WHERE id='$product_id'");

?>