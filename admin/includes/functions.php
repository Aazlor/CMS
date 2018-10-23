<?

ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);

function clean_url($url){
	$url = strtolower($url);
	if(!preg_match('/http:\/\//', $url)){
		$url = preg_replace('/^\//', '', $url);
		$url = preg_replace('/\.html/', '', $url);
		$url = preg_replace('/ /', '-', $url);
		$url = preg_replace('/\//', '-', $url);
		$url = preg_replace('/\\\/', '-', $url);
		$url = preg_replace('/&/', 'and', $url);
		$url = '/'.$url.'.html';
	}
	return $url;
}

function countdim($array){
	if (is_array(reset($array))){
		$return = countdim(reset($array)) + 1;
	}	
	else{
		$return = 1;
	}
	return $return;
}

function resize($width, $height, $value){
	if($value['width'] != '' && $value['height'] != ''){
		$modwidth = $value['width'];	
		$modheight = $value['height'];	
	}
	elseif($value['width'] != '' && $value['height'] == ''){
		$modwidth = $value['width'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	elseif($value['height'] != '' && $value['width'] == ''){
		$modheight = $value['height'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}

	if(isset($value['minwidth']) && $value['minwidth'] != ''){
		$modwidth = $value['minwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	if(isset($value['minheight']) && $value['minheight'] != '' && $modheight < $value['minheight']){
		$modheight = $value['minheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if($modwidth == ''){
		$modwidth = $width;
	}
	if($modheight == ''){
		$modheight = $height;
	}

	if(isset($value['maxheight']) && $value['maxheight'] != '' && $value['maxheight'] < $modheight){
		$modheight = $value['maxheight'];	
		$diff = $height / $modheight;
		$modwidth = $width / $diff;
	}
	
	if(isset($value['maxwidth']) && $value['maxwidth'] != '' && $value['maxwidth'] < $modwidth){
		$modwidth = $value['maxwidth'];	
		$diff = $width / $modwidth;
		$modheight = $height / $diff;
	}
	
	return $modwidth.'||'.$modheight;
}

function pre($array){
	echo'<pre>';
	print_r($array);
	echo'</pre>';
}

function createArray($string){
	$a = explode('{{}}', $string);
	foreach($a as $v){
		if(!strstr($v, '(())'))
			continue;
		$s = explode('(())', $v);
		$x[$s[0]] = $s[1];
	}
	return($x);
}

function buildForm($data, $templateVars){

	pre($data);
	pre($templateVars);

	foreach($vars as $key => $value){
		$pretty_key = str_replace('_', ' ', $key);
	}
}

/*
form :: post action

gallery (translate into ajax)

--
is_array
--

image
select
radio
checkbox
details
file
text
number
textarea
categories
---
meta title
meta description
meta keywords
---
if page
	template
	page_id
if product
	product_id

*/

?>