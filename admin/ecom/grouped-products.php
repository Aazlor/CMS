<?

$content = $product_list = $product_id = '';

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

if(file_exists($_SERVER['DOCUMENT_ROOT'].'/admin/ecom/group_list.php'))
	$grouped = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/admin/ecom/group_list.php'));


if(!empty($featured)){
	$get_featured = implode(',', $featured);
	$query = "SELECT * FROM $database WHERE id IN ($get_featured)";

	$get_products = $mysqli->query($query);

	while($product = $get_products->fetch_assoc()){
		include($_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.php');
		if(!empty($gallery_array)){
			$image = '/products/'.$product['id'].'/'.$gallery_array[0];
		}
		else{
			$image = '';
		}

		$product_array[$product['id']] = '
		<li class="SortItem" id="itemID_'.$product['id'].'">
		<div class="Product" data-product_id="'.$product['id'].'">
				<div class="img" style="background-image: url(\''.$image.'\');"></div>
				<h3>'.$product['name'].'</h3>
			<div class="Delete" data-id="'.$product['id'].'"><input type="image" src="images/cross.gif" class="Delete"></div>
		</div>
		</li>
		';
	}

	foreach ($featured as $value) {
		if(isset($product_array[$value]))
		$content .= $product_array[$value];
	}
}
else{
	$content = '<p class="none">No product groups to list.</p>';
}

?>

<script>
$(document).ready(function(){


	function updateFeatured(){
		$(".Content").trigger("sortupdate");
	}


	$('.Content').on('click', '.Delete', function(e){
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

	$('#newGroup').on('click', function(){	
		var newGroupName = $('#groupName').val();

		$.ajax({
			type: "POST",
			url: "/admin/ecom/ajax.php",
			data: { newGroupName: newGroupName }
		}).done(function( msg ) {
			// $(_this).closest('ul').html(msg);
			console.log(msg);
		});
	});

	// $('.ProductList').on('click', '.SortItem', function(){
	// 	if($('.Content .SortItem').length < <?= $featured_product_limit ?>){
	// 		$(this).fadeOut('slow', function(){
	// 			$(this).find('.Delete').fadeIn('fast');
	// 			$(this).appendTo('.Content').fadeIn('slow',function(){
	// 				updateFeatured();
	// 			});
	// 		});
	// 		$('.none').fadeOut('slow', function(){
	// 			$(this).remove();
	// 		});
	// 	}
	// 	else
	// 		alert('You have the maximum number of featured products already selected.');
	// });

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
	$( ".Content" ).on( "sortupdate", function( event, ui ) {

		var sorted = $( ".Content" ).sortable( "serialize" );
		var category = $('select.Cats').val();
		
		$.ajax({
			type: "POST",
			url: "ecom/sort-featured.php?id=<?= $product_id ?>",
			datatype: "html",
			data: {sorted: sorted, cat: category}
		}).done(function( msg ) {
			console.log(msg);
		});

	});
	<? /***** END SORT JS *****/ ?>
});
$(function() {
	$( ".Content" ).sortable({
		placeholder: "ui-state-highlight",
		opacity: 0.5,
	});
	$( ".Content" ).disableSelection();
});
</script>

<style type="text/css">
.ProductList .Delete {
	display: none;
}
</style>

<div class="Title">
	<?= $site_name; ?> - Product Groups
</div>

<div class="Label">
	Create New Product Group
</div>
<div class="LabelInsert">
	<input type="text" name="group" value="" class="text Primary" placeholder="Name this group." id="groupName">

	<div class="Submit Right NoMargins">
		<input type="submit" value="Add Group" id="newGroup">
	</div>
</div>

<div class="Label">
	Groupings
</div>

<div class="Content SortItem">
	<?= $content ?>
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
	<input type="text" name="search" value="" class="text SearchProducts" placeholder="Type a full or partial product name.">
</div>

<div class="Label">
	Product List
</div>
<div class="LabelInsert ProductList">
	<?= $product_list; ?>
</div>