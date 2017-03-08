<?php
require_once('autoload.php');

$user = $_POST['username'];

function printResponse($res, $text){
	echo json_encode(
		array(
			'val' => 			$res, 
			'responseText' =>	$text
		)
	);
	die();
}

$reg = new FaceRecognation($user);

$res = $reg->checkUser();
if($res === false) printResponse($res, $reg->responseText);

printResponse(true, 'Username frei.');
?>