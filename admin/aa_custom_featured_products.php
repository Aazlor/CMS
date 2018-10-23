<?

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'aa_functions.php';

$featured_file = $_SERVER['DOCUMENT_ROOT'].'/admin/aa_custom_featured_products_list.txt';

if(file_exists($featured_file)){
	$featured = unserialize(file_get_contents($featured_file));
	$featured_string = implode(',', $featured);
}

/***** AJAX *****/
if(isset($_POST['search'])){

	$query = "SELECT * FROM $database WHERE type='Product' ";
	if($_POST['search'] != '')
		$query .= (is_numeric($_POST['search'])) ? "&& (id LIKE '%$_POST[search]%') " : "&& (name LIKE '%$_POST[search]%') ";

	$query .= "&& (id NOT IN (".implode(',', $featured).")) ";

	$query .= "ORDER BY sort ASC, id DESC";

	$products = $mysqli->query($query);
	echo displayProducts($products);	

	exit;
}
elseif(isset($_POST['sorted'])){
	$_POST['sorted'] = str_replace('itemID[]=', '', $_POST['sorted']);
	$order = explode('&', $_POST['sorted']);

	file_put_contents ($featured_file , serialize($order) );
	exit;
}



$content = $product_list = $product_id = '';

foreach($templates['Product'] as $key => $value){
	if($value == '{gallery}'){
		$gallery_vars = $Gallery[$key];
		foreach($gallery_vars as $k => $v){
			if(stristr($k, '{image}')){
				$image_path = $key;
				break 2;
			}
		}
	}
}

if(!empty($featured)){
	$query = "SELECT * FROM $database WHERE id IN ($featured_string) ORDER BY field(id, $featured_string)";
	$get_products = $mysqli->query($query);
	$content = displayProducts($get_products);
}
else{
	$content = '<p class="none">No featured products to list.</p>';
}

include 'aa_header.php';

?>

<script>
$(document).ready(function(){

	function updateFeatured(){
		$(".Content.Featured").trigger("sortupdate");
	}

	$('.Content.Featured').on('click', '.Delete', function(e){
		var this_ = $(this).closest('.SortItem');
		if(confirm("Are you sure you want to no longer feature this product?")){
			$(this_).fadeOut('slow', function(){
				$(this).remove();
				updateFeatured();
			});
		}
		e.preventDefault();
		e.stopPropagation();
	});

	$('#FeaturedOptions').on('click', '.SortItem', function(e){
		if($('.Content.Featured .SortItem').length < <?= $featured_product_limit ?>){
			$(this).fadeOut('slow', function(){
				$(this).find('.Delete').fadeIn('fast');
				$(this).appendTo('.Content.Featured').fadeIn('slow',function(){
					updateFeatured();
				});
			});
			$('.none').fadeOut('slow', function(){
				$(this).remove();
			});
		}
		else
			alert('You have the maximum number of featured products already selected.');
	});
	
	$('.FindProduct').on('keyup', function() {
		var contentid = $(this).data('contentid');
		console.log(contentid);
		var search = $('.FindProduct').val();
		// var search_cat = $('select.Cats').val();
		var img_path = $('#'+contentid).data('img_path');

		// console.log(search_val, search_cat, img_path);

		$.ajax({
			type: "POST",
			url: "aa_custom_featured_products.php",
			data: { search: search, img_path: img_path }
		}).done(function( msg ) {
			$('#'+contentid).fadeOut('fast', function(){
				$('#'+contentid).html(msg);
				$('#'+contentid).fadeIn('fast');
			});
		});		

		
	});

	<? /***** SORT JS *****/ ?>
	$( ".Content.Featured" ).on( "sortupdate", function( event, ui ) {

		var sorted = $( ".Content.Featured" ).sortable( "serialize" );
		var category = $('select.Cats').val();
		
		$.ajax({
			type: "POST",
			url: "<? $_SERVER['PHP_SELF'] ?>",
			datatype: "html",
			data: {sorted: sorted, cat: category}
		}).done(function( msg ) {
			console.log(msg);
		});

	});
	<? /***** END SORT JS *****/ ?>
});
$(function() {
	$( ".Content.Featured" ).sortable({
		placeholder: "ui-state-highlight",
		opacity: 0.5,
	});
	$( ".Content.Featured" ).disableSelection();
});
</script>

<style type="text/css">
#FeaturedOptions .Delete {
	display: none;
}

.ProductList.SortItem.Featured .Product {
	cursor: move;
}
</style>


<div class="Title">
	<?= $site_name; ?> Featured Products
</div>

<div class="Label">
	Featured Products
</div>

<div class="Content SortItem ProductList Featured">
	<?= $content ?>
</div>

<div class="Label">
	Find a Product
</div>
<div class="LabelInsert">
	<input type="text" name="search" value="" class="text FindProduct" data-contentid="FeaturedOptions" placeholder="Enter a product name, partial name, or use * to return all products.">
	<button name="search" class="featuredSearch" >Search</button>
</div>

<div class="Label">
	Product List
</div>
<div class="LabelInsert ProductList" id="FeaturedOptions" data-img_path="<?= $image_path ?>">
	<?= $product_list; ?>
</div>