<?php
require_once('autoload.php');

if(!isset($_POST['username']) || !isset($_POST['base64data'])){
	echo json_encode(
		array(
			'val' => false, 
			'responseText' => 'Es wurden nicht alle Felder ausgefüllt.'
		)
	);
	die();
}
	
$user = $_POST['username'];
$img = $_POST['base64data'];	

$login = new FaceRecognation($user);

$res = $login->login($img);

echo json_encode($res);
die();
?>