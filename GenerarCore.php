<?php
/*
Aplicación generadora de core de MVC. Creada por Mauro Rodríguez Vila en 2017
*/

function set_and_enum_values($conn, $table , $field ){ 
	//Obtener los valores de una enumeracion desde la BD

    $query = "SHOW COLUMNS FROM `$table` LIKE '$field'";
    $result = mysqli_query( $conn, $query ) or die( 'Error getting Enum/Set field ' . mysqli_error() );
    $row = mysqli_fetch_row($result);
    $values = null;

    if(stripos($row[1], 'enum') !== false || stripos($row[1], 'set') !== false)
    {
        $values = str_ireplace(array('enum(', 'set('), '', trim($row[1], ')'));
        $values = explode(',', $values);
        $values = array_map(function($str) { return trim($str, '\'"'); }, $values);
    }

    return $values;
}

function full_copy( $source, $target ) {  
	//Copiar todos los ficheros de un directorio a otro
    if ( is_dir( $source ) ) {  
        @mkdir( $target );  
        $d = dir( $source );  
        while ( FALSE !== ( $entry = $d->read() ) ) {  
            if ( $entry == '.' || $entry == '..' ) {  
                continue;  
            }  
            $Entry = $source . '/' . $entry;   
            if ( is_dir( $Entry ) ) {  
                full_copy( $Entry, $target . '/' . $entry );  
                continue;  
            }  
            copy( $Entry, $target . '/' . $entry );  
        }  
   
        $d->close();  
    }else {  
        copy( $source, $target );  
    }  
}  

function array_push_assoc($array, $key, $value){
	//Añade un elemento con su clave a un array asociativo
	$array[$key] = $value;
	return $array;
}

//Compruebo si tengo los datos necesarios para generar la aplicación
if(isset($_REQUEST['user1'])) $user1=$_REQUEST['user1'];
if(isset($_REQUEST['pass1'])) $pass1=$_REQUEST['pass1'];
if(isset($_REQUEST['bd'])) $bd=$_REQUEST['bd'];
if(isset($_REQUEST['user2'])) $user2=$_REQUEST['user2'];
if(isset($_REQUEST['pass2'])) $pass2=$_REQUEST['pass2'];
if(isset($_REQUEST['path'])) $path=$_REQUEST['path'];

