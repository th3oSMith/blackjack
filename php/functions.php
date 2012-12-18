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

function resetChallenge($db) {
	
	if (isset($_SESSION['target']))
	
	{
		
	$up=$db->prepare("UPDATE users SET user_challenger=0, user_status=0 WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['target']
				));
		
	unset($_SESSION['target']);
	
		}


function update_tokens($user_id,$amount,$db){ // Montant algébrique à ajouter (gain ou perte)
	
	$query = $db->prepare('SELECT user_pot FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
			
	$pot = $query->fetch();
	$pot = $pot + $amount;
	
	$query = $dbb->prepare('UPDATE users SET user_pot = :pot WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id,
			'pot' => $pot
			));
			
	return $pot;
}

function is_immunized($user_id,$db){	// Renvoie vrai si le joueur a une immunité
	$query = $db->prepare('SELECT * FROM users WHERE user_immunity_pending = 1 AND user_id = :user_id');
	$query->execute(array(
			'user_id' => $user_id
			));
			
	$immunized=$query->fetch();	

	return $immunized;
}

function immunity_cost($user_id,$db){	// Renvoie le coût de l'achat d'une nouvelle immunité
	$query = $db->prepare('SELECT user_immunity_used WHERE user_id = :user_id');
	$query->execute(array(
			'user_id' => $user_id
			));
	$immunity_number = $query->fetch();
	
	return (2000 + 1000*$immunity_number);	// A VOIR : COÛT DE BASE D'UNE IMMUNITE ?
}
?>