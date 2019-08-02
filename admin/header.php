<!DOCTYPE html>
<html>
<head>
<title><?php echo $site_name; ?> Admin Control Panel</title>

<!-- JS/CSS INCLUDES -->
<script src="../js/jquery-3.3.1.min.js"></script>
<script src="../js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<link rel="stylesheet" href="../js/jquery-ui-1.12.1.custom/jquery-ui.min.css" type="text/css" media="all" />
<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/jquery-form/form@4.2.2/dist/jquery.form.min.js" integrity="sha384-FzT3vTVGXqf7wRfy8k4BiyzvbNfeYjK+frTVqZeNDFl8woCbF0CYG6g2fMEFFo/i" crossorigin="anonymous"></script>

<!-- CUSTOM JS/CSS -->
<link rel="stylesheet" href="/admin/style.css" type="text/css" media="all">
<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />

<script type="text/javascript" src="js.js"></script>

</head>
<body>

<div id="Container">

	<div id="Header" style="position: relative;">

		<div class="Left Logo"><a href="/admin"><img src="/site/<?= $logo ?>" style="max-width: 600px;"> <img src="/site/one-hudson-road.gif" alt="<?= $site_name ?>" title="<?= $site_name ?>" class="Text"></a></div>
		<div style="position: absolute; bottom: 10px; right: 0;">Administrative Content Management System  |  <a href="log_out.php">Logout</a></div>
		<div class="Clear"></div>
	</div>
	
	<div id="ContentContainer">
		<div id="Navigation">
			
	<?php
			if($check = $mysqli->query("SELECT * FROM $database WHERE type='pages' LIMIT 1")){
				$get_pages = $mysqli->query("SELECT * FROM $database WHERE type='pages' ORDER BY name ASC");
				while($get_page = $get_pages->fetch_array()){
					$check = preg_replace('/ /', '', $get_page[2]);
					if(in_array("$check", $subtemplates)){
						echo'
						<li>
							<a href="manage.php?id='.$get_page[0].'"><span>'.$get_page[2].'</span></a>
							<ul>
								<li><a href="/admin/manage.php?p='.$get_page[0].'&x=add">Add Page</a></li>
								<li><a href="/admin/manage.php?p='.$get_page[0].'&x=edit">Edit Page</a></li>
								<li><a href="/admin/manage.php?p='.$get_page[0].'&x=remove">Remove Page</a></li>
								'. $nothing /*	<li><a href="/admin/manage.php?p='.$get_page[0].'&x=sort">Sort Page</a></li>	*/.'
							</ul>
						</li>						
						';
					}
					else{
						echo'
							<li><a href="manage.php?id='.$get_page[0].'"><span>'.$get_page[2].'</span></a></li>
						';
					}
				}
			}
?>
			
			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>

			<li class="menupop myaccount"><a href="/admin/manage_categories.php"><span>Categories</span></a></li>

<? /*			<li class="menupop myaccount"><a href="/admin/manage.php?section=tags"><span>Tags</span></a></li>		*/ ?>
							
			<li class="menupop myaccount"><a href="/admin/products.php"><span>Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/product_groups.php"><span>Product Groups</span></a></li>
			<li class="menupop myaccount"><a href="/admin/product_sort.php"><span>Sort Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/custom_featured_products.php"><span>Featured Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/manage_coupons.php"><span>Coupons</span></a></li>

<? /*
			<li class="menupop myaccount"><a href="/admin/manage.php?section=cart_options"><span>Cart Options</span></a></li>

			<li class="menupop myaccount"><a href="/admin/manage.php?section=shipping_options"><span>Shipping Options</span></a></li>
*/?>

			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>

			<li class="Borderless"><a href="settings.php"><span>Settings</span></a></li>
		</div>
		
		<div id="Content">
			<div id="Dialog"></div>
