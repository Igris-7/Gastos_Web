<?php
function limpiaNumero($numero){
	$numero = str_replace(",", "", $numero);
	$numero = str_replace("$", "", $numero);
	return $numero;
}
function validaRequerido($valor){
	if (trim($valor)=="") {
		return false;
	} else {
		return true;
	}
}
function validaEntero($valor, $opciones=null){
	if (filter_var($valor, FILTER_VALIDATE_INT, $opciones)===FALSE) {
		return false;
	} else {
		return true;
	}
}
function validaDecimal($valor, $opciones=null){
	if (filter_var($valor, FILTER_VALIDATE_FLOAT,$opciones)===FALSE) {
		return false;
	} else {
		return true;
	}
}
function validaEmail($valor){
	if (filter_var($valor, FILTER_VALIDATE_EMAIL)===FALSE) {
		return false;
	} else {
		return true;
	}
}
function validaFecha($fecha){
	//AAAA-MM-DD (ISO)
	//limpiamos espacios
	$fecha = trim($fecha);
	//separamos los elementos de la fecha
	$aFecha = explode("-",$fecha);
	//Tiene tres elementos
	if(count($aFecha)!=3){
		return false;
	}
	//m-d-a => a-m-d
	return checkdate($aFecha[1], $aFecha[2], $aFecha[0]);
}
function validaCadena($cadena){
	return addslashes(htmlentities($cadena));
}
?>