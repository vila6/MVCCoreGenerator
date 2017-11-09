<?php
session_start();
include './Functions/Authentication.php';

if (!IsAuthenticated()){
	header('Location:./Controller/Login_Controller.php');
}
else{
	header('Location:./Controller/Index_Controller.php');
}

?>
