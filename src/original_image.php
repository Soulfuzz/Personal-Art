<img src="<?php  
if (isset($_GET["image"])){
	echo $_GET["image"];
}
else {
	echo $upload_file; 
}
?>" width="640" class="img">
