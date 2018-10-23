<?php

include('header.php');
include('config.php');

$tab_title = 'Manage Products';
foreach($tabs as $value){
	unset($active);
	if($_GET['x'] == $value){
		$active = ' class="active"';
		$tab_title .= ' - '.ucwords($value);
	}
	$get_tabs .= '<li'.$active.'><a href="/admin/manage_products.php?x='.$value.'">'.ucwords($value).' Product</a></li>';
}
echo'


<div class="Title">'.$tab_title.'</div>

<div id="Tabs">
	'.$get_tabs.'
	<div class="Clear"></div>
</div>
';

if($_POST['submit_add'] == "yes"){
	
	foreach($_POST as $key => $value){
		if(preg_match('/submit_add/', $key) || preg_match('/Submit/', $key)){
		}
		elseif(preg_match('/category_/', $key)){
			$category = preg_replace('/category_/', '', $key);
			$category = '|'.$category.'|';
			$relation .= $category;
		}
		elseif(preg_match('/name/i', $key)){
			$product_name = $value;
		}
		else{
			if(preg_match('/^\+/', $key)){
				$groupings[$key] = $value;
			}
			else {
				$content .= '{{}}'.$key.'(())'.$value;
			}			
		}
	}
	
	$content = preg_replace('/^{{}}/', '', $content);
	
	/***** START IMAGE RESIZER *****/
	
	foreach($_FILES as $file_key=>$file_value){
		if($_FILES[$file_key]['name'] != ''){
			$imagename = $_FILES[$file_key]['name'];
			if(preg_match('/.jpeg/i', $imagename)){
				$imagetype = '.jpeg';
			}
			elseif(preg_match('/.jpg/i', $imagename)){
				$imagetype = '.jpg';
			}
			elseif(preg_match('/.gif/i', $imagename)){
				$imagetype = '.gif';
			}
			else{
				$error = 1;
				echo '____FAIL____';
				break 1;
			}
			
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../products/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			foreach($image_sizes as $image_size_key => $image_size_value){
				
				if($image_size_key != "medium"){
					$size_var = $image_size_key.'-';
				}
				else{
					$size_var = '';
				}
				
				
				/*****	BEGIN IMAGE RESIZE *****/
				
				$save = "../products/" . $size_var . $imagepath; //This is the new file you saving
				$file = "../products/" . $imagepath; //This is the original file
				
				list($width, $height) = getimagesize($file) ;
				
				$modwidth = $image_size_value['width'];
				$modheight = $image_size_value['height'];
				
				if(preg_match('/min_/', $modwidth)){
					$minwidth = preg_replace('/min_/', '', $modwidth);
										
					$diff = $width / $minwidth;
					
					$modwidth = $minwidth;
					if(preg_match('/min\_/', $modheight)){
						$minheight = preg_replace('/min_/', '', $modheight);
						$modheight = $height / $diff;
						
						if($minheight > $modheight){
							$diff = $modheight / $minheight;	
							$modwidth = $modwidth / $diff;
							$modheight = $minheight;					
						}
					}
					else{
						$modheight = $height / $diff;
					}
				}
								
				if($modwidth != '' && $modheight != ''){
					$modwidth = $modwidth;
					$modheight = $modheight;
				}
				elseif($modwidth == '' && $modheight != ''){
					$diff = $height / $modheight;	
					$modwidth = $width / $diff;
					$modheight = $modheight;
				}
				elseif($modwidth != '' && $modheight == ''){
					$diff = $width / $modwidth;	
					$modwidth = $modwidth;
					$modheight = $height / $diff;
				}
				else{
					$modheight = $height;
					$modwidth = $width;
				}
				
				$tn = imagecreatetruecolor($modwidth, $modheight) ;
				if($imagetype == '.jpg' || $imagetype == '.jpeg'){
					$image = imagecreatefromjpeg($file);
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
					imagejpeg($tn, $save, 100);
				}
				elseif($imagetype == '.gif'){
					$image = imagecreatefromgif($file);
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
					imagegif($tn, $save, 100);
				}
			}
			
			if(preg_match('/^\+/', $file_key)){
				$groupings[$file_key] = $_FILES[$file_key]['name'];
			}
			else{
				$record_images[$file_key] = $_FILES[$file_key]['name'];
			}
		}
	}
	foreach($record_images as $key => $value){
		$image_list .= '{{}}'.$key.'(())'.$value;
	}
	foreach($groupings as $key => $value){
		$groups .= '{{}}'.$key.'(())'.$value;
	}
	
	$content .= $image_list.$groups;
	
	$mysqli->query("INSERT INTO $database (type, name, relation, info) VALUES ('Product', '$product_name', '$relation', '$content')");
	/***** END IMAGE RESIZER *****/
	
	echo '
	<div class="Title">Created Product - '.$product_name.'</div>
	';
	
	$show_content = preg_split('/{{}}/', $content);
	
	foreach($show_content as $value){
		$show = preg_split('/\(\(\)\)/', $value);
		
		if(preg_match('/^\+[0-9]/', $show[0])){
			$grouping_key = preg_replace('/_.*$/', '', $show[0]);
			$grouping_key = $grouping_key.'_';
			$grouping_name = str_replace($grouping_key, '', $show[0]);
			if(preg_match('/image/i', $show[0])){
				$get_groupings[$grouping_key] .= '
					<div class="Label">'.$grouping_name.'</div>
					<div class="LabelInsert">
						<img src="../products/small-'.$show[1].'">
					</div>
					<div class="Clear"></div>
				';
			}
			else{
				$get_groupings[$grouping_key] .= '
					<div class="Label">'.$grouping_name.'</div>
					<div class="LabelInsert">
						'.$show[1].'
					</div>
					<div class="Clear"></div>
				';
			}
		}
		elseif(preg_match('/image/i', $show[0])){
			$display_key = preg_replace('/_/', ' ', $show[0]);
			echo '
					<div class="Label">'.$display_key.'</div>
					<div class="LabelInsert">
						<img src="../products/small-'.$show[1].'">
					</div>
					<div class="Clear"></div>
			';
		}
		else{
			$display_key = preg_replace('/_/', ' ', $show[0]);
			echo '
				<div class="Label">'.$display_key.'</div>
				<div class="LabelInsert">
					'.$show[1].'
				</div>
				<div class="Clear"></div>
			';
		}
	}
	
	foreach($get_groupings as $key => $value){
		echo'
			<div class="Field">
			'.$value.'
			</div>
		';
	}
}
if($_POST['submit_edit'] == "yes"){
	
	$id = $_POST['product'];
	$product_get = $mysqli->query("SELECT * FROM $database WHERE id='$id'");
	$product_get = mysql_fetch_row($product_get);
	
	$product_info = preg_split('/{{}}/', $product_get[4]);
	$product = array();
	foreach($product_info as $key=>$value){
		$split = preg_split('/\(\(\)\)/', $value);
		$product[$split[0]] = $split[1];
	}
	foreach($_POST as $key => $value){
		if(preg_match('/submit_edit/', $key) || preg_match('/Submit/', $key) || preg_match('/product/', $key) || $key == '' || $value == ''){
		}
		elseif(preg_match('/category_/', $key)){
			$category = preg_replace('/category_/', '', $key);
			$category = '|'.$category.'|';
			$relation .= $category;
		}
		elseif(preg_match('/name/i', $key)){
			$product_name = $value;
		}
		elseif(preg_match('/remove_/i', $key)){
			$remove[] = $value;
		}
		elseif(preg_match('/^\+/', $key)){
			$groupings[$key] = $value;
			unset($content[$key]);
		}
		elseif(preg_match('/image/i', $key)){
			if($_FILES[$key]['name'] == ''){
				$content[$key] = $product[$key];
			}
		}
		else {
			$content[$key] = $value;
		}			
	}
	
	/***** START IMAGE RESIZER *****/
	
	foreach($_FILES as $file_key=>$file_value){
		if(in_array($file_key, $remove)){
			
		}
		elseif($_FILES[$file_key]['name'] != ''){
			if($content[$file_key] != ''){
				unlink("../products/$content[$file_key]");
				unlink("../products/small-$content[$file_key]");
				unlink("../products/large-$content[$file_key]");
				unset($content[$file_key]);
			}
			$imagename = $_FILES[$file_key]['name'];
			if(preg_match('/.jpeg/i', $imagename)){
				$imagetype = '.jpeg';
			}
			elseif(preg_match('/.jpg/i', $imagename)){
				$imagetype = '.jpg';
			}
			elseif(preg_match('/.gif/i', $imagename)){
				$imagetype = '.gif';
			}
			else{
				$error = 1;
				echo '____FAIL____';
				break 1;
			}
			
			$source = $_FILES[$file_key]['tmp_name'];
			$target = "../products/".$imagename;
			move_uploaded_file($source, $target);
			
			$imagepath = $imagename;
			
			foreach($image_sizes as $image_size_key => $image_size_value){
				
				if($image_size_key != "medium"){
					$size_var = $image_size_key.'-';
				}
				else{
					$size_var = '';
				}
				
				
				/*****	BEGIN IMAGE RESIZE *****/
				
				$save = "../products/" . $size_var . $imagepath; //This is the new file you saving
				$file = "../products/" . $imagepath; //This is the original file
				
				list($width, $height) = getimagesize($file) ;
				
				$modwidth = $image_size_value['width'];
				$modheight = $image_size_value['height'];
				
				if(preg_match('/min_/', $modwidth)){
					$minwidth = preg_replace('/min_/', '', $modwidth);
										
					$diff = $width / $minwidth;
					
					$modwidth = $minwidth;
					if(preg_match('/min\_/', $modheight)){
						$minheight = preg_replace('/min_/', '', $modheight);
						$modheight = $height / $diff;
						
						if($minheight > $modheight){
							$diff = $modheight / $minheight;	
							$modwidth = $modwidth / $diff;
							$modheight = $minheight;					
						}
					}
					else{
						$modheight = $height / $diff;
					}
				}
								
				if($modwidth != '' && $modheight != ''){
					$modwidth = $modwidth;
					$modheight = $modheight;
				}
				elseif($modwidth == '' && $modheight != ''){
					$diff = $height / $modheight;	
					$modwidth = $width / $diff;
					$modheight = $modheight;
				}
				elseif($modwidth != '' && $modheight == ''){
					$diff = $width / $modwidth;	
					$modwidth = $modwidth;
					$modheight = $height / $diff;
				}
				else{
					$modheight = $height;
					$modwidth = $width;
				}
				
				$tn = imagecreatetruecolor($modwidth, $modheight) ;
				if($imagetype == '.jpg' || $imagetype == '.jpeg'){
					$image = imagecreatefromjpeg($file);
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height);
					imagejpeg($tn, $save, 100);
				}
				elseif($imagetype == '.gif'){
					$image = imagecreatefromgif($file);
					imagecopyresampled($tn, $image, 0, 0, 0, 0, $modwidth, $modheight, $width, $height) ;
					imagegif($tn, $save, 100);
				}
			}
			
			if(preg_match('/^\+/', $file_key)){
				$groupings[$file_key] = $_FILES[$file_key]['name'];
			}
			else{
				$record_images[$file_key] = $_FILES[$file_key]['name'];
			}
		}
	}
	/***** END IMAGE RESIZER *****/
	foreach($record_images as $key => $value){
		$image_list .= '{{}}'.$key.'(())'.$value;
	}
	foreach($groupings as $key => $value){
		$groups .= '{{}}'.$key.'(())'.$value;
	}
	
	foreach($remove as $value){
		if($content[$value] != ''){
			unlink("../products/$content[$value]");
			unlink("../products/small-$content[$value]");
			unlink("../products/large-$content[$value]");
			unset($content[$value]);
		}
	}
	
	foreach($content as $key=>$value){
		if($key != '' && $value != ''){
			$update_content .= '{{}}'.$key.'(())'.$value;
		}
	}
	$update_content .= $image_list.$groups;
	$update_content = preg_replace('/^{{}}/', '', $update_content);
	
	$mysqli->query("UPDATE $database SET name='$product_name', relation='$relation', info='$update_content' WHERE id='$id'");
	
	
	echo '
	<div class="Label">Updated Product - '.$product_name.'</div>
	
	<div class="Label">Product</div>
	<div class="LabelInsert">'.$product_name.'</div>
	<div class="Clear"></div>
	';
	
	$show_content = preg_split('/{{}}/', $update_content);
	
	foreach($show_content as $value){
		$show = preg_split('/\(\(\)\)/', $value);
		
		if(preg_match('/^\+[0-9]/', $show[0])){
			$grouping_key = preg_replace('/_.*$/', '', $show[0]);
			$grouping_key = $grouping_key.'_';
			$grouping_name = str_replace($grouping_key, '', $show[0]);
			if(preg_match('/image/i', $show[0])){
				$get_groupings[$grouping_key] .= '
					<div class="Label">'.$grouping_name.'</div>
					<div class="LabelInsert">
						<img src="../products/small-'.$show[1].'">
					</div>
					<div class="Clear"></div>
				';
			}
			else{
				$get_groupings[$grouping_key] .= '
					<div class="Label">'.$grouping_name.'</div>
					<div class="LabelInsert">
						'.$show[1].'
					</div>
					<div class="Clear"></div>
				';
			}
		}
		elseif(preg_match('/image/i', $show[0])){
			$display_key = preg_replace('/_/', ' ', $show[0]);
			echo '
				<div class="Label">'.$display_key.'</div>
				<div class="LabelInsert">
					<img src="../products/small-'.$show[1].'">
				</div>
				<div class="Clear"></div>
			';
		}
		else{
			$display_key = preg_replace('/_/', ' ', $show[0]);
			echo '
				<div class="Label">'.$display_key.'</div>
				<div class="LabelInsert">
					'.$show[1].'
				</div>
				<div class="Clear"></div>
			';
		}
	}
	
	if(isset($get_groupings)){
		foreach($get_groupings as $key => $value){
			echo'
				<div class="Field">
				'.$value.'
				</div>
			';
		}
	}
}
if($_POST['submit_remove'] == "yes"){	
	$id = $_POST['product'];
	if($id != ''){
		$product_get = $mysqli->query("SELECT * FROM $database WHERE id='$id'");
		$product_get = mysql_fetch_row($product_get);
		
		$product_info = preg_split('/{{}}/', $product_get[4]);
		$product = array();
		foreach($product_info as $key=>$value){
			$split = preg_split('/\(\(\)\)/', $value);
			$product[$split[0]] = $split[1];
			if(preg_match('/image/i', $split[0])){
				unlink("../products/$split[1]");
				unlink("../products/small-$split[1]");
				unlink("../products/large-$split[1]");
			}
		}
		
		$mysqli->query("DELETE FROM $database WHERE id='$id'");
		
		echo '<div class="Removed">'.$product_get[2].' Removed Successfully</div>';
	}
}

