<?php

$host = getenv('DB_HOST') ?: 'u543420.mysql.masterhost.ru';
$user = getenv('DB_USER') ?: 'u543420';
$password = getenv('DB_PASSWORD') ?: 'Dying.E5tIsOt';
$database = getenv('DB_NAME') ?: 'u543420';

$con = mysqli_connect($host, $user, $password, $database);

mysqli_query($con, "SET NAMES 'utf8'");

if(!$con){
    die('Connection Failed'. mysqli_connect_error());
}

?>