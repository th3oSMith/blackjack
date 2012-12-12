<?php

session_start();

require "functions.php";
$db=db_connect();

$json['error']=1;

if (isset($_POST['login'])){
	
	
	
	
	$verif=$db->prepare("SELECT user_id FROM users WHERE user_login=:login AND user_mdp=:mdp");
	$verif->execute(array(
			"login"=>$_POST['login'],
			"mdp"=>sha1($_POST['password'])
			));
			
	$count=$verif->rowCount();
	
	if ($count!=0) {

		$data=$verif->fetch();
		$_SESSION['id']=$data['user_id'];
		$json['error']=0;
	
}
}

echo json_encode($json);


?>
