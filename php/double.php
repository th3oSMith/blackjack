<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);

$user=get_user($db);

if ($user['user_joueur']==$table['table_mvt']){
	

if ($user['user_mise']<=$user['user_pot']){
	
	
	$json['error']=0;
	
	$up=$db->prepare("UPDATE users SET user_pot=user_pot-:mise, user_mise=user_mise+:mise WHERE user_id = :id");
	
	$up->execute(array(
					"mise"=>$user['user_mise'],
					"id"=>$_SESSION['id']
					));
	
	
	
}else{
	
	$json['error']=1;
	
}

echo json_encode($json);


}


?>
