<?php
require 'dataConnection.php';

$host = $connectionArray['host'];
$user = $connectionArray['user'];
$pass = $connectionArray['pass'];
$db = $connectionArray['db'];

new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);

