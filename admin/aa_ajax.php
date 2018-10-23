<?
require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

$product_list = '';

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
		echo iterate_categories($sub_cats, [], true);
	}
	else{
		echo 'fail';
	}
}
elseif(isset($_POST['deleteid']) && $_POST['deleteid'] != ''){
	$start_deletion = $mysqli->query("SELECT * FROM $database WHERE id='$_POST[deleteid]'");
	prune($start_deletion);
}

elseif(isset($_POST['search'])){
	// pre($_POST);

	$query = "SELECT * FROM $database WHERE type='Product' ";
	if($_POST['search'] != '')
		$query .= (is_numeric($_POST['search'])) ? "&& (id LIKE '%$_POST[search]%') " : "&& (name LIKE '%$_POST[search]%') ";
	if($_POST['category'] != '')
		$query .= "&& relation LIKE '%($_POST[category])%'";
	$query .= "ORDER BY sort ASC, id DESC";

	$products = $mysqli->query($query);
	echo displayProducts($products);
}

?>