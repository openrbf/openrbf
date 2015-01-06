<?php

if(isset($_GET)){
	$data = explode('x',$_GET['size']); 
	if(!is_array($data) || count($data) < 2) die("Error");
	if(!isset($data[2])) $data[2] = 'cccccc';
	image($data[0], $data[1], $data[2]);
	exit;
}

function image($width, $height, $background){
    $image = imagecreate($width, $height);  
	$background = imagecolorallocate($image, base_convert(substr($background, 0, 2), 16, 10), base_convert(substr($background, 2, 2), 16, 10), base_convert(substr($background, 4, 2), 16, 10));

    header("Content-Type: image/png"); 
    imagepng($image);
    imageDestroy($image);
}

?>
