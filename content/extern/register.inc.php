<div class="alert alert-info alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<p>
		<strong>Information: </strong>
		You need to activate and then allow us to access to your local webcam. Otherwise a login / registration through this site will not be possible.
	</p>
</div>

<div class="container" id="registerField">
	<div class="row">
		<div class="col-lg-6">
			<h1>Registration</h1>
			<br>
			
			<label for="regUsername">Choose Username:</label>
			<input type="text" name="regUsername" id="regUsername" class="form-control" onkeyup="javascript:void(check_username(this.value, 'regUsername'));">
			<br>
			
			<label for="regMail">E-Mail adress:</label>
			<input type="email" name="regMail" id="regMail" class="form-control" onkeyup="javascript:void(check_email(this.value, 'regMail'));">
			<br>
			
			<a href="?s=login" class="btn btn-backward" id="backBtn">&laquo; Back to Login</a>
			<a href="javascript:void(register());" class="btn btn-register" id="regBtn">Register now &raquo;</a>
			<br><br>
		</div>
		<div class="col-lg-6">
			<label for="livecam">Identification-Image:</label>
			<center>
				<div id="livecam"></div>
			</center>
			<br>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<p>
					<strong>Information: </strong>
					Please make sure that your complete head is visible in the identification-image. 
					A webcam must be installed to register and login to prohibit data access from a third party for example by uploading and identify with your social media images.
				</p>
			</div>
		</div>
	</div>
</div>

<script language="javascript" type="text/javascript">
	Webcam.attach('#livecam');
</script>