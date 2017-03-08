<?php
require_once('autoload.php');

$email = $_POST['email'];

if(filter_var($email, FILTER_VALIDATE_EMAIL)){
	$db = new DatabaseConnection();
	$row = $db->query(
		'SELECT `email` FROM `user` WHERE `email` = :email', 
		array('email' => $email)
	);
	if($row){
		$res = array(
			'val' => 			false, 
			'responseText' => 	'E-Mail Adresse bereits vergeben.'
		);
	}else{
		$res = array(
			'val' => 			true, 
			'responseText' => 	'E-Mail Adresse erfüllt Kriterien.'
		);
	}
}else{
	$res = array(
		'val' => 			false, 
		'responseText' => 	'E-Mail Adresse ist ungültig.'
	);
}

echo json_encode($res);
?>