<?php

session_start();
require "functions.php";
require "join-table.php";

$db=db_connect();

$nom=$_POST['name'];

$query=$db->prepare("SELECT * FROM tables WHERE table_nom=:nom");

$query->execute(array(
				"nom"=>$nom
				));

$count=$query->rowCount();

if ($count==0){
	

	
	
	$json['error']='0';	
	$creation=$db->prepare("INSERT INTO tables (table_nom, table_id,table_nb_joueur,table_phase,table_mvt,table_owner) VALUES(:nom, :id, 0,-2,-1,:owner)");

	$creation->execute(array(
					"nom"=>$nom,
					"id"=>'',
					"owner"=>$_SESSION['id']
					));
					
	$id_table=$db->lastInsertId();

	$json = joinTable($id_table,$db);
	
	//Creation de l'entrée online_tabke
	
	$up=$db->prepare("INSERT INTO online_table VALUES (:id, 9999999999, 9999999999, 9999999999)");
	
	$up->execute(array(
				"id"=>$id_table
				));
	
			
}else
{
	$json['error']='1';
	
	
	}


echo json_encode($json);




?>
