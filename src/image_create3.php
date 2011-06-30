<?php
$svgwidth=640;
$svgheight=4800;
include "../lib/WideImage/WideImage.php";

function array_equal($a, $b, $strict=false) {
    if (count($a) !== count($b)) {
        return false;
    }
    sort($a);
    sort($b);
    return ($strict && $a === $b) || $a == $b;
}


function ThreeDToOneD($a,$b,$c,$k){
    return ($c+$k*$b+$k*$k*$a);
}
function OneDToThreeD($a,$k){
    $z[1]=floor($a/($k*$k));
    $z[2]=floor(($a-$k*$k*$z[1])/$k);
    $z[3]=$a-$k*$k*$z[1]-$k*$z[2];
    return $z;
}
function typewrite($x,$y,$l,$h,$red,$green,$blue){
        $fillcolor = "rgb(".$red.",".$green.",".$blue.")";
        print "\t<rect x=\"$x\" y=\"$y\" width=\"$l\" height=\"$h\" style=\"fill:$fillcolor;\"/>\n";
}

$n=8; // boxwidth
$k=ceil(256/$n); //max box number
// lets devides [0-255]^3 in [256/n]^3 boxes
// and count how how often color are in this box
$N=array(); // array of Boxes - contains number of hits in Box
$RedMean=array();
$GreenMean=array();
$BlueMean=array();

$maxsizex=400;
$maxsizey=400;

$imgtmp = WideImage::loadFromUpload('upload_image')->resize(400, 400, 'fill');

$width=$imgtmp->getWidth();
$height=$imgtmp->getHeight();
if ($width>$maxsizex || $height>$maxsizey)
{
    $maxsizex=min($width,$maxsizex);
    $maxsizey=min($height,$maxsizey);
    $img=$imgtmp->resize($maxsizex, $maxsizey, 'fill');
    $width=$maxsizex;
    $height=$maxsizey;

}else{
    $img = $imgtmp;
}


for ($i=0; $i < $img->getWidth(); $i++){
	for ($j=0; $j < $img->getHeight(); $j++){
                $color=$img->getRGBAt($i,$j);
                $red=floor($color['red']/$n); // red box index
                $green=floor($color['blue']/$n); // green box index
                $blue=floor($color['green']/$n); // blue box index
                $index=intval(ThreeDToOneD($red, $green,  $blue, $k)); //go from 3 dimensions to one dimension
                if ((!empty($N)) &&  array_key_exists($index, $N))
                {
                    $N[$index]+=1;
                    $RedMean[$index]+=$color['red']-$red*$n;  //difference to Box color
                    $GreenMean[$index]+=$color['green']-$green*$n;  //difference to Box color
                    $BlueMean[$index]+=$color['blue']-$blue*$n;  //difference to Box color
                }
                else
                {
                    $N[$index]=1;
                    $RedMean[$index]=$color['red']-$red*$n;  //difference to Box color
                    $GreenMean[$index]=$color['green']-$green*$n;  //difference to Box color
                    $BlueMean[$index]=$color['blue']-$blue*$n;  //difference to Box color
                }

	}
}

// do posteriory calculations
foreach ($N as $index => $Number) {

    $RedMean[$index]/=$Number;  //take box avarage (difference to Box color)
    $GreenMean[$index]/=$Number;
    $BlueMean[$index]/=$Number;

    $color=OneDToThreeD($index, $k);

    //go from avereage difference to Box color to Box avarae color
    $RedMean[$index]+=$color[1]*$n;
    $GreenMean[$index]+=$color[2]*$n;
    $BlueMean[$index]+=$color[3]*$n;
}

arsort($N,SORT_NUMERIC);
$totalcolor=count($N);
// echo $totalcolor;
$maxcolor=200;//min(200,$totalcolor);
$maxdist=5;
$maxdistfactor=0.7;
$colorcount=0;
$maxdistrun=10;

//try to sort out similar colors
while ($colorcount<$maxcolor){
    foreach ($N as $index => $Number) {
        $i=0;$toclose=false;
        $color=OneDToThreeD($index,$k);
        while ($i<$colorcount && !$toclose)
        {
            $i++;
            $color2=OneDToThreeD($ColorArray[$i],$k);
            $toclose=((abs($color[1]-$color2[1])<$maxdist)
              &&(abs($color[2]-$color2[2])<$maxdist)
              &&(abs($color[3]-$color2[3])<$maxdist)
                );
        }
        if (!$toclose){
            $colorcount++;
            $ColorArray[$colorcount]=$index;
            $N2[$index]=$Number;
            if ($colorcount>=$maxcolor || (($colorcount%$maxdistrun)==0))
                break;
        }
    }
    if ($maxdist<=1)
        break;
    $maxdist*=$maxdistfactor;
}
arsort($N2,SORT_NUMERIC);




//print_r($final_colors);
header("Content-type: image/svg+xml");
print('<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>');
?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/SVG/DTD/svg10.dtd">
<svg width="<?php echo $svgwidth;?>px" height="<?php echo $svgheight;?>px" xmlns="http://www.w3.org/2000/svg"><?php
srand((double) microtime() * 1000000); //initalizing random generator
$maxlinelength=200;
$minlinelength=5;
$colorcount=0;
$h1=10;  // height of colored line
$h2=5; //spacing between lines
$typwriter_x_max=640;
$typwriter_x=1;
$typwriter_y=1;

//foreach ($ColorArray as $index) {
//foreach ($N as $index => $Number) {
foreach ($N2 as $index => $Number) {

        $l=floor(rand($minlinelength,$maxlinelength));
        $witdth=min($l,$typwriter_x_max-$typwriter_x);

        $red=floor($RedMean[$index]);
        $green=floor($GreenMean[$index]);
        $blue=floor($BlueMean[$index]);
        
        typewrite($typwriter_x, $typwriter_y, $witdth, $h1, $red, $green, $blue);

        if ($l>$witdth || ($l>=($typwriter_x_max-$typwriter_x)))
        {

            $typwriter_x=1;
            $typwriter_y=$typwriter_y+$h1+$h2;
            
            $witdth=$l-$witdth;
            if ($witdth>0){
                typewrite($typwriter_x, $typwriter_y, $witdth, $h1, $red, $green, $blue);
                $typwriter_x+=$witdth;
            }




        }else{
            $typwriter_x+=$witdth;
        }

//          debug output
//        echo $Number."-".$red.":".$green.".".$blue."---";
        $colorcount++;
        if ($colorcount>=$maxcolor)
            break;
}
?>
</svg>
