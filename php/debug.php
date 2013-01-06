<?php
session_start();
 
require "functions.php";

$db=db_connect();

/*
$kid_audebert=20130209;

$log->info("test Initialisé");


//tranche(13,$db,3,$log);

detranche(13,$db,$log);

echo $_SESSION['id'];
*/


$query="";


for ($x=1;$x<5;$x++){
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('1-A.".$x."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('1-B.".$x."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('1-C.".$x."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('1-D.".$x."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-AG.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-AD.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-BG.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-BD.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-CG.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-CD.".($x-1)."',0);";
	$query.="INSERT INTO etages (`level`,`malus`) VALUES ('2-D.".($x-1)."',0);";
}

echo $query;

$up=$db->prepare($query);

$up->execute();

?>
