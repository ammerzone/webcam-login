<?php 
session_start();

$url = explode('?', str_replace('index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))[0];
define('BASE_URL', $url);
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title>Webcam login</title>
	
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	
	
	<link type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="css/main.css">

	<script language="javascript" type="text/javascript" src="//code.jquery.com/jquery-3.1.1.min.js"></script>
	<script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.22/webcam.min.js"></script>
	<script language="JavaScript" type="text/javascript" async src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/faceRecognition.js"></script>
</head>
<body xmlns="http://w3.org/1999/xhtml">
	<?php
		// Check if logged in
		if(isset($_SESSION['webcam_login_session'])){
			// Set content path to 'intern'
			define('CONTENT_PATH', 'content/intern/');
			
			// Set default file to 'main'
			define('DEFAULT_FILE', 'main');
		}else{
			// Set content path to 'extern'
			define('CONTENT_PATH', 'content/extern/');
			
			// Set default file to 'login'
			define('DEFAULT_FILE', 'login');
		}
		
		define('ERROR_PATH', 'content/error/');
		
		// Check if URL parameter (=s) is given
		if(isset($_GET['s'])){
			// Get URL parameter and include file
			if(file_exists(CONTENT_PATH . $_GET['s'] . '.inc.php')){
				require_once(CONTENT_PATH . $_GET['s'] . '.inc.php');
			}else{
				if(file_exists(CONTENT_PATH . DEFAULT_FILE . '.inc.php')){
					require_once(CONTENT_PATH . DEFAULT_FILE . '.inc.php');
				}else{
					require_once(ERROR_PATH . '404.html');
				}
			}
		}else{
			require_once(CONTENT_PATH . DEFAULT_FILE . '.inc.php');
		}
	?>
	<div id="ajaxResponse"></div>
</body>
</html>