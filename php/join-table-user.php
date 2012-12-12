<?php
session_start();

require "functions.php";
require "join-table.php";
$db=db_connect();

$json = joinTable($_POST['id'],$db);


echo json_encode($json);




?>
