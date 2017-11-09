	<?php

	class Login{


		function __construct(){	
			$this->render();
		}

		function render(){

			include '../View/Header_log.php'; 
			
		?>
			<h1><?php echo $strings['Login']; ?></h1>		
			<form name = 'Form' action='../Controller/Login_Controller.php' method='post' onsubmit='return comprobar_login()'>
				<table>
				<tr><td>login : </td><td><input type = 'text' name = 'login' size = '15' value = ''></td></tr>
				<tr><td>password : </td><td><input type = 'password' name = 'password' size = '15' value = ''></td></tr>
				</table>
			<input type='submit' name='action' value='Login'>

			</form>
							
		<?php
			include '../View/Footer.php';
		} //fin metodo render

	} //fin Login

	?>