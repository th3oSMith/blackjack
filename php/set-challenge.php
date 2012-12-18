<?php

session_start();
require "functions.php";


$db=db_connect();
$table = get_table($db);
$user=get_user($db);
$json['error']=1;
$history=unserialize($user['user_history']);
$delay=20;

if ($history[$_POST['id']]>time()-$delay){
		$json['error']=2;
}

if ($_SESSION['id']!=$_POST['id'] && $json['error']!=2){
	
	$user_challenger['login']=$user['user_login'];
	$user_challenger['id']=$_SESSION['id'];
	
	$user_challenger=serialize($user_challenger);
	
	$sum=-1;
	
	if ($_POST['defi']!=-1){
		
		
		
		$sum=$user['user_defi_sum'];
		
		if ($user['user_pot']<$sum){
			
			$json["error"]=4;
			
			die(json_encode($json));
			
		}
		
		
		}
	
	
	$query=$db->prepare("UPDATE users SET user_challenger=:challenger, user_status=1, user_challenge_type=:jetons, user_reponse=1 WHERE user_id=(SELECT online_user FROM online WHERE online_user=:id) AND user_status=0");

	$query->execute(array(
					"challenger"=>$user_challenger,
					"id"=>$_POST['id'],
					"jetons"=>$sum
					));
				

	
					
	if ($query->rowCount()!=0){ 
		

		$_SESSION['target']=$_POST['id'];
		$history[$_POST['id']]=time();
		
		$history=serialize($history);
		
		$update=$db->prepare("UPDATE users SET user_history=:history WHERE user_id=:id");
		
		$update->execute(array(
						"history"=>$history,
						"id"=>$_SESSION['id']
						));
						
		
		$json['error']=0;
			
	}

}


echo json_encode($json);
?>
