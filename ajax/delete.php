<?php
require_once('autoload.php');

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

$delete = new FaceRecognition($user);
$res = $delete->deleteUser();

echo json_encode($res);
die();
?>