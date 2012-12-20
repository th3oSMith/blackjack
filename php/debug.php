<?php


require "functions.php";
require "do.php";


$db=db_connect();


//echo score(unserialize("a:2:{i:0;a:2:{i:0;i:1;i:1;i:1;}i:1;a:2:{i:0;i:2;i:1;i:12;}}"));

echo 		$_SESSION['maxi']=$maxi;
echo		$_SESSION['j1']=$score[1];
echo		$_SESSION['j2']=$score[2];

?>
