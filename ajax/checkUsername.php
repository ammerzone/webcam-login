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
		printResponse(false, 'Username bereits vergeben.');

}
printResponse(true, 'Username frei.');
?>