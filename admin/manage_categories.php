<?php

include('header.php');
include('config_categories.php');

$tab_title = 'Manage Categories';
foreach($category_tabs as $value){
	unset($active);
	if($_GET['x'] == $value){
		$active = ' class="active"';
		$tab_title .= ' - '.ucwords($value);
	}
	$get_tabs .= '<li'.$active.'><a href="/admin/manage_categories.php?x='.$value.'">'.ucwords($value).' Category</a></li>';
}
echo'


<div class="Title">'.$tab_title.'</div>

<div id="Tabs">
	'.$get_tabs.'
	<div class="Clear"></div>
</div>
';

if($_POST["submit_add"] == "yes")
{
	$cat_relation = $_POST['Absolute_Category'];
	$name = $_POST['category_name'];
	
	foreach($_POST as $key => $value){
		if(preg_match("/parent/", $key)){
			$relation .= '|'.$value.'|';
		}
	}
	
	if($relation == ''){
		$relation = $_POST['Absolute_Category'];		
	}
			
	$mysqli->query("INSERT INTO $database (type, name, relation) VALUES ('category', '$name', '$relation')");
	
	echo '
	<table align="center" id="admin_content_inner">
		<tr>
			<td valign="top" align="center" valign="top">
			<div class="title" style="padding-bottom: 10px;">You\'ve Created the Category:</div>
			<div class="Modified">
			'.$name.'
			</div>
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td align="center">
			<a href="?x=add">Add another Category</a>
			</td>
		</tr>
	</table>';

}

if($_POST["submit_edit"] == "yes")
{
	$cat_relation = $_POST['Absolute_Category'];
	$name = $_POST['category_name'];
	$id = $_POST['id'];
	
	foreach($_POST as $key => $value){
		if(preg_match("/parent/", $key)){
			$relation .= '|'.$value.'|';
		}
	}
	
	if($relation == ''){
		$relation = $_POST['Absolute_Category'];		
	}
			
	$mysqli->query("UPDATE $database SET name='$name', relation='$relation' WHERE id='$id'");
	
	echo '
	<table align="center" id="admin_content_inner">
		<tr>
			<td valign="top" align="center" valign="top">
			<div class="title" style="padding-bottom: 10px;">You\'ve Updated the Category:</div>
			<div class="Modified">
			'.$name.'
			</div>
			</td>
		</tr>
		<tr><td height="15"></td></tr>
		<tr>
			<td align="center">
			<a href="?x=edit">Edit another Category</a>
			</td>
		</tr>
	</table>';
}

if($_POST["Remove"] == "Delete")
{
	$cat_relation = $_POST['Absolute_Category'];
	$name = $_POST['name'];
	$id = $_POST['id'];

	if($_POST['Type'] == 'Absolute'){
		$update_relation = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$id|%'");
		while($get = mysql_fetch_row($update_relation)){
			$update = preg_replace('/\|$id\|', $get[4]);
			$mysqli->query("UPDATE $database SET relation='$update' WHERE id='$get[0]'");
		}
	}
	$mysqli->query("DELETE FROM $database WHERE id='$id'");
	
	echo '
	<table align="center" id="admin_content_inner">
		<tr>
			<td valign="top" align="center" valign="top">
			<div class="title" style="padding-bottom: 10px;">You\'ve Deleted the Category:</div>
			<div class="Modified">
			'.$name.'
			</div>
			</td>
		</tr>
	</table>
	';
}

