<?

include 'config_site_info.php';
include 'array_templates.php';
include 'db_connect.php';
include 'functions.php';

function couponFetchProduct($coupon){
	global $database, $mysqli, $templates;

	$productDisplay = '';

	if(isset($coupon['info']['Product']) && $coupon['info']['Product'] != ''){
		$id = $coupon['info']['Product'];
		$query = "SELECT * FROM $database WHERE type='Product' && id='$id'";
		$product = $mysqli->query($query)->fetch_assoc();

		$image_path = array_search('{gallery}', $templates['Product']);
		$file_path = $_SERVER['DOCUMENT_ROOT'].'/products/'.$product['id'].'/'.$image_path.'.txt';
		$gallery_array = unserialize(file_get_contents($file_path));

		$item = current($gallery_array);

		$product['bg'] = (isset($item['Image']) && $item['Image'] != '') ? $item['Image'] : '';

		$productDisplay .= '
		<li class="SortItem" id="itemID_'.$product['id'].'">
			<div class="Product" data-product="'.$product['id'].'">
			<h4>Product ID: '.$product['id'].'</h4>
				<div class="img">
					<img src="/products/'.$product['id'].'/thumb-'.$product['bg'].'">
				</div>
				<h3>'.$product['name'].'</h3>
			</div>
		</li>
		<input type="hidden" name="Product" value="'.$product['id'].'">
		<button class="ChangeProduct">Change Product</button>
		';
	}

	return $productDisplay;
}

