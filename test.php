<?php 
header("Content-type: image/svg+xml");
print('<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>');
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">

<?php
include_once "src/image_lib.php";
include "lib/WideImage/WideImage.php";

use ImageCreator as imagelib;
use WideImageLib as wideimage;

//$test=new imagelib(400,500);

$test2= new wideimage("http://andykdocs.de/andykdocs/document/25-Jahre-Lars-Lindwedel/Lars-One-of-life-s-pleasures.png");

$color = new ColorPickerRGB($test2);
$color->calculateColors();

$image=new ImageCreator(700,500,"Test",$color);
$image->drawPicture();
//echo "bla";

/*
for ($i=0; $i < count($colors); $i++){
	echo $colors[]
}
*/
?>