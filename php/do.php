<?php

session_start();
require "functions.php";

$db=db_connect();
$table = get_table($db);
$user=get_user($db);

$query=$db->prepare("SELECT user_joueur FROM users WHERE user_id=:id");

$query->execute(array(
				"id"=>$_SESSION['id']
				));
				
				
;
$joueur=$query->fetch();
$joueur=$joueur['user_joueur'];


if ($joueur==$table['table_mvt']){

	$json['error']=0;

	switch ($table['table_phase']){
		
		
		case -2: //Initialisation des cartes de la partie
		
		//Creation du paquet de cartes 
	
		$i=0;
			
		for ($couleur=0;$couleur<4;$couleur++){
			for ($carte=1;$carte<14;$carte++){
				
				$paquet[$i][0]=$couleur;
				$paquet[$i][1]=$carte;
				
				$i++;
				}
		}
		
		//Mélange des cartes
		
		shuffle($paquet);
		
		
		$croupier[0]=$paquet[0];	
		
		
		$txtCroupier=serialize($croupier); // On laisse les cartes pour le croupier même s'il n'existe plus, il doit y avoir un test dessus quelque part.
		$txtPaquet=serialize($paquet);
		
		
		
		$update=$db->prepare("UPDATE tables SET table_pot=0, table_time=:time, table_croupier=:croupier, table_cartes=:paquet, table_cursor=1+2*table_nb_joueur, table_phase=table_phase+1 WHERE table_id= :id");
		
		$update->execute(array(
						"paquet"=>$txtPaquet,
						"id"=>$table['table_id'],
						"croupier"=>$txtCroupier,
						"time"=>time()
						));
						
						
		//Attribution des mains aux différents participants
		
		
		
		for ($i=1;$i<=$table['table_nb_joueur'];$i++){
			
			$main[0]=$paquet[1+2*($i-1)];
			$main[1]=$paquet[1+2*($i-1)+1];
			
			$txtMain=serialize($main);
			
			$getID=$db->prepare("SELECT user_id FROM users WHERE user_joueur=:joueur AND user_table= :id");
			
			$getID->execute(array(
						"joueur"=>$i,
						"id"=>$table['table_id']
						));
						
			$tmp=$getID->fetch();
			
			$deal=$db->prepare("UPDATE users SET user_main= :main WHERE user_id= :id");
			$deal->execute(array(
							"main"=>$txtMain,
							"id"=>$tmp['user_id']
							));							
			
		}
		
		
		

		
		
		
		break;
		
		case -1:
		$query=$db->prepare("SELECT user_pot FROM users WHERE user_id=:id");
		
		$query->execute(array(
						"id"=>$_SESSION['id']
						));
		
			
						
		if ($_POST['mise']+$user['user_debt']<5000 && $_POST['mise']>0){
			
			$json['error']=0;
			

			
			$miser=$db->prepare("UPDATE users SET user_debt=user_debt+:mise, user_mise=:mise WHERE user_id = :id");
			
			$miser->execute(array(
						"mise"=>$_POST['mise'],
						"id"=>$_SESSION['id']
						));
						
			$up=$db->prepare("UPDATE tables SET table_pot= table_pot + :mise WHERE table_id=:id");
			
			$up->execute(array(
						"mise"=>$_POST['mise'],
						"id"=>$table['table_id']
						));
			
			suivant($db,$table);
			$json['mise']=$_POST['mise'];
			
		}else{
			
			if ($_POST['mise']<0){
				
				 $json['error']=2; 
				 }
			else{
				$json['error']=1;
		}
			
		}
		
		
		break;
		
		
		
		case 0: // Récupération des main et du casino par chacun des joueurs

		//On avance le joueur
		
		suivant($db,$table);
			
		break;
		
		case 1 : //Tour de jeu de chaque Joueur
		
		//On avance le joueur
		suivant($db,$table);
		
		break;
		
		case 2 : // Tour de comptage des points
		
		//Calcul des points des joueurs
		
		
		$query=$db->prepare("SELECT user_main FROM users WHERE user_table = :id ORDER BY user_joueur ASC");
			
		$query->execute(array(
							"id"=>$table['table_id'],
							));
		
		
		$i=1;	
		while ($data=$query->fetch()){
			
			

						
			$main=unserialize($data['user_main']);
			
			$total=0;
			
			$total=score($main);
			
			$score[$i]=$total;
			$i++;
			}
			
			
		//On met à zéro le score des gens qui sont brulés
	
	
		for ($x=1;$x<=count($score);$x++)
		{
			if ($score[$x]>21){$score[$x]=0;} //on ne s'occupe pas des perdants
			
			}
		
		//Détermination du plus haut score
		
		
		
		
		$maxi = max($score);
		$paquet=unserialize($table['table_cartes']);
		
		//On passe à la phase suivante
		
		
			$update=$db->prepare("UPDATE tables SET table_croupier=:maxi, table_phase=table_phase+1 WHERE table_id=:id");
			
			$update->execute(array(
						"id"=>$table['table_id'],
						"maxi"=>$maxi
						));	

			break;
			
			case 3: //Récupération du score de chaque joueur & des cartes du croupier
			
			
			$score_joueur=score(unserialize($user['user_main']));
			$maxi=$table['table_croupier'];
			
			
			$duree=$user['user_mise'];
			$gain=0;
			$coeff=2;
			
			if ($score_joueur==21){
				
				$coeff=3;
				
			}
			
			if ($score_joueur<22){
				
				 //Si le joueur est en course
				

						
					if ($score_joueur==$maxi){ //Si le joueur a gagné
						$gain=$table['table_pot'];
					}else{ // Le joueur a perdu
						
						defeat($log,$duree);
						
					}
						
					
				
				
			}else{ //Le joueur a perdu car brulé
				
				//Insérer le script lien avec Kettu pour trancher les gens
				defeat($log,$duree);
				
			}
					
					
			
				
				$json['gain']=$gain-$user['user_mise'];
				
				
				
				$up=$db->prepare("UPDATE users SET user_pot = user_pot + :gain, user_mise=0 WHERE user_id =:id");
				
				$up->execute(array(
							"id"=>$_SESSION['id'],
							"gain"=>$gain
							));
				
				
			suivant($db,$table);
			
			break;
			
			case -7:
			
			
			$update=$db->prepare("UPDATE tables SET table_mvt=:mvt, table_phase=:phase WHERE table_id=:id");
			
			$mvtA=unserialize($table['table_save']);
			$phaseA=unserialize($table['table_save']);
			
			$update->execute(array(
						"id"=>$table['table_id'],
						"mvt"=>$mvtA[0],
						"phase"=>$phaseA[1]
						));

			$up=$db->prepare("UPDATE online_table SET online_player1 = 10000000000, online_player2=10000000000, online_player3=10000000000 WHERE table_id = :id");
		
		
			$up->execute(array(
					"id"=>$table['table_id']
					));
			
			break;
			
			
			case -8:
			
			$timeout=time()-5;
			$absent=unserialize($table['table_save']);
			$absent=$absent[2];
			
			
			$json['nb_joueur']=$table['table_nb_joueur'];
			
			
			
			if ($user['user_joueur']<$absent){
				
				$json['joueur']=$user['user_joueur'];
				
			}else{
				
				
				
				$up=$db->prepare("UPDATE users SET user_joueur=user_joueur-1 WHERE user_id=:id");
				
				$up->execute(array(
							"id"=>$_SESSION['id']
							));
				
				$json['joueur']=$user['user_joueur']-1;
							
				
			}
			
			suivant($db,$table);
			
			break;
		
		
		
		

}
	
}else{
	
	$json['error']=1;
	
	
	}


echo json_encode($json);


	


function suivant($db,$table){
	
			if ($table['table_mvt']<$table['table_nb_joueur']){
			
			$update=$db->prepare("UPDATE tables SET table_mvt=table_mvt+1, table_cursor=table_cursor+2 WHERE table_id = :id");
			
			$update->execute(array(
						"id"=>$table['table_id']
						));
		}else{ //On passe à la phase de jeu suivante
		
			$update=$db->prepare("UPDATE tables SET table_mvt=1, table_phase=table_phase+1 WHERE table_id=:id");
			
			$update->execute(array(
						"id"=>$table['table_id']
						));
			
			
		}
}


function score($main)
{
	$total=0;
	$nbAs=0;
			
			
			for ($y=0;$y<count($main);$y++){
				
				
				if ($main[$y][1]<11){
					
					$total+=$main[$y][1];
					
					
				}else{
					
					$total+=10;
				}
				
				if ($main[$y][1]==1){
					
					$total+=10; //on ajoute 10 pour avoir 11
					$nbAs++; // On note qu'on a eu un as
					
				}
				
				
			}
			
			while ($total > 21 && $nbAs>0) {
				
				$total-=10;
				$nbAs--;
				
			}
	
	return $total;
	
	
	}
?>
