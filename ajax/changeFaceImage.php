<?php
require_once('autoload.php');

if(!isset($_POST['base64data'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}

if(!isset($_SESSION['webcam_login_session'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'You need to be logged in to do that.'
		)
	);
	die();
}

$user = explode(';', $_SESSION['webcam_login_session'])[1];
$img = $_POST['base64data'];

$change = new FaceRecognition($user);
$res = $change->changeFaceImage($img);

echo json_encode($res);
die();
?>