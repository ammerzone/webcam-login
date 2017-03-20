<?php
require_once('autoload.php');

$logout = new FaceRecognition(NULL);

$logout->logout();

echo json_encode(true);
die();
?>