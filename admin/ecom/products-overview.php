<?

$product_list = '';

foreach($vars as $key => $value){
	if($value == '{gallery}'){
		$gallery_vars = 'Gallery__'.$key;
		$gallery_vars = $$gallery_vars;
		foreach($gallery_vars as $k => $v){
			if(preg_match('/{image}/', $k)){
				$image_path = $key;
				break 2;
			}			
		}
	}
}

$get_products = $mysqli->query("SELECT * FROM $database WHERE type='Product' ORDER BY sort ASC, id DESC");
$count = $get_products->num_rows;
if($count > 0){

	//$except = array("rar", "zip", "mp3", "mp4", "mp3", "mov", "flv", "wmv", "swf", "png", "gif", "jpg", "bmp", "avi");
	$except = array("png", "gif", "jpg", "bmp", "jpeg");
	$imp = implode('|', $except);

	$product_list .= '<div class="SubTitle" style="text-align: center;">Viewing '.$count.' products</div>';

	$x=1;
	while($product = $get_products->fetch_array()){
		include($_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.php');
		$background = '';
		if(!empty($gallery_array)){
			foreach($gallery_array as $value){
				$split = explode('||', $value);
				foreach($split as $v){
					if(preg_match('/^.*\.('.$imp.')$/i', $v)){
						$background = 'style="background: url(\'/products/'.$product['id'].'/thumb-'.$v.'\') center center no-repeat; background-size: cover;"';
						break 2;
					}
				}
			}
		}

	$product_list .= '
		<li class="SortItem" id="itemID_'.$product['id'].'">
			<div class="Product" data-product_id="'.$product['id'].'">
			<h4>Product ID: '.$product['id'].'</h4>
				<div class="img" '.$background.'></div>
				<h3>'.$product['name'].'</h3>
			</div>
			<div class="Delete" data-id="'.$product['id'].'"><img src="images/cross.gif"></div>
		</li>
		';
		$x++;
	}
}
else{
	$product_list = '<div class="SubTitle" style="text-align: center;">No Products Added</div>';
}

?>

<script type="text/javascript">
$(document).ready(function(){
	$('input.NewProduct').click(function(){
		window.location.replace("?section=product");
	});

	$('.ProductList').on('mouseenter', '.Delete', function(e){
		$(this).siblings('.Product').stop().fadeTo("fast", 0.3);
	});
	$('.ProductList').on('mouseleave', '.Delete', function(e){
		$(this).siblings('.Product').stop().fadeTo("fast", 1);
	});

	$('.ProductList').on('click', '.Delete', function(){
		console.log(this);
		var this_ = $(this).closest('li');
		var id = $(this).data('id');
		if(confirm("Are you sure you want to delete this product?  This cannot be undone.")){
			$.ajax({
				type: "POST",
				url: "/admin/ecom/ajax.php",
				data: { deleteid: id }
			}).done(function(msg){
				$(this_).remove();
			});
		}
	});

	$('.ProductList').on('click', '.Product', function(){
		var product_id = $(this).data('product_id');
		window.location.href = '?section=product&product_id='+product_id;
	});

	$('.SearchProducts').change(function() {
		var search_val = $(this).val();
		$.ajax({
		type: "POST",
		url: "/admin/ecom/ajax.php",
		data: { search_val: search_val, img_path: '<?= $image_path ?>' }
	}).done(function( msg ) {
		$('.ProductList').fadeOut('fast', function(){
			$('.ProductList').html(msg);
			$('.ProductList').fadeIn('fast');
		});
	});
});

$('select.Cats').change(function() {
	var search_val = $(this).val();
	$.ajax({
		type: "POST",
		url: "/admin/ecom/ajax.php",
		data: { search_cat: search_val, img_path: '<?= $image_path ?>' }
	}).done(function( msg ) {
		$('.ProductList').fadeOut('fast', function(){
			$('.ProductList').html(msg);
			$('.ProductList').fadeIn('fast');
		});
	});
});

	<? /***** SORT JS *****/ ?>


	$( ".ProductList" ).on( "sortupdate", function( event, ui ) {

		var sorted = $( ".ProductList" ).sortable( "serialize" );
		var category = $('select.Cats').val();
		
		$.ajax({
			type: "POST",
			url: "ecom/sort-products.php?id=<?= (isset($product_id)) ? $product_id : '' ?>",
			datatype: "html",
			data: {sorted: sorted, cat: category}
		}).done(function( msg ) {
			console.log(msg);
		});

	});
	<? /***** END SORT JS *****/ ?>
});

$(function() {
	$( ".ProductList" ).sortable({
		placeholder: "ui-state-highlight",
		opacity: 0.5,
		helper: 'clone',
	});
	$( ".ProductList" ).disableSelection();
});
</script>

<div class="Title">
	<?= $site_name; ?> Products
</div>

	<div class="Submit">
		<input type="submit" value="Add New Product" class="NewProduct">
	</div>

	<div class="Label">
		Search Products
	</div>
	<div class="LabelInsert">
		<select name="Category" class="Cats">
			<option value=''>View By Category</option>
			<?
			$query = "SELECT * FROM $database WHERE type='Category' ORDER BY name ASC";
			$cats = $mysqli->query($query);
			while($cat = $cats->fetch_assoc()){
				echo '<option value="'.$cat[id].'">'.$cat[name].'</option>';
			}
			?>
		</select>
	</div>
	<div class="LabelInsert">
		<input type="text" name="search" value="" class="text SearchProducts">
	</div>

	<div class="Label">
		Product List
	</div>
	<div class="LabelInsert ProductList sortable ui-sortable">
		<?= $product_list; ?>
	</div>