<?php
//----------------------------------------------------
// DB connection function
// Use CONSTANTS defined in config.inc
//----------------------------------------------------
include '../Model/config.inc';


function ConnectDB()
{
    $mysqli = new mysqli("localhost", USER , PASS , DB);
    	
	if ($mysqli->connect_errno) {
		include '../View/MESSAGE_View.php';
		new MESSAGE("Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, '../index.php');
		return false;
	}
	else{
		return $mysqli;
	}
}

?>