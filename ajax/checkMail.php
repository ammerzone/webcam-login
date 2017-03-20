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

if(!isset($_POST['email']))
	printResponse(false, '');

$email = $_POST['email'];

if(filter_var($email, FILTER_VALIDATE_EMAIL)){
	$db = new DatabaseConnection();
	$row = $db->query('SELECT `email` FROM `user`');
	
	if(sizeof($row) > 0){
		$res = false;
		foreach($row as $key => $val){
			if($val['email'] == $email){
				$res = true;
				break;
			}
		}
		if($res === true)
			printResponse(false, 'E-Mail adress already exists.');
	}
	
	printResponse(true, 'E-Mail adress is valid.');
}else{
	printResponse(false, 'E-Mail adresse is invalid.');
}

echo json_encode($res);
?>