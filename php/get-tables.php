<?php

session_start();
require "functions.php";
$db=db_connect();


//On supprime les tables sans joueurs & celles qui ont timeout

$timeout=time()-300;

$query=$db->prepare("SELECT table_id FROM tables WHERE table_nb_joueur=0 OR table_time<:timeout");

$query->execute(array(
			"timeout"=>$timeout
			));

while ($data = $query->fetch())
{
$del=$db->prepare("DELETE FROM online_table WHERE table_id = :id");

$del->execute(array(
			"id"=>$data['table_id']
			));

}
$del=$db->prepare("DELETE FROM tables WHERE table_nb_joueur=0");

$del->execute();


$query=$db->prepare("SELECT * FROM tables WHERE table_nb_joueur<3 AND table_croupier='' ORDER BY  table_nb_joueur DESC");
$query->execute();

$count=$query->rowCount();

if ($count !=0){
	
	$json['error']='0';
	$i=0;
	while ($data=$query->fetch()){
		
		$infos["admin"]=0;
		
		if ($data['table_owner']==$_SESSION['id'])
		{
			$infos["admin"]=1;}
		
		$infos["id"]=$data['table_id'];
		$infos["nom"]=$data['table_nom'];
		$infos["nbJ"]=$data['table_nb_joueur'];
		
		
		$tables[$i]=$infos;
		$i++;
		
		
	}
	
	$json['list']=$tables;
	

	
	
	
}else{
	
	$json['error']='1';
	
}



	echo json_encode($json);






?>
