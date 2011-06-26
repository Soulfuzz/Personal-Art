<form method="POST" action="image_create.php" enctype="multipart/form-data" id="imform">
	    <input type="text" name="image_text" />
		<input type="file" name="upload_image" />
		<select name="type" size="1">
				      <option value="2">Bild per Text</option>
				      <option value="1">Bild per Zufall</option>
		</select>
		<input type="submit" value="Bild erzeugen">
</form>

