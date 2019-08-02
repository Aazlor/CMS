<?
require 'login_check.php';

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

$topLevelCategories = $mysqli->query("SELECT * FROM $database WHERE type='Category' && relation='Primary' ORDER BY sort, name ASC");

if(isset($_REQUEST['id']) && $_REQUEST['id'] != ''){
	$id = $_REQUEST['id'];
	$data = getData($id);
}
elseif(isset($_REQUEST['product']) && $_REQUEST['product'] == 'new'){
	$data = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY id DESC LIMIT 1")->fetch_assoc();

	if(isset($data['name'])){
		$mysqli->query("INSERT INTO $database (type) VALUES ('Product')");
		$data = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY id DESC LIMIT 1")->fetch_assoc();

		if( is_dir($_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id']) ){
			$files = glob($_SERVER['DOCUMENT_ROOT'].'/products/'.$data['id'].'/*'); // get all file names
			foreach($files as $file){ // iterate files
				if(is_file($file))
					unlink($file); // delete file
			}
		}
	}
	$id = $data['id'];
}
else{
	$data = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY id DESC LIMIT 1")->fetch_assoc();
	$id = $data['id'];
	$data['info'] = createArray($data['info']);
}

$product_args = '';
if($data['type'] == 'Product'){
	if( is_dir($_SERVER['DOCUMENT_ROOT'].'/products/'.$id) === false ){
		mkdir($_SERVER['DOCUMENT_ROOT'].'/products/'.$id, 0777);
	}
	$product_args = '<input type="hidden" name="type" value="Product">';
}

$template = (stristr($data['type'], 'page')) ?  $templates[$data['info']['template']] : $templates[$data['type']];

$html = buildForm($data, $template, $topLevelCategories);

include 'header.php';

	if(isset($_SESSION['post_response'])){
		echo $_SESSION['post_response'];
		unset($_SESSION['post_response']);
	}

?>

<form method="POST" enctype="multipart/form-data" action="post_operations.php">

	<?= $html ?>

	<div class="Submit">
		<?= $product_args ?>
		<input type="hidden" name="id" value="<?= $id ?>"/>
		<input type="hidden" name="save" value="yes"/>
		<input type="submit" value="Save" />
	</div>
</form>