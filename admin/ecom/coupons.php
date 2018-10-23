<?

if($_POST[submit] == 'Create Coupon'){
	$skip = array('Name', 'Discount', 'submit_coupon', 'submit');
	foreach($_POST as $k => $v){
		if(!in_array($k, $skip))
			$info .= '{{}}'.$k.'(())'.$v;
		else
			$_POST[$k] = mysql_escape_string($v);
	}
	$info = mysql_escape_string($info);
	$query = "INSERT INTO $database (type, name, relation, info) VALUES ('Coupon', '$_POST[Name]', '$_POST[Discount]', '$info')";
	if($mysqli->query($query))
		echo ('<meta http-equiv="refresh" content="0;">');
	else
		echo mysql_error();
}
if($_POST[update_coupon] == 'yes'){
	$skip = array('Name', 'Discount', 'update_coupon', 'submit', 'id');
	foreach($_POST as $k => $v){
		if(!in_array($k, $skip))
			$info .= '{{}}'.$k.'(())'.$v;
		$info = mysql_escape_string($info);
	}
	$query = "UPDATE $database SET name='$_POST[Name]', relation='$_POST[Discount]', info='$info' WHERE id='$_POST[id]'";
	if($mysqli->query($query))
		echo ('<meta http-equiv="refresh" content="0;">');
	else
		echo mysql_error();
}

?>

<div class="Title">
	<?= $site_name; ?> Coupons/Promo Codes
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
</style>

<script type="text/javascript">
	$(document).ready(function(){
		$('.Label.CreateCoupon').click(function(){
			$('.LabelInsert.CreateCoupon').toggle('slow');
			$('.LabelInsert.CreateCoupon').find('input').each(function(){
				$(this).val('');
			});
			tinyMCE.activeEditor.setContent('');
			$('.LabelInsert.CreateCoupon').find('input[type="hidden"]').attr('name', 'create_coupon');
			$('.LabelInsert.CreateCoupon').find('input[type="submit"]').val('Create Coupon');
		});

		$('tr.Update').dblclick(function(){
			$('.LabelInsert.CreateCoupon').fadeOut('fast');
			$('.LabelInsert.CreateCoupon').fadeIn('slow');
			$('.LabelInsert.CreateCoupon').find('input[type="hidden"]').attr('name', 'update_coupon');
			$('.LabelInsert.CreateCoupon').find('input[type="submit"]').val('Update Coupon');

			$('.LabelInsert.CreateCoupon').find('input[name="Name"]').val($(this).find('td:eq(0)').text());
			$('.LabelInsert.CreateCoupon').find('input[name="Discount"]').val($(this).find('td:eq(1)').text());
			tinyMCE.activeEditor.setContent($(this).find('td:eq(2)').text());
			$('.LabelInsert.CreateCoupon').find('input[name="Start"]').val($(this).find('td:eq(3)').text());
			$('.LabelInsert.CreateCoupon').find('input[name="End"]').val($(this).find('td:eq(4)').text());
			$('.LabelInsert.CreateCoupon').find('.Submit').prepend('<input type="hidden" name="id" value="'+$(this).data('id')+'">')
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

<div class="Label CreateCoupon">
	Create Coupon/Promo Code
</div>
<div class="LabelInsert CreateCoupon">
	<form method="POST" enctype="multipart/form-data" action="/admin/manage_ecom.php?section=coupons">
		<div class="Label">Code</div>
		<div class="LabelInsert"><input type="text" name="Name" value="" class="text"></div>

		<div class="Label">Discount</div>
		<div class="LabelInsert"><input type="text" name="Discount" value="" class="text"></div>
		<div class="Details"><p>This can be a flat amount or a percent.<br>For flat amount use numbers only. Ex: $20 discount, just enter 20.<br>For percentage enter the number followed by %.  Ex: 10%.</p></div>

		<div class="Label">Description</div>
		<div class="LabelInsert"><textarea name="Description"></textarea></div>

		<div class="Label">Available Date</div>
		<div class="LabelInsert"><input type="date" name="Start" value="" class="text"></div>

		<div class="Label">Experation Date</div>
		<div class="LabelInsert"><input type="date" name="End" value="" class="text"></div>
		
		<div class="Submit">
			<input type="hidden" name="submit_coupon" value="yes">
			<input type="submit" name="submit" value="Create Coupon">
		</div>
	</form>
</div>


<div class="LabelInsert">
	<div class="SubTitle">Current Coupon/Promo Codes</div>
	<div class="Details"><p>Double click on a coupon row to edit.</p></div>

	<table id="Codes">
		<tr>
			<th>Code</th>
			<th>Discount</th>
			<th>Description</th>
			<th>Available</th>
			<th>Expires</th>
		</tr>
	<?
		$coupons = $mysqli->query("SELECT * FROM $database WHERE type='Coupon'");
		$count = mysql_num_rows($coupons);
		if($count > 0){
			while($coupon = mysql_fetch_assoc($coupons)){
				$split = explode('{{}}', $coupon[info]);
				foreach($split as $v){
					$s = explode('(())', $v);
					$coupon[$s[0]] = $s[1];
				}
				?>
				<tr data-id="<?= $coupon[id] ?>" class="Update">
					<td><?= $coupon[name] ?></td>
					<td><?= $coupon[relation] ?></td>
					<td><?= $coupon[Description] ?></td>
					<td><?= $coupon[Start] ?></td>
					<td><?= $coupon[End] ?></td>
				</tr>
				<?
			}
		}
	?>
	</table>
</div>