<?php 

header("Content-type: image/svg+xml");
print('<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>');
$image_text=htmlspecialchars($_POST["image_text"]);
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">

<?php
include_once "../src/image_lib.php";
include_once "../lib/WideImage/WideImage.php";

use ImageCreator as imagelib;
use WideImageLib as wideimage;


$image= new WideImageLib('upload_image');
//$color=$image->getMeanColor(3,10,30,30);
//$color->printColor();


$color = new ColorPickerRGB($image);
//$color->calculateColors();

$art=new ImageCreator($image,640,20,$color);
$art->drawPictureByText($image,$image_text);
$art->printInlineSVG();

?>