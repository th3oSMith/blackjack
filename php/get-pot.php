<?php

session_start();
require "functions.php";

$db=db_connect();
$user=get_user($db);


$json['pot']=$user['user_pot'];

echo json_encode($json);

?>
