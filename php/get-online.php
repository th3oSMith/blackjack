<?php
session_start();

require "functions.php";

$db=db_connect();
$user=get_user($db);

$json['challenger']=0;

$json['login']=$user['user_login'];
$json['pot']=$user['user_pot'];


$query=$db->prepare("SELECT * FROM online WHERE online_user=:user");

$query->execute(array("user"=>$_SESSION['id']));

$count=$query->rowCount();
$data=$query->fetch();


if (user_verified()){
	

	if ($count==0){
		$insert=$db->prepare('INSERT INTO `online` values(:id,:ip,:user,:time);');
		$insert->execute(array(
			'id'=>'',
			'user'=>$_SESSION['id'],
			'ip' => $_SERVER["REMOTE_ADDR"],
			'time'=>time()
			));
		}else{
			
			$update=$db->prepare('UPDATE online SET online_time=:time WHERE online_user=:user');
			
			$update->execute(array(
				'time'=>time(),
				'user'=>$_SESSION['id']
				));
		}	

//Récupération des challenges

$getChallenge=$db->prepare("SELECT user_challenger, user_challenge_type FROM users WHERE user_id=:id AND user_status=1");

$getChallenge->execute(array(
				"id"=>$_SESSION['id']
				));
				
				
				
if ($getChallenge->rowCount()!=0){
	
	$data=$getChallenge->fetch();
	
	$challenger=unserialize($data['user_challenger']);
	
	
	$json['challenger']=$challenger;
	
	if ($data['user_challenge_type']==-1){
		
		$json['challenger']['type']='duel';
		
	}
	
}



}

$query->closeCursor();


$time_out=time()-5;

$query=$db->prepare("SELECT online_user, user_login
					FROM online
					JOIN users
					ON users.user_id=online.online_user
					WHERE online_time < :time");

$query->execute(array(
	"time"=>$time_out
	));
	
	
	
while ($data=$query->fetch())
{
	
	$challenger['login']=$data["user_login"];
	$challenger['id']=$data["online_user"];
	
	
	$challenger=serialize($challenger);
	
	$_SESSION['debug']=$challenger;
	
	
	$up=$db->prepare("UPDATE users SET user_status=0, user_challenger=0 WHERE user_challenger=:challenger");
	
	$up->execute(array(
				"challenger"=>$challenger
			));
	
	}


$delete=$db->prepare("DELETE FROM online WHERE online_time < :time");
$delete->execute(array(
	"time"=>$time_out
	));

$query=$db->prepare("SELECT user_login, user_id, user_pot
					FROM online
					LEFT JOIN users
					ON users.user_id=online.online_user;"
					);
					
$query->execute();
$count=$query->rowCount();

if ($count!=0){
	$json['error']='0';
	$i=0;
	
	while ($data=$query->fetch()){
		
		$infos["id"]=$data['user_id'];
		$infos["login"]=$data["user_login"];
		$infos["pot"]=$data["user_pot"];
		
		$accounts[$i]=$infos;
		$i++;
		}
		
	$json['list']=$accounts;
	
}else{
	$json['error']='2';

}

$query->closeCursor();

echo json_encode($json);






?>
