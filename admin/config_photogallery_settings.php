<?php

$Files__News = array (
	'Type' => "Article",
	'Date' => "{text}",
	'Content' => "{textarea}",
	'File' => "{file}",
);

$Files__Documents = array (
	'Type' => "Document",
	'Title' => "{text}",
	'File' => "{file}",
);

/*
$Gallery__Collection = array (
	'Type' => "Collection",
	'Image{image}' => 	array(
		medium => array(
			width => "420",
			height => "130"
		),
	),
	'Rollover_Image{image}' => 	array(
		medium => array(
			width => "420",
			height => "130"
		),
	),
	'Name' => "{text}",
	'Description' => "{textarea}",
);
*/

$Gallery__Gallery = array (
	'Type' => "Photo",
	'Image{image}' => 	array(
		medium => array(
			maxwidth => "1080",
			maxheight => "900"
		),
	),
	'Collection_Info_Url' => "{text}",
);

$Gallery__Fading_Images = array (
	'Type' => "Image",
	'Image{image}' => 	array(
		medium => array(
			width => "1080",
			height => ""
		),
	),
);

$Gallery__Product = array(
	'Type' => "Swatch",
	'Thumbnail{image}' => 	array(
		medium => array(
			width => "130",
			height => ""
		),
	),
	'Swatch_Name' => "{text}",
	'Designed_by' => "{text}",
	'Rug_Main_Image{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Rug_Additional_Image_1{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Rug_Additional_Image_2{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Rug_Additional_Image_3{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Rug_Additional_Image_4{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Rug_Additional_Image_5{image}' => 	array(
		medium => array(
			width => "579",
			height => ""
		),
		small => array(
			width => "49",
			height => ""
		),
	),
	'Tear_Sheet' => "{file}",
	'Rug_Care_Instructions' => "{file}",
);

$Gallery__Backgrounds = array (
	'Type' => "Background",
	'Image{image}' => array(
		medium => array(
			width => "1080",
			height => "700",
		),
	),
);

$Gallery__Designers = array (
	'Type' => "Designer",
	'Image{image}' => 	array(
		medium => array(
			width => "200",
			height => "200"
		),
	),
	'Designer_Name' => "{text}",
	'Bio' => "{textarea}",
);

$Gallery__InStock = array(
	'Type' => "In-Stock",
	'Image{image}' => array(
		medium => array(
			width => "310",
			height => "",
		),
	),
	'Design' => "{text}",
	'Tearsheet' => "{file}",
);

$Files__Calendar = array (
	'Type' => "Calendar",
	'Month' => "{text}",
	'Day' => "{text}",
	'Year' => "{text}",
	'Details' => "{text}",
	'{Sort}' => "No",
);

$Gallery__Experience = array (
	'Type' => "Building",
	'Image{image}' => 	array(
		medium => array(
			width => "150",
			height => "150"
		),
		large => array(
			width => "235",
			height => "235"
		)
	),
	'Name' => "{text}",
	'Location' => "{text}",
	'Building_Type' => "{text}",
	'More_Info' => "{textarea}",
);

$Gallery__HomePage = array (
	'Photo{image}' => 	array(
		medium => array(
			width => "",
			height => ""
		)
	),
);

$Gallery__SubPage = array (
	'Blurred_Photo{image}' => 	array(
		medium => array(
			width => "",
			height => ""
		)
	),
);

$Gallery__Locations = array(
	'Type' => 'Locations',
	'Location_Name' => '{text}',
	'Address_For_Map' => '{text}',
	'Location_Contact_Email' => '{text}',
	'Location_Text' => '{textarea}',
);

?>