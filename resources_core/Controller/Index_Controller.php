<?php
session_start();
include '../Functions/Authentication.php';
if (!IsAuthenticated()){
	header('Location: ../index.php');
}
else{
	include '../View/Index_View.php';
	new Index();
}

?>