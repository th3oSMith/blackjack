<?php

function db_connect() {
	// définition des variables de connexion à la base de données	
	try {
		$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		// INFORMATIONS DE CONNEXION
		$host = 	'localhost';
		$dbname = 	'poker';
		$user = 	'poker';
		$password = 	'pokerface';
		// FIN DES DONNEES
		
		$db = new PDO('mysql:host='.$host.';dbname='.$dbname.'', $user, $password, $pdo_options);
		return $db;
	} catch (Exception $e) {
		die('Erreur de connexion : ' . $e->getMessage());
	}
}

function user_verified() {
	return isset($_SESSION['id']);
}


function get_table($db){

	$query=$db->prepare("SELECT * FROM tables WHERE table_id=(SELECT user_table FROM users WHERE user_id=:id_joueur)");
	
	
	$query->execute(array(
				"id_joueur"=>$_SESSION['id']
				));
				
	return $query->fetch();

}

function get_user($db){
	
	$query=$db->prepare("SELECT * FROM users WHERE user_id=:id");
	
	$query->execute(array(
					"id"=>$_SESSION['id']
					));
					
	return $query->fetch();
	
}


function absent($db,$table,$timeout){
	
	$query=$db->prepare("SELECT * FROM (SELECT * from online_table WHERE table_id=:id ) AS test WHERE 
				online_player1<:timeout ");
		
		$query->execute(array(
				"timeout"=>$timeout,
				"id"=>$table['table_id']
				));
			
		$joueur=3;
		
		if ($query->rowCount()!=0){
			$joueur=1;
			}
			
		$query=$db->prepare("SELECT * FROM (SELECT * from online_table WHERE table_id=:id ) AS test  WHERE 
				online_player2<:timeout ");
		
		
		$query->execute(array(
				"timeout"=>$timeout,
				"id"=>$table['table_id']
				));
			
				
		if ($query->rowCount()!=0){
			$joueur=2;
			}
	
	return $joueur;
	
}


?>