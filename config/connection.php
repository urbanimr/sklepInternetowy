<?php
require 'dataConnection.php';

$host = $connectionArray['host'];
$user = $connectionArray['user'];
$pass = $connectionArray['pass'];
$db = $connectionArray['db'];
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

$conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass, $options);

