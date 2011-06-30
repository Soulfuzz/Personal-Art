		var page="images";
		
		function Upload () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'src/upload.php', { method: 'get' });
			new Ajax.Updater('textcol', 'src/upload_text.php', { method: 'get' });
			page="upload";
			pageTracker._trackEvent("Upload", "click", "Upload", "1");
		}

		function Images () {
			document.getElementById("imagecol").innerHTML="loading...";
			new Ajax.Updater('imagecol', 'src/personalized_image.php', { method: 'get' });
			new Ajax.Updater('textcol', 'src/image_text.php', { method: 'get' });
			page="images";
			pageTracker._trackEvent("Main", "click", "Main Page", "1");
		}