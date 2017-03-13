<?php
require_once('autoload.php');

$email = $_POST['email'];

function printResponse($res, $text){
	echo json_encode(
		array(
			'val' => 			$res, 
			'responseText' =>	$text
		)
	);
	die();
}

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
			printResponse(false, 'E-Mail Adresse bereits vergeben.');
	}
	
	printResponse(true, 'E-Mail Adresse erfüllt Kriterien.');
}else{
	printResponse(false, 'E-Mail Adresse ist ungültig.');
}

echo json_encode($res);
?>