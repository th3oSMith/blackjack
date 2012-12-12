<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);

$joueur=get_user($db);

if ($joueur['user_joueur']==$table['table_mvt']){
	
	echo "test";
	
	
	$query=$db->prepare("SELECT user_main FROM users WHERE user_id=:id");
	
	$query->execute(array(
					"id"=>$_SESSION['id']
					));
					
	$main=unserialize($query->fetch()['user_main']);
	
	$paquet=unserialize($table['table_cartes']);
	
	$main[]=$paquet[$table['table_cursor']];
	
	$up=$db->prepare("UPDATE users SET user_main=:main WHERE user_id=:id");
	
	$up->execute(array(
				"main"=>serialize($main),
				"id"=>$_SESSION['id']
				));
				
	$up=$db->prepare("UPDATE tables SET table_cursor=table_cursor+1 WHERE table_id=:id");
	
	$up->execute(array(
				"id"=>$table['table_id']
				));
	
	
	
	
	
	
}









?>
