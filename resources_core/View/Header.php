<?php
	
	include_once '../Functions/Authentication.php';
	if (!isset($_SESSION['idioma'])) {
		$_SESSION['idioma'] = 'SPANISH';
		include '../Locates/Strings_' . $_SESSION['idioma'] . '.php';
	}
	else{
		//$_SESSION['idioma'] = 'SPANISH'; // quitar y solucionar el problema de que inicilice el idioma a galego
		include '../Locates/Strings_' . $_SESSION['idioma'] . '.php';
	}
?>
<html>
 
<head>
	<meta charset="UTF-8">
	<title>Ejemplo pagina web</title>
	<script type="text/javascript" src="../View/js/tcal.js"></script> 
	<script type="text/javascript" src="../View/js/Validaciones.js"></script> 
	<script type="text/javascript" src="../View/js/comprobar.js"></script> 
	<link rel="stylesheet" type="text/css" href="../View/css/JulioCSS-2.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="../View/css/tcal.css" media="screen" />
</head>
<body>
<header>
	<p style="text-align:center">
	<h1>
<?php echo $strings['Portal de Gestión']; ?>
	</h1>
	</p>
	

	<div width: 50%; align="left">
		<form action="../Controller/CambioIdioma.php" method="get">
			<?php echo $strings['idioma']; ?>
			<select name="idioma" onChange='this.form.submit()'>
		        <option value="SPANISH"> </option>
				<option value="ENGLISH"><?php echo $strings['INGLES']; ?></option>
		        <option value="SPANISH"><?php echo $strings['ESPAÑOL']; ?></option>
			</select>
		</form>
		
<?php
	
	if (IsAuthenticated()){
?>

<?php
		echo $strings['Usuario'] . ' : ' . $_SESSION['login'] . '<br>';
?>			
		</div>
		<div width: 50%; align="right">
			<a href='../Functions/Desconectar.php'>
				<img src='../View/Icons/sign-error.png'>
			</a>
	</div>
<?php
	}
	else{
		echo $strings['Usuario no autenticado']; 
		echo 	'<form action=\'../Controller/Register_Controller.php\' method=\'post\'>
					<input type=\'submit\' name=\'action\' value=\'REGISTER\'>
				</form>';
	}	
?>
</header>

<div id = 'main'>
<?php
	//session_start();
	if (IsAuthenticated()){
		include '../View/menuLateral.php';
	}
?>
<article>