if($_GET['x'] == "add"){
	if($_POST['step'] == 2){
		
		echo '<div class="Label">Create Product</div>
		<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
		';
		
		for($count = 0; $count < $fields_array_number; $count++){
					
			foreach($fields[$count] as $key => $value){
				unset($size);
				
				if(preg_match('/{[1-9]}/', $value) && !preg_match('/\|\+\|/', $value)){
					$value = preg_replace('/^{[1-9]}$/', '', $value);
					$size = preg_replace('/^.*{/', '', $value);
					$size = preg_replace('/}$/', '', $size);
					$size_width = $size*7;
					$size = 'maxlength="'.$size.'" style="width: '.$size_width.'px;"';
				}

				if(preg_match('/\|\+\|/', $value)){
					unset($grouping_content);

					$display_key = preg_replace('/_/', ' ', $key);
					echo '
					<div class="Field">
						<div class="Label">'.$display_key.'</div>
						<div class="InputField">
					';
					
					$groupings = preg_replace('/^\|\+\|/', '', $value);
					$groupings = preg_split('/{\+}/', $groupings);
					
					foreach($groupings as $v2){
						if($v2 != ''){
							unset($size);
							if(preg_match('/{[1-9]}/', $value)){
								$value = preg_replace('/^{[1-9]}$/', '', $value);
								$size = preg_replace('/^.*{/', '', $value);
								$size = preg_replace('/}$/', '', $size);
								$size_width = $size*7;
								$size = 'maxlength="'.$size.'" style="width: '.$size_width.'px;"';
							}
							
							if(preg_match('/{text}/', $v2)){
								$name = preg_replace('/{text}/', '', $v2);
								$input = '<input type=\"text\" name=\"+"+num+"_'.$name.'\" '.$size.'>';
							}
							elseif(preg_match('/{image}/', $v2)){
								$name = preg_replace('/{image}/', '', $v2);
								$input = '<input type=\"file\" name=\"+"+num+"_'.$name.'\" '.$size.'>';
							}
							elseif(preg_match('/{checkbox}/', $v2)){
								$name = preg_replace('/{checkbox}/', '', $v2);
								$input = '<input type=\"checkbox\" name=\"+"+num+"_'.$name.'\" style="width: 14px;">';
							}
							$grouping_content .= '<div class=\"Label\">'.ucwords($name).'</div><div class=\"LabelInsert\">'.$input.'</div><div class=\"Clear\"></div>';
						}
					}
					?>
					<script type="text/javascript">
					function addGrouping() {
						var ni = document.getElementById('groupings');
						var numi = document.getElementById('theValue');
						var num = (document.getElementById("theValue").value -1)+ 2;
						numi.value = num;
						var divIdName = "my"+num+"Div";
						var newdiv = document.createElement('div');
						newdiv.setAttribute("id",divIdName);
						newdiv.setAttribute("class","Options");
						newdiv.innerHTML = "<div style=\"float: left;\"><a href=\"javascript:;\" onclick=\"removeSubtype(\'"+divIdName+"\');\">x</a></div><div class=\"Option\"><?php echo $grouping_content; ?><div class=\"Clear\"></div></div>";
						
						ni.appendChild(newdiv);
					}
					
					function removeSubtype(divNum) {
					  var d = document.getElementById('groupings');
					  var olddiv = document.getElementById(divNum);
					  d.removeChild(olddiv);
					}
					</script>
					<?php
						echo '
							<div class="add"><a href="javascript:;" onclick="addGrouping();">Add '.ucwords($key).'</a></div>
							<div id="groupings"> </div>
							<input type="hidden" value="0" id="theValue" />
						</div>
						<div class="Clear"></div>
					</div>
						';
				}
				elseif(preg_match('/{text}/', $value)){
					$display_key = preg_replace('/_/', ' ', $key);
					echo '
						<div class="Label">'.$display_key.'</div>
						<div class="LabelInsert">
							<input type="text" class="text" name="'.$key.'" value="" '.$size.'>
						</div>
						<div class="Clear"></div>
					';
				}
				elseif(preg_match('/{textarea}/', $value)){
					$display_key = preg_replace('/_/', ' ', $key);
					echo '
						<div class="Label">'.$display_key.'</div>
						<div class="LabelInsert">
							<textarea name="'.$key.'"></textarea>
						</div>
						<div class="Clear"></div>
					';
				}
				elseif(preg_match('/{checkbox}/', $value)){
					$display_key = preg_replace('/_/', ' ', $key);
					$Label = preg_replace('/{checkbox}/', '', $value);
					echo '
						<div class="Label">'.$display_key.'</div>
						<div class="LabelInsert">
							<input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;">
						</div>
						<div class="Clear"></div>
					';
				}
				elseif(preg_match('/{image}/', $value)){
					$display_key = preg_replace('/_/', ' ', $key);
					$Label = preg_replace('/{image}/', '', $value);
					echo '
						<div class="Label">'.$display_key.'</div>
						<div class="LabelInsert">
							<input type="image" name="'.$key.'" value=""></textarea>
						</div>
						<div class="Clear"></div>
					';
				}
			}
		}
		
		$a=0;
		while($a < $image_per_product){
			$a++;
			echo '
				<div class="Label">Image</div>
				<div class="LabelInsert">
					<input type="file" name="image_'.$a.'" value=""></textarea>
				</div>
				<div class="Clear"></div>
			';
		}
		
		foreach($_POST as $key => $value){
			if(preg_match('/category/', $key)){
				$cats .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
			}
		}
		
		echo '
			<div class="Submit">		
				'.$cats.'
				<input type="hidden" name="submit_add" value="yes">
				<input type="Submit" name="Submit" value="Create Product">
			</div>
		</form>
		';
	}
	else{
		$check = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
		if(mysql_fetch_row($check)){
			foreach($category_names as $absolute_category){
				$html .= '
					<div class="Label">Select '.$absolute_category.'</div><div class="AbsoluteCategory">
				';
			
				$get_category = 'Absolute_'.$absolute_category;
				
				$a=0;
				$get_parents = $mysqli->query("SELECT * FROM $database WHERE relation='$get_category' AND type='category'");
				while($got_parent = mysql_fetch_row($get_parents)){
					$html .= '
						<div class="cat_primary">
							<div class="CatPrimary">
								<input type="checkbox" name="category_'.$got_parent[0].'" value="'.$got_parent[0].'"> 
								'.$got_parent[2].' 
							</div>
					';
					$get_children = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$got_parent[0]|%' && type='category'");
					while($got_child = mysql_fetch_row($get_children)){
						$html .= '
						<div class="cat_sub2">
							<input type="checkbox" name="category_'.$got_child[0].'" value="'.$got_child[0].'"> 
							'.$got_child[2].'
						</div>
						';
					}
					$a++;
					if($a > 3){
						$html .= '</div>';
						$a=0;
					}
					else{
						$html .= '</div>';
					}
				}
				if($a == 0){
					$html .= '<div class="Clear"></div></div>';				
				}
				else{
					$html .= '<div class="Clear"></div></div></div>';
				}
			}
			
			echo '
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=add">
				'.$html.'
				<div class="Submit">
					<input type="hidden" name="step" value="2"/>
					<input type="submit" value="Continue" />
				</div>
			</form>';
		}
		else{
			echo '
			<div style="margin: 20px auto;">
				<div class="title" style="text-align: center;">
				No categories have been added.
				</div>
				
				<div style="margin-top: 15px; text-align: center;">
				<a href="manage_categories.php?x=add">Create a category</a>
				</div>
			</div>
			';
		}
	}
}

