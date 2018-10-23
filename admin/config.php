<?php


$templates = array(
	0 => 'Home',
	1 => 'About',
	2 => 'Contact',
	3 => 'FAQ',
	4 => 'Blog',
	5 => 'Basic',
	6 => 'QA',
	7 => 'Search',
	8 => 'Steals_and_Deals',
);

$Home = array(
	'File' => "home.php",
	'Scrolling_Images' => '{gallery}',
	'Signup_Text' => "{textarea}",
);

$About = array (
	'File' => "basic.php",
	'Left_Image{image}' => array(
		'medium' => array(
			'width' => 350,
			'height' => '',
		),
	),
	'{details}1' => 'Image is 350px wide and auto sizes in hieght.  Image not required.',
	'Content' => "{textarea}",
	'Bottom_Banner{image}' => array(
		'medium' => array(
			'width' => 1200,
			'height' => 150,
		),
	),
	'{details}2' => 'Banner is 1200px wide with a hieght of 150px.  Image not required.',
);

$Contact = array(
	'File' => 'contact.php',
	'Content' => '{textarea}',
);

$FAQ = array(
	'File' => "faq.php",
	'FAQ' => '{gallery}',
	'Content' => "{textarea}",
);

$Blog = array(
	'File' => "blog.php",
	'News' => '{gallery}',
	'Content' => "{textarea}",
);

$Basic = array(
	'File' => 'basic.php',
	'Title' => '{text}',
	'Content' => '{textarea}',
);

$QA = array(
	'File' => "qa.php",
	'Content' => "{textarea}",
);

$Search = array(
	'File' => "search.php",
	// 'Content' => "{textarea}",
);

$Steals_and_Deals = array(
	'File' => 'steals_and_deals.php',
	'Homepage_Giveaway_Image{image}' => [
		'medium' => [ 
			'maxwidth' => '', 'maxheight' => ''
		],
	],
	'Giveaway_Product_Images' => '{gallery}',
	'Giveaway_Product_Name' => '{text}',
	'Giveaway_Product_Description' => '{textarea}',
	'Giveaway_Product_Price' => '{number}',
	'Giveaway_Product_Price{details}' => 'Numbers Only.  This field will not let you continue if you put a non-number value.',	
	'1{details}' => '<br><br><br>',	
	'Contest_Rules' => '{textarea}',
	'Post_Signup' => '{textarea}',
	'Giveaway_Active{checkbox}' => array(
		'Yes' => 'Yes',
	),
	'Giveaway_Month{select}' => array(
		'Jan' => 'Jan',
		'Feb' => 'Feb',
		'Mar' => 'Mar',
		'Apr' => 'Apr',
		'May' => 'May',
		'Jun' => 'Jun',
		'Jul' => 'Jul',
		'Aug' => 'Aug',
		'Sep' => 'Sep',
		'Oct' => 'Oct',
		'Nov' => 'Nov',
		'Dec' => 'Dec',
	),
	'Giveaway_Year' => '{number}',
);

/***** Products *****/
$Product_Vars = array(
	'Name' => '{text}',
	'Stock_Number' => '{text}',
	'Select_Categories' => '{categories}',
	'Product_Images' => '{gallery}',
	'Description' => '{textarea}',
	'This_Product_Has_Options{togglebox}' => array(
		'checked' => 'Options',
		'unchecked' => 'Price'
	),	
	'This_Product_Has_Options{details}' => '<p>Check the box above if a product has multiple sizes, different colors, different prices, or some other variant.</p><p>This will open a tab called "Pricing" just underneath product photos.</p>',
	// 'Options' => '{gallery}',	
	'Price' => '{number}',
	'Price{details}' => 'Numbers Only.  This field will not let you continue if you put a non-number value.',
	'Verbage_Price' => '{textarea}',
	'Verbage_Price{details}' => 'This is the price text that will display under the product thumbnails.',
	'Pricing' => '{gallery}',
	// 'Show_on_Homepage{checkbox}' => array(
	// 	'Yes' => 'Yes',
	// ),
	'Filler{checkbox}' => array(
		'Yes' => 'Yes',
	),
	'Filler_Price_Area_Text' => '{text}',
	'Out_of_Stock{checkbox}' => array(
		'Yes' => 'Yes',
	),
);

