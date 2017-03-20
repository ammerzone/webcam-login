<?php
require_once('autoload.php');

if(!isset($_POST['username']) || !isset($_POST['base64data'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}
	
$user = $_POST['username'];
$img = $_POST['base64data'];

if(strlen($user) == 0 || strlen($img) == 0){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}	

$login = new FaceRecognition($user);

$res = $login->login($img);

echo json_encode($res);
die();
?>