<?php
//namespace de\LarsGiere\PersonalArt;

interface ImageLib{
	public function __construct($image);
	public function resize($x,$y);
	public function getRGBAt($x,$y);
	public function saveOriginalImage();
	public function getHeight();
	public function getWidth();
	public function getMeanColor($x,$y,$width,$height);
}

class WideImageLib implements ImageLib{
	/*! @class WideImageLib
    @abstract The ImageLib will be the container for all image files and image manipulation
    @discussion The ImageLib needs an image by upload or url
	*/

	protected $image;
	
	/*! @function __construct()
    @abstract gets an image url or the uploaded image variable name and creates a ImageLib object
    @param image
 	*/
	public function __construct($image){
		if (filter_var($image, FILTER_VALIDATE_URL) !== false) {
			$this->image=WideImage::loadFromFile($image);
		}
		else{
			$this->image=WideImage::loadFromFile($_FILES[$image]['tmp_name']);
		}
		$this->saveOriginalImage();
	}
	
	public function resize($width,$height){
		$this->image->resize($width,$height, 'fill');
	}
	
	public function getRGBAt($x,$y){
		return $this->image->getRGBAt($x,$y);
	}
	
	public function saveOriginalImage(){
		$this->image->saveToFile('test_x.jpg');
	}
	
	public function getHeight(){
		return $this->image->getHeight();
	}
	public function getWidth(){
		return $this->image->getWidth();
	}
	
	public function getMeanColor($x,$y,$width,$height){
		$color=new ColorRGB();
		$image_height=$this->getHeight();
		$image_width=$this->getWidth();
		for ($i=0; $i < $width; $i++){
			for ($j=0; $j < $height; $j++){
				if ($x+$i < $image_width && $y+$j < $image_height){
					$color->addRGBArray($this->getRGBAt($x+$i,$y+$j));
				}
			}
		}
		$color->divide($width*$height);
		return $color;
	}
}

class ColorRGB{
	public $red,$green,$blue,$alpha;
	
	public function __construct(){
		$this->red=0;
		$this->green=0;
		$this->blue=0;
		$this->alpha=0;
	}
	
	public function addRGBArray($color){
		$this->red+=$color['red'];
		$this->green+=$color['green'];
		$this->blue+=$color['blue'];
		$this->alpha+=$color['alpha'];
	}
	
	public function divide($divisor){
		$this->red=floor($this->red/$divisor);
		$this->green=floor($this->green/$divisor);
		$this->blue=floor($this->blue/$divisor);
		$this->alpha=floor($this->alpha/$divisor);
	}
	
	public function printColor(){
		echo "Red: ".$this->red." Green: ".$this->green." Blue: ".$this->blue." Alpha: ".$this->alpha;
	}
}

interface ColorPicker{
	public function __construct(ImageLib $image);
	public function calculateColors();
}


class ColorPickerRGB implements ColorPicker {
	protected $image;
	public $maxcolor=200;
	public $index;
	public $redMean=array(), $blueMean=array(), $greenMean=array();
	
