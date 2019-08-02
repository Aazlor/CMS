<?
require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

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
	if(isset($_POST['category']) && $_POST['category'] != ''){
		$sort_category = $mysqli->query("SELECT * FROM $database WHERE id='$_POST[category]'")->fetch_assoc();

		if (substr($sort_category['misc'], -1, 1) == ',')
			$sort_category['misc'] = substr($sort_category['misc'], 0, -1);

		// pre($sort_category);
		$query .= "&& relation LIKE '%($_POST[category])%'";
	}
	if(isset($sort_category['misc']) && $sort_category['misc'] != ''){
 		$query .= "ORDER BY field(id, $sort_category[misc]), id DESC";
	}
	else
		$query .= "ORDER BY id DESC";

	// echo '<hr>'.$query.'<hr>';

	$products = $mysqli->query($query);
	echo displayProducts($products);
}
elseif(isset($_POST['gallery'])){
	if(!isset($_POST['function']))
		exit;

	// pre($_POST);

	$data = getData($_POST['id']);

	switch ($_POST['function']) {
		case 'add':

			unset($data['info'], $data['name'], $data['relation']);

			$html = galleryAdd($_POST['gallery'], $data);
			break;
		
		case 'sort':
			gallerySort($data, $_POST['gallery'], $_POST['sort']);
			echo true;
			exit;
			break;

		default:
			$html = galleryItem($_POST['gallery'], $data, $_POST['item']);
			break;
	}

	// pre($html);

	echo json_encode($html);
}
elseif(isset($_POST['sortProducts'])){

	$values = [];
	parse_str($_POST['sort'], $values);
	$order = implode(',', $values['itemID']);

	$query = "UPDATE $database SET misc='$order' WHERE id=$_POST[categoryID]";

	$mysqli->query($query);
}

?>