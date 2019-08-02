<?
include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';
$product_groups_file = $_SERVER['DOCUMENT_ROOT'].'/admin/product_groups_list.txt';
if(!file_exists($product_groups_file)){
	$file = fopen($product_groups_file,"w");
	fwrite($file, '');
	fclose($file);
	chmod($file, 0777);
}
$groups = unserialize(file_get_contents($product_groups_file));
if(!is_array($groups))
	$groups = [];
function displayGroups($groups){
	global $mysqli, $database, $templates;
	$html = '';
	$image_path = array_search('{gallery}', $templates['Product']);
	if(!empty($groups)){
		$i=0;
		foreach($groups as $k => $v){
			$html .= '<ul class="group" data-group="'.$k.'" id="group'.$i.'"><div class="groupName"><span class="name" data-name="'.$k.'">Product Group '.$k.'</span> <span class="ui-icon ui-icon-pencil"></span></div>';
			if(!empty($v)){
				foreach($v as $product_id){
					if($product_id == '') continue;
					$product = $mysqli->query("SELECT * FROM $database WHERE id='$product_id'");
					$product = $product->fetch_assoc();
					$product_vars = unserialize(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.txt'));
					$html .= '<li data-product="'.$product_id.'"><img src="/products/'.$product['id'].'/'.$product_vars[0]['Image'].'"></li>';
				}
			}
			$html .= '</ul>';
			$i++;
		}
	}
	return $html;
}
function change_key( $array, $old_key, $new_key ) {
    if( ! array_key_exists( $old_key, $array ) )
        return $array;
    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;
    return array_combine( $keys, $array );
}
/***** AJAX *****/
if(isset($_POST['search'])){
	$flatten_array = [];
	if(!empty($groups)){
		foreach($groups as $v){
			$flatten_array = array_filter(array_merge($flatten_array, $v));
		}
	}
	$query = "SELECT * FROM $database WHERE type='Product' ";
	if($_POST['search'] != '')
		$query .= (is_numeric($_POST['search'])) ? "&& (id LIKE '%$_POST[search]%') " : "&& (name LIKE '%$_POST[search]%') ";
	if(!empty($flatten_array))
		$query .= "&& (id NOT IN (".implode(',', $flatten_array).")) ";
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
elseif(isset($_POST['add']) && $_POST['add'] == 'new-group'){
	array_push($groups, []);
	$file = fopen($product_groups_file, "w+");
	fwrite($file, serialize($groups));
	fclose($file);
	echo displayGroups($groups);
	exit;
}
elseif(isset($_POST['dropped'])){
	$items = json_decode($_POST['items'], true);
	foreach($_POST['order'] as $v){
		$list[$v] = $items[$v];
	}
	$file = fopen($product_groups_file, "w+");
	fwrite($file, serialize($list));
	fclose($file);
	echo displayGroups($list);
	exit;
}
elseif(isset($_POST['newGroupName'])){
	$groups = change_key( $groups, $_POST['group'], $_POST['newGroupName'] );
	$file = fopen($product_groups_file, "w+");
	fwrite($file, serialize($groups));
	fclose($file);
	exit;
}
if(isset($_POST) && !empty($_POST)) exit;
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
	$query = "SELECT * FROM $database WHERE id IN ($groups_string) ORDER BY field(id, $groups_string)";
	$get_products = $mysqli->query($query);
	$content = displayProducts($get_products);
}
else{
	$content = '<p class="none">No featured products to list.</p>';
}
include 'header.php';
?>

<script>
$(document).ready(function(){
	$('#ProductGroups').on('click', 'a', function(e){
		e.preventDefault();
	});
	function saveGroups(refresh){
		var items = {};
		var order = [];
		$('.group').each(function(){
			var group_id = $(this).data('group')
			items[group_id] = [];
			order.push(group_id);
			$(this).find('li').each(function(i){
				items[group_id].push($(this).data('product'));
			})
		})
		$.ajax({
			type: "POST",
			url: "<?= $_SERVER['PHP_SELF'] ?>",
			data: { dropped: 'yes', items: JSON.stringify(items), order: order }
		}).done(function( msg ) {
			// $('.productGroups').html(msg);
			// if(refresh === 1){
				$('.FindProduct').trigger('keyup');
				// $('.group, #ProductGroups').droppable(droppableVars);
				// $('.group').sortable(sortableVars);	
			// }
		});		
	}
	/***** CREATE GROUP *****/
	$('button.dialogAddProductGroup').on('click', function(e){
		e.preventDefault();
		e.stopPropagation();
		$.ajax({
			type: "POST",
			url: "<? $_SERVER['PHP_SELF'] ?>",
			datatype: "html",
			data: {add: 'new-group'}
		}).done(function( msg ) {
			// console.log(msg);
			$('.productGroups').html(msg);
			$('.group, #ProductGroups, .remove').droppable(droppableVars);
			$('.group').sortable(sortableVars);
		});		
	});	
	/*****  DRAGGABLE *****/
	var droppableVars = { 
		accept: ".SortItem, .group li", 
		drop: function(event, ui) {
			$(this).removeClass("border").removeClass("over");
			var dropped = ui.draggable;
			var droppedOn = $(this);
			var target = event.target.id;
			var source = ui.draggable[0].parentElement.id;
			if(target === source){
				return false;
			}
			// console.log(dropped)
			if($(dropped).hasClass('SortItem'))
				var droppedid = $(dropped).find('.Product').data('product');
			else
				var droppedid = $(dropped).data('product');
			var droppedImgSrc = $(dropped).find('img').attr('src');
			var addHtml = '<li data-product="'+droppedid+'" class="ui-sortable-handle"><img src="'+droppedImgSrc+'"></li>';
			$(dropped).remove();
			if(!$(droppedOn).hasClass('remove'))
				$(addHtml).appendTo(droppedOn);
			
			saveGroups();
		}, 
		over: function(event, elem) {
			$(this).addClass("over");
			console.log("over");
		},
		out: function(event, elem) {
			$(this).removeClass("over");
		},
		revert: function(dropped){
			$(this).animate({ top: 0, left: 0 }, 'fast');
		}
	};
	var draggableVars = {
		revert: function(dropped){
			$(this).animate({ top: 0, left: 0 }, 'fast');
		}		
	}
	var sortableVars = {
		items: 'li',
		forceHelperSize: true,
		helper: "clone",
		placeholder: "ui-state-highlight",
		update: function(){
			saveGroups();
		}
	}
	$('.group, #ProductGroups, .remove').droppable(droppableVars);
	$('.group').sortable(sortableVars);
	/***** SORT JS *****/
	// $( ".group" ).on( "sortupdate", function( event, ui ) {
	// 	saveGroups();
	// });	
	/***** SEARCH *****/
	$('.FindProduct').donetyping(function() {
		var contentid = $(this).data('contentid');
		console.log(contentid);
		var search = $('.FindProduct').val();
		var img_path = $('#'+contentid).data('img_path');
		$.ajax({
			type: "POST",
			url: "<?= $_SERVER['PHP_SELF'] ?>",
			data: { search: search, img_path: img_path }
		}).done(function( msg ) {
			$('#'+contentid).fadeOut('fast', function(){
				$('#'+contentid).html(msg);
				$('#'+contentid).fadeIn('fast');
				// $(".SortItem, .group li").draggable(draggableVars);
				$("#"+contentid).sortable(sortableVars);
			});
		});		
	});
	/***** RENAME/DELETE GROUP *****/
	$('.productGroups').on('click', '.groupName span', function(){	
		var line = '';
		var name = $(this).siblings('span');
		var currentName = $(name).data('name');
		var editBlock = '<input type="text" name="changeName" value="'+currentName+'" data-group="'+currentName+'"><button class="changeName">save</button><span class="DeleteGroup" data-group="'+currentName+'"><img src="/admin/images/cross.gif"></span>';
		$(name).html(editBlock);
		$(name).addClass('inEdit');
	});
	$('.productGroups').on('click', '.DeleteGroup', function(){
		$(this).closest('.group').remove();
		saveGroups();
	});
	$('.productGroups').on('click', 'button.changeName', function(){
		var groupName = $(this).siblings('input[name="changeName"]').data('group');
		var newGroupName = $(this).siblings('input[name="changeName"]').val();
		var span = $(this).parent('.name');
		if($('span[data-name="'+newGroupName+'"]').length > 0){
			alert('This name already exists.');
			return false;
		}
		$.ajax({
			type: "POST",
			url: "<?= $_SERVER['PHP_SELF'] ?>",
			data: { group: groupName, newGroupName: newGroupName }
		}).done(function( msg ) {
			$(span).data('group', newGroupName);
			$(span).text('Product Group '+newGroupName);
			$(span).removeClass('inEdit');
		});
	});	
});
</script>

<style type="text/css">
	#ProductGroups .Delete {
		display: none;
	}
	.ProductList .SortItem .Product {
		cursor: move;
	}
	.productGroups {
		display: flex;
		flex-wrap: wrap;
	}
	.productGroups ul.group {
		margin: 5px;
		border: 2px solid #000;
		list-style: none;
		list-style-position: inside;
		padding: 0;
		min-height: 100px;
		max-width: 25%;
		flex-wrap: wrap;
		display: flex;
		justify-content: space-around;
		height: max-content;
	}
	.productGroups ul.group .groupName {
		line-height: 25px;
		background: #dde1ea;
		color: #000;
		padding-left: 10px;
		display: block;
		height: 25px;
		width: 100%;
		position: relative;
		padding-right: 30px;
	}
	.productGroups ul.group .groupName .ui-icon {
		position: absolute;
		top: 6px;
		right: 6px;
		border: 1px solid #c5c5c5;
		border-radius: 3px;
		background-color: #f6f6f6;
		font-weight: normal;
		cursor: pointer;
	}
	.productGroups ul.group li {
		display: inline-flex;
		cursor: move;
		z-index: 1;
		height: max-content;
	}
	.productGroups ul.group li:hover {
		cursor: move;
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
		margin: -1px;
		z-index: 999;
	}
	.productGroups ul.group li img {
		max-height: 100px;
	}
	.ui-sortable-placeholder {
		background: yellow;
	}
	.ui-draggable-dragging {
		z-index: 999;
		background-color: white;
		height: 0;
		width: 0;
	}
	.ui-draggable-dragging * {
		line-height: 0;
		font-size: 0;
		padding: 0;
		margin: 0;
		width: 0;
	}
	.ui-draggable-dragging img {
		width: 80px;
		max-width: 80px;
		padding: 0;
		margin: 0;
		height: auto;
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
	}
	.over {
		background-color: highlight;
		box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
	}
	/*** GROUP NAME ***/
	.DeleteGroup {
		float: left;
	}
	.inEdit input {
		font-family: arial;
		border: 1px solid #CDCED0;
		padding: 2px;
		/* width: 400px; */
		width: calc(100% - 150px);	
	}
	.remove {
		display: block;
		float: right;
		padding: 30px;
		border: 2px dashed #000;
		background-color: #eef0f4;
	}
</style>


<div class="Title">
	<?= $site_name; ?> Product Groups
</div>

<div class="Label">
	Product Groups
</div>

<div class="Content">
	<button class="dialogAddProductGroup"><span class="ui-icon ui-icon-plusthick"></span> Add Product Group</button>

	<div class="remove">DRAG & DROP HERE TO REMOVE FROM GROUP</div>
	<div class="Clear"></div>

	<div class="productGroups">
		<?= displayGroups($groups) ?>
	</div>

<?
	// SortItem ProductList Featured">
?>
	<div class="Details">
		<p>Dropping an item always adds it to the end of the group.  Drag and drop a product inside its same container to move it to the end of the group.</p>
		<p>To remove an from a group drag and drop it into the Product List below.</p>
	</div>
</div>

<div class="Label">
	Product Quick Search
</div>
<div class="LabelInsert">
	<input type="text" name="search" value="" class="text FindProduct" data-contentid="ProductGroups" placeholder="Enter a product name, partial name, or use * to return all products.">
	<button name="search" class="featuredSearch" >Search</button>
</div>

<div class="Label">
	Product List
</div>
<div class="LabelInsert ProductList" id="ProductGroups" data-img_path="<?= $image_path ?>">
	<?= $product_list; ?>
</div>