if($_GET['x'] == "edit"){
	if($_POST['product'] != ''){
		
		$id = $_POST['product'];
		$product = $mysqli->query("SELECT * FROM $database WHERE id='$id'");
		$product = mysql_fetch_row($product);
		
		$contents = preg_split('/{{}}/', $product[4]);
		foreach($contents as $value){
			$sub_content = preg_split('/\(\(\)\)/', $value);
			$content[$sub_content[0]] = $sub_content[1];
			
			if(preg_match('/^\+[0-9]/', $sub_content[0])){
				$product_groupings[$sub_content[0]] = $sub_content[1];
			}
		}
		
		$a=0;
		$category_list = array();
		$categories = preg_split('/\|\|/', $product[3]);
		foreach($categories as $value){
			$category = str_replace('|', '', $value);
			$category_list[$a] = $category;
			$a++;
		}

		if($_POST['step'] == 2){
			
			echo '<div class="Label">Edit Product</div>
			<form method="POST" enctype="multipart/form-data" action="'.$_SERVER["PHP_SELF"].'">
			';
			
			for($count = 0; $count < $fields_array_number; $count++){
						
				foreach($fields[$count] as $key => $value){
					unset($size);
					$array_key = str_replace(' ', '_', $key);
					
					if(preg_match('/{[1-9]}/', $value) && !preg_match('/\|\+\|/', $value)){
						$value = preg_replace('/^{[1-9]}$/', '', $value);
						$size = preg_replace('/^.*{/', '', $value);
						$size = preg_replace('/}$/', '', $size);
						$size_width = $size*7;
						$size = 'maxlength="'.$size.'" style="width: '.$size_width.'px;"';
					}
	
					if(preg_match('/\|\+\|/', $value)){
						unset($grouping_content);
						$display_key = preg_replace('/_/', ' ', $key);
	
						echo '
						<div class="Field">
							<div class="Label">'.$display_key.'</div>
							<div class="InputField">
						';
						
						$groupings = preg_replace('/^\|\+\|/', '', $value);
						$groupings = preg_split('/{\+}/', $groupings);
						foreach($groupings as $v2){
							if($v2 != ''){
								unset($size);
								if(preg_match('/{[1-9]}/', $value)){
									$value = preg_replace('/^{[1-9]}$/', '', $value);
									$size = preg_replace('/^.*{/', '', $value);
									$size = preg_replace('/}$/', '', $size);
									$size_width = $size*7;
									$size = 'maxlength="'.$size.'" style="width: '.$size_width.'px;"';
								}
								
								if(preg_match('/{text}/', $v2)){
									$name = preg_replace('/{text}/', '', $v2);
									$input = '<input type=\"text\" name=\"+"+num+"_'.$name.'\" '.$size.'>';
								}
								elseif(preg_match('/{image}/', $v2)){
									$name = preg_replace('/{image}/', '', $v2);
									$input = '<input type=\"file\" name=\"+"+num+"_'.$name.'\" '.$size.'>';
								}
								elseif(preg_match('/{checkbox}/', $v2)){
									$name = preg_replace('/{checkbox}/', '', $v2);
									$input = '<input type=\"checkbox\" name=\"+"+num+"_'.$name.'\" style="width: 14px;">';
								}
								$grouping_content .= '<div class=\"Label\">'.ucwords($name).'</div><div class=\"LabelInsert\">'.$input.'</div><div class=\"Clear\"></div>';
							}
						}
						?>
						<script type="text/javascript">
						function addGrouping() {
							var ni = document.getElementById('groupings');
							var numi = document.getElementById('theValue');
							var num = (document.getElementById("theValue").value -1)+ 2;
							numi.value = num;
							var divIdName = "my"+num+"Div";
							var newdiv = document.createElement('div');
							newdiv.setAttribute("id",divIdName);
							newdiv.setAttribute("class","Options");
							newdiv.innerHTML = "<div style=\"float: left;\"><a href=\"javascript:;\" onclick=\"removeSubtype(\'"+divIdName+"\');\">x</a></div><div class=\"Option\"><?php echo $grouping_content; ?><div class=\"Clear\"></div></div>";
							
							ni.appendChild(newdiv);
						}
						
						function removeSubtype(divNum) {
						  var d = document.getElementById('groupings');
						  var olddiv = document.getElementById(divNum);
						  d.removeChild(olddiv);
						}
						</script>
						<?php
							if(isset($product_groupings)){
								foreach($product_groupings as $gkey => $gvalue){
									$grouping_key = preg_replace('/_.*$/', '', $gkey);
									$grouping_key = $grouping_key.'_';
									$grouping_name = str_replace($grouping_key, '', $gkey);
									if(preg_match('/image/i', $gkey)){
										$get_groupings[$grouping_key] .= '
											<div class="LabelInsert">
												<img src="../products/small-'.$gvalue.'">
												<input type="hidden" name="'.$gkey.'" value="'.$gvalue.'">
											</div>
										';
									}
									else{
										$get_groupings[$grouping_key] .= '
											<div class="Label">
												'.$gvalue.'
												<input type="hidden" name="'.$gkey.'" value="'.$gvalue.'">
											</div>
										';
									}
								}
							}
						
							echo '
								<div class="add"><a href="javascript:;" onclick="addGrouping();">Add '.ucwords($key).'</a></div>
								<div id="groupings">';
								
								if(isset($get_groupings)){
									$a=0;
									foreach($get_groupings as $key=>$value){
										$a++;
										$value = preg_replace('/\+[0-9]*_/', '+'.$a.'_', $value);
										
										
										echo'
											<div class="Options" id="my'.$a.'Div">
												<div style="float: left;"><a href="javascript:;" onclick="removeSubtype(\'my'.$a.'Div\');">x</a></div>
												<div class="Option">
												'.$value.'
												</div>
												<div class="Clear"></div>
											</div>
										';
										
									}
								}
								
							echo' </div>
								<input type="hidden" value="'.$a.'" id="theValue" />
							</div>
							<div class="Clear"></div>
						</div>
							';
					}
					elseif(preg_match('/{text}/', $value)){
						if(preg_match('/^name$/i', $key)){
							$display_key = preg_replace('/_/', ' ', $key);
							echo '
								<div class="Label">'.$display_key.'</div>
								<div class="LabelInsert">
									<input type="text" class="text" name="'.$key.'" value="'.$product[2].'" '.$size.'>
								</div>
								<div class="Clear"></div>
							';
						}
						else{
							$display_key = preg_replace('/_/', ' ', $key);
							echo '
								<div class="Label">'.$display_key.'</div>
								<div class="LabelInsert">
									<input type="text" class="text" name="'.$key.'" value="'.$content[$array_key].'" '.$size.'>
								</div>
								<div class="Clear"></div>
							';
						}
					}
					elseif(preg_match('/{textarea}/', $value)){
						$display_key = preg_replace('/_/', ' ', $key);
						echo '
							<div class="Label">'.$key.'</div>
							<div class="LabelInsert">
								<textarea name="'.$key.'">'.$content[$array_key].'</textarea>
							</div>
							<div class="Clear"></div>
						';
					}
					elseif(preg_match('/{checkbox}/', $value)){
						$Label = preg_replace('/{checkbox}/', '', $value);
						$display_key = preg_replace('/_/', ' ', $key);
						echo '
							<div class="Label">'.$display_key.'</div>
							<div class="LabelInsert">
								<input type="checkbox" name="'.$key.'" value="yes" style="width: 14px;">
							</div>
							<div class="Clear"></div>
						';
					}
					elseif(preg_match('/{image}/', $value)){
						$Label = preg_replace('/{image}/', '', $value);
						$display_key = preg_replace('/_/', ' ', $key);
						echo '
							<div class="Label">'.$display_key.'</div>
							<div class="LabelInsert">
								<input type="file" name="'.$key.'" value="">
								<br>
								<input type="checkbox" name="remove_'.$key.'" value="'.$key.'" style="width: 14px;"> Remove Image
							</div>
							<div class="LabelInsert">
								<img src="../products/'.$content[$array_key].'" width="150">
							</div>
							<div class="Clear"></div>
						';
					}
				}
			}
			
			$a=0;
			while($a < $image_per_product){
				$a++;
				$get_image = 'image_'.$a;
				echo '
					<div class="Label">Image</div>
					<div class="LabelInsert">
						<input type="hidden" name="'.$get_image.'" value="'.$content[$get_image].'">
						<input type="file" name="'.$get_image.'" value="">
					</div>
					<div class="Remove">
						<input type="checkbox" name="remove_'.$get_image.'" value="'.$get_image.'" style="width: 14px;"> Remove Image
					</div>
					<div class="LabelInsert">
						<img src="../products/'.$content[$get_image].'" width="150">
					</div>
					<div class="Clear"></div>
				';
			}
			
			foreach($_POST as $key => $value){
				if(preg_match('/category/', $key)){
					$cats .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
			}
			
			echo '
				<div class="Submit">		
					'.$cats.'
					<input type="hidden" name="submit_edit" value="yes">
					<input type="hidden" name="product" value="'.$id.'">
					<input type="Submit" name="Submit" value="Update Product">
				</div>
			</form>
			';
		}
		else{
			$check = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
			if(mysql_fetch_row($check)){
				foreach($category_names as $absolute_category){
					$html .= '
						<div class="Label">Select '.$absolute_category.'</div><div class="AbsoluteCategory">
					';
					
					$get_category = 'Absolute_'.$absolute_category;
					
					$a=0;
					$get_parents = $mysqli->query("SELECT * FROM $database WHERE relation='$get_category' && type='category'");
					while($got_parent = mysql_fetch_row($get_parents)){
						if(in_array($got_parent[0], $category_list)){
							$checked = 'checked="checked"';
						}
						else{
							$checked = '';
						}
						$html .= '
						<div class="cat_primary">
							<div class="CatPrimary">
								<input type="checkbox" name="category_'.$got_parent[0].'" value="'.$got_parent[0].'" '.$checked.'> 
								'.$got_parent[2].' 
							</div>
						';
						$get_children = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$got_parent[0]|%' && type='category'");
						while($got_child = mysql_fetch_row($get_children)){
							if(in_array($got_child[0], $category_list)){
								$checked = 'checked="checked"';
							}
							else{
								$checked = '';
							}
							$html .= '
							<div class="cat_sub2">
								<input type="checkbox" name="category_'.$got_child[0].'" value="'.$got_child[0].'" '.$checked.'> 
								'.$got_child[2].'
							</div>
							';
						}
						$a++;
						if($a > 3){
							$html .= '</div>';
							$a=0;
						}
						else{
							$html .= '</div>';
						}
					}
					if($a == 0){
						$html .= '<div class="Clear"></div></div>';				
					}
					else{
						$html .= '<div class="Clear"></div></div></div>';
					}
				}
				
				echo '
				<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit">
					'.$html.'
					<div class="Submit">
						<input type="hidden" name="step" value="2"/>
						<input type="hidden" name="product" value="'.$id.'"/>
						<input type="submit" value="Continue" />
					</div>
				</form>';
			}
		}		
	}
	else{
		if($_POST['search_edit'] == 'yes'){
			$search_parameter = $_POST['query'];
			if($search_parameter == ''){
				$search_query = "SELECT * FROM $database WHERE type='Product' ORDER BY name ASC";
			}
			else{
				$search_query = "SELECT * FROM $database WHERE type='Product' && name LIKE '%$search_parameter%' OR info LIKE '%$search_parameter%' ORDER BY name ASC";
			}
			$products = $mysqli->query($search_query);
		}
		else{
			$products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY name ASC");
		}
		
		$check = $mysqli->query("SELECT * FROM $database WHERE type='Product' LIMIT 1");
		if($check = mysql_fetch_row($check)){
			
			echo '
			<div class="Search">
				<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit">
					<input type="text" class="text" name="query" value="'.$_POST['query'].'" class="Form">
					<input type="submit" name="Search" value="Search" class="Submit">
					<input type="hidden" name="search_edit" value="yes">
				</form>
			</div>
			<div class="Label">Select Product</div>
			';
			
			while($product = mysql_fetch_row($products)){
				unset($contents);
				$contents = preg_split('/{{}}/', $product[4]);
				foreach($contents as $value){
					unset($sub_content);
					unset($show_image);
					$sub_content = preg_split('/\(\(\)\)/', $value);
					$content[$sub_content[0]] = $sub_content[1];
					if(preg_match('/image/i', $sub_content[0])){
						$show_image = $sub_content[1];
					}
				}
				
				echo'
				<div class="EditProduct">
					<div class="Left">
						<div class="Name">'.$product[2].'</div>
';
#						<div class="Price">$'.$content['Price'].'</div>
echo'
					</div>
					<div class="Left">
						<div class="Categories">
							<div class="title">Categories</div>
				';
							$categories = preg_split('/\|\|/', $product[3]);
							foreach($categories as $value){
								$category = str_replace('|', '', $value);
								$get_cat = $mysqli->query("SELECT * FROM $database WHERE id='$category'");
								$get_cat_name = mysql_fetch_row($get_cat);
								echo '<div class="Category">'.$get_cat_name[2].'</div>';
							}
						echo'</div>
					</div>';
				
				echo '<div class="Description Left">
						<div class="title">Description</div>
						<div>'.substr($content['Description'], 0, 200).'</div>
					</div>';			
				echo '<div class="Image Right"><img src="../products/small-'.$show_image.'"></div>
					<div class="Clear"></div>
					<div class="Edit"><form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit"><input type="submit" name="Edit" value="Edit"><input type="hidden" name="product" value="'.$product[0].'"></form></div>
				</div>
				';

			}
		}
		else{
				echo '
				<div style="height: 30px; display: block;">
				No products have been added.
				</div>
				
				<div style="height: 30px; display: block;">
				<a href="manage_products.php?x=add">Create a product</a>
				</div>
				';
		}
	}
}

if($_GET['x'] == "remove"){
	if($_POST['search_edit'] == 'yes'){
		$search_parameter = $_POST['query'];
		if($search_parameter == ''){
			$search_query = "SELECT * FROM $database WHERE type='Product' ORDER BY name ASC";
		}
		else{
			$search_query = "SELECT * FROM $database WHERE type='Product' && name LIKE '%$search_parameter%' OR info LIKE '%$search_parameter%' ORDER BY name ASC";
		}
		$products = $mysqli->query($search_query);
	}
	else{
		$products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY name ASC");
	}
	
	$check = $mysqli->query("SELECT * FROM $database WHERE type='Product' LIMIT 1");
	if($check = mysql_fetch_row($check)){
		
		echo '
		<div class="Search">
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove">
				<input type="text" class="text" name="query" value="'.$_POST['query'].'" class="Form">
				<input type="submit" name="Search" value="Search" class="Submit">
				<input type="hidden" name="search_edit" value="yes">
			</form>
		</div>
		';
		
		while($product = mysql_fetch_row($products)){
			unset($content);
			$contents = preg_split('/{{}}/', $product[4]);
			foreach($contents as $value){
				unset($sub_content);
				unset($show_image);
				$sub_content = preg_split('/\(\(\)\)/', $value);
				$content[$sub_content[0]] = $sub_content[1];
				if(preg_match('/image/i', $sub_content[0])){
					$show_image = $sub_content[1];
				}
			}
			
			echo'
			<div class="EditProduct">
				<div class="Left">
					<div class="Name">'.$product[2].'</div>
					<div class="Price">$'.$content['Price'].'</div>
				</div>
				<div class="Left">
					<div class="Categories">
						<div class="title">Categories</div>
			';
						$categories = preg_split('/\|\|/', $product[3]);
						foreach($categories as $value){
							$category = str_replace('|', '', $value);
							$get_cat = $mysqli->query("SELECT * FROM $database WHERE id='$category'");
							$get_cat_name = mysql_fetch_row($get_cat);
							echo '<div class="Category">'.$get_cat_name[2].'</div>';
						}
					echo'</div>
				</div>';
			
			echo '<div class="Description Left">
					<div class="title">Description</div>
					<div>'.substr($content['Description'], 0, 200).'</div>
				</div>';			
			echo '<div class="Image"><img src="../products/small-'.$show_image.'"></div>
				<div class="Clear"></div>
				<div class="Edit"><form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove"><input type="submit" name="Remove" value="Remove"><input type="hidden" name="product" value="'.$product[0].'"><input type="hidden" name="submit_remove" value="yes"></form></div>
			</div>
			';

		}
	}
	else{
			echo '
			<div style="height: 30px; display: block;">
			No products have been added.
			</div>
			
			<div style="height: 30px; display: block;">
			<a href="manage_products.php?x=add">Create a product</a>
			</div>
			';
	}	
}

if($_GET['x'] == "view"){
	if($_POST['search_edit'] == 'yes'){
		$search_parameter = $_POST['query'];
		if($search_parameter == ''){
			$search_query = "SELECT * FROM $database WHERE type='Product' ORDER BY name ASC";
		}
		else{
			$search_query = "SELECT * FROM $database WHERE type='Product' && name LIKE '%$search_parameter%' OR info LIKE '%$search_parameter%' ORDER BY name ASC";
		}
		$products = $mysqli->query($search_query);
	}
	else{
		$products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY name ASC");
	}
	
	$check = $mysqli->query("SELECT * FROM $database WHERE type='Product' LIMIT 1");
	if($check = mysql_fetch_row($check)){
		
		echo '
		<div class="Search">
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove">
				<input type="text" class="text" name="query" value="'.$_POST['query'].'" class="Form">
				<input type="submit" name="Search" value="Search" class="Submit">
				<input type="hidden" name="search_edit" value="yes">
			</form>
		</div>
		';
		
		while($product = mysql_fetch_row($products)){
			unset($content);
			$contents = preg_split('/{{}}/', $product[4]);
			foreach($contents as $value){
				unset($sub_content);
				unset($show_image);
				$sub_content = preg_split('/\(\(\)\)/', $value);
				$content[$sub_content[0]] = $sub_content[1];
				if(preg_match('/image/i', $sub_content[0])){
					$show_image = $sub_content[1];
				}
			}
			
			echo'
			<div class="EditProduct">
				<div class="Left">
					<div class="Name">'.$product[2].'</div>
					<div class="Price">$'.$content['Price'].'</div>
				</div>
				<div class="Left">
					<div class="Categories">
						<div class="title">Categories</div>
			';
						$categories = preg_split('/\|\|/', $product[3]);
						foreach($categories as $value){
							$category = str_replace('|', '', $value);
							$get_cat = $mysqli->query("SELECT * FROM $database WHERE id='$category'");
							$get_cat_name = mysql_fetch_row($get_cat);
							echo '<div class="Category">'.$get_cat_name[2].'</div>';
						}
					echo'</div>
				</div>';
			
			echo '<div class="Description Left">
					<div class="title">Description</div>
					<div>'.substr($content['Description'], 0, 200).'</div>
				</div>';			
			echo '<div class="Image"><img src="../products/small-'.$show_image.'"></div>
				<div class="Clear"></div>
				<div class="Edit"><a href="/store/product/'.$product[2].'" target="_blank">View</a></div>
			</div>
			';

		}
	}
	else{
			echo '
			<div style="height: 30px; display: block;">
			No products have been added.
			</div>
			
			<div style="height: 30px; display: block;">
			<a href="manage_products.php?x=add">Create a product</a>
			</div>
			';
	}	
}

include('footer.php');

?>


