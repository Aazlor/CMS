<?php

function pre($array){
	echo'<pre>';
	print_r($array);
	echo'</pre>';
}

require('login_check.php');
require('db_connect.php');
require('config_site_info.php');
include('config_page_templates.php');
include('config_product_settings.php');
error_reporting(0);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="en"><head>
<title></title>

<link rel="stylesheet" href="/admin/style.css" type="text/css" media="all">

<script type="text/javascript">
function showNav(el) { el.getElementsByTagName('UL')[0].style.left='auto'; }
function hideNav(el) { el.getElementsByTagName('UL')[0].style.left='-999em'; }
function pressthis(step) {if (step == 1) {if(navigator.userAgent.indexOf('Safari') >= 0) {Q=getSelection();}else {if(window.getSelection)Q=window.getSelection().toString();else if(document.selection)Q=document.selection.createRange().text;else Q=document.getSelection().toString();}} else {location.href='http://lilbnyfufu.wordpress.com/wp-admin/post-new.php?text='+encodeURIComponent(Q.toString())+'&amp;popupurl='+encodeURIComponent(location.href)+'&amp;popuptitle='+encodeURIComponent(document.title);}}
</script>
<script type="text/javascript">
var ids=new Array('cats','products','pages');

function switchid(id){	
	hideallids();
	showdiv(id);
}

function hideallids(){
	for (var i=0;i<ids.length;i++){
		hidediv(ids[i]);
	}		  
}

function hidediv(id) {
	if (document.getElementById) {
		document.getElementById(id).style.display = 'none';
	}
	else {
		if (document.layers) {
			document.id.display = 'none';
		}
		else {
			document.all.id.style.display = 'none';
		}
	}
}
function showdiv(id) {		  
	if (document.getElementById) { // DOM3 = IE5, NS6
		document.getElementById(id).style.display = 'block';
	}
	else {
		if (document.layers) { // Netscape 4
			document.id.display = 'block';
		}
		else {
			document.all.id.style.display = 'block';
		}
	}
}
</script>

<script type="text/javascript" src="/admin/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	// General options
		mode : "textareas",
	theme : "advanced",
	plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",

<?php /*
	// Theme options
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	
	// Office example CSS
	content_css : "css/office.css",

	// Drop lists for link/image/media/template dialogs
	template_external_list_url : "js/template_list.js",
	external_link_list_url : "js/link_list.js",
	external_image_list_url : "js/image_list.js",
	media_external_list_url : "js/media_list.js",

*/

	$theme_buttons = '
	theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,formatselect,fontselect,fontsizeselect",
	theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview",
	theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
	theme_advanced_buttons4 : "styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		theme_advanced_font_sizes : "Font Size 8px=8px,Font Size 10px=10px,Font Size 12px=12px,Font Size 14px=14px,Font Size 16px=16px,Font Size 18px=18px,Font Size 20px=20px,Font Size 22px=22px",
	';

?>

	// Theme options
<?php	echo $theme_buttons;	?>

	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,

	// Replace values for the template plugin
	template_replace_values : {
		username : "Some User",
		staffid : "991234"
	}
});
</script>
<link rel="stylesheet" type="text/css" href="/design/jquery.fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="/design/jquery.fancybox/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/design/jquery.fancybox/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="/design/jquery.fancybox/jquery.fancybox-1.2.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a.enlarge").fancybox();
	});
</script>

</head>
<body>
<div id="Container">

	<div id="Header">

		<div class="Left"><img src="/design/logo_home.jpg"></div>
		<div class="Right">Administrative Content Management System  |  <a href="">Logout</a></div>
		<div class="Clear"></div>

	</div>
	
	<div id="ContentContainer">
		<div id="Navigation">
			<li><a href="manage_categories.php"><span>Categories</span></a></li>
			<li><a href="manage_products.php"><span>Products</span></a></li>
						
	<?php
			$check = $mysqli->query("SELECT * FROM $database WHERE type='pages' LIMIT 1");
			if($check = mysql_fetch_row($check)){
				$get_pages = $mysqli->query("SELECT * FROM $database WHERE type='pages'");
				while($get_page = mysql_fetch_row($get_pages)){
					echo'
						<a href="admin/manage_homepage.php"><span>'.$get_page[2].'</span></a>
						';
					}
				}
	?>					
			<li><a href="settings.php"><span>Settings</span></a></li>
		</div>
		
		<div id="Content">
