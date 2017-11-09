<?php

session_start();
if(!isset($_REQUEST['login']) && !(isset($_REQUEST['password']))){
	include '../View/Login_View.php';
	$login = new Login();
}
else{

	function ConnectDB(){

		include '../Model/config.inc';
	    $mysqli = new mysqli("localhost", USER , PASS , DB);
	    	
		if ($mysqli->connect_errno) {
			include '../View/MESSAGE_View.php';
			new MESSAGE("Error MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, '../index.php');
			return false;
		}
		else{
			return $mysqli;
		}
	}

	function Login($login, $password){

		$mysqli = ConnectDB();
		$sql = "select * from USUARIOS where login = '".$login."'";

		$result = $mysqli->query($sql);
		if ($result->num_rows == 1){  // existe el usuario
			$tupla = $result->fetch_array();
			if ($tupla['password'] == $password){ //  coinciden las passwords
				return true;
			}
			else{
				return 'La contraseña para este usuario es errónea'; //las passwords no coinciden
			}
		}
		else{
	    		return "El usuario no existe"; //no existe el usuario
		}

	}
	
	$respuesta = Login($_REQUEST['login'], $_REQUEST['password']);

	if ($respuesta == 'true'){
		session_start();
		$_SESSION['login'] = $_REQUEST['login'];
		header('Location:../index.php');
	}
	else{
		include '../View/MESSAGE_View.php';
		new MESSAGE($respuesta, '../Controller/Login_Controller.php');
	}

}

?>

