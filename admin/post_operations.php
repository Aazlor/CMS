<?

require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

/***** ARGUMENTS *****/

if(isset($_POST['function'])){
	if($_POST['function'] == 'gallery_add')
		galleryAddSubmit($_POST, $_FILES);

	if($_POST['function'] == 'gallery_edit'){
		galleryEditSubmit($_POST, $_FILES);
	}

	$pretty_key = str_replace('_', ' ', preg_replace('/{.*?}/', '', $_POST['field_name']));

	$html = galleryDisplay($_POST, $_POST['field_name'], $pretty_key);
	echo $html['html'];
	exit;

	// header('Location: ./manage.php?id='.$_POST['id']);
	// exit;
}
if(isset($_POST['save'])){


	$id = $_POST['id'];
	unset($_POST['id'], $_POST['save']);

	if(isset($_POST['type']) && $_POST['type'] == 'Product')
		saveProduct($id, $_POST);
	else
		savePage($id, $_POST, $_FILES);

	header('Location: ./manage.php?id='.$id);
	exit;
}





?>