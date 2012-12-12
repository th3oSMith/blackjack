<?php
session_start();
require "functions.php";

$db=db_connect();

$table=get_table($db);


$json['mvt']=$table['table_mvt'];
$json['phase']=$table['table_phase'];



echo json_encode($json);



?>