$featured_product_limit = 20;

/***** Galleries *****/
# Medium has to be the last size in the sequence since it modifies the original file based on filename.

$Gallery__Product_Images = array(
	'Type' => 'Image',
	'Image{image}' => array(
		'thumb' => array(
			'maxwidth' => 190,
			'maxheight' => 190,
		),
		'large' => array(
			'maxwidth' => 1000,
			'maxheight' => 1000,
		),
		'medium' => array(
			'maxwidth' => 370,
			'maxheight' => 370,
		),
	),
);

$Gallery__Giveaway_Product_Images = array(
	'Type' => 'Image',
	'Image{image}' => array(
		'thumb' => array(
			'maxwidth' => 190,
			'maxheight' => 190,
		),
		'large' => array(
			'maxwidth' => 1000,
			'maxheight' => 1000,
		),
		'medium' => array(
			'maxwidth' => 370,
			'maxheight' => 370,
		),
	),
);

$Gallery__Options = array(
	'Type' => 'Option',
	'Name' => '{text}',
	'Description' => '{textarea}',
	'Price_Alteration' => '{text}',
	'Price_Alteration{details}' => '<p>Enter the price <i>difference</i> between the base price and the price of this option.<br>This can be a flat amount or a percent.  <br> Examples: $1 increase type "+1.00" <br>$.50 decrease enter "-0.50"<br>10% decrease type "-10%"<br>Without the quotes<br>If none, enter 0</p>',
);

$Gallery__Pricing = array(
	'Type' => 'Option',
	'Name' => '{text}',
	'Description' => '{textarea}',
	'Price' => '{number}',
	'Price{details}' => '<p>Enter the price for this product</p>',
);

$Gallery__Sizes = array(
	'Type' => 'Size',
	'Name' => '{text}',
	'Price_Alteration' => '{text}',
	'Price_Alteration{details}' => '<p>Enter the price <i>difference</i> between the base price and the price of this option.<br>This can be a flat amount or a percent.  <br> Examples: $1 increase type "+1.00" <br>$.50 decrease enter "-0.50"<br>10% decrease type "-10%"<br>Without the quotes<br>If none, enter 0</p>',
);

$Gallery__Scrolling_Images = array(
	'Type' => 'Scrolling Image',
	'Image{image}' => array(
		'small' => array(
			'maxwidth' => '',
			'maxheight' => 200,
		),
		'medium' => array(
			'maxwidth' => '',
			'maxheight' => '',
		),
	),
	'Description' => '{textarea}',
	'{details}1' => 'Will be text shown when image is hovered over.',
	'URL' => '{text}',
	'{details}2' => 'This is the url path to where you want this image to link to.  This can be an article, product or anything else, and is not required.',
);

$Gallery__FAQ = array(
	'Type' => 'FAQ',
	'Question' => '{text}',
	'Answer' => '{textarea}',
);

$Gallery__News = array(
	'Type' => 'News',
	'Title' => '{text}',
	'Image{image}' => array(
		'medium' => array(
			'width' => 350,
			'height' => '',
		),
	),
	'{details}1' => 'Image is 350px wide and auto sizes in hieght.  Image not required.',
	'Date' => '{text}',
	'Article' => '{textarea}',
);



/***** Sub Templates *****/

$subtemplates = array(
	0 => 'BragBook',
	1 => '',
	
);

$Sub_BragBook = array(
	'File' => "bragbook_gallery.php",
	'Title' => "{text}",
	'Content' => "{textarea}",
	'PhotoGallery' => "{gallery}",
);
?>