<div id="loginField">
	<center>
		<div id="livecam"></div>
	</center>
	<br>
	<input type="text" name="lgnUsername" id="lgnUsername" class="form-control" placeholder="Username">
	<a href="javascript:void(login());" class="btn btn-login" id="lgnBtn">Login</a>
	<br><br><br>
	<hr>
	<div id="register">
		<small>Noch nicht dabei? <a href="?s=register">Hier</a> registrieren!</small>
	</div>
</div>
<div id="loginResponse"></div>
<script language="javascript" type="text/javascript">
	Webcam.attach('#livecam');
</script>