/************************************************************/
/////////	Start Get Categories View
if($_POST['Absolute_category'] != ''){
	$show_absolute_category = $_POST['Absolute_category'];
}
elseif($category_absolute_amount <= 1){
	$show_absolute_category = 'Absolute_'.$category_names[0];
}
if($show_absolute_category != ''){
	$cat_relation = $show_absolute_category;
	
	if(!preg_match("/^Absolute_/", $cat_relation)){
		$cat_relation = 'Absolute_'.$cat_relation;
	}
	
	$all = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$cat_relation' LIMIT 1");
	if($all_test = mysql_fetch_row($all)){
		$all = $mysqli->query("SELECT * FROM $database WHERE relation='$cat_relation' ORDER BY name ASC");
		
		while($primary_cat = mysql_fetch_row($all)){
			$key = $primary_cat[2].'{{}}'.$primary_cat[0];
			$pri_cat_list[$key] = '';
			$sub_cat_list = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$primary_cat[0]|%' AND type='category' ORDER BY name ASC");
			while($sub_cat_get = mysql_fetch_row($sub_cat_list)){
				$pri_cat_list[$key][] = $sub_cat_get[2];
			}
		}
		
		$a=1;
		foreach($pri_cat_list as $k=>$v){
			$k = preg_replace('/{{}}[1-9].*/', '', $k);
			if($a == 1){
				$primary_cats .= '<tr>';
			}
				$primary_cats .= '
					<td class="cat_primary" valign="top" align="left">'.$k.'
				';
				foreach($v as $k2=>$v2){
					$primary_cats .= '
					<br><span class="cat_sub2">'.$v2.'</span>
					';
				}
			if($a < 6){
				$primary_cats .= '</td><td width="10"></td>';
			}
			elseif($a == 6){
				$primary_cats .= '</td></tr>';
				$a=0;
			}
			$a++;
		}
		if($a != 1){
			$primary_cats .= '</tr>';
		}
	}
}
/////////	End Get Categories View
/************************************************************/

if(!isset($_GET['x'])){
	$all = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
	if($all_test = mysql_fetch_row($all)){					
		echo '
		<table align="center" width="100%">
			<tr>
				<td valign="top" align="left" valign="top" colspan="10">
				<div class="Label">Current Categories</div>
				</td>
			</tr>
			'.$primary_cats.'
		</table>';
	}
	else{
		echo '<tr><td><span class="title">No categories have been added.</span></td></tr></table>';
	}	
}
if($_GET['x'] == 'add'){
	if($category_absolute_amount <=1 || $_POST['Absolute_Category'] != ''){
		
		if($mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1")){
			if($_POST['Absolute_Category'] != ''){
				$relation = 'Absolute_'.$_POST['Absolute_Category'];
			}
			else{
				$relation = 'Absolute_'.$category_names[0];
			}
			
			$get_primary_cats = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$relation'");
			while($get = mysql_fetch_row($get_primary_cats)){
				$primary_categories .= '<div style="display: block; float: left; width: 135px;"><input type="checkbox" name="parent_'.$get[0].'" value="'.$get[0].'"> '.$get[2].'</div>';
			}
		}
		
		echo'
		<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'">
			<div class="Label">Category Name</div>
			<div class="LabelInsert"><input name="category_name" value="" class="text"></div>
		';
		if($primary_categories != ''){
			echo'
			<div class="Label">Select Parent Categories</div>
			<div class="LabelInsert">'.$primary_categories.'</div>
			';
		}
		echo'						
			<div style="height: 15px; display: block;"></div>
			
			<div class="Submit">		
				<input type="hidden" name="Absolute_Category" value="'.$relation.'"/>
				<input type="hidden" name="submit_add" value="yes"/>
				<input type="submit" value="Add Category" />
			</div>
			
		</form>
		';
	
		$all = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
		if($all_test = mysql_fetch_row($all)){					
			echo '
			<table align="center" width="100%">
				<tr>
					<td valign="top" align="left" valign="top" colspan="10">
					<div class="Label">Current Categories</div>
					</td>
				</tr>
				'.$primary_cats.'
			</table>';
		}
		else{
			echo '<tr><td><span class="title">No categories have been added.</span></td></tr></table>';
		}
	}
	else{
		echo'
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=add">
				<table align="center" id="admin_content_inner" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" align="left">
						<div class="title" style="padding-bottom: 10px;">Select Category Type</div>
		';
						
		foreach($category_names as $value){
			echo '<input type="submit" name="Absolute_Category" value="'.$value.'" style="width: 200px;"><div style="height: 15px; display: block;"></div>';
		}
		
		echo'						
						</td>
					</tr>
					<tr><td style="padding-top: 10px; padding-bottom: 10px;" colspan="9"></td></tr>
			</form>
		';
	}
}

