<?php

class Index {

	function __construct(){
		$this->render();
	}

	function render(){
	
		include '../Locates/Strings_SPANISH.php';
		include 'Header.php';
		include 'Footer.php';
	}

}

?>