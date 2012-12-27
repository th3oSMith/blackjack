<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);
$user=get_user($db);

$query=$db->prepare("SELECT user_main, user_joueur,user_mise,user_login,user_pot,user_id FROM users WHERE user_table=:table");

$query->execute(array(
			"table"=>$table['table_id']
			));



while ($data=$query->fetch()){
	

	
	
	$json['mise'][$data['user_joueur']]=$data['user_mise'];
	$json['nick'][$data['user_joueur']]=$data['user_login'];
	$json['pot'][$data['user_joueur']]=$data['user_pot'];
	$json['debt'][$data['user_joueur']]=$data['user_debt'];

	if ($data['user_id']==$_SESSION['id'] || $table['table_phase']==4){
		
		$json['main'][$data['user_joueur']]=unserialize($data['user_main']);
		
		}else{
		
		$json['main'][$data['user_joueur']][0]=unserialize($data['user_main'])[0];
		$json['main'][$data['user_joueur']][1]=array(9,9);
			
		}

	if ($table['table_phase']<0){
	$json['main'][$data['user_joueur']]=array(array(9,9),array(9,9));
	}


}




$paquet=unserialize($table['table_cartes']);
//$json['main'][0]=unserialize($table['table_croupier']);

$json['nb_joueur']=$table['table_nb_joueur'];

if ($table['table_phase']<0){
	$json['main'][0]=array(array(9,9));
	}


//On notifie que le joueur est toujours vivant

$joueur="online_player".$user['user_joueur'];


$up=$db->prepare("UPDATE online_table SET `".$joueur."` = :time WHERE table_id = :id");



$up->execute(array(
			"id"=>$table['table_id'],
			"time"=>time()
			));


//On vérifie qu'aucun joueur ne s'est déconnecté

$timeout=time()-5;

$query=$db->prepare("SELECT * FROM (SELECT * from online_table WHERE table_id=:id ) AS test WHERE 
				online_player1<:timeout OR online_player2<:timeout OR online_player3<:timeout");
				

				
$query->execute(array(
				"timeout"=>$timeout,
				"id"=>$table['table_id']
				));

$dataS=$query->fetch();

$json['logout']=0;


if ($query->rowCount()!=0 && $table['table_phase']!=-8 && $table['table_phase']!=-7 ){

		//On recherche qui s'est déconecté !
		
		$joueur=absent($db,$table,time()-5);
		
	
		$json['logout']=1;
		
		
		$save_phase=$table['table_phase'];
		$save_mvt=$table['table_mvt'];
		
		
		
		
		if ($joueur==$table['table_nb_joueur'] && $save_phase!=-2){
			
			$save_mvt=1;
			$save_phase++;
			
		}
		
		
		$table_save=array($save_mvt,$save_phase,$joueur);
		
		$mvt=1;
		$owner=$table['table_owner'];
		
		
		if ($joueur==1){$mvt=2;
		
		//On choisit l'owner
		
		$query=$db->prepare("SELECT user_id FROM users WHERE user_table=:table AND user_joueur=2");
		
		$query->execute(array(
					"table"=>$table['table_id']
					));
					
		$owner=$query->fetch()['user_id'];
		
		
		
		}
		
		$update=$db->prepare("UPDATE tables SET table_owner=:owner, table_nb_joueur=table_nb_joueur-1 ,table_save=:save, table_mvt=:mvt, table_phase=-8 WHERE table_id=:id");
			
		$update->execute(array(
					"id"=>$table['table_id'],
					"mvt"=>$mvt,
					"save"=>serialize($table_save),
					"owner"=>$owner
					));
		
		//$absent="online_player".$joueur;
	/*	
		$up=$db->prepare("UPDATE online_table SET online_player1 = 10000000000, online_player2=10000000000, online_player3=10000000000 WHERE table_id = :id");
		
		
		$up->execute(array(
					"id"=>$table['table_id']
					));
		*/
					
		//On desinscrit le joueur de la table
		
		$up=$db->prepare("UPDATE users SET user_table = NULL WHERE user_table=:id AND user_joueur=:joueur");
		
		$up->execute(array(
					"id"=>$table['table_id'],
					"joueur"=>$joueur
					));
		
	}


echo json_encode($json);
	
	
	




?>

