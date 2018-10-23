<!DOCTYPE html>
<html>
<head>
<title><?php echo $site_name; ?> Admin Control Panel</title>

<link rel="stylesheet" href="/admin/aa_style.css" type="text/css" media="all">
<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />

<script src="/js/jquery-ui-1.10.3/jquery-1.9.1.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.widget.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.mouse.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.sortable.js"></script>

<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
<script type="text/javascript" src="aa_js.js"></script>

</head>
<body>

<div id="Container">

	<div id="Header" style="position: relative;">

		<div class="Left"><a href="/admin"><img src="/site/logo.jpg" style="max-width: 600px;"></a></div>
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
							<a href="aa_manage.php?id='.$get_page[0].'"><span>'.$get_page[2].'</span></a>
							<ul>
								<li><a href="/admin/aa_manage.php?p='.$get_page[0].'&x=add">Add Page</a></li>
								<li><a href="/admin/aa_manage.php?p='.$get_page[0].'&x=edit">Edit Page</a></li>
								<li><a href="/admin/aa_manage.php?p='.$get_page[0].'&x=remove">Remove Page</a></li>
								'. $nothing /*	<li><a href="/admin/aa_manage.php?p='.$get_page[0].'&x=sort">Sort Page</a></li>	*/.'
							</ul>
						</li>						
						';
					}
					else{
						echo'
							<li><a href="aa_manage.php?id='.$get_page[0].'"><span>'.$get_page[2].'</span></a></li>
						';
					}
				}
			}
?>
			
			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>

			<li class="menupop myaccount"><a href="/admin/aa_manage_categories.php"><span>Categories</span></a></li>

<? /*			<li class="menupop myaccount"><a href="/admin/aa_manage.php?section=tags"><span>Tags</span></a></li>		*/ ?>
							
			<li class="menupop myaccount"><a href="/admin/aa_products.php"><span>Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/aa_products_groups.php"><span>Grouped Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/aa_custom_featured_products.php"><span>Featured Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/aa_manage.php?section=coupons"><span>Coupons</span></a></li>

<? /*
			<li class="menupop myaccount"><a href="/admin/aa_manage.php?section=cart_options"><span>Cart Options</span></a></li>

			<li class="menupop myaccount"><a href="/admin/aa_manage.php?section=shipping_options"><span>Shipping Options</span></a></li>
*/?>

			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>
											
			<li class="Borderless"><a href="aa_settings.php"><span>Settings</span></a></li>
		</div>
		
		<div id="Content">
