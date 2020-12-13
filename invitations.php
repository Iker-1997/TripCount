<?php

require_once("internal/functions.php");
initGlobal();

session_start(['name' => 'TRIPCOUNT_SESS_ID']);

if(!isset($_SESSION["username"])){
	session_destroy();
	header("Location: login.php");
}

?>