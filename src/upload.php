<form method="POST" action="index.php" enctype="multipart/form-data" id="imform">
	<table width="100%">
		<tr>
			<td> 
				Dein Name:
			</td>
			<td>
				<input type="text" name="author_name" />
			</td>
		</tr>
		<tr>
			<td>
				Text:
			</td>
			<td>
				<input type="text" name="image_text" />
			</td>
		</tr>
		<tr>
			<td>
			Bild:
			</td>
			<td>
				<input type="file" name="upload_image" />
			</td>
		</tr>
		<tr>
			<td>
			Art des Bildes:
			</td>
			<td>
			<select name="type" size="1">
				      <option value="1">Lars per Text Originalfarben</option>
				      <option value="3">Robert per Zufall RGB</option>
				      <option value="4">Robert per Zufall HSV</option>
			</select>
			</td>
		</tr>
		<tr>
			<td> </td>
			<td>
			<br>
			<input type="hidden" name="upload" value="true";
			<input type="submit" value="Bild erzeugen">
			</td>
		</tr>
	</table>
</form>

