<?php

session_start();
require "functions.php";


$db=db_connect();

resetChallenge($db,false);


?>
