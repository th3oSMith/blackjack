<?php

session_start();
require "functions.php";

$db=db_connect();
$user=get_user($db);

$json['pot']=1;

if ($user['user_debt']>=5000){
	
	$json['pot']=0;
	
}



echo json_encode($json);

?>
