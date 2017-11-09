
	<?php

	class Register{


		function __construct(){	
			$this->render();
		}

		function render(){

			include '../View/Header.php'; //header necesita los strings
		?>
			<h1><?php echo $strings['Registro']; ?></h1>	
			<div class='notifications bottom-right'></div>
			<form name = 'Form' action='../Controller/Register_Controller.php' method='post' onsubmit='return comprobar_usuarios()'>
				login : <input type = 'text' name = 'login' size = '15' value = '' onblur="esVacio(this)  && comprobarText(this,15)" ><br>
					password : <input type = 'text' name = 'password' size = '32' value = '' onblur="esVacio(this)  && comprobarText(this,32)" ><br>
					NombreU : <input type = 'text' name = 'NombreU' size = '15' value = '' onblur="esVacio(this)  && comprobarText(this,15)" ><br>
					ApellidosU : <input type = 'text' name = 'ApellidosU' size = '30' value = '' onblur="esVacio(this)  && comprobarText(this,30)" ><br>
					TituloAcademicoU : <input type = 'text' name = 'TituloAcademicoU' size = '100' value = '' onblur="esVacio(this)  && comprobarText(this,100)" ><br>
					TipoContratoU : <input type = 'text' name = 'TipoContratoU' size = '40' value = '' onblur="esVacio(this)  && comprobarText(this,40)" ><br>
					CentroU : <input type = 'text' name = 'CentroU' size = '100' value = '' onblur="esVacio(this)  && comprobarText(this,100)" ><br>
					DepartamentoU : <input type = 'text' name = 'DepartamentoU' size = '100' value = '' onblur="esVacio(this)  && comprobarText(this,100)" ><br>
					UniversidadU : <input type = 'text' name = 'UniversidadU' size = '40' value = '' onblur="esVacio(this)  && comprobarText(this,40)" ><br>
					TipoU: <select name='TipoU' ><option value='A'>A</option><option value='P'>P</option></select><br>
					
			<input type='submit' name='action' value='REGISTER'>

			</form>
				
		
			<a href='../Controller/usuarios_Controller.php'>Volver </a>
		
		<?php
			include '../View/Footer.php';
		} //fin metodo render

	} //fin REGISTER

	?>