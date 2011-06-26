		var page="images";
		
		function Upload () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'src/upload.php', { method: 'get' });
			document.getElementById("textcol").innerHTML="loading...";
			new Ajax.Updater('textcol', 'src/upload_text.php', { method: 'get' });
			page="upload";
		}

		function Images () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'src/personalized_image.php', { method: 'get' });
			document.getElementById("textcol").innerHTML="loading...";
			new Ajax.Updater('textcol', 'src/image_text.php', { method: 'get' });
			page="images";
		}