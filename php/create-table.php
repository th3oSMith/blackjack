<?php

session_start();
require "functions.php";
require "join-table.php";

$db=db_connect();
$user=get_user($db);

//On effectue les paiement en cas de défi


if ($user['user_challenge_type']!=-1){
	
	//Utiliser la fonction de Nicolas
	

	//On enlève les jetons au joueur
	$up=$db->prepare("UPDATE users SET user_pot = user_pot + :pot WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['id'],
				"pot"=>$user['user_challenge_type']
				));
				
	//On les rajoute au challenger
	
	$target = unserialize($user["user_challenger"])["id"];
	
	//A modifier pour équilibrage de l'augmentation de la défisum
	
	$up=$db->prepare("UPDATE users SET user_pot = user_pot - :pot , user_defi_sum = user_defi_sum +200 WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$target,
				"pot"=>$user['user_challenge_type']
				));
	
}


$nom=0;
$challenger=$_POST['challenger'];
	

	
	
	$json['error']='0';	
	$creation=$db->prepare("INSERT INTO tables (table_nom, table_id,table_nb_joueur,table_phase,table_mvt,table_owner) VALUES(:nom, :id, 0,-2,-1,:owner)");

	$creation->execute(array(
					"nom"=>$nom,
					"id"=>'',
					"owner"=>$_SESSION['id']
					));
					
	$id_table=$db->lastInsertId();
	

	$json = joinTable($id_table,$db);
	
	
	//Creation de l'entrée online_tabke
	
	$up=$db->prepare("INSERT INTO online_table VALUES (:id, 9999999999, 9999999999, 9999999999)");
	
	$up->execute(array(
				"id"=>$id_table
				));
	
	//Envoi de la table à l'autre utilisateur
	
	$up=$db->prepare("UPDATE users SET user_status=0, user_reponse=2, user_challenge_type=:table, user_challenger=0 WHERE user_id=:id ;");
	
	$up->execute(array(
					"id"=>$_SESSION['id'],
					"table"=>$id_table
					));
		


echo json_encode($json);




?>
