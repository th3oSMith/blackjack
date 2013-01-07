<?php

session_start();

require "functions.php";
$db=db_connect();

$json['error']=1;

if (isset($_POST['login'])){
	
	
	
	
	$verif=$db->prepare("SELECT user_id FROM users WHERE user_login=:login AND user_mdp=:mdp");
	$verif->execute(array(
			"login"=>$_POST['login'],
			"mdp"=>md5($_POST['password'])
			));
			
	$count=$verif->rowCount();
	
	if ($count!=0) {

		$data=$verif->fetch();
		$_SESSION['id']=$data['user_id'];
		$json['error']=0;
	
}
}

$query=$db->prepare("SELECT user_disconnect_start, user_disconnect_end FROM users where user_login = :login");

$query->execute(array(
			"login"=>$_POST['login']
			));
			
$user=$query->fetch();

$now=new DateTime("now");
$start=new DateTime($user['user_disconnect_start']);
$end=new DateTime($user['user_disconnect_end']);


if ($now->getTimestamp()>$start->getTimestamp() && $now->getTimestamp()<$end->getTimestamp()){$json['error']=2;}


echo json_encode($json);


?>
