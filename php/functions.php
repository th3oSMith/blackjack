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

<<<<<<< HEAD

function resetChallenge($db, $perte) {
=======
function resetChallenge($db) {
>>>>>>> b5d1cfdd63fff8cf33298174fd2062a4e1b98947
	
	$user=get_user($db);
	
	if (isset($_SESSION['target'])){
		
		
	if ($perte){
		
		

	//On enlève les jetons au joueur
	$up=$db->prepare("UPDATE users SET user_pot = user_pot - :pot WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['target'],
				"pot"=>$user['user_defi_sum']
				));
				
	//On les rajoute au challenger
	
	
	$up=$db->prepare("UPDATE users SET user_pot = user_pot + :pot WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['id'],
				"pot"=>$user['user_defi_sum']
				));
		
		
		
		
		
	}
		
	$up=$db->prepare("UPDATE users SET user_challenger=0, user_status=0, user_reponse=0 WHERE user_id=:id");
	
	$up->execute(array(
				"id"=>$_SESSION['target']
				));
		
	unset($_SESSION['target']);
	
		}
}

function get_tokens($user_id,$db){ // Montant algébrique à ajouter (gain ou perte)
	
	$query = $db->prepare('SELECT user_pot FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
			
	$pot = $query->fetch();
	return $pot;
}

function update_tokens($user_id,$amount,$db){ // Montant algébrique à ajouter (gain ou perte)
	
	$query = $db->prepare('SELECT user_pot FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
			
	$pot = $query->fetch();
	$pot = $pot + $amount;
	
	$query = $db->prepare('UPDATE users SET user_pot = :pot WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id,
			'pot' => $pot
			));
			
	$query = $db->prepare('SELECT user_pot_max FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
	$pot_max = $query->fetch();
	
	if($pot > $pot_max){
		$query = $db->prepare('UPDATE users SET user_pot_max = :pot_max WHERE user_id = :id');
		$query->execute(array(
			'pot_max' => $pot_max,
			'id' => $user_id
			));
	}
			
	return $pot;
}

function immunity_cost($user_id,$immunity_start,$immunity_end,$db){	// Renvoie le coût de l'achat d'une nouvelle immunité
	$query = $db->prepare('SELECT user_immunity_used WHERE user_id = :user_id');
	$query->execute(array(
			'user_id' => $user_id
			));
	$immunity_number = $query->fetch();
	$cost = 2000 + 1000*$immunity_number;
	$i = $immunity_number;
	while($i < ($immunity_number + $immunity_end  - $immunity_start)){
		$cost = $cost + 1000*$i;
	}
	return $cost;	// A VOIR : COÛT DE BASE D'UNE IMMUNITE ?
}

function immunize($user_id,$immunity_hour_start,$db){
	$query = $db->prepare('UPDATE users SET user_immunity_start = :immunity_start user_immunity_end = :immunity_end WHERE user_id = :user_id');
	if($immunity_hour_start < date("H")){
		$immunity_start = date("Y-"). (date("d")+1) . date("-m ") . $immunity_hour_start . date(":i:00");
		$immunity_end = date("Y-"). (date("d")+1) . date("-m ") . ($immunity_hour_start+1) . date(":i:00");	// Durée de l'immunité : 1 heure
	}
	else{
		$immunity_start = date("Y-d-m") . $immunity_hour_start . date(":i:00");
		$immunity_end = date("Y-d-m") . ($immunity_hour_start+1) . date(":i:00");
	}
	$query->execute(array(
			'immunity_start' => $immunity_start,
			'immunity_end' => $immunity_end,
			'user_id' => $user_id
			));
}

?>
