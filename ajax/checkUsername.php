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

if(!isset($_POST['username']))
	printResponse(false, '');

$user = $_POST['username'];

if(strlen($user) < 5)
	printResponse(false, 'User name must have a minimum size of 5.');

$db = new DatabaseConnection();

$row = $db->query('SELECT `name` FROM `user`');

if(sizeof($row) > 0){
	$res = false;
	foreach($row as $key => $val){
		if($val['name'] == $user){
			$res = true;
			break;
		}
	}
	if($res === true)
		printResponse(false, 'User name already exists.');

}
printResponse(true, 'User name valid.');
?>