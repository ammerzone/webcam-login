/**
* Login with Webcam snapshot and Username via Ajax
* 
* @access 	public
* @return 	void
* @see 		login()
*/
function login(){
	/* Make snap of Webcam with the Webcam.js */
	Webcam.snap(function(data_uri){
		document.getElementById('loginResponse').style.display = 'block';
		
		/* Send Webcam snap and username via ajax */
		$.ajax({
			type: 'post', 
			url: 'ajax/uploadLogin.php',
			dataType: 'json',
			data: {
				base64data : data_uri, 
				username : document.getElementById('lgnUsername').value
			}, 
			beforeSend : function(){
				document.getElementById('loginResponse').innerHTML = '<center>Überprüfe Login:<br><img class="loader" src="media/img/loader.gif"></center>';
			}, 
			success: function(data){
				if(data.val === true){
					window.location.reload();
				}else{
					document.getElementById('loginResponse').style.color = '#a94442';
					document.getElementById('loginResponse').style.backgroundColor = '#f2dede';
					
					document.getElementById('loginResponse').innerHTML = '<b>Fehler:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeLoginResponse());">Schließen</button></center>';
				}
			}, 
			error: function(data){
				document.getElementById('loginResponse').style.color = '#a94442';
				document.getElementById('loginResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('loginResponse').innerHTML = '<b>Fehler:</b> Fehler bei der Verbindung zum Bildupload, bitte versuche es erneut.<br><br><center><button class="btn" onclick="javascript:void(closeLoginResponse());">Schließen</button></center>';
			}
		});
	});
}

/**
* Schließt Login Antwort-Box und setzt die Farbwerte auf neutral zurück
* 
* @access 	public
* @return 	void
* @see 		closeLoginResponse()
*/
function closeLoginResponse(){
	document.getElementById('loginResponse').style.color = '#000';
	document.getElementById('loginResponse').style.backgroundColor = '#fefefe';
	
	document.getElementById('loginResponse').style.display = 'none';
}

/**
* Check in realtime, if Username is valid
* 
* @access 	public
* @param 	string 	val
* @return 	void
* @see 		check_username()
*/
function check_username(val){
	/* Send actual username and check if it's free and valid */
	$.ajax({
		type : 'post', 
		url : 'ajax/checkUsername.php', 
		dataType: 'json', 
		data: {
			username : val
		}, 
		beforeSend: function(){
			document.getElementById('checkUsername').innerHTML = '<img class="loader" src="media/img/loader.gif">';
		}, 
		success: function(data){
			document.getElementById('checkUsername').innerHTML = data.responseText;
		}, 
		error: function(data){
			document.getElementById('checkUsername').innerHTML = data.responseText;//'<img class="loader" src="media/img/loader.gif">';
		}
	});
}

/**
* Check in realtime if E-Mail is valid
* 
* @access 	public
* @param 	string 	val
* @return 	void
* @see 		check_email()
*/
function check_email(val){
	/* Send actual email and check if it's not taken and valid */
	$.ajax({
		type : 'post', 
		url : 'ajax/checkMail.php', 
		dataType: 'json', 
		data: {
			email : val
		}, 
		beforeSend: function(){
			document.getElementById('checkMail').innerHTML = '<img class="loader" src="media/img/loader.gif">';
		}, 
		success: function(data){
			document.getElementById('checkMail').innerHTML = data.responseText;
		}, 
		error: function(data){
			document.getElementById('checkMail').innerHTML = data.responseText;//'<img class="loader" src="media/img/loader.gif">';
		}
	});
}

/**
* Register a new User with Username, E-Mail and Webcam snapshot via Ajax using the Microsoft Face API
* 
* @access 	public
* @return 	void
* @see 		register
*/
function register(){
	document.getElementById('registerResponse').style.display = 'block';
	
	/* Make snap of Webcam */
	Webcam.snap(function(data_uri){
		/* Send Webcam snap and username via ajax */
		$.ajax({
			type: 'post', 
			url: 'ajax/uploadRegister.php',
			dataType: 'json',
			data: {
				base64data : data_uri, 
				username : document.getElementById('regUsername').value, 
				mail : document.getElementById('regMail').value
			}, 
			beforeSend : function(){
				document.getElementById('registerResponse').innerHTML = '<center>Registriere neuen Account:<br><img class="loader" src="media/img/loader.gif"></center>';
			}, 
			success: function(data){
				if(data.val === true){
					document.getElementById('registerResponse').style.color = '#3c763d';
					document.getElementById('registerResponse').style.backgroundColor = '#dff0d8';
					
					document.getElementById('registerResponse').innerHTML = '<b>Erfolg:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeRegisterResponse());">Schließen</button></center>';
				}else{
					document.getElementById('registerResponse').style.color = '#a94442';
					document.getElementById('registerResponse').style.backgroundColor = '#f2dede';
					
					document.getElementById('registerResponse').innerHTML = '<b>Fehler:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeRegisterResponse());">Schließen</button></center>';
				}
			}, 
			error: function(data){
				document.getElementById('registerResponse').style.color = '#a94442';
				document.getElementById('registerResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('registerResponse').innerHTML = '<b>Fehler</b> Fehler bei der Verbindung zum Bildupload, bitte versuche es erneut.<br><br><center><button class="btn" onclick="javascript:void(closeRegisterResponse());">Schließen</button></center>';
			}
		});
	});
}

/**
* Schließt Register Antwort-Box und setzt die Farbwerte auf neutral zurück
* 
* @access 	public
* @return 	void
* @see 		closeRegisterResponse()
*/
function closeRegisterResponse(){
	document.getElementById('registerResponse').style.color = '#000';
	document.getElementById('registerResponse').style.backgroundColor = '#fefefe';
	
	document.getElementById('registerResponse').style.display = 'none';
}

/**
* Destroy the running Session via Ajax
* 
* @access 	public
* @return 	void
* @see 		logout()
*/
function logout(){
	document.getElementById('logoutResponse').style.display = 'block';
	
	/* Logout via ajax */
	$.ajax({
		type: 'post', 
		url: 'ajax/logout.php',
		dataType: 'json',
		beforeSend : function(){
			document.getElementById('logoutResponse').innerHTML = '<center>Melde Account ab:<br><img class="loader" src="media/img/loader.gif"></center>';
		}, 
		success: function(data){
			if(data === true){
				window.location.reload();
			}else{
				document.getElementById('logoutResponse').style.color = '#a94442';
				document.getElementById('logoutResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('logoutResponse').innerHTML = '<b>Fehler:</b> Es gab einen Fehler beim Versuch sich auszuloggen, bitte versuche es erneut.<br><br><center><button class="btn" onclick="javascript:void(closeLogoutResponse());">Schließen</button></center>';
			}
		}, 
		error: function(data){
				document.getElementById('logoutResponse').style.color = '#a94442';
				document.getElementById('logoutResponse').style.backgroundColor = '#f2dede';
				
			document.getElementById('logoutResponse').innerHTML = '<b>Fehler:</b> Fehler bei der Verbindung zum Logout, bitte versuche es erneut.<br><br><center><button class="btn" onclick="javascript:void(closeLogoutResponse());">Schließen</button></center>';
		}
	});
}

/**
* Schließt Logout Antwort-Box und setzt die Farbwerte auf neutral zurück
* 
* @access 	public
* @return 	void
* @see 		closeLogoutResponse()
*/
function closeLogoutResponse(){
	document.getElementById('logoutResponse').style.color = '#000';
	document.getElementById('logoutResponse').style.backgroundColor = '#fefefe';
	
	document.getElementById('logoutResponse').style.display = 'none';
}