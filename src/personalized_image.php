<embed src="<?php 
if (isset($_GET["image"])){
	echo $_GET["image"];
}
else if (isset($svg_file)) { 
	echo $svg_file; 
}
else {
	echo "test.svg";
}
?>" width="640" height="900"
				type="image/svg+xml"
				pluginspage="http://www.adobe.com/svg/viewer/install/" class="img" /> 
