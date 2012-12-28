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



function resetChallenge($db, $perte) {
	
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

function get_tokens($user_id,$db){ //Pot du joueur
	
	$query = $db->prepare('SELECT user_pot FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
			
	$pot = $query->fetch()['user_pot'];
	return $pot;
}

function update_tokens($user_id,$amount,$db){ // Montant algébrique à ajouter (gain ou perte)
	
	$query = $db->prepare('SELECT user_pot FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
			
	$pot = $query->fetch()['user_pot'];
	$pot = $pot + $amount;
	
	$query = $db->prepare('UPDATE users SET user_pot = :pot WHERE user_id = :id');  // UPDATE users SET user_pot=user_pot + :amount
	$query->execute(array(
			'id' => $user_id,
			'pot' => $pot
			));
			
	$query = $db->prepare('SELECT user_pot_max FROM users WHERE user_id = :id');
	$query->execute(array(
			'id' => $user_id
			));
	$pot_max = $query->fetch()['user_pot_max'];
	
	if($pot > $pot_max){
		$query = $db->prepare('UPDATE users SET user_pot_max = :pot_max WHERE user_id = :id');
		$query->execute(array(
			'pot_max' => $pot, //Pot ou Pot Max ????
			'id' => $user_id
			));
	}
			
	return $pot;
}

function immunity_cost($user_id,$immunity_start,$immunity_end,$db){	// Renvoie le coût de l'achat d'une nouvelle immunité
	
	
	$query = $db->prepare('SELECT user_immunity_used FROM users WHERE user_id = :user_id');
	$query->execute(array(
			'user_id' => $user_id
			));
			
	$immunity_number = $query->fetch()['user_immunity_used'];
	$cost = 2000 + 1000*$immunity_number;
	
	$purchase_cost=0;
	
	$i = $immunity_number;
	while($i < ($immunity_number + $immunity_end  - $immunity_start)){
		$cost = $cost + 1000*$i;
		$purchase_cost+=$cost;
		$i++;
	}
	return $purchase_cost;	// A VOIR : COÛT DE BASE D'UNE IMMUNITE ?
}

/**
 * 
 * Règle de cout : on augmente le prix de 50% à chaque fois;
 * 
 **/


function immunize($user_id,$immunity_hour_start,$immunity_hour_end,$db){
	
	
	$query=$db->prepare('UPDATE users SET user_immunity_start = :immunity_start, user_immunity_end = :immunity_end WHERE user_id = :user_id');
	if($immunity_hour_start < date("H")){
		$immunity_start = date("Y") . date("-m-"). (date("d")+1) ." ". $immunity_hour_start . date(":00:00");
		$immunity_end = date("Y") . date("-m-"). (date("d")+1) ." ". ($immunity_hour_end) . date(":00:00");	
	}
	else{
		$immunity_start = date("Y-m-d ") . $immunity_hour_start . date(":00:00");
		$immunity_end = date("Y-m-d ") . ($immunity_hour_end) . date(":00:00");
	}
	
	
	$query->execute(array(
			'immunity_start' => $immunity_start,
			'immunity_end' => $immunity_end,
			'user_id' => $user_id
			));
}


function defeat(){
	
		//Mettre ici l'interfaçage des tranchages avec Kettu
	
	
}


function get_login(){
	
	require 'kettu.class.php'; //On importe le fichier de classe Kettu tiré du www
	

	define( 'KETTU_ENABLE_LINK', true );				// 
	define( 'KETTU_URL', 'https://192.168.92.18' );		//Contantes utilisées par la classe Kettu

	$kettu = new Kettu('ip',$_SERVER['REMOTE_ADDR']);	//On demande gentiement à Kettu de nous renvoyer les infos sur l'utilisateur

	$login='';

	if ($kettu->is_valid()){							//Si l'utilisateur existe
		 $login = strtolower($kettu->firstname).".".strtolower($kettu->name);	//On met en forme son login
		 
		 //On vérifie si l'utilisateur existe sur le casino et dans le cas contraire on le crée
		 
		 $db=db_connect();
		 
		 $query=$db->prepare("SELECT * from users WHERE user_login=:login");
		 
		 $query->execute(array(
					"login"=>$login
					));
					
		if ($query->rowCount()==0){ // Si l'utilisateur n'existe pas
		 
			$write=$db->prepare("INSERT INTO users (user_id,user_login,user_mdp,user_pot,user_victory,user_defeat, user_defi_sum) VALUES(:id, :login,:mdp,:pot,:win,:lose, :defi)");


			$write->execute(array(
			"id"=>'',
			"login"=>$login,
			"mdp"=>$kettu->md5_password,
			"pot"=>"1000",
			"win"=>"0",
			"lose"=>"0",
			"defi"=>"1000"
			));
		 
		 
		 
		}
	}

	return $login;	
}


function add_malus($level, $malus_quantity, $db){
	
	$query=$db->prepare("UPDATE etages SET `".$level."` = `".$level."` +:malus WHERE 1");
	
	$query->execute(array(
			"malus"=>$malus_quantity
			));
			
	
	
}

?>