//En caso de no tener los datos de acceso al gestor de BDs, los pido
if(empty($user1)){
	?>
<html>
		<head>
			<link rel="stylesheet" type="text/css" href="./resources_core/View/css/JulioIU.css" media="screen" />
		</head>
		<body background="./resources_core/bg.jpg">
		<div style="text-align: center">
		<center>
		<table>
			<tr><td><h2 align=center>Primer Paso</h2></td></tr>
			<tr><td><h3 align=center>Acceso a la Base de Datos</h3></td></tr>
			<form action=./GenerarCore.php method='post' >
				<tr><td>Usuario gestor BD: </td><td><input name='user1' type = 'text' required ></td></tr><br>
				<tr><td>Contraseña gestor BD: </td><td><input name='pass1' type = 'password' ></td></tr><br>
				</table>
				<input type='submit' name='enviar' >
			</form>
			</center>
			</div>
	</body>
</html>



<?php

//En caso de no tener los datos de acceso a la BD de la aplicación, los pido
}else if(empty($bd) || empty($user2)){
	$mysqliBDs = new mysqli("localhost", $user1 , $pass1);
	if ($mysqliBDs->connect_errno) {
		echo "<h1>Error al conectar con la BD, pruebe de nuevo<br>";
		echo '<a href "./GenerarCore.php"> Volver </a></h1>';
		exit();
	}
	$bdlistquery = mysqli_query($mysqliBDs, "SHOW DATABASES");


	while(($row =  mysqli_fetch_array($bdlistquery,MYSQLI_NUM))) {
		$bdlist[] = $row[0];
	}
	?>


	<html>
		<head>
			<link rel="stylesheet" type="text/css" href="./resources_core/View/css/JulioIU.css" media="screen" />
		</head>
		<body background="./resources_core/bg.jpg">
		<div style="text-align: center">
		<center>
			<table><tr><td>
				<h2 align=center>Segundo Paso</h2>
				<h3 align=center>Selección de BD</h3>
				</td></tr>
				<form action=./GenerarCore.php method='post' >
					<input name='ip' type = 'hidden' value = <?php echo "'"."localhost"."'"?>>
					<input name='user1' type = 'hidden' value = <?php echo "'".$user1."'"?>>
					<input name='pass1' type = 'hidden' value = <?php echo "'".$pass1."'"?>>
					<tr><td><label for="bd">Base de datos</label> <br/></td>
					<td><select name="bd">
					<?php
						foreach($bdlist as $bdname){
							echo '<option value="'. $bdname .'">'.$bdname.'</option>';
						}
					?>
					</select></td></tr><br>
					<tr><td>Usuario: </td><td><input name='user2' type = 'text' required ></td></tr><br>
					<tr><td>Contraseña: </td><td><input name='pass2' type = 'password' required></td></tr><br>
					<tr><td>Directorio: </td><td><input name='path' type = 'text' required></td></tr><br>
					<tr><td><input type='submit' name='enviar' ></td></tr></table>
				</form>
				</div>
		</center>
	</body>
</html>

<?php
}else{

//Creo el usuario para la BD y le doy permisos
$mysqli = new mysqli("localhost",$user1,$pass1);
if ($mysqli->connect_errno) {
	echo "Error al conectar con la BD: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, '../GenerarCore.php';
}

$sql = "CREATE USER '".$user2."'@'"."localhost"."' IDENTIFIED BY '".$pass2."'";
$result = $mysqli->query($sql);
$query = "GRANT ALL PRIVILEGES ON ".$bd." .* TO '".$user2."'@'"."localhost"."'";			
$result=$mysqli->query($query);
if (!$result){
	echo 'Error in granting privileges.';
	exit();
}


//Me conecto a la base de datos, saco error si lo hay
 $mysqli = new mysqli("localhost", $user2 , $pass2 , $bd);
    	
	if ($mysqli->connect_errno) {
		echo "Error al conectar con la BD: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error, '../GenerarCore.php';
	}

//Consigo los nombres de las tablas de la BD
  $tableList = array();
  $subTableList = array();
  $res = mysqli_query($mysqli,"SHOW TABLES");
  while($cRow = mysqli_fetch_array($res))
  {
    $tableList[] = $cRow[0];
  }

//Compruebo que exista la tabla usuarios. En caso contrario saco error
$usersString = null;
foreach ($tableList as $nomTable) {
	if(strtolower($nomTable) == "usuarios")	$usersString = $nomTable;
}
if($usersString==null){
	echo "ERROR: No se encuentra la tabla usuarios en la BD. <br> <a href='./GenerarCore.php'> Volver </a>";
	exit();
}

//Creo el directorio para la aplicación generada si no existe aun y le doy permisos
if(!file_exists($path)){ 
	mkdir($path, 0777);
	chmod($path, 0777);
}

//Creo las carpetas necesarias
if(!file_exists("./".$path."/Model")){ 
	mkdir("./".$path."/Model", 0777);
	chmod("./".$path."/Model", 0777);
}
if(!file_exists("./".$path."/Controller")){ 
	mkdir("./".$path."/Controller", 0777);
	chmod("./".$path."/Controller", 0777);
}
if(!file_exists("./".$path."/Functions")){ 
	mkdir("./".$path."/Functions", 0777);
	chmod("./".$path."/Functions", 0777);
}
if(!file_exists("./".$path."/Locates")){ 
	mkdir("./".$path."/Locates", 0777);
	chmod("./".$path."/Locates", 0777);
}
if(!file_exists("./".$path."/View")){ 
	mkdir("./".$path."/View", 0777);
	chmod("./".$path."/View", 0777);
}

//Genero el config.inc
$configinc = fopen('./'.$path.'/Model/config.inc',"w");
fwrite($configinc, '<?php
//-------------------------------
// Data base parameters definition
// Generated File
//-------------------------------
define("DB","'.$bd.'");
define("USER","'.$user2.'");
define("PASS","'.$pass2.'");
//-------------------------------
?>');

//Genero los archivos estáticos
copy('./resources_core/index.php', './'.$path.'/index.php');
copy('./resources_core/Model/Access_DB.php', './'.$path.'/Model/Access_DB.php');
copy('./resources_core/Controller/CambioIdioma.php', './'.$path.'/Controller/CambioIdioma.php');
copy('./resources_core/Controller/Login_Controller.php', './'.$path.'/Controller/Login_Controller.php');
copy('./resources_core/View/Login_View.php', './'.$path.'/View/Login_View.php');
copy('./resources_core/Controller/Index_Controller.php', './'.$path.'/Controller/Index_Controller.php');
copy('./resources_core/Controller/Register_Controller.php', './'.$path.'/Controller/Register_Controller.php');
if(!file_exists('./'.$path.'/View/Footer.php')){
	copy('./resources_core/View/Footer.php', './'.$path.'/View/Footer.php');
}
if(!file_exists('./'.$path.'/View/Header.php')){
	copy('./resources_core/View/Header.php', './'.$path.'/View/Header.php');
}
copy('./resources_core/View/Header_reg.php', './'.$path.'/View/Header_reg.php');
copy('./resources_core/View/Header_log.php', './'.$path.'/View/Header_log.php');
copy('./resources_core/View/Index_View.php', './'.$path.'/View/Index_View.php');
if(!file_exists('./'.$path.'/View/MESSAGE_VIEW.php')){
	copy('./resources_core/View/MESSAGE_View.php', './'.$path.'/View/MESSAGE_View.php');
}
full_copy('./resources_core/Functions', './'.$path.'/Functions');
if(!file_exists('./'.$path.'/View/css')){
	full_copy('./resources_core/View/css', './'.$path.'/View/css');
}
if(!file_exists('./'.$path.'/View/Icons')){
	full_copy('./resources_core/View/Icons', './'.$path.'/View/Icons');
}
full_copy('./resources_core/View/js', './'.$path.'/View/js');
full_copy('./resources_core/View/img', './'.$path.'/View/img');



//Introduzco mis strings pregenerados en variables
include './resources_core/Locates/Strings_ENGLISH.php';
$strings_eng = $strings;
include './resources_core/Locates/Strings_SPANISH.php';
$strings_esp = $strings;

//si no existe el fichero de idioma inglés lo creo
if (!file_exists('./'.$path.'/Locates/Strings_ENGLISH.php')){
	copy('./resources_core/Locates/Strings_ENGLISH.php', './'.$path.'/Locates/Strings_ENGLISH.php');
}else{ 	
	//si no, añado mis strings a la variable ya existente
	include './'.$path.'/Locates/Strings_ENGLISH.php';
	$strings_eng_destiny = $strings;
	$finalstring=array_merge($strings_eng,$strings_eng_destiny);
	$strings_eng_file = fopen('./'.$path.'/Locates/Strings_ENGLISH.php',"w");
	fwrite($strings_eng_file, "<?php
\$strings =
array(");

	//y por último creo el fichero desde 0 con todos los strings
	$i=0;
	foreach($finalstring as $key => $value){
		fwrite($strings_eng_file,"'".$key."'=>'".$value."'");
			if($i != count($finalstring)-1){
			 	fwrite($strings_eng_file, ",
				");
			 }
		$i++;
	}
	fwrite($strings_eng_file, ")
	;
	?>");
}

//si no existe el fichero de idioma español lo creo
if (!file_exists('./'.$path.'/Locates/Strings_SPANISH.php')){
	copy('./resources_core/Locates/Strings_SPANISH.php', './'.$path.'/Locates/Strings_SPANISH.php');
}else{ 	
	//si no, añado mis strings a la variable ya existente
	include './'.$path.'/Locates/Strings_SPANISH.php';
	$strings_esp_destiny = $strings;
	$finalstring=array_merge($strings_esp,$strings_esp_destiny);
	$strings_esp_file = fopen('./'.$path.'/Locates/Strings_SPANISH.php',"w");
	fwrite($strings_esp_file, "<?php
\$strings =
array(");

	//y por último creo el fichero desde 0 con todos los strings
	$i=0;
	foreach($finalstring as $key => $value){
		fwrite($strings_esp_file,"'".$key."'=>'".$value."'");
			if($i != count($finalstring)-1){
			 	fwrite($strings_esp_file, ",
				");
			 }
		$i++;
	}
	fwrite($strings_esp_file, ")
	;
	?>");
}

///////////////////////////////////////////////////
//////	VISTAS REGISTRO Y LOGIN		///////////////
///////////////////////////////////////////////////
$usersString = null;
$register = fopen('./'.$path.'/View/Register_View.php',"w");
foreach ($tableList as $nomTable) {
	if(strtolower($nomTable) == "usuarios")	$usersString = $nomTable;
}
if($usersString==null){
	echo "ERROR: No se encuentra la tabla usuarios en la BD. <br> <a href='./GenerarCore.php'> Volver </a>";
	exit();
}
fwrite($register,'
	<?php
	//Vista de registro. Permite crear nuevos usuarios.
	class Register{


		function __construct(){	
			$this->render();
		}

		function render(){

			include \'../View/Header_reg.php\'; //header necesita los strings
		?>
			<h1><?php echo $strings[\'Registro\']; ?></h1>	
			<form name = \'Form\' action=\'../Controller/Register_Controller.php\' method=\'post\' onsubmit=\'return comprobar_' . $usersString . '()\'>
				<table>'
);

$result = mysqli_query($mysqli, 'SELECT * FROM ' . $usersString);
$finfo = $result->fetch_fields(); //Obtener las columnas de cada tabla
$js = fopen('./'.$path.'/View/js/comprobar_reg.js',"w"); //función javascript generada para comprobaciones en la vista de registro

fwrite($js,"function comprobar_".$usersString."(){
	return (");

//Formulario de registro, también añado al JS de registro las funciones necesarias en funcion al tipo
foreach($finfo as $field){
		switch($field->type){
			case 3:
				//tipo int
				fwrite($register, '<tr><td>' . $field->name . ' : </td><td><input type = \'number\' name = \'' . $field->name . '\' min = \'\' max = \'\' value = \'\' comprobarInt(this,' . $field->length . ')" ></td></tr>
					');		
					fwrite($js, 'comprobarInt(Form.' . $field->name . ', ' . $field->length . ')');	
				break;
			case 10:
				//tipo date
				fwrite($register,'<tr><td>' . $field->name . ' : </td><td><input class = "tcal" type = \'date\' name = \'' . $field->name . '\' min = \'\' max = \'\' value = \'\'" ></td></tr>
					');		
				break;
			case 253:
			case 13:
				//tipo varchar o year
				fwrite($register,'<tr><td>' . $field->name . ' : </td><td><input type = \'text\' name = \'' . $field->name . '\' size = \'40\' value = \'\' comprobarText(this,' . $field->length . ')" ></td></tr>
					');			
				fwrite($js, 'comprobarText(Form.' . $field->name . ', ' . $field->length . ')');
				break;
			case 254:
				//tipo enum
				fwrite($register,'<tr><td>' . $field->name . ': </td><td><select name=\'' . $field->name . '\' >');
				
				$enumList = set_and_enum_values($mysqli,$usersString, $field->name );

				foreach((array)$enumList as $value){
				    fwrite($register, '<option value=\'' . $value . '\'>' . $value . '</option>');
				}
				fwrite($register, '</select></td></tr>
					');
				break;
			default:
				fwrite($register,'<tr><td>' . $field->name . ' : </td><td><input type = \'text\' name = \'' . $field->name . '\' size = \'40\' value = \'\' comprobarText(this,' . $field->length . ')" ></td></tr>
				');	
				fwrite($js, 'comprobarText(Form.' . $field->name . ', ' . $field->length . ')');
				break;
		}		

		if($field != end($finfo)){
			fwrite($js,' && ');
		}
	}

//añado al js la comprobación de si un atributo clave es vacío
	$PKqueryu = mysqli_query($mysqli, "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE COLUMN_KEY = 'PRI' AND TABLE_NAME = '" . $usersString . "'");
	  while($cRow = mysqli_fetch_array($PKqueryu))
	  {
	    $PKusers[] = $cRow[0];
	  }	
	  foreach ($PKusers as $pkey) {
			fwrite($js, 'esVacio(Form.' . $pkey . ')');
		if($pkey != end($PKusers)){
			fwrite($js,' && ');
		}
	  }
//Cierro el fichero js
	fwrite($js,')
	}');

//Final vista register
fwrite($register,'</table>
			<input type=\'submit\' name=\'action\' value=\'REGISTER\'>

			</form>
				
		
			<a href=\'../Controller/' . $usersString . '_Controller.php\'>Volver </a>
		
		<?php
			include \'../View/Footer.php\';
		} //fin metodo render

	} //fin REGISTER

	?>'
);




	///////////////////////////////////////////////////
	//////			CONTROLADORES 		///////////////
	///////////////////////////////////////////////////
	echo "Creando controlador de Registro<br>";
$registerController = fopen('./'.$path.'/Controller/Register_Controller.php',"w");
fwrite($registerController, "<?php
//Controlador de registro: maneja la vista Register_View.
session_start();
if(!isset(\$_POST['login'])){
	include '../View/Register_View.php';
	\$register = new Register();
}
else{
	//Conecta con la base de datos
	function ConnectBD(){

		include_once '../Model/config.inc';
	    \$mysqli = new mysqli(\"localhost\", USER , PASS , DB);
	    	
		if (\$mysqli->connect_errno) {
			include '../View/MESSAGE_View.php';
			new MESSAGE(\"Error MySQL: (\" . \$mysqli->connect_errno . \") \" . \$mysqli->connect_error, '../index.php');
			return false;
		}
		else{
			return \$mysqli;
		}
	}

	//comprueba si ya existe el usuario a registrar
	function Register(\$login){

		\$mysqli = ConnectBD();
		\$sql = \"select * from " . $usersString . " where login = '\".\$login.\"'\";

		\$result = \$mysqli->query(\$sql);
		if (\$result->num_rows == 1){  // existe el usuario
				return 'El usuario ya existe';
		}
		else{
	    		return true; //no existe el usuario
		}

	}
	
	\$respuesta = Register(\$_REQUEST['login']);

	if (\$respuesta == 'true'){
		
		include_once '../Locates/Strings_'.\$_SESSION['idioma'].'.php';
		\$_SESSION['login'] = 'nobody';
		session_destroy();
		include './" . $usersString . "_Controller.php';
		\$" . $usersString . " = get_data_form();
		\$respuesta = \$" . $usersString . "->ADD();
		session_start();
		Include '../View/MESSAGE_View.php';
		new MESSAGE(\$respuesta, '../Controller/Login_Controller.php');
		unset(\$_SESSION['login']);
	}
	else{
		include '../View/MESSAGE_View.php';
		new MESSAGE(\$respuesta, '../Controller/Login_Controller.php');
	}

}

?>");



//Bucle que recorre todas las tablas para generar
//los controladores y modelos de estas

foreach ($tableList as $nomTable) {
	$controller = fopen("./".$path."/Controller/" . $nomTable . "_Controller.php", "w"); //Fichero del controlador a generar
	$model = fopen("./".$path."/Model/" . $nomTable . "_Model.php", "w"); //Fichero del modelo a generar

	//Creo un array con los nombres de columnas
	$subTablesQuery = mysqli_query($mysqli,"DESCRIBE ". $nomTable);
	while($cRow = mysqli_fetch_array($subTablesQuery)){
	    $subTableList[] = $cRow[0];
	}

	//Creo un array con los tipos de datos
	$typesQuery = mysqli_query($mysqli, "SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_NAME = '" . $nomTable."'");
	while($cRow = mysqli_fetch_array($typesQuery)){
	    $typesList[] = $cRow[0];
	}

	//Consigo el array de claves primarias
	$typesListAsoc = array_combine($subTableList, $typesList); //Creo un array asociativo en el cual las claves son los nombres de columna y los valores los tipos
	$PKquery = mysqli_query($mysqli, "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE COLUMN_KEY = 'PRI' AND TABLE_NAME = '" . $nomTable . "'");
	  while($cRow = mysqli_fetch_array($PKquery))
	  {
	    $PK[] = $cRow[0];
	  }	





	echo "Creando controlador de ".$nomTable."<br>";
	//empiezo a escribir en el fichero del controlador
		fwrite($controller,"
		<?php
			//Controlador de ".$nomTable.": maneja la vista ".$nomTable."_View.php
			session_start(); //solicito trabajar con la session

			include '../Functions/Authentication.php';

			if (!IsAuthenticated()){
				header('Location:../index.php');
			}

		//Inlcuyo las vistas y el modelo necesarios
		include '../Model/" . $nomTable . "_Model.php';
		include '../View/" . $nomTable . "_SHOWALL_View.php';
		include '../View/" . $nomTable . "_SEARCH_View.php';
		include '../View/" . $nomTable . "_ADD_View.php';
		include '../View/" . $nomTable . "_EDIT_View.php';
		include '../View/" . $nomTable . "_DELETE_View.php';
		include '../View/" . $nomTable . "_SHOWCURRENT_View.php';
		include '../View/MESSAGE_View.php';

		//Obtener datos de formulario
		function get_data_form(){
			");
	foreach ($subTableList as $subTable) {
		fwrite($controller, "$" . $subTable . ' = $_REQUEST[\'' . $subTable . '\'];
			');
	}
	fwrite($controller,'$action = $_REQUEST[\'action\'];

		$' . $nomTable . ' = new '. $nomTable. '_Model($'.implode(",$", $subTableList).');
		return $'.$nomTable.';
	}');

	fwrite($controller, 'if (!isset($_REQUEST[\'action\'])){
		$_REQUEST[\'action\'] = \'\';
	}
		Switch ($_REQUEST[\'action\']){
			//Añadir '.$nomTable.'
			case \'ADD\':
				if (!$_POST){
					new ' . $nomTable . '_ADD();
				}
				else{
					$' . $nomTable . ' = get_data_form();
					$respuesta = $' . $nomTable . '->ADD();
					new MESSAGE($respuesta, \'../Controller/' . $nomTable . '_Controller.php\');
				}
				break;		
			//Borrar '.$nomTable.'
			case \'DELETE\':
				if (!$_POST){
					$' . $nomTable . ' = new ' . $nomTable . '_Model(');

	//Necesito las claves primarias para el borrado. Por una parte introduzco estas
	//y para el resto de atributos unas comillas sin nada dentro, ya que no son necesarios
	for($x=0; $x<count($PK); $x++){
		fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
		if($x < count($PK)-1) fwrite($controller,",");
	}
 	if(count($PK) != (count($subTableList))) fwrite($controller,",");

	for($x=0; $x<( count($subTableList) - count($PK) ); $x++){
		fwrite($controller, "''");
		if($x != (count($subTableList)-count($PK)-1)) fwrite($controller,",");
	}

fwrite($controller, ');
					$valores = $' . $nomTable . '->RellenaDatos(');

	for($x=0; $x<count($PK); $x++){
		fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
		if($x < count($PK)-1) fwrite($controller,",");
	}

					 fwrite($controller,');
					new ' . $nomTable . '_DELETE($valores);
				}
				else{
					$' . $nomTable . ' = get_data_form();
					$respuesta = $' . $nomTable . '->DELETE();
					new MESSAGE($respuesta, \'../Controller/' . $nomTable . '_Controller.php\');
				}
				break;
			case \'EDIT\':	
			//Editar '.$nomTable.'	
				if (!$_POST){
					$' . $nomTable . ' = new ' . $nomTable . '_Model(');

	for($x=0; $x<count($PK); $x++){
		fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
		if($x < count($PK)-1) fwrite($controller,",");
	}
 	if(count($PK) != (count($subTableList))) fwrite($controller,",");

	for($x=0; $x<( count($subTableList) - count($PK) ); $x++){
		fwrite($controller, "''");
		if($x != (count($subTableList)-count($PK)-1)) fwrite($controller,",");
	}

		fwrite($controller,');
					$valores = $' . $nomTable . '->RellenaDatos(');

		for($x=0; $x<count($PK); $x++){
			fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
			if($x < count($PK)-1) fwrite($controller,",");
		}
					fwrite($controller,');
				new ' . $nomTable . '_EDIT($valores);
				}
				else{
					
					$' . $nomTable . ' = get_data_form();

					$respuesta = $' . $nomTable . '->EDIT();
					new MESSAGE($respuesta, \'../Controller/' . $nomTable . '_Controller.php\');
				}
				
				break;
			case \'SEARCH\':
				if (!$_POST){
					new ' . $nomTable . '_SEARCH();
				}
				else{
					$' . $nomTable . ' = get_data_form();
					$datos = $' . $nomTable . '->SEARCH();

					$lista = array(\'' . implode("','", $subTableList) . '\');

					new ' . $nomTable . '_SHOWALL($lista, $datos, \'../index.php\');
				}
				break;
			case \'SHOWCURRENT\':
				$' . $nomTable . ' = new ' . $nomTable . '_Model(');

			for($x=0; $x<count($PK); $x++){
				fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
				if($x < count($PK)-1) fwrite($controller,",");
			}
		 	if(count($PK) != (count($subTableList))) fwrite($controller,",");

			for($x=0; $x<( count($subTableList) - count($PK) ); $x++){
				fwrite($controller, "''");
				if($x != (count($subTableList)-count($PK)-1)) fwrite($controller,",");
			}

				fwrite($controller,');
				$valores = $' . $nomTable . '->RellenaDatos(');

				for($x=0; $x<count($PK); $x++){
					fwrite($controller, '$_REQUEST[\'' . $PK[$x] . '\']');
					if($x < count($PK)-1) fwrite($controller,",");
				}
			fwrite($controller, ');
				new ' . $nomTable . '_SHOWCURRENT($valores);
				break;
			default:
				if (!$_POST){
					$' . $nomTable . ' = new ' . $nomTable . '_Model(');
					for($x=0; $x<count($subTableList); $x++){
						fwrite($controller, "''");
						if($x != (count($subTableList)-1)) fwrite($controller,",");
					}

					fwrite($controller,');
				}
				else{
					$' . $nomTable . ' = get_data_form();
				}
				$datos = $' . $nomTable . '->SEARCH();
				$lista = array(\'' . implode("','", $subTableList) . '\');
				new ' . $nomTable . '_SHOWALL($lista, $datos);												
			}
	?>');


	///////////////////////////////////////////////////
	//////			MODELOS 			///////////////
	///////////////////////////////////////////////////
 	echo "Creando modelo de  ".$nomTable.". Columnas: " . implode(", ",$subTableList) . "<br>"; //Muestro los nombres de las subtablas obtenidas separadas por comas



	fwrite($model, '<?php
	//Modelo del objeto '.$nomTable.'
	class ' . $nomTable . '_Model { 
		');
	foreach ($subTableList as $subTable) {
	fwrite($model,'	var $' . $subTable . ';
		');
	}

	fwrite($model, '
		var $mysqli;

		//Constructor de la clase
		//
		function __construct($' . implode(",$", $subTableList) . '){
		');
		foreach ($subTableList as $subTable) {
			if($typesListAsoc[$subTable] == 'date'){
				fwrite($model, 'if ($' . $subTable .' == \'\'){
					$this->' . $subTable . ' = $' . $subTable . ';
				}
				else{
					$this->' . $subTable . ' = date_format(date_create($' . $subTable . '), \'Y-m-d\');
				}');
			}else{
			fwrite($model, '$this->' . $subTable . ' = $' . $subTable . ';
				');
			}
		}

	fwrite($model,"include_once 'Access_DB.php';
		\$this->mysqli = ConnectDB();
		}



	//Metodo ADD
	//Inserta en la tabla  de la bd  los valores
	// de los atributos del objeto. Comprueba si la clave/s esta vacia y si 
	//existe ya en la tabla
	function ADD()
	{
	    if (");
		 for($x=0; $x<count($PK); $x++){
			fwrite($model, '($this->'. $PK[$x] . ' <> \'\')');
			if($x < count($PK)-1) fwrite($model," && ");
		}

	        fwrite($model, "){

	    \$sql = \"SELECT * FROM " . $nomTable . " WHERE (");


	        for($x=0; $x<count($PK); $x++){
	        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
					fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
				}else{
					fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
				}
				if($x < count($PK)-1) fwrite($model," && ");
			}


	        fwrite($model, ")\";

			if (!\$result = \$this->mysqli->query(\$sql)){
				return 'No se ha podido conectar con la base de datos'; // error en la consulta (no se ha podido conectar con la bd
			}
			else {
				if (\$result->num_rows == 0){
					
					\$sql = \"INSERT INTO " . $nomTable . " (
					".implode(",", $subTableList).
					") 
							VALUES (");
		foreach ($subTableList as $subTable) {
			if(substr_compare($typesListAsoc[$subTable], 'int', 0, 3, true) == 0){
				fwrite($model, "
					\$this->" . $subTable);
			}else{
				fwrite($model, "
					'\$this->" . $subTable . "'");
			}

			if($subTable != $subTableList[count($subTableList)-1]){
				fwrite($model, ",");
			}
		}

			fwrite($model, ")\";

				if (!\$this->mysqli->query(\$sql)) {
					return 'Error en la inserción';
				}
				else{
					return 'Inserción realizada con éxito'; //operacion de insertado correcta
				}
					
				}
				else
					return 'Ya existe en la base de datos'; // ya existe
			}
	    }
	    else{
	        return 'Introduzca un valor'; // introduzca un valor para el usuario
		}
	}

	//funcion de destrucción del objeto: se ejecuta automaticamente
	//al finalizar el script
	function __destruct()
	{

	}


	//funcion Consultar: hace una búsqueda en la tabla con
	//los datos proporcionados. Si van vacios devuelve todos
	function SEARCH()
	{
	    \$sql = \"select " . implode(",", $subTableList) . "
	    		from " . $nomTable . "
	    		where
	    			(");
			foreach ($subTableList as $subTable) {
				fwrite($model, "
								(". $subTable ." LIKE '%\$this->" . $subTable . "%')");

				if($subTable != $subTableList[count($subTableList)-1]){
					fwrite($model, "&&");
				}
			}

	fwrite($model,
			")\";
			if (!(\$resultado = \$this->mysqli->query(\$sql))){
			return 'Error en la consulta sobre la base de datos';
		}
	    else{
			return \$resultado;
		}
	}


	function DELETE()
	{
    \$sql = \"SELECT * FROM " . $nomTable . " WHERE (");

	    for($x=0; $x<count($PK); $x++){
	    	//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
				fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
			}else{
				fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
			}
			if($x < count($PK)-1) fwrite($model," && ");
		}



	fwrite($model, ")\";
    \$result = \$this->mysqli->query(\$sql);
    if (\$result->num_rows == 1)
    {
        \$sql = \"DELETE FROM " . $nomTable . " WHERE (");

        for($x=0; $x<count($PK); $x++){
        	//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
				fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
			}else{
				fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
			}
			if($x < count($PK)-1) fwrite($model," && ");
		}

	
	fwrite($model, ")\";
        \$this->mysqli->query(\$sql);
    	return \"Borrado correctamente\";
    }
    else
        return \"No existe en la base de datos\";
	}

	function RellenaDatos()
	{
	    \$sql = \"SELECT * FROM " . $nomTable . " WHERE (");

	    for($x=0; $x<count($PK); $x++){
	    	//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
				fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
			}else{
				fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
			}
			if($x < count($PK)-1) fwrite($model," && ");
		}


	fwrite($model, ")\";
	    if (!(\$resultado = \$this->mysqli->query(\$sql))){
			return 'No existe en la base de datos'; // 
		}
	    else{
			\$result = \$resultado->fetch_array();
			return \$result;
		}
	}

	function EDIT()
	{

	    \$sql = \"SELECT * FROM " . $nomTable ." WHERE (");

        for($x=0; $x<count($PK); $x++){
        	//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
				fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
			}else{
				fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
			}
			if($x < count($PK)-1) fwrite($model," && ");
		}



	    fwrite($model, ")\";

	    \$result = \$this->mysqli->query(\$sql);
	    
	    if (\$result->num_rows == 1)
	    {
			\$sql = \"UPDATE " . $nomTable ." SET
				");	
		foreach ($subTableList as $subTable) {
			fwrite($model, "" . $subTable . " = ");
			//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
			if(substr_compare($typesListAsoc[$subTable], 'int', 0, 3, true) == 0){
				fwrite($model, "
					\$this->" . $subTable);
			}else{
				fwrite($model, "
					'\$this->" . $subTable . "'");
			}

			if($subTable != $subTableList[count($subTableList)-1]){
				fwrite($model, ",
			");
			}
		}		
		fwrite($model, "WHERE (");

        for($x=0; $x<count($PK); $x++){
        	//Compruebo si es del tipo int, ya que en este caso no debe llevar comillas
        	if(substr_compare($typesListAsoc[$PK[$x]], 'int', 0, 3, true) == 0){
				fwrite($model, ''. $PK[$x] . ' = $this->' . $PK[$x]);
			}else{
				fwrite($model, ''. $PK[$x] . ' = \'$this->' . $PK[$x] . '\'');
			}
			if($x < count($PK)-1) fwrite($model," && ");
		}

		fwrite($model, "
					)\";
			
	        if (!(\$resultado = \$this->mysqli->query(\$sql))){
				return 'Error en la modificación'; 
			}
			else{
				return 'Modificado correctamente';
			}
	    }
	    else
	    	return 'No existe en la base de datos';
	}



	}//fin de clase

	?> ");		


	//Reinicio de variables de tabla para evitar superposiciones en el bucle
	$subTableList = null;
	$typesList = null;
	$PK = null;
}
echo "<h1>Core generado satisfactoriamente.<a href='".$path."'> Acceder a la aplicación</a>";

//Fin de la aplicación generadora
}
?>