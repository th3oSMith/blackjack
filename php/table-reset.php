<?php
session_start();

require "functions.php";
$db=db_connect();
$table=get_table($db);

if ($_SESSION['id']==$table['table_owner'] && $table['table_phase']==3);

$up=$db->prepare("UPDATE tables SET table_phase=-2, table_mvt=1, table_croupier=NULL WHERE table_id = :id");

$up->execute(array(
			"id"=>$table['table_id']
			));

$up=$db->prepare("UPDATE users SET user_main=NULL WHERE user_table=:id");

$up->execute(array(
				"id"=>$table['table_id']
				));


?>
