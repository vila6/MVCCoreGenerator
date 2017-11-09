
	  
	function comprobarText(campo,size) {
		if (campo.value.length>size) {
    		alert('Longitud incorrecta. El atributo ' + campo.name + 'debe ser maximo ' + size + ' y es ' + campo.value.length);
        campo.focus();
    		return false;
  		}
  		return true;
  	}

  	function comprobarInt(campo,size) {
		if (campo.value.length>size) {
			valormaximo = (10 ** size) -1;
    		alert('Longitud incorrecta. El atributo ' + campo.name + 'debe ser maximo ' + valormaximo + ' y es ' + campo.value);
        campo.focus();
    		return false;
  		}
  		return true;
  	}

  	function esVacio(campo){
  		if ((campo.value == null) || (campo.value.length == 0)){
  			alert('El atributo ' + campo.name + ' no puede ser vacio');
  			campo.focus();	
  			return false;
  		}
  		else{
  			return true;
  		}
  	} 
  	
