<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);

$delTable=$db->prepare("UPDATE tables SET table_nb_joueur=table_nb_joueur-1 WHERE table_id = (SELECT user_table FROM users WHERE user_id=:id)");
$delTable->execute(array(
			"id"=>$_SESSION['id']
			));

$delTable=$db->prepare("UPDATE users SET user_table=NULL WHERE user_id=:id");

$delTable->execute(array(
				"id"=>$_SESSION['id']
				));


$json['error']=0;

echo json_encode($json);



?>
