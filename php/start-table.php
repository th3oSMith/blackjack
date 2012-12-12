<?php

session_start();
require "functions.php";



$db=db_connect();
$table=get_table($db);


if ($_SESSION['id']==$table['table_owner']){

$start=$db->prepare("UPDATE tables SET table_mvt=1 WHERE table_id=:id");

$start->execute(array(
			"id"=>$table['table_id']
			));
}


?>
