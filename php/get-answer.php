<?php

session_start();
require "functions.php";


$db=db_connect();
$table = get_table($db);
$user=get_user($db);
$expiration=20;
$history=unserialize($user['user_history']);
$json['error']=0;


$test_tmp=$history[$_SESSION['target']]+$expiration;


$query=$db->prepare("SELECT user_reponse, user_challenge_type FROM users WHERE user_id=:target");

$query->execute(array(
				"target"=>$_SESSION['target']
				));
				
$data=$query->fetch();
				
$reponse=$data["user_reponse"];


$expired=false;

if (time()>$test_tmp){
	
	resetChallenge($db,true);
	
	$expired=true;
	
}
	
	
if ($reponse!=1 || $expired){
	
	if ($reponse==2){
		
		//Pas d'erreur
		$json['error']=1;
		
		$json["table_id"]=$data['user_challenge_type'];
		
	}else{
		
		$json['error']=2;
		
	}
	
}


echo json_encode($json);


?>
