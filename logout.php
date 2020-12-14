<?php

session_start(['name' => 'TRIPCOUNT_SESS_ID']);
session_destroy();
header("Location: login.php");

?>