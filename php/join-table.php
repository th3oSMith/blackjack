<?php

function joinTable($table_id,$db){
	
	$table=get_table($db);
	$user=get_user($db);
	
	//Verification que la table destination n'est pas pleine
	
	$query=$db->prepare("SELECT table_nb_joueur FROM tables WHERE table_id = :id");
	
	$query->execute(array(
				"id"=>$table_id
				));
				
	$data=$query->fetch();
	
	if ($data["table_nb_joueur"]< 3 && $user["user_table"]!=$table_id && $user['user_pot']>0){
		
		$json['error']=	'0';
	
	
	//Desinscription du membre de la table précédente

	$desiscription=$db->prepare("UPDATE tables SET table_nb_joueur=table_nb_joueur-1 WHERE table_id=(SELECT user_table FROM users WHERE user_id=:id_joueur) ");

	$desiscription->execute(array(
						"id_joueur"=>$_SESSION['id']
						));

	
	$update=$db->prepare("UPDATE users SET user_table=:table_id, user_joueur=:joueur, user_main=NULL, user_mise=0 WHERE user_id=:id");

	$update->execute(array(
				"id"=>$_SESSION['id'],
				"table_id"=>$table_id,
				"joueur"=>$data["table_nb_joueur"]+1
				));
				
	$update=$db->prepare("UPDATE tables SET table_nb_joueur=table_nb_joueur+1 WHERE table_id = :id");
	
	$update->execute(array(
				"id"=>$table_id
				));
				
		//Suppression des tables vides


$query=$db->prepare("SELECT table_id FROM tables WHERE table_nb_joueur=0");

$query->execute();

while ($dataA = $query->fetch())
{
$del=$db->prepare("DELETE FROM online_table WHERE table_id = :id");

$del->execute(array(
			"id"=>$dataA['table_id']
			));

}
$del=$db->prepare("DELETE FROM tables WHERE table_nb_joueur=0");

$del->execute();


				
	$json['joueur']=$data["table_nb_joueur"]+1;
	$json['table_id']=$table_id;
				
				
	}else{
		
		$json['error']='1';
		
		if ($user['user_pot']==0){$json['error']='2';}
		
		
	}
	
	return $json;
	
}










?>
