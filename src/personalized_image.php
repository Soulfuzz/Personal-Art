<embed src="<?php 
if (isset($_GET["image"])){
	echo $_GET["image"];
}
else { 
	echo $svg_file; 
}
?>" width="640" height="900"
				type="image/svg+xml"
				pluginspage="http://www.adobe.com/svg/viewer/install/" class="img" /> 
