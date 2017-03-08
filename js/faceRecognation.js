function login(){
	/* Make snap of Webcam */
	Webcam.snap(function(data_uri){
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
				document.getElementById('loginResponse').innerHTML = '<img class="loader" src="media/img/loader.gif">';
			}, 
			success: function(data){
				if(data.val === true){
					document.getElementById('loginResponse').innerHTML = data.responseText;
					window.location.reload();
				}else{
					document.getElementById('loginResponse').innerHTML = data.responseText;
				}
			}, 
			error: function(data){
				document.getElementById('loginResponse').innerHTML = 'Fehler bei der Verbindung zum Bildupload, bitte versuche es erneut.';
			}
		});
	});
}

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
			document.getElementById('checkUsername').innerHTML = JSON.stringify(data);//'<img class="loader" src="media/img/loader.gif">';
		}
	});
}

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
			document.getElementById('checkMail').innerHTML = JSON.stringify(data);//'<img class="loader" src="media/img/loader.gif">';
		}
	});
}

function register(){
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
				document.getElementById('registerResponse').innerHTML = '<img class="loader" src="media/img/loader.gif">';
			}, 
			success: function(data){
				if(data.val === true){
					document.getElementById('registerResponse').innerHTML = data.responseText;
				}else{
					document.getElementById('registerResponse').innerHTML = data.responseText;
				}
			}, 
			error: function(data){
				document.getElementById('registerResponse').innerHTML = 'Fehler bei der Verbindung zum Bildupload, bitte versuche es erneut.';
			}
		});
	});
}

function logout(){
	/* Logout via ajax */
	$.ajax({
		type: 'post', 
		url: 'ajax/logout.php',
		dataType: 'json',
		success: function(data){
			if(data.val === true){
				window.location.reload();
				document.getElementById('loginResponse').innerHTML = data.responseText;
			}else{
				document.getElementById('loginResponse').innerHTML = data.responseText;
			}
		}, 
		error: function(data){
			document.getElementById('loginResponse').innerHTML = 'Fehler bei der Verbindung zum Logout, bitte versuche es erneut.';
		}
	});
}