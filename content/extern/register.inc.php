<div id="registerField">
	<table>
		<tr>
			<td>
				<label for="regUsername">Username w&auml;hlen:</label>
			</td>
			<td>
				<input type="text" name="regUsername" id="regUsername" class="form-control" onkeyup="javascript:void(check_username(this.value));">
			</td>
			<td>
				<div id="checkUsername"></div>
			</td>
		</tr>
		<tr>
			<td>
				<label for="regMail">E-Mail Adresse:</label>
			</td>
			<td>
				<input type="email" name="regMail" id="regMail" class="form-control" onkeyup="javascript:void(check_email(this.value));">
			</td>
			<td><div id="checkMail"></div></td>
		</tr>
	</table>
	<div>Identifikations-Bild:</div>
	<center>
		<div id="livecam"></div>
		<br>
		<br>
		<a href="?s=login" class="btn btn-backward">Zur&uuml;ck zum Login</a>
		<a href="javascript:void(register());" class="btn btn-register" id="regBtn">Registrieren</a>
	</center>
	<hr>
	<div>
		<small>
			<strong>Hinweis: </strong>Achten Sie darauf, dass auf das Identifikations-Bild der gesamte Kopf sichtbar zu erkennen ist. 
			Es muss eine Webcam vorhanden sein um Datenzugriff von Dritten durch z.B. Bilder aus dem social media zu verhindern.
		</small>
	</div>
	<br>
</div>
<div id="registerResponse"></div>
<script language="javascript" type="text/javascript">
	Webcam.attach('#livecam');
</script>