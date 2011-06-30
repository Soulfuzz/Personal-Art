<?php
$svgwidth=640;
$svgheight=4800;
include "../lib/WideImage/WideImage.php";

function rgb2hsv_special($rgb) {
//    $r = ($rgb >> 16) & 0xFF;
//    $g = ($rgb >> 8) & 0xFF;
//    $b = $rgb & 0xFF;
   $var_R = ($rgb['red']);
   $var_G = ($rgb['green']);
   $var_B = ($rgb['blue']);

   $var_Min = min($var_R, $var_G, $var_B);
   $var_Max = max($var_R, $var_G, $var_B);
   $del_Max = $var_Max - $var_Min;

   $v = $var_Max/255.0;

   if ($var_Max==0 || $del_Max == 0) {
      $h = 0;
      $s = 0;
   } else {
      $s = $del_Max / $var_Max;

      if      ($var_R>=$var_B && $var_R>=$var_G)
        $h = (($var_G-$var_B)/$del_Max);
      else if ($var_G >= $var_B)
        $h = (($var_B-$var_R)/$del_Max)+2;
      else 
        $h = (($var_R-$var_G)/$del_Max)+4;
      
      if ($h < 0) $h+=6;
      if ($h > 6) $h-=6;
   }

   return array('hue'=>($h*60), 'sat'=>($s*360),'val'=> ($v*360));//attention: h,s and v were multiplied by 360
   //   should have value sin [0-360]^3
}

function hsv2rgb_special($h, $s, $v) {
    $h=$h/60.0;$s=$s/360.0;$v=$v/360.0;//attention: s and v are devided by 360
    if($s == 0) {
        $red = $green = $blue = $v * 255;
    } else {
        $hi=floor($h);
        $f=$h-$hi;
        $p=$v*(1-$s);
        $q=$v * (1 - $s *$f);
        $t= $v *( 1 - $s *(1 - $f));

        if ($hi==0 || $hi==6) {
            $red = $v; $green = $t;  $blue= $p;
        }elseif($hi==1) {
            $red = $q; $green = $v;  $blue= $p;
        }elseif($hi==2) {
            $red = $p; $green = $v;  $blue= $t;
        }elseif($hi==3) {
            $red = $p; $green = $q;  $blue= $v;
        }elseif($hi==4) {
            $red = $t; $green = $p;  $blue= $v;
        }elseif($hi==5) {
            $red = $v; $green = $p;  $blue= $q;
        }

        $red = round($red * 255);
        $green = round($green * 255);
        $blue = round($blue * 255);
    }
    return array($red, $green, $blue);
}


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
$k=ceil(360/$n); //max box number
// lets devides [0-360]^3 in [360/n]^3 boxes
// and count how how often color are in this box
$N=array(); // array of Boxes - contains number of hits in Box
$HueMean=array();
$SatMean=array();
$LightMean=array();

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

for ($i=0; $i < $width; $i++){
	for ($j=0; $j < $height; $j++){
                $color=rgb2hsv_special($img->getRGBAt($i,$j));

                $hue=floor($color['hue']/$n); // red box index
                $sat=floor($color['sat']/$n); // green box index
                $val=floor($color['val']/$n); // blue box index
                $index=intval(ThreeDToOneD($hue, $sat,  $val, $k)); //go from 3 dimensions to one dimension
                if ((!empty($N)) &&  array_key_exists($index, $N))
                {
                    $N[$index]+=1;
                    $HueMean[$index]+=$color['hue']-$hue*$n;  //difference to Box color
                    $SatMean[$index]+=$color['sat']-$sat*$n;  //difference to Box color
                    $LightMean[$index]+=$color['val']-$val*$n;  //difference to Box color
                }
                else
                {
                    $N[$index]=1;
                    $HueMean[$index]=$color['hue']-$hue*$n;  //difference to Box color
                    $SatMean[$index]=$color['sat']-$sat*$n;  //difference to Box color
                    $LightMean[$index]=$color['val']-$val*$n;  //difference to Box color
                }

	}
}

// do posteriory calculations
foreach ($N as $index => $Number) {

    $HueMean[$index]/=$Number;  //take box avarage (difference to Box color)
    $SatMean[$index]/=$Number;
    $LightMean[$index]/=$Number;

    $color=OneDToThreeD($index, $k);

    //go from avereage difference to Box color to Box avarae color
    $HueMean[$index]+=$color[1]*$n;
    $SatMean[$index]+=$color[2]*$n;
    $LightMean[$index]+=$color[3]*$n;
}

arsort($N,SORT_NUMERIC);
$totalcolor=count($N);
// echo $totalcolor;
$maxcolor=100;//min(200,$totalcolor);
$maxdist=$k/3.0; //Abstand in Boxen als Einheit
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

        list($red,$green,$blue)=hsv2rgb_special($HueMean[$index], $SatMean[$index], $LightMean[$index]);
        
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
