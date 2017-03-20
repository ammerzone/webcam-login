<center>
	<b>Change E-Mail adress:</b><br><br>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<label for="newMail">E-Mail adress:</label>
			<input type="email" name="newMail" id="newMail" class="form-control" onkeyup="javascript:void(check_email(this.value, 'newMail'));">
		</div>
	</div>
	<br>
	<a href="javascript:void(changeMail());" class="btn btn-save">Save new E-Mail</a>
</center>