function couponOptions($id, $function){
	global $database, $mysqli;

	$coupon_vars = [
		'id' => '',
		'Name' => '',
		'info' => [
			'Name' => '',
			'Application' => '',
			'Discount' => '',
			'Discount_Type' => '',
			'Description' => '',
		],
	];

	$deleteHTML = '';

	if($id != ''){
		$coupon = getData($id);
		foreach($coupon_vars['info'] as $k => $v){
			if(!isset($coupon['info'][$k]))
				$coupon['info'][$k] = '';
		}
		$deleteHTML = '<div class="Delete"><input type="checkbox" name="delete" value="yes" id="delete"> Delete this Coupon</div>';
	}
	else
		$coupon = $coupon_vars;

	$productDisplay = couponFetchProduct($coupon);
	
	$html['title'] = $function;
	$html['html'] = '
	<form method="POST" enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'">
		'.$deleteHTML.'
		<div class="Label">Coupon Application</div>
		<div class="LabelInsert">
			<select name="Application" id="application">
			';

			$arr = ['global' => 'Sitewide','category' => 'Specific Category','product' => 'Single Product'];
			foreach ($arr as $key => $value) {
				$selected = ($coupon['info']['Application'] == $key) ? 'selected="selected"' : '';
				$html['html'] .= '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}
			$html['html'] .= '
			</select>

			<div id="application_options">'.$productDisplay.'</div>
		</div>

		<div class="Label">Coupon Code</div>
		<div class="LabelInsert"><input type="text" name="Name" value="'.$coupon['info']['Name'].'" class="text"></div>

		<div class="Label">Discount</div>
		<div class="LabelInsert">
			<input type="number" name="Discount" value="'.$coupon['info']['Discount'].'" class="text">
			<select name="Discount_Type" id="Discount_Type">
			';

			$arr = ['percent' => '% Off', 'flat' => '$ Off'];
			foreach ($arr as $key => $value) {
				$checked = ($coupon['info']['Discount_Type'] == $key) ? 'checked="checked"' : '';
				$html['html'] .= '<option value="'.$key.'" '.$checked.'>'.$value.'</option>';
			}
			$html['html'] .= '
			</select>			
		</div>

		<div class="Label">Description of Coupon / Note to self</div>
		<div class="LabelInsert"><textarea name="Description">'.$coupon['info']['Description'].'</textarea></div>
		<div class="Details">This description is for internal use only.  Use this to remind yourself what this purpose of this coupon is and any related info.</div>
		
		<div class="Submit">
			<input type="hidden" name="submit_coupon" value="yes">
			<input type="hidden" name="id" value="'.$coupon['id'].'" class="hiddenID">
		</div>
	</form>
	';

	return $html;
}

function fetchValidForData($id, $selected){
	global $database, $mysqli, $templates;

	if($id > 0)
		$coupon = getData($id);

	switch ($selected) {
		case 'category':
			$html['html'] = '<select name="Category">
				<option selected="selected" disabled>Select a Product Category</option>';
				
				$query = "SELECT * FROM $database WHERE type='Category' ORDER BY name ASC";
				$cats = $mysqli->query($query);
				while($cat = $cats->fetch_assoc()){
					$selected = (isset($coupon['info']['Category']) && $cat['id'] == $coupon['info']['Category']) ? 'selected="selected"' : '';
					$html['html'] .= '<option value="'.$cat['id'].'" '.$selected.'>'.$cat['name'].'</option>';
				}		
			$html['html'] .= '</select>';
			break;
		case 'product':
			$image_path = array_search('{gallery}', $templates['Product']);

			$html['html'] = '
			<div class="Label">
				Product Quick Search
			</div>
			<div class="LabelInsert">
				<input type="text" name="search" value="" class="text FindProduct" data-contentid="ProductGroups" placeholder="Enter a product name, partial name, or use * to return all products." data-img_path="'.$image_path.'">
			</div>

			<div class="LabelInsert ProductList" id="ProductGroups"></div>';
			break;		
		default:
			$html['html'] = '';
			break;
	}

	return $html;
}

/***** AJAX *****/
if(isset($_POST['function'])){

	if($_POST['function'] == 'fetchValidForData'){
		echo json_encode(fetchValidForData($_POST['id'], $_POST['selected']));
		exit;
	}
	if($_POST['function'] == 'toggleCouponStatus'){
		$status = $mysqli->real_escape_string($_POST['status']);
		$id = $mysqli->real_escape_string($_POST['id']);
		$mysqli->query("UPDATE $database SET misc='$status' WHERE type='Coupon' && id='$id'");
		exit;
	}

	echo json_encode(couponOptions($_POST['id'], $_POST['function']));

	exit;
}
if(isset($_POST['search'])){

	$query = "SELECT * FROM $database WHERE type='Product' ";
	if($_POST['search'] != '')
		$query .= (is_numeric($_POST['search'])) ? "&& (id LIKE '%$_POST[search]%') " : "&& (name LIKE '%$_POST[search]%') ";

	$query .= "ORDER BY sort ASC, id DESC";

	$products = $mysqli->query($query);
	echo displayProducts($products, false);

	exit;
}
if(isset($_POST['submit_coupon'])){

	if(isset($_POST['delete']) && $_POST['delete'] == 'yes' && isset($_POST['id']) && $_POST['id'] != ''){
		$mysqli->query("DELETE FROM $database WHERE type='Coupon' && id='$_POST[id]'");
		exit;
	}

	$vars = '';
	foreach($_POST as $k => $v){
		if($k == 'submit_coupon' || $k == 'id') continue;

		$vars .= '{{}}'.$k.'(())'.htmlspecialchars($v);
	}

	$vars = $mysqli->real_escape_string($vars);

	$query = ($_POST['id'] == '') ?
		"INSERT INTO $database (type, name, info) VALUES ('Coupon', '$_POST[Name]', '$vars')" :
		"UPDATE $database SET info='$vars', name='$_POST[Name]' WHERE type='Coupon' && id='$_POST[id]'";

	if($mysqli->query($query)){
		echo true;
	}

	exit;
}


include 'header.php';

?>

<div class="Title">
	<?= $site_name; ?> Coupons
</div>

<style type="text/css">
	.LabelInsert.CreateCoupon {
		display: none;
	}

	.Label.CreateCoupon {
		cursor: pointer;
	}

	table#Codes {
		border-collapse: collapse;
		width: 100%;
	}

	table th {
		background-color: #dde1ea;
		margin: 5px;
		padding: 5px;
		text-align: center;
		border-left: 1px solid #fff;
		cursor: pointer;
	}

	table th:first-child {
		border: none;
	}

	table td {
		max-width: 300px;
		margin: 5px;
		padding: 5px;
	}

	table tr {
		border-bottom: 1px solid #dde1ea;
	}

	.SubTitle {
		margin-top: 50px;
	}

	#CouponDialog input[type="number"] {
		max-width: 50%;
	}

	#CouponDialog select {
		font-family: arial;
		border: 1px solid #CDCED0;
		padding: 5px;
		/* width: 400px; */
		height: 26px;
		position: relative;
		top: 1px;
	}

	#CouponDialog #application_options {
		display: inline;
	}

	#CouponDialog #application_options .SubTitle {
		margin-top: 10px;
	}

	#CouponDialog #application_options .SortItem {
		margin: 0 5px;
		max-width: 150px;
	}
	
	#CouponDialog #application_options .SortItem .Delete {
		display: none;
	}

	#CouponDialog textarea {
		display: block;
		height: 2em;
		padding: 10px 10px;
		width: calc(100% - 20px);
	}

	td.status button {
		padding: 5px 10px;
	}

	td.status .ui-icon {
		background-image: url(/admin/images/tick.gif);
		margin-right: 10px;
	}

	.ChangeProduct {
		padding: 5px;
		float: right;		
	}

