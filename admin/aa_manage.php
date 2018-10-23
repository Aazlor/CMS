<?
require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

$topLevelCategories = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='Primary' ORDER BY sort, name ASC");

if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
	$id = $_REQUEST['id'];
	$data = getData($id);
}
else{
	$data = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY id DESC LIMIT 1")->fetch_assoc();
	$id = $data['id'];
	$data['info'] = createArray($data['info']);
}

if($data['type'] == 'Product'){
	if( is_dir($_SERVER['DOCUMENT_ROOT'].'/products/'.$id) === false ){
		mkdir($_SERVER['DOCUMENT_ROOT'].'/products/'.$id, 0777);
	}	
}

$template = (stristr($data['type'], 'page')) ?  $templates[$data['info']['template']] : $templates[$data['type']];

$html = buildForm($data, $template, $topLevelCategories);

include 'aa_header.php';

	echo $html;
?>