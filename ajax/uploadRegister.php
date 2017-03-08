<?php
require_once('autoload.php');

function printResponse($res, $text){
	echo json_encode(
		array(
			'val' => 			$res, 
			'responseText' =>	$text
		)
	);
	die();
}

if(!isset($_POST['username']) || !isset($_POST['base64data']) || !isset($_POST['mail'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
		)
	);
	die();
}

if(strlen($_POST['username']) == 0 || strlen($_POST['base64data']) == 0 || strlen($_POST['mail']) == 0){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
		)
	);
	die();
}

$img = $_POST['base64data'];
$user = $_POST['username'];
$email = $_POST['mail'];

$reg = new FaceRecognation($user);

$res = $reg->register($email, $img);
if($res != false){ 
	echo json_encode($res);
	die();
}
?>