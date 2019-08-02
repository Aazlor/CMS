<?

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

$product_list = '';

$image_path = array_search('{gallery}', $templates['Product']);

include 'header.php';
?>

<div class="Title">
	<?= $site_name; ?> Products
</div>

	<div class="Submit">
		<input type="submit" value="Add New Product" class="NewProduct">
	</div>

	<div class="Label">
		Select Category
	</div>
	<div class="LabelInsert">
		<select name="Category" class="SortProducts" data-contentid="ProductList">
			<option value='' selected="selected" disabled="disabled">Select a Category</option>
			<?
			$query = "SELECT * FROM $database WHERE type='Category' ORDER BY name ASC";
			$cats = $mysqli->query($query);
			while($cat = $cats->fetch_assoc()){
				echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
			}
			?>
		</select>
	</div>

	<div class="Label">
		Product List
	</div>
	<div id="ProductList" class="LabelInsert ProductList HideDelete" data-img_path="<?= $image_path ?>"></div>