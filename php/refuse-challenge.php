<?php

session_start();
require "functions.php";

$db=db_connect();
$user=get_user($db);

if ($user['user_challenge_type']!=-1){
	
	//Utiliser la fonction de Nicolas
	

	//On enlève les jetons au joueur
	$up=$db->prepare("UPDATE users SET user_pot = user_pot - :pot WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['id'],
				"pot"=>$user['user_challenge_type']
				));
				
	//On les rajoute au challenger
	
	$target = unserialize($user["user_challenger"]);
	$target = $target["id"];
	
	$up=$db->prepare("UPDATE users SET user_pot = user_pot + :pot WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$target,
				"pot"=>$user['user_challenge_type']
				));
	
}


$up=$db->prepare("UPDATE users SET user_status=0,user_reponse=0, user_challenge_type=0 WHERE user_id = :id");

$up->execute(array(
			"id"=>$_SESSION['id']
			));



?>
