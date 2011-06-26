<?php
class PersonalArt
{    
	$svgwidth=640;
	$svgheight=480;
	$height="20";
	$colors=new Array();
	
	
	function PersonalArt($colors){
		$this->colors=colors;
	}
	
	function createSVGByText($text) {		
    	$x=0; $y=0;
    	$ascii_text=$this->encodeText($text);
	    for ($j=0; $j < sizeof($ascii_text); $j++) {
	    	$c=$j%$this->corlors.size();
	    	$red = $this->colors[$c][red];
			$blue = $this->colors[$c][blue];
			$green = $this->colors[$c][green];
			$color = "rgb(".$red.",".$green.",".$blue.")";
	    	echo "\t<rect x=\"$x\" y=\"$y\" width=\"$ascii_text[$j]\" height=\"$this->height\" style=\"fill:$color;\"/>\n";
	    	$x+=$ascii_text[$j];
	    	if ($x > $this->svgwidth){
	    		$x=0;
	    		$y+=40; 
	    	}
	    }
	}
	
	function createSVGByChance() {
		
	}
	
	
	function encodeText($txtData){
            for ($i = 0;$i<strlen($txtData);$i++)
                $tmpStr[$i]= ord(substr($txtData, $i, 1));
            return $tmpStr;
	}
}
?>