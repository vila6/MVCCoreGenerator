<?php
session_start();
if(!isset($_POST['login'])){
	include '../View/Register_View.php';
	$register = new Register();
}
else{

	function ConnectBD(){

		include_once '../Model/config.inc';
	    $mysqli = new mysqli(IP, USER , PASS , DB);
	    	
		if ($mysqli->connect_errno) {
			include '../View/MESSAGE_View.php';
			new MESSAGE("Error MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, '../index.php');
			return false;
		}
		else{
			return $mysqli;
		}
	}

	function Register($login){

		$mysqli = ConnectBD();
		$sql = "select * from usuarios where login = '".$login."'";

		$result = $mysqli->query($sql);
		if ($result->num_rows == 1){  // existe el usuario
				return 'El usuario ya existe';
		}
		else{
	    		return true; //no existe el usuario
		}

	}
	
	$respuesta = Register($_REQUEST['login']);

	if ($respuesta == 'true'){
		
		include_once '../Locates/Strings_'.$_SESSION['idioma'].'.php';
		$_SESSION['login'] = 'nobody';
		session_destroy();
		include './usuarios_Controller.php';
		$usuarios = get_data_form();
		$respuesta = $usuarios->ADD();
		session_start();
		Include '../View/MESSAGE_View.php';
		new MESSAGE($respuesta, '../Controller/Login_Controller.php');
		unset($_SESSION['login']);
	}
	else{
		include '../View/MESSAGE_View.php';
		new MESSAGE($respuesta, '../Controller/Login_Controller.php');
	}

}

?>