</style>

<script type="text/javascript">
	$(document).ready(function(){

		dialog = $( "#CouponDialog" ).dialog({
			autoOpen: false,
			height: 500,
			width: 900,
			modal: true,
			buttons: {
				"Save": saveCoupon,
				Cancel: function() {
					dialog.dialog( "close" );
				}
			},
			close: function() {}
		});

		function saveCoupon(){
			var options = { 
				success:	   showResponse,  // post-submit callback 
			};

			$('#CouponDialog form').ajaxSubmit(options);
		}

		function showResponse(msg){
			// console.log(msg);
			location.reload()
		}

		$('button.dialogCreateCoupon, button.editCoupon').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();

			var id = $(this).closest('tr').data('id');

			if(id === undefined)
				id = '';

			$.ajax({
				type: "POST",
				url: "<?= $_SERVER['PHP_SELF'] ?>",
				data: { function: 'CouponDialogOpen', id: id },
				dataType: 'json',
			}).done(function(msg){
				// console.log(msg);

				$('#CouponDialog').html(msg.html);
				$('#CouponDialog').closest('.ui-dialog').find('.ui-dialog-title').html(msg.title);
				dialog.dialog( "open" );
				tinymce.init({
					selector: "textarea",
					setup: function (editor) {
						editor.on('change', function () {
							tinymce.triggerSave();
						});
					}
				});

			});
		});

		$('#CouponDialog').on('change', '#application', function(){

			var selected = $(this).val();
			var coupon_id = $('#CouponDialog').find('.hiddenID').val();

			$.ajax({
				type: "POST",
				url: "<?= $_SERVER['PHP_SELF'] ?>",
				data: { function: 'fetchValidForData', id: coupon_id, selected: selected },
				dataType: 'json',
			}).done(function(msg){
				$('#CouponDialog').find('#application_options').html(msg.html);
			});
		});

		/***** SEARCH *****/
		$('#CouponDialog').on('keyup', '.FindProduct', function() {
			var contentid = $(this).data('contentid');
			var search = $(this).val();
			var img_path = $(this).data('img_path');

			$.ajax({
				type: "POST",
				url: "<?= $_SERVER['PHP_SELF'] ?>",
				data: { search: search, img_path: img_path }
			}).done(function( msg ) {
				$('#'+contentid).html(msg);
			});		
		});

		/***** SELECT PRODUCT *****/
		$('#CouponDialog').on('click', '.SortItem', function(){
			console.log(this);

			var productId = $(this).find('.Product').data('product');
			var inputVar = '<input type="hidden" name="Product" value="'+productId+'">';
			var changeButton = '<button class="ChangeProduct">Change Product</button>';

			$('#application_options').html($(this));
			$(inputVar).appendTo('#application_options');
			$(changeButton).appendTo('#application_options');
		});
			/** SELECT NEW PRODUCT **/
		$('#CouponDialog').on('click', 'button.ChangeProduct', function(e){
			e.preventDefault();
			e.stopPropagation();
			$('#CouponDialog').find('#application').trigger('change');
			return false;
		});

		/***** DELETE CHECKBOX *****/
		$('#CouponDialog').on('click', 'input:checkbox#delete', function(){
			if($(this).is(':checked'))
				$(this).closest('.Delete').addClass('ui-state-error');
			else
				$(this).closest('.Delete').removeClass('ui-state-error');
		});		

		$('td').on('click', '.toggleCouponStatus', function(){
			var clicked = $(this).closest('td');
			var coupon_id = $(this).closest('tr').data('id');

			if($(this).data('status') == 'Active'){
				var alert_title = 'Disable Coupon';
				var alert_text = 'This coupon will be turned off and will no longer work.';
				var button = '<button class="toggleCouponStatus">inactive</button>';
				var newStatus = 'inactive';
			}
			else{
				var alert_title = 'Activate Coupon';
				var alert_text = 'This coupon will be activated and can be used during checkout.';
				var button = '<button class="toggleCouponStatus" data-status="Active"><span class="ui-icon"></span>ACTIVE</button>';
				var newStatus = 'Active';
			}

			$('<div></div>').appendTo('body').html('<h3>'+alert_text+'</h3>').dialog({
				modal: true, title: alert_title, zIndex: 10000, autoOpen: true,
				width: 'auto', resizable: false,
				buttons: {
					Confirm: function () {

						$(clicked).html(button);

						$.ajax({
							type: "POST",
							url: "<?= $_SERVER['PHP_SELF'] ?>",
							data: { function: 'toggleCouponStatus', id: coupon_id, status: newStatus },
							// dataType: 'json',
						}).done(function(msg){
							console.log(msg);
						});

						$('body').append('<h1>Confirm Dialog Result: <i>Yes</i></h1>');

						$(this).dialog("close");
					},
					Cancel: function () {
						$('body').append('<h1>Confirm Dialog Result: <i>No</i></h1>');
						$(this).dialog("close");
					}
				},
				close: function (event, ui) {
					$(this).remove();
				}
			});
		});

		//Table Sort
		$('table').on('click', 'th', function(){
			var thisEQ = $(this).index();
			var $tbody = $(this).closest('table tbody');
			var hRow = $(this).closest('tr');

			if($(this).hasClass('asc')){
				$tbody.find('tr').sort(function(a,b){ 
					var tda = $(a).find('td:eq('+thisEQ+')').text(); // can replace 1 with the column you want to sort on
					var tdb = $(b).find('td:eq('+thisEQ+')').text(); // this will sort on the second column
						 // if a < b return 1
					return tda < tdb ? 1 
						// else if a > b return -1
						: tda > tdb ? -1 
						// else they are equal - return 0	
						: 0;		   
				}).appendTo($tbody);
			}
			else{
				$tbody.find('tr').sort(function(a,b){ 
					var tda = $(a).find('td:eq('+thisEQ+')').text(); // can replace 1 with the column you want to sort on
					var tdb = $(b).find('td:eq('+thisEQ+')').text(); // this will sort on the second column
						 // if a < b return 1
					return tda > tdb ? 1 
						// else if a > b return -1
						: tda < tdb ? -1 
						// else they are equal - return 0	
						: 0;		   
				}).appendTo($tbody);			
			}		

			$(hRow).prependTo($tbody);
			if($(this).hasClass('asc') || $(this).hasClass('desc')){
				$(this).toggleClass('asc desc');
			}
			else{
				$('.asc').removeClass('asc');
				$('.desc').removeClass('desc');
				$(this).addClass('asc');
			}
		});
	});