if($_GET['x'] == 'edit'){
	if($category_absolute_amount <=1 || $_POST['Absolute_Category'] != ''){
		if($_POST['category'] == ''){
		
			if($mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1")){
				if($_POST['Absolute_Category'] != ''){
					$relation = 'Absolute_'.$_POST['Absolute_Category'];
				}
				else{
					$relation = 'Absolute_'.$category_names[0];
				}
				
				$get_primary_cats = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$relation'");
				while($get = mysql_fetch_row($get_primary_cats)){
					$primary_categories .= '<div style="display: block; float: left; width: 75px;"><input type="checkbox" name="parent_'.$get[0].'" value="'.$get[0].'"> '.$get[2].'</div>';
				}
			}
			$all = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
			if($all_test = mysql_fetch_row($all)){
				unset($primary_cats);		
				$cat_relation = $relation;
				
				if(!preg_match("/^Absolute_/", $cat_relation)){
					$cat_relation = 'Absolute_'.$cat_relation;
				}
				
				$all = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$cat_relation' LIMIT 1");
				if($all_test = mysql_fetch_row($all)){
					$all = $mysqli->query("SELECT * FROM $database WHERE relation='$cat_relation' ORDER BY name ASC");
					
					$a=1;
					while($primary_cat = mysql_fetch_row($all)){
						$a++;
						$key = $primary_cat[2].'{{}}'.$primary_cat[0];
						$parent_list .= 'OR \'%|'.$primary_cat[0].'|%\' ';			
						$pri_cat_list[$key] = '';
						$sub_cat_list = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$primary_cat[0]|%' AND type='category' ORDER BY name ASC");
						while($sub_cat_get = mysql_fetch_row($sub_cat_list)){
							$pri_cat_list[$key][] = $sub_cat_get[2].'{{}}'.$sub_cat_get[0];
						}
					}
					
					$query = "SELECT * FROM $database WHERE relation LIKE '' $parent_list";
					
					$get_other = $mysqli->query($query);
					while($sub_cat_get = mysql_fetch_row($get_other)){
						$pri_cat_list['Other'][] = $sub_cat_get[2].'{{}}'.$sub_cat_get[0];
					}
					
					$a=1;
					foreach($pri_cat_list as $k=>$v){
						$k = preg_split('/{{}}/', $k);
						if($a == 1){
							$primary_cats .= '<tr>';
						}
							$primary_cats .= '
								<td class="cat_primary" valign="top" align="left">
									<div class="CatPrimary">
										'.$k[0].'
										<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit">
											<input type="submit" name="Edit" value="Edit">
											<input type="hidden" name="category" value="'.$k[1].'">
											<input type="hidden" name="Absolute_Category" value="'.$cat_relation.'">
										</form>
									</div>
							';
							foreach($v as $k2=>$v2){
								$v2 = preg_split('/{{}}/', $v2);
								$primary_cats .= '
								<div class="cat_sub2">
									'.$v2[0].'
									<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit">
										<input type="submit" name="Edit" value="Edit">
										<input type="hidden" name="category" value="'.$v2[1].'">
										<input type="hidden" name="Absolute_Category" value="'.$cat_relation.'">
									</form>
								</div>
								';
							}
						if($a < 3){
							$primary_cats .= '</td><td width="10"></td>';
						}
						elseif($a == 3){
							$primary_cats .= '</td></tr>';
							$a=0;
						}
						$a++;
					}
					if($a != 1){
						$primary_cats .= '</tr>';
					}
				}
				
				echo'
							
								<table align="center" width="100%" class="ShowCategories">
									<tr>
										<td valign="top" align="left" valign="top" colspan="10">
										<div class="Label">Current Categories - Edit</div>
										</td>
									</tr>
									'.$primary_cats.'
								</table>
				';
			}
			else{
				echo '<tr><td><span class="title">No categories have been added.</span></td></tr></table>';
			}
		}
		else{
			$id = $_POST['category'];
			$edit = $mysqli->query("SELECT * FROM $database WHERE id='$id' LIMIT 1");
			$edit = mysql_fetch_row($edit);
			
			$parent_cats = preg_split('/\|\|/', $edit[3]);
			foreach($parent_cats as $value){
				$parent_cats[$key] = preg_replace('/\|/', '', $value);
			}
							
			$relation = $_POST['Absolute_Category'];				
			$get_primary_cats = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$relation'");
			while($get = mysql_fetch_row($get_primary_cats)){
				if(in_array($get[0], $parent_cats)){
					$checked = 'checked="checked"';
				}
				else {
					$checked = '';
				}
				$primary_categories .= '<div style="display: block; float: left; width: 135px;"><input type="checkbox" name="parent_'.$get[0].'" value="'.$get[0].'" '.$checked.'> '.$get[2].'</div>';
			}
			
			echo'
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'">
				<div class="Label">Category Name</div>
				<div class="LabelInsert"><input name="category_name" value="'.$edit[2].'" class="text"></div>
				
				<div class="Label">Select Parent Categories</div>
				<div class="LabelInsert">'.$primary_categories.'</div>
				</div>
							
				<div class="Submit">
					<input type="hidden" name="Absolute_Category" value="'.$relation.'"/>
					<input type="hidden" name="id" value="'.$id.'"/>
					<input type="hidden" name="submit_edit" value="yes"/>
					<input type="submit" value="Edit Category" />
				</div>
			</form>
			';
		}
	}
	else{
		echo'
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=edit">
				<table align="center" id="admin_content_inner" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" align="left">
						<div class="title" style="padding-bottom: 10px;">Select Category Type</div>
		';
						
		foreach($category_names as $value){
			echo '<input type="submit" name="Absolute_Category" value="'.$value.'" style="width: 200px;"><div style="height: 15px; display: block;"></div>';
		}
		
		echo'						
						</td>
					</tr>
					<tr><td style="padding-top: 10px; padding-bottom: 10px;" colspan="9"></td></tr>
			</form>
		';
	}
}