	public function __construct(ImageLib $image){
		$this->image=$image;
	}
	
	
	public function calculateColors(){
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
	
		
		$width=$this->image->getWidth();
		$height=$this->image->getHeight();
		if ($width>$maxsizex || $height>$maxsizey)
		{
		    $maxsizex=min($width,$maxsizex);
		    $maxsizey=min($height,$maxsizey);
		    $this->image->resize($maxsizex, $maxsizey, 'fill');
		    $width=$maxsizex;
		    $height=$maxsizey;
		
		}
		
		
		for ($i=0; $i < $this->image->getWidth(); $i++){
			for ($j=0; $j < $this->image->getHeight(); $j++){
		                $color=$this->image->getRGBAt($i,$j);
		                $red=floor($color['red']/$n); // red box index
		                $green=floor($color['blue']/$n); // green box index
		                $blue=floor($color['green']/$n); // blue box index
		                $index=intval($this->ThreeDToOneD($red, $green,  $blue, $k)); //go from 3 dimensions to one dimension
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
		
		    $color=$this->OneDToThreeD($index, $k);
		
		    //go from avereage difference to Box color to Box avarae color
		    $RedMean[$index]+=$color[1]*$n;
		    $GreenMean[$index]+=$color[2]*$n;
		    $BlueMean[$index]+=$color[3]*$n;
		}
		
		arsort($N,SORT_NUMERIC);
		$totalcolor=count($N);
		//echo $totalcolor;
		$maxcolor=200;//min(200,$totalcolor);
		$maxdist=5;
		$maxdistfactor=0.7;
		$colorcount=0;
		$maxdistrun=10;
		
		//try to sort out similar colors
		while ($colorcount<$maxcolor){
		    foreach ($N as $index => $Number) {
		        $i=0;$toclose=false;
		        $color=$this->OneDToThreeD($index,$k);
		        while ($i<$colorcount && !$toclose)
		        {
		            $i++;
		            $color2=$this->OneDToThreeD($ColorArray[$i],$k);
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

		$this->index=$N2;
		$this->redMean=$RedMean;
		$this->blueMean=$BlueMean;
		$this->greenMean=$GreenMean;
	}
	
	private function ThreeDToOneD($a,$b,$c,$k){
	    return ($c+$k*$b+$k*$k*$a);
	}
	
	private function OneDToThreeD($a,$k){
	    $z[1]=floor($a/($k*$k));
	    $z[2]=floor(($a-$k*$k*$z[1])/$k);
	    $z[3]=$a-$k*$k*$z[1]-$k*$z[2];
	    return $z;
	}
	
}

class PickColorsByHSV implements ColorPicker {
	public function __construct(ImageLib $image){
		
	}
	public function calculateColors(){
		
	}
}

class TextToAscii{
	public $text, $ascii_text;
	
	public function __construct($text){
		$this->text=$text;
	}
	
	public function Encode(){
            for ($i = 0;$i<strlen($this->text);$i++)
                $tmpStr[$i]= ord(substr($this->text, $i, 1));
            $this->ascii_text=$tmpStr;
            return $tmpStr;
	}
}


class ImageCreator{
	
	private $svgwidth, $svgheight, $line_height, $colors, $svg;
	
	public function __construct(ImageLib $image,$width,$line_height,$colors){
		$this->svgwidth=$width;
		$this->svgheight=floor($image->getHeight()*($width/$image->getWidth()));
		$this->colors=$colors;
		$this->line_height=$line_height;
	}
	
	function drawRectangle($x,$y,$l,$h,$red,$green,$blue){
	        $fillcolor = "rgb(".$red.",".$green.",".$blue.")";
	        return "\t<rect x=\"$x\" y=\"$y\" width=\"$l\" height=\"$h\" style=\"fill:$fillcolor;\"/>\n";
	}
	
	function drawRectangleByColor($x,$y,$l,$h,ColorRGB $color){
	        $fillcolor = "rgb(".$color->red.",".$color->green.",".$color->blue.")";
	        return "\t<rect x=\"$x\" y=\"$y\" width=\"$l\" height=\"$h\" style=\"fill:$fillcolor;\"/>\n";
	}
	
	function drawPictureByText(ImageLib &$image, $text){
		$t=new TextToAscii($text);
		$ascii_text=$t->Encode();
		$height=$this->line_height;
    	$x=0; $y=0;
    	$output="<svg width=\"".$this->svgwidth."px\" height=\"".$this->svgheight."px\" xmlns=\"http://www.w3.org/2000/svg\">\n";
    	//$mod=7680/sumArray($ascii_text);
    	//$ascii_text=multArray($ascii_text,$mod);
    	$width_calc=$image->getHeight()/$this->svgheight;
		$height_calc=$image->getWidth()/$this->svgwidth;
		$text_count=0;
	    for ($y=0; $y <= $this->svgheight -$height ;) {
	    	$text_count=$text_count%sizeof($ascii_text);
	    	if ($x+$ascii_text[$text_count] > $this->svgwidth){
	    		$width_x=$this->svgwidth-$x;
	    	}
	    	else{
	    		$width_x=$ascii_text[$text_count];
	    	}
	    	if ($width_x > 0){
		    	$color=$image->getMeanColor(floor($x*$width_calc),floor($y*$height_calc),ceil($width_x*$width_calc),floor($height*$height_calc));
		    	$output.=$this->drawRectangleByColor($x,$y,$width_x,$height,$color); 
	    	}
	    	$x+=$ascii_text[$text_count];
	    	if ($x > $this->svgwidth){
	    		$y+=$height+floor($height/2);
	    		if ($y >= $this->svgheight - $height){
	    			break;
	    		}
	    		$end_width=$x-$this->svgwidth;
	    		$x=0;
	    		$color=$image->getMeanColor(floor($x*$width_calc),floor($y*$height_calc),ceil($end_width*$width_calc),floor($height*$height_calc));
	    		$red = $color->red;
				$blue = $color->blue;
				$green = $color->green;
	    		$output.=$this->drawRectangle($x,$y,$end_width,$height,$red,$green,$blue);
	    		$x+=$end_width;
	    	}
	    	$text_count+=1;
	    }
	    $output.="</svg>";
		$this->svg=$output;
	}
	
	function drawPictureSimple(){
		$output="<svg width=\"".$this->svgwidth."px\" height=\"".$this->svgheight."px\" xmlns=\"http://www.w3.org/2000/svg\">\n";

		srand((double) microtime() * 1000000); //initalizing random generator
		$maxlinelength=200;
		$minlinelength=5;
		$colorcount=0;
		$h1=$this->lineheight;  // height of colored line
		$h2=5; //spacing between lines
		$typwriter_x_max=640;
		$typwriter_x=1;
		$typwriter_y=1;
		$RedMean=$this->colors->redMean;
		$BlueMean=$this->colors->blueMean;
		$GreenMean=$this->colors->greenMean;
		
		
		//foreach ($ColorArray as $index) {
		//foreach ($N as $index => $Number) {
		foreach ($this->colors->index as $index => $Number) {
		
		        $l=floor(rand($minlinelength,$maxlinelength));
		        $witdth=min($l,$typwriter_x_max-$typwriter_x);
		
		        $red=floor($RedMean[$index]);
		        $green=floor($GreenMean[$index]);
		        $blue=floor($BlueMean[$index]);
		        
		        $output.=$this->drawRectangle($typwriter_x, $typwriter_y, $witdth, $h1, $red, $green, $blue);
		
		        if ($l>$witdth || ($l>=($typwriter_x_max-$typwriter_x)))
		        {
		
		            $typwriter_x=1;
		            $typwriter_y=$typwriter_y+$h1+$h2;
		            
		            $witdth=$l-$witdth;
		            if ($witdth>0){
		                $output.=$this->drawRectangle($typwriter_x, $typwriter_y, $witdth, $h1, $red, $green, $blue);
		                $typwriter_x+=$witdth;
		            }

		        }else{
		            $typwriter_x+=$witdth;
		        }
		
		//          debug output
		//        echo $Number."-".$red.":".$green.".".$blue."---";
		        $colorcount++;
		        if ($colorcount>=$this->colors->maxcolor)
		            break;
		}
		$output.="</svg>";
		$this->svg=$output;
	}
	
	
	public function saveAsSVG($filename) {
		$text="<?xml version=\"1.0\" encoding=\"iso-8859-1\" standalone=\"no\"?>\n<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.0//EN\" \"http://www.w3.org/TR/SVG/DTD/svg10.dtd\">";
		$text=$this->svg;
		$fh = fopen($filename, 'w') or die("can't open file");
		fwrite($fh, $text);
		fclose($fh);	
	}
	
	public function printInlineSVG(){
		echo $this->svg;
	}
	
	public function printSVG(){
		
	}
	
	public function printJPG(){
		
	}
}

?>