</script>

<div class="Label">Manage Coupons and Promotion Codes</div>

<div class="CreateCoupon">
	<button class="dialogCreateCoupon"><span class="ui-icon ui-icon-plusthick"></span> Create New Coupon</button>
</div>

<div class="LabelInsert">
	<div class="SubTitle">Current Coupon/Promo Codes</div>

	<table id="Codes">
		<tr>
			<th>Code</th>
			<th>Discount</th>
			<th>Applies to</th>
			<th>Description</th>
			<th>Edit</th>
			<th>Current Status</th>
		</tr>
	<?
		$coupons = $mysqli->query("SELECT * FROM $database WHERE type='Coupon'");
		if($coupons->num_rows > 0){
			while($coupon = $coupons->fetch_assoc()){

				$coupon['info'] = createArray($coupon['info']);
				if($coupon['name'] == '') continue;
				?>
				<tr data-id="<?= $coupon['id'] ?>" class="Update">
					<td><?= $coupon['name'] ?></td>
					<td>
						<?= ($coupon['info']['Discount_Type'] == 'percent') ? $coupon['info']['Discount'].'%' : '$'.$coupon['info']['Discount'] ?>
					</td>
					<td>
						<?
							if($coupon['info']['Application'] == 'global')
								echo 'Sitewide';
							if($coupon['info']['Application'] == 'category'){
								$cat = getData($coupon['info']['Category']);
								echo 'Category: '.$cat['name'];
							}
							if($coupon['info']['Application'] == 'product' && isset($coupon['info']['Product'])){
								$item = getData($coupon['info']['Product']);
								// pre($item);
								echo 'Product: 	<a href="/admin/manage.php?id='.$item['id'].'">'.$item['name'].'</a>';
							}
						?>
					</td>
					<td><?= htmlspecialchars_decode($coupon['info']['Description']) ?></td>
					<td align="center">
						<button class="editCoupon"><img src="/admin/images/options.png"></button>
					</td>
					<td align="center" class="status">
						<? 
							if($coupon['misc'] == 'Active')
								echo '<button class="toggleCouponStatus" data-status="Active"><span class="ui-icon"></span>ACTIVE</button>';
							else
								echo '<button class="toggleCouponStatus">inactive</button>';
						?>
					</td>
				</tr>
				<?
			}
		}
	?>
	</table>
</div>




<div class="LabelInsert CreateCoupon" id="CouponDialog"></div>