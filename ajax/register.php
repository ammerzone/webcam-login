<?php
require_once('autoload.php');

if(!isset($_POST['username']) || !isset($_POST['base64data']) || !isset($_POST['mail'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}

$img = $_POST['base64data'];
$user = $_POST['username'];
$email = $_POST['mail'];

if(strlen($user) == 0 || strlen($img) == 0 || strlen($email) == 0){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}

if(strlen($user) == 0 || strlen($img) == 0 || strlen($email) == 0){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Not all fields were filled out.'
		)
	);
	die();
}

$reg = new FaceRecognition($user);

$res = $reg->register($email, $img);


if($res != false){ 
	echo json_encode(
		array(
			'val' => $res['val'], 
			'responseText' => $res['responseText']
		)
	);
	die();
}
?>