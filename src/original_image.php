<img src="<?php  
if (isset($_GET["image"])){
	echo $_GET["image"];
}
else if (isset($upload_file)) {
	echo $upload_file; 
}
else{
	echo "test.jpg";
}
?>" width="640" class="img">
