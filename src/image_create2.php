<?
header("Content-type: image/svg+xml");
print('<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>');
$svgwidth=640;
$svgheight=480;
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">
<?php

include "lib/WideImage/WideImage.php";
$image_text=htmlspecialchars($_POST["image_text"]);
$type=$_POST["type"];



function Encode($txtData){
            for ($i = 0;$i<strlen($txtData);$i++)
                $tmpStr[$i]= ord(substr($txtData, $i, 1));
            return $tmpStr;
}


function array_equal($a, $b, $strict=false) {
    if (count($a) !== count($b)) {
        return false;
    }
    sort($a);
    sort($b);
    return ($strict && $a === $b) || $a == $b;
}



$ascii_text=Encode($image_text);
$img = WideImage::loadFromUpload('upload_image')->resize(5, 3, 'fill');
//echo WideImage::loadFromUpload('upload_image');

//$img->saveToFile('test_x.jpg');
//$img->output('jpg', 45);

for ($i=1; $i <= $img->getWidth(); $i++){
	for ($j=1; $j <= $img->getHeight(); $j++){
		$color[$j+$img->getHeight()*($i-1)-1]=$img->getRGBAt($i,$j);	
	}
}

//print_r($color);
$cf_count=0;
for ($i=0; $i < $img->getWidth()*$img->getHeight(); $i++){
	$found=false;
	for ($j=0; $j < $i; $j++){
		if (array_equal($color[$i],$color[$j])) $found = true;
	}
	if (!$found){ 
		$final_colors[$cf_count]=$color[$i]; 
		$cf_count++;
	}
}
//print_r($final_colors);
?>
<svg width="<?=$svgwidth;?>px" height="<?=$svgheight;?>px" xmlns="http://www.w3.org/2000/svg"><?

if ($type == 1) {
	srand((double) microtime() * 1000000); //initalizing random generator
	for ($i = 0; $i <= $cf_count; $i+=1) {
		$x = floor(rand(0,$svgwidth-1)); //avoid getting a range 0..0 for rand function
		$y = floor(rand(0,$svgheight-1));
		$width = floor(rand(0,$svgwidth-$x)); //avoid getting rect outside of viewbox
		$height = floor(rand(0,$svgheight-$y));
		$red = $final_colors[$i][red];
		$blue = $final_colors[$i][blue];
		$green = $final_colors[$i][green];
		$color = "rgb(".$red.",".$green.",".$blue.")";
		print "\t<rect x=\"$x\" y=\"$y\" width=\"$width\" height=\"$height\" style=\"fill:$color;\"/>\n";
	}
}

elseif ($type == 2) {
    	$height="20";
    	$x=0; $y=0;
	    for ($j=0; $j < sizeof($ascii_text); $j++) {
	    	$c=$j%$cf_count;
	    	$red = $final_colors[$c][red];
			$blue = $final_colors[$c][blue];
			$green = $final_colors[$c][green];
			$color = "rgb(".$red.",".$green.",".$blue.")";
	    	echo "\t<rect x=\"$x\" y=\"$y\" width=\"$ascii_text[$j]\" height=\"$height\" style=\"fill:$color;\"/>\n";
	    	$x+=$ascii_text[$j];
	    	if ($x > $svgwidth){
	    		$x=0;
	    		$y+=40; 
	    	}
	    }
}
?>
</svg>