if($_GET['x'] == 'remove'){
	if($category_absolute_amount <=1 || $_POST['Absolute_Category'] != ''){
		if($mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1")){
			if($_POST['Absolute_Category'] != ''){
				$relation = 'Absolute_'.$_POST['Absolute_Category'];
			}
			else{
				$relation = 'Absolute_'.$category_names[0];
			}
			
			$get_primary_cats = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$relation'");
			while($get = mysql_fetch_row($get_primary_cats)){
				$primary_categories .= '<div style="display: block; float: left; width: 75px;"><input type="checkbox" name="parent_'.$get[0].'" value="'.$get[0].'"> '.$get[2].'</div>';
			}
		}
		$all = $mysqli->query("SELECT * FROM $database WHERE type='category' LIMIT 1");
		if($all_test = mysql_fetch_row($all)){
			unset($primary_cats);		
			$cat_relation = $relation;
			
			if(!preg_match("/^Absolute_/", $cat_relation)){
				$cat_relation = 'Absolute_'.$cat_relation;
			}
			
			$all = $mysqli->query("SELECT * FROM $database WHERE type='category' AND relation='$cat_relation' LIMIT 1");
			if($all_test = mysql_fetch_row($all)){
				$all = $mysqli->query("SELECT * FROM $database WHERE relation='$cat_relation' ORDER BY name ASC");
				
				$a=1;
				while($primary_cat = mysql_fetch_row($all)){
					$a++;
					$key = $primary_cat[2].'{{}}'.$primary_cat[0];
					$pri_cat_list[$key] = '';
					$sub_cat_list = $mysqli->query("SELECT * FROM $database WHERE relation LIKE '%|$primary_cat[0]|%' AND type='category' ORDER BY name ASC");
					while($sub_cat_get = mysql_fetch_row($sub_cat_list)){
						$pri_cat_list[$key][] = $sub_cat_get[2].'{{}}'.$sub_cat_get[0];
					}
				}				
				
				$query = "SELECT * FROM $database WHERE relation LIKE '' $parent_list";
				
				$get_other = $mysqli->query($query);
				while($sub_cat_get = mysql_fetch_row($get_other)){
					$pri_cat_list['Other'][] = $sub_cat_get[2].'{{}}'.$sub_cat_get[0];
				}
					
				$a=1;
				foreach($pri_cat_list as $k=>$v){
					$k = preg_split('/{{}}/', $k);
					if($a == 1){
						$primary_cats .= '<tr>';
					}
						$primary_cats .= '
							<td class="cat_primary" valign="top" align="left">
								<div class="CatPrimary">
									'.$k[0].'
									<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'">
										<input type="submit" name="Remove" value="Delete" onclick="return confirm(\'Are you sure you want to delete '.$k[0].'?\')">
										<input type="hidden" name="id" value="'.$k[1].'">
										<input type="hidden" name="name" value="'.$k[0].'">
										<input type="hidden" name="Type" value="Absolute">
										<input type="hidden" name="Absolute_Category" value="'.$cat_relation.'">
									</form>
								</div>
						';
						foreach($v as $k2=>$v2){
							$v2 = preg_split('/{{}}/', $v2);
							$primary_cats .= '
							<div class="cat_sub2">
								'.$v2[0].'
								<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'">
									<input type="submit" name="Remove" value="Delete" onClick="return confirm(\'Are you sure you want to delete '.$k[0].'?\')">
									<input type="hidden" name="id" value="'.$v2[1].'">
									<input type="hidden" name="name" value="'.$v2[0].'">
									<input type="hidden" name="Absolute_Category" value="'.$cat_relation.'">
								</form>
							</div>
							';
						}
					if($a < 3){
						$primary_cats .= '</td><td width="10"></td>';
					}
					elseif($a == 3){
						$primary_cats .= '</td></tr>';
						$a=0;
					}
					$a++;
				}
				if($a != 1){
					$primary_cats .= '</tr>';
				}
			}
			
			echo'				
					<table align="center" width="100%" class="ShowCategories">
						<tr>
							<td valign="top" align="left" valign="top" colspan="10">
							<div class="Label">Current Categories - Remove</div>
							</td>
						</tr>
						'.$primary_cats.'
					</table>
								
				</td>
			';
		}
		else{
			echo '<div class="title">No categories have been added.</div>';
		}
	}
	else{
		echo'
			<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER["PHP_SELF"].'?x=remove">
				<table align="center" id="admin_content_inner" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top" align="left">
						<div class="title" style="padding-bottom: 10px;">Select Category Type</div>
		';
						
		foreach($category_names as $value){
			echo '<input type="submit" name="Absolute_Category" value="'.$value.'" style="width: 200px;"><div style="height: 15px; display: block;"></div>';
		}
		
		echo'						
						</td>
					</tr>
					<tr><td style="padding-top: 10px; padding-bottom: 10px;" colspan="9"></td></tr>
			</form>
		';
	}
}

