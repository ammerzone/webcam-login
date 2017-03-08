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

if(strlen($user) < 5)
	printResponse(false, 'Username muss mind. 5 Zeichen lang sein.');

if(file_exists('../media/user/login/' . $user . '.jpeg'))
	printResponse(false, 'Username bereits vergeben.');

$db = new DatabaseConnection();

$row = $db->query(
	'SELECT `name` FROM `user` WHERE `name` = :user', 
	array('user' => $user)
);

if($row)
	printResponse(false, 'Username bereits vergeben.');


printResponse(true, 'Username frei.');
?>
