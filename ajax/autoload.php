<?php
session_start();

$url = explode('ajax', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'])[0];
define('BASE_URL', $url);

require_once('../oop/DatabaseConnection.class.php');
require_once('../oop/FaceRecognition.class.php');
?>