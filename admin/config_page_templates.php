<?php


$templates = array(
	0 => 'Standard',
	1 => 'Home',
	2 => 'Footer',
	3 => 'About',
	4 => 'Collection',
	5 => 'Gallery',
	6 => 'Collection',
	7 => 'Contact',
	8 => 'Designers',
	9 => 'InStock',
);

$Standard = array(
	'File' => "basic.php",
	'Content' => "{textarea}",
);

$Designers = array(
	'File' => "designers.php",
	'Designers' => "{photogallery}",
);

$Home = array(
	'File' => "home.php",
	'Fading_Images' => "{photogallery}",
	'Photobox_Narrow{image}' => array(
		medium => array(
			width => "1080",
			height => "192"
		),
	),
	'Copyright_Left_Title' => "{text}",
	'Copyright_Left_Content' => "{textarea}",
	'Copyright_Right_Title' => "{text}",
	'Copyright_Right_Content' => "{textarea}",
);

$Footer = array(
	'File' => "footer.php",
	'Left_Title' => "{text}",
	'Left_Content' => "{textarea}",
	'Right_Title' => "{text}",
	'Right_Content' => "{textarea}",
);

$About = array(
	'File' => "about.php",
	'Top_Title' => "{text}",
	'Top_Content' => "{textarea}",
	'Middle_Title' => "{text}",
	'Middle_Content' => "{textarea}",
	'Bottom_Title' => "{text}",
	'Bottom_Content' => "{textarea}",
/*
	'Photo{image}' => array(
		medium => array(
			width => "546",
			height => ""
		),
	),	
	'SubPhoto{image}' => array(
		medium => array(
			width => "546",
			height => ""
		),
	),	
*/
	'Collage_Top_Left{image}' => array(
		medium => array(
			width => "238",
			height => "379"
		),
	),	
	'Collage_Top_Right{image}' => array(
		medium => array(
			width => "303",
			height => "379"
		),
	),	
	'Collage_Bottom_Left{image}' => array(
		medium => array(
			width => "238",
			height => "425"
		),
	),	
	'Collage_Bottom_Right{image}' => array(
		medium => array(
			width => "303",
			height => "425"
		),
	),	

);

$Collection = array(
	'File' => "collection.php",
	'Content' => "{textarea}",
	'Contemporary_Image{image}' => array(
		medium => array(
			width => "390",
			height => "300"
		),
	),
	'Contemporary_Description' => '{textarea}',
	'Transitional_Image{image}' => array(
		medium => array(
			width => "390",
			height => "300"
		),
	),
	'Transitional_Description' => '{textarea}',
	'Moroccan_Image{image}' => array(
		medium => array(
			width => "390",
			height => "300"
		),
	),
	'Moroccan_Description' => '{textarea}',
	'Commercial_Image{image}' => array(
		medium => array(
			width => "390",
			height => "300"
		),
	),
	'Commercial_Description' => '{textarea}',
//	'Collection' => "{photogallery}",
);

$Gallery = array(
	'File' => "gallery.php",
	'Gallery' => "{photogallery}",
);

/*
$Collection = array(
	'File' => "Collection.php",
);
*/

$Contact = array(
	'File' => "contact.php",
	'Content' => "{textarea}",
	'Backgrounds' => "{photogallery}",
	'Locations' => "{photogallery}",
);

$InStock = array(
	'File' => 'in-stock.php',
	'Content' => "{textarea}",
	'InStock' => "{photogallery}",
);




/***** Sub Templates *****/

$subtemplates = array(
	0 => 'Collection',	
);

$Sub_Collection = array(
	'File' => "collections.php",
	'Title' => "{text}",
	'Collection_Blurb' => "{textarea}",
	'Category{select}' => array(
		0 => 'Contemporary',
		1 => 'Transitional',
		2 => 'Moroccan',
		3 => 'Commercial',
	),
	'Product' => "{photogallery}",
);
?>