<?php
require_once('autoload.php');

$logout = new FaceRecognation(NULL);

$logout->logout();

echo json_encode(true);
die();
?>