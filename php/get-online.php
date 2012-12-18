<?php
session_start();

require "functions.php";

$db=db_connect();
$user=get_user($db);

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
}

$query->closeCursor();


$time_out=time()-5;
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
