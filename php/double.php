<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);

$user=get_user($db);

if ($user['user_joueur']==$table['table_mvt']){
	

if ($user['user_mise']+$user['debt']<5000){ //Arbitraire
	
	$json['error']=0;
	
	$up=$db->prepare("UPDATE users SET user_debt=user_debt+:mise, user_mise=user_mise+:mise WHERE user_id = :id");
	
	$up->execute(array(
					"mise"=>$user['user_mise'],
					"id"=>$_SESSION['id']
					));
	
	$up=$db->prepare("UPDATE tables SET table_pot= table_pot + :mise WHERE table_id=:id");
	
	$up->execute(array(
				"mise"=>$user['user_mise'],
				"id"=>$table['table_id']
				));
	
	
}else{
	
	$json['error']=1;
	
}

echo json_encode($json);


}


?>
