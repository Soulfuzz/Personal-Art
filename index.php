<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<title>PWrsonal Art Project</title>
	<meta name="author" content="Lars Giere" />
	<link rel="stylesheet" href="css/main.css" type="text/css" />
	<script type="text/javascript" src="lib/Prototype/prototype.js"></script>
	<script type="text/javascript">

		var status=1;
		var page="images";
		
		function Upload () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'upload.php', { method: 'get' });
			document.getElementById("textcol").innerHTML="loading...";
			new Ajax.Updater('textcol', 'upload_text.php', { method: 'get' });
			page="upload";
		}

		function Images () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'personalized_image.php', { method: 'get' });
			document.getElementById("textcol").innerHTML="loading...";
			new Ajax.Updater('textcol', 'image_text.php', { method: 'get' });
			page="images";
		}

		function Load_Original () {
			if (status == 1 && page == "images") {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'original_image.php', { method: 'get' });
				status=2;
			}
			else if (status > 1 && page == "images"){
				status = 1;
			}
		}

		function Load_Personal () {
			if (status == 2 && page == "images") {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'personalized_image.php', { method: 'get' });
			}
		}


		function Image_Create() {
			new Ajax.Request('imagecol','test.php', {
				  parameters: $('imform').serialize(true)
				  });
		}
	</script>
</head>
<body>
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
			<div class="col" id="imagecol" onmouseover="Load_Original();status=2;" onmouseout="Load_Personal();status=1;">
				<?php include "personalized_image.php"; ?>
			</div>
			<div class="col last" id="textcol">
				<h4>Max Mustermann</h4>
				<div>
					Lorem ipsum dolor sit amet, consectetur adipisici elit, sed eiusmod tempor incidunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquid ex ea commodi consequat. Quis aute iure reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint obcaecat cupiditat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.

--

Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.

Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.
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