if($_GET['x'] == 'sort'){

	if($_POST['order'] == 'yes'){
		echo '<div class="Message"><img src="images/tick.gif"> Categories have been sorted.</div>';
	}
	
	
	$all = $mysqli->query("SELECT * FROM $database WHERE type='category' && relation='Absolute_Product' ORDER BY extra ASC");
	if(mysql_num_rows($all) > 0){
		$a=1;
		$listitems .= '<li id="listItem_0" style="display: none;"></li>';
		while($get = mysql_fetch_row($all)){
			$listitems .= '<li id="listItem_'.$get[0].'"><img src="images/arrow.png" alt="move" width="16" height="16" class="handle" />'.$get[2].'</li>';
			$listorder .= '<div class="LabelInsert">'.$get[2].'</div>';
		}
		echo'
			<script type="text/javascript" src="images/jquery-1.3.2.min.js"></script>
			<script type="text/javascript" src="images/jquery-ui-1.7.1.custom.min.js"></script>
			<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />
			<script type="text/javascript">
			  // When the document is ready set up our sortable with it\'s inherant function(s)
			  $(document).ready(function() {
			    $("#test-list").sortable({
			      handle : \'.handle\',
			      update : function () {
					  var order = $(\'#test-list\').sortable(\'serialize\');
			  		$("#info").load("calls/sort-generated-pages.php?"+order);
			      }
			    });
			});
			</script>
			<div class="Label">Sort Categories</div>

			<table align="left">
				<tr>
					<td valign="top" align="left" valign="top">
					';/*
					<pre>
					<div id="info">Waiting for update</div>
					</pre>
					*/echo'
					<ul id="test-list">
					  '.$listitems.'
					</ul>
					</td>
				</tr>
				<tr><td height="10"></td></tr>
				<tr>
					<td align="left">
						<div class="Submit" style="position: relative; text-align: left; left: 48px;">
					<form method="POST" enctype="multipart/form-data" name="image_upload_form" action="'.$_SERVER['PHP_SELF'].'?p='.$_GET[p].'&x=sort">
					<input type="hidden" name="order" value="yes"/>
					<input type="submit" value="Save Order" style="padding: 2px 5px;"/>
					</form>
						</div>
				</tr>
			</table>
		';
	}
	else{
		echo '<span class="title">There are no categories to list.';
	}
}

include('footer.php');

?>