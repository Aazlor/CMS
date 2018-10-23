<?php

require('login_check.php');
require('db_connect.php');
require('config_site_info.php');
include('config.php');
#error_reporting(0);

?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo $site_name; ?> Admin Control Panel</title>

<link rel="stylesheet" href="/admin/style.css" type="text/css" media="all">
<link rel="stylesheet" href="stylesheet.css" type="text/css" media="all" />

<script src="/js/jquery-ui-1.10.3/jquery-1.9.1.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.core.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.widget.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.mouse.js"></script>
<script src="/js/jquery-ui-1.10.3/ui/jquery.ui.sortable.js"></script>

<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
<script type="text/javascript">
tinymce.init({
    selector: "textarea",
    plugins : 'advlist autolink link image lists charmap print preview emoticons table wordcount',
    browser_spellcheck : true
 });

$(document).ready(function() {
	$('.number').blur(function(){
		if($.isNumeric($(this).val()) == true){
			$(this).css('border', '1px solid #CDCED0');		
		}
		else{
			$(this).focus();
			$(this).css('border', '2px solid #cc0000');
		}
	});

	function execTogglebox(toggleCheckBox){
		console.log(toggleCheckBox)
		var checked = $(toggleCheckBox).data('checked');
		var unchecked = $(toggleCheckBox).data('unchecked');
		if($(toggleCheckBox).prop('checked')){
			$('.Wrapper-'+checked).show();
			$('.Wrapper-'+unchecked).hide();
		}
		else{
			$('.Wrapper-'+checked).hide();
			$('.Wrapper-'+unchecked).show();
		}		
	}

	$('.togglebox').each(function(){
		execTogglebox(this);
	});

	$('.togglebox').on('change', function(){
		execTogglebox(this);
	});
});
</script>

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
							<a href="manage_pages.php?y='.$get_page[0].'"><span>'.$get_page[2].'</span></a>
							<ul>
								<li><a href="/admin/manage_subpages.php?p='.$get_page[0].'&x=add">Add Page</a></li>
								<li><a href="/admin/manage_subpages.php?p='.$get_page[0].'&x=edit">Edit Page</a></li>
								<li><a href="/admin/manage_subpages.php?p='.$get_page[0].'&x=remove">Remove Page</a></li>
								'. $nothing /*	<li><a href="/admin/manage_subpages.php?p='.$get_page[0].'&x=sort">Sort Page</a></li>	*/.'
							</ul>
						</li>						
						';
					}
					else{
						echo'
							<li><a href="manage_pages.php?y='.$get_page[0].'"><span>'.$get_page[2].'</span></a></li>
						';
					}
				}
			}
?>
			
			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>

			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=categories"><span>Categories</span></a></li>

<? /*			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=tags"><span>Tags</span></a></li>		*/ ?>
							
			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=products"><span>Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=groupedproducts"><span>Grouped Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=featuredproducts"><span>Featured Products</span></a></li>
			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=coupons"><span>Coupons</span></a></li>

<? /*
			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=cart_options"><span>Cart Options</span></a></li>

			<li class="menupop myaccount"><a href="/admin/manage_ecom.php?section=shipping_options"><span>Shipping Options</span></a></li>
*/?>

			<div style="height: 2px; width: 100%; border-top: 2px solid #999; margin-top: -1px; display: block;"></div>
											
			<li class="Borderless"><a href="settings.php"><span>Settings</span></a></li>
		</div>
		
		<div id="Content">
