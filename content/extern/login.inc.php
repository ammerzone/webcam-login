<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<strong>Information: </strong>
	You need to activate and then allow us to access to your local webcam. Otherwise a login / registration through this site will not be possible.
</div>

<div class="container" id="loginField">
	<div class="row">
		<div class="col-lg-6">
			<h1>Login</h1>
			<br>
			<div class="col-lg-12 col-md-6 col-sm-12">
				<label for="">Username:</label>
				<input type="text" name="lgnUsername" id="lgnUsername" class="form-control" placeholder="Username">
				<br>
			</div>
			<div class="col-lg-12 col-md-6 col-sm-12">
				<a href="javascript:void(login());" class="btn btn-login" id="lgnBtn">Login</a>
				<br><br>
				
				<p>
					Not a member yet? Register <a href="?s=register">here</a>!
				</p>
				<br><br>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="col-xs-12">
				<label for="livecam">Identification-Image:</label>
				<center>
					<div id="livecam"></div>
				</center>
			</div>
		</div>
	</div>
</div>

<script language="javascript" type="text/javascript">
	Webcam.attach('#livecam');
</script>

