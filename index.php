<?php 
session_start();

$url = explode('?', str_replace('index.php', '', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']))[0];
define('BASE_URL', $url);

/* Force the request url always to https, needed for webcam stream */
if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on"){
	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	exit();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
	<title>Webcam login</title>
	
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta http-equiv="Content-Style-Type" content="text/css">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	
	<script language="javascript" type="text/javascript" src="//code.jquery.com/jquery-3.1.1.min.js"></script>
	<script language="javascript" type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.22/webcam.min.js"></script>
	<script language="javascript" type="text/javascript" src="js/faceRecognation.js"></script>
	<link type="text/css" rel="stylesheet" href="css/main.css">
</head>
<body>
	<?php
		/* Check if logged in */
		if(isset($_SESSION[''])){
			/* Set content path to 'intern' */
			define('CONTENT_PATH', 'content/intern/');
			
			/* Set default file to '' */
			define('DEFAULT_FILE', 'main');
		}else{
			/* Set content path to 'extern' */
			define('CONTENT_PATH', 'content/extern/');
			
			/* Set default file to 'login' */
			define('DEFAULT_FILE', 'login');
		}
		
		define('ERROR_PATH', 'content/error/');
		
		/* Check if URL parameter (=s) is given */
		if(isset($_GET['s'])){
			/* Get URL parameter and include file */
			switch($_GET['s']){
				case 'login' : 
					if(file_exists(CONTENT_PATH . $_GET['s'] . '.inc.php')){
						require_once(CONTENT_PATH . 'login.inc.php');
					}
					break;
				case 'register' : 
					if(file_exists(CONTENT_PATH . $_GET['s'] . '.inc.php')){
						require_once(CONTENT_PATH . 'register.inc.php');
					}
					break;
				default: 
					if(file_exists(CONTENT_PATH . $_GET['s'] . '.inc.php')){
						require_once(ERROR_PATH . '404.html');
					}
					break;
			}
		}else{
			require_once(CONTENT_PATH . DEFAULT_FILE . '.inc.php');
		}
	?>
</body>
</html>