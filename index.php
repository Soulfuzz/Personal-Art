<?php 
$svg_file="test.svg";
$upload_file="test.jpg";


if (isset($_POST["upload"])){
	$author_name=$_POST["author_name"];
	$image_text=$_POST["image_text"];
	include "src/image_create_by_lib.php";
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>Personal Art Project</title>
	<meta name="author" content="Lars Giere" />
	<link rel="stylesheet" href="css/main.css" type="text/css" />
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-24188784-1']);
	  _gaq.push(['_setDomainName', '.lars-giere.de']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();


				
	</script>
	<script type="text/javascript">

		
		function Load_Original (imageurl) {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'src/original_image.php', { method: 'get', parameters: {image: imageurl} });
				status=2;
		}

		function Load_Personal (imageurl) {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'src/personalized_image.php', { method: 'get', parameters: {image: imageurl} });
		}
		
		function Image_Create() {
			new Ajax.Request('imagecol','src/test.php', {
				  parameters: $('imform').serialize(true)
				  });
		}
	</script>
	<script type="text/javascript" src="lib/Prototype/prototype.js"></script>
	<script type="text/javascript" src="script/image_changer.js"></script>
	<script type="text/javascript" src="script/content_load.js"></script>
</head>
<body>
	<div id="changer" class="verticaltext"><a href="#" onclick="Load_Personal('<?php echo $svg_file ?>')">Personalisiert</a> - <a href="#"  onclick="Load_Original('<?php echo $upload_file ?>')">Original</a></div>
	<div id="content">
		<div id="header">
			<p id="top">Personal Art is rather a concept of the visualization of a context than real art</p>
			
			<h1>Personal Art Project</h1>
			
			<ul id="menu">
				<li><a href="javascript:Images()">Personalized Art</a></li>
				<li><a href="javascript:Upload()">Upload</a></li>
			</ul>
			
			<div id="pitch">	
				<!-- The idea of the Personal Art Project is to give users the posiibility to see their favorite pictures and the corresponding ideas they have to it in a new way by breaking all this information down into an infographic which is itself a little personal piece of art.
		 		 -->	
		  	</div>
		</div>
	
		<div id="cols">
			<div class="col" id="imagecol">
				<?php 
					include "src/personalized_image.php"; 
				?>
			</div>
			<div class="col last" id="textcol">
				<?php include "src/image_text.php"; ?>
			</div>
			<div class="x"></div>
		</div>
		
		<div id="footer">
			<p id="right">
				<a href="#">Twitter</a>
				<a href="#">Facebook</a>
				<a href="#">RSS</a>
			</p>
			<p>
				<a href="#">Home</a>
				<a href="#">Media Presence</a>
				<a href="#">Social Networks</a>
				<a href="#">Blog</a>
				<a href="#">About Us</a>
				<a href="#">Contact Us</a>
			</p>
			<p>Copyright &copy; 2011 Personal Art Project &middot; Design: tbd</p>
		</div>
	</div>
</body>
</html>
