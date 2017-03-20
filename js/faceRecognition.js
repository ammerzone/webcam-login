/**
* Ajax requests for face Recognition actions
* 
* @author 			Jules Rau <admin@jules-rau.de>
* @copyright 		Jules Rau
* @license 			MIT license
* @origin 			https://github.com/ammerzone/webcam-login
* @version 	1.0		20.03.2017
*/





/**
* Login with Webcam snapshot and Username via Ajax
* 
* @access 	public
* @return 	void
* @see 		login()
*/
function login(){
	// Make snap of Webcam with the Webcam.js
	Webcam.snap(function(data_uri){ 
		document.getElementById('ajaxResponse').style.display = 'block';
		
		// Send Webcam snap and username via ajax
		$.ajax({
			type: 'post', 
			url: 'ajax/login.php',
			dataType: 'json',
			data: {
				base64data : data_uri, 
				username : document.getElementById('lgnUsername').value
			}, 
			beforeSend : function(){
				document.getElementById('ajaxResponse').innerHTML = '<center>Check Login:<br><img class="loader" src="media/img/loader.gif"></center>';
			}, 
			success: function(data){
				if(data.val === true){
					window.location.reload();
				}else{
					document.getElementById('ajaxResponse').style.color = '#a94442';
					document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
					
					document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
				}
			}, 
			error: function(data){
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> Failed to connect to the image upload procedure please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		});
	});
}

/**
* Check in realtime, if Username is valid
* 
* @access 	public
* @param 	string 	val
* @param 	string 	id
* @return 	void
* @see 		check_username()
*/
function check_username(val, id){
	// Send actual username and check if it's free and valid
	$.ajax({
		type : 'post', 
		url : 'ajax/checkUsername.php', 
		dataType: 'json', 
		data: {
			username : val
		}, 
		beforeSend: function(){
			document.getElementById(id).style.borderColor = 'rgba(0, 0, 0, .15)';
		}, 
		success: function(data){
			if(data.val === true){
				document.getElementById(id).style.backgroundColor = '#BFB';
				document.getElementById(id).style.borderColor = 'green';
			}else{
				document.getElementById(id).style.backgroundColor = '#FBB';
				document.getElementById(id).style.borderColor = 'red';
			}
		}, 
		error: function(data){
			document.getElementById(id).style.backgroundColor = '#FBB';
			document.getElementById(id).style.borderColor = 'red';
		}
	});
}

/**
* Check in realtime if E-Mail is valid
* 
* @access 	public
* @param 	string 	val
* @param 	string 	id
* @return 	void
* @see 		check_email()
*/
function check_email(val, id){
	// Send actual email and check if it's not taken and valid
	$.ajax({
		type : 'post', 
		url : 'ajax/checkMail.php', 
		dataType: 'json', 
		data: {
			email : val
		}, 
		beforeSend: function(){
			document.getElementById(id).style.borderColor = 'rgba(0, 0, 0, .15)';
		}, 
		success: function(data){
			if(data.val === true){
				document.getElementById(id).style.backgroundColor = '#BFB';
				document.getElementById(id).style.borderColor = 'green';
			}else{
				document.getElementById(id).style.backgroundColor = '#FBB';
				document.getElementById(id).style.borderColor = 'red';
			}
		}, 
		error: function(data){
			document.getElementById(id).style.backgroundColor = '#FBB';
			document.getElementById(id).style.borderColor = 'red';
		}
	});
}

/**
* Register a new User with Username, E-Mail and Webcam snapshot via Ajax using the Microsoft Face API
* 
* @access 	public
* @return 	void
* @see 		register()
*/
function register(){
	document.getElementById('ajaxResponse').style.display = 'block';
	
	// Make snap of Webcam
	Webcam.snap(function(data_uri){
		// Send Webcam snap and username via ajax
		$.ajax({
			type: 'post', 
			url: 'ajax/register.php',
			dataType: 'json',
			data: {
				base64data : data_uri, 
				username : document.getElementById('regUsername').value, 
				mail : document.getElementById('regMail').value
			}, 
			beforeSend : function(){
				document.getElementById('ajaxResponse').innerHTML = '<center>Register new account:<br><img class="loader" src="media/img/loader.gif"></center>';
			}, 
			success: function(data){
				if(data.val === true){
					document.getElementById('ajaxResponse').style.color = '#3c763d';
					document.getElementById('ajaxResponse').style.backgroundColor = '#dff0d8';
					
					document.getElementById('ajaxResponse').innerHTML = '<b>Success:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
				}else{
					document.getElementById('ajaxResponse').style.color = '#a94442';
					document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
					
					document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
				}
			}, 
			error: function(data){
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Failure</b> Failed to connect to the image upload procedure please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		});
	});
}

/**
* Change the users E-Mail adress in the database
* 
* @access 	public
* @return 	void
* @see 		changeMail()
*/
function changeMail(){
	document.getElementById('ajaxResponse').style.display = 'block';
	
	// Send E-Mail via Ajax
	$.ajax({
		type: 'post', 
		url: 'ajax/changeMail.php',
		dataType: 'json',
		data: {
			mail : document.getElementById('newMail').value
		}, 
		beforeSend : function(){
			document.getElementById('ajaxResponse').innerHTML = '<center>Change E-Mai adress:<br><img class="loader" src="media/img/loader.gif"></center>';
		}, 
		success: function(data){
			if(data.val === true){
				document.getElementById('ajaxResponse').style.color = '#3c763d';
				document.getElementById('ajaxResponse').style.backgroundColor = '#dff0d8';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Success:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}else{
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		}, 
		error: function(data){
			document.getElementById('ajaxResponse').style.color = '#a94442';
			document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
			
			document.getElementById('ajaxResponse').innerHTML = '<b>Failure</b> Failed to connect to the email changement procedure, please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
		}
	});
}

/**
* Change the face image and Id in the database and Microsoft Face API
* 
* @access 	public
* @return 	void
* @see 		changeMail()
*/
function changeFaceImage(){
	document.getElementById('ajaxResponse').style.display = 'block';
	
	// Make snap of Webcam
	Webcam.snap(function(data_uri){
		// Send Webcam snap via ajax
		$.ajax({
			type: 'post', 
			url: 'ajax/changeFaceImage.php',
			dataType: 'json',
			data: {
				base64data : data_uri
			}, 
			beforeSend : function(){
				document.getElementById('ajaxResponse').innerHTML = '<center>Change Fface image:<br><img class="loader" src="media/img/loader.gif"></center>';
			}, 
			success: function(data){
				if(data.val === true){
					document.getElementById('ajaxResponse').style.color = '#3c763d';
					document.getElementById('ajaxResponse').style.backgroundColor = '#dff0d8';
					
					document.getElementById('ajaxResponse').innerHTML = '<b>Success:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
				}else{
					document.getElementById('ajaxResponse').style.color = '#a94442';
					document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
					
					document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
				}
			}, 
			error: function(data){
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Failure</b> Failed to connect to the image upload procedure, please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		});
	});
}

/**
* Deletes the user from the database and the Microsoft Face API
* 
* @access 	public
* @return 	void
* @see 		changeMail()
*/
function deleteUser(){
	document.getElementById('ajaxResponse').style.display = 'block';
	
	// Send E-Mail via Ajax
	$.ajax({
		type: 'post', 
		url: 'ajax/delete.php',
		dataType: 'json',
		beforeSend : function(){
			document.getElementById('ajaxResponse').innerHTML = '<center>Delete account:<br><img class="loader" src="media/img/loader.gif"></center>';
		}, 
		success: function(data){
			if(data.val === true){
				document.getElementById('ajaxResponse').style.color = '#3c763d';
				document.getElementById('ajaxResponse').style.backgroundColor = '#dff0d8';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Success:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}else{
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Failure:</b> ' + data.responseText + '<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		}, 
		error: function(data){
			document.getElementById('ajaxResponse').style.color = '#a94442';
			document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
			
			document.getElementById('ajaxResponse').innerHTML = '<b>Failure</b> Failed to connect to the account deletement procedure, please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
		}
	});
}

/**
* Destroy the running Session via Ajax
* 
* @access 	public
* @return 	void
* @see 		logout()
*/
function logout(){
	document.getElementById('ajaxResponse').style.display = 'block';
	
	// Logout via ajax
	$.ajax({
		type: 'post', 
		url: 'ajax/logout.php',
		dataType: 'json',
		beforeSend : function(){
			document.getElementById('ajaxResponse').innerHTML = '<center>Logout account:<br><img class="loader" src="media/img/loader.gif"></center>';
		}, 
		success: function(data){
			if(data === true){
				window.location.reload();
			}else{
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
				document.getElementById('ajaxResponse').innerHTML = '<b>Fehler:</b> There was an error while trying to log out, please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
			}
		}, 
		error: function(data){
				document.getElementById('ajaxResponse').style.color = '#a94442';
				document.getElementById('ajaxResponse').style.backgroundColor = '#f2dede';
				
			document.getElementById('ajaxResponse').innerHTML = '<b>Fehler:</b> Failed to connect to the Logout procedure, please try again.<br><br><center><button class="btn" onclick="javascript:void(closeAjaxResponse());">Schließen</button></center>';
		}
	});
}

/**
* Closes the response box and sets the color values neutral
* 
* @access 	public
* @return 	void
* @see 		closeAjaxResponse()
*/
function closeAjaxResponse(){
	document.getElementById('ajaxResponse').style.color = '#000';
	document.getElementById('ajaxResponse').style.backgroundColor = '#fefefe';
	
	document.getElementById('ajaxResponse').style.display = 'none';
	
	document.getElementById('ajaxResponse').innerHTML = '';
}