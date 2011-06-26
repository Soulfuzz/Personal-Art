		var status=1;
			
		function Load_Original () {
			if (status == 1 && page == "images") {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'src/original_image.php', { method: 'get' });
				status=2;
			}
			else if (status > 1 && page == "images"){
				status = 1;
			}
		}

		function Load_Personal () {
			if (status == 2 && page == "images") {
				document.getElementById("imagecol").innerHTML="loading...";
				new Ajax.Updater('imagecol', 'src/personalized_image.php', { method: 'get' });
			}
		}