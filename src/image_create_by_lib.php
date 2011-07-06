<?php 
	include_once "src/image_lib.php";
	include_once "lib/WideImage/WideImage.php";
	
	use ImageCreator as imagelib;
	use WideImageLib as wideimage;
	
	$image_text=htmlspecialchars($_POST["image_text"]);
	$author_name=htmlspecialchars($_POST["author_name"]);
	
	$uploadDir = 'userimages/';
	$svg_file=$uploadDir.$author_name.".svg";
	
	$upload_file = $uploadDir . $_FILES['upload_image']['name'];
	
	$image= new WideImageLib('upload_image');
	
	$color = new ColorPickerRGB($image);
	//$color->calculateColors();
	
	$art=new ImageCreator($image,640,20,$color);
	$art->drawPictureByText($image,$image_text);
	$art->saveAsSVG($svg_file);
	
	move_uploaded_file($_FILES['upload_image']['tmp_name'], $upload_file); 
?>