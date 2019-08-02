<?

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

$product_list = '';

$image_path = array_search('{gallery}', $templates['Product']);

$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY sort ASC, id DESC");

$products = displayProducts($get_products);

include 'header.php';
?>

<div class="Title">
	<?= $site_name; ?> Products
</div>

	<div class="Submit">
		<a href="/admin/manage.php?product=new"><button type="button">Add New Product</button></a>
	</div>

	<div class="Label">
		Search Products
	</div>
	<div class="LabelInsert">
		<select name="Category" class="Cats" data-contentid="ProductList">
			<option value=''>View All Categories</option>
			<?
			$query = "SELECT * FROM $database WHERE type='Category' ORDER BY name ASC";
			$cats = $mysqli->query($query);
			while($cat = $cats->fetch_assoc()){
				echo '<option value="'.$cat['id'].'">'.$cat['name'].'</option>';
			}
			?>
		</select>
	</div>
	<div class="LabelInsert">
		<input type="text" name="search" value="" class="text SearchProducts" data-contentid="ProductList">
	</div>

	<div class="Label">
		Product List
	</div>
	<div id="ProductList" class="LabelInsert ProductList" data-img_path="<?= $image_path ?>">
		<?
			echo $products;
		?>
	</div>