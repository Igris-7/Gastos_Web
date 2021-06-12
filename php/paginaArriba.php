<?php  
/*******************
Variables paginacion
********************/
$TAMANO_PAGINA = 7;
$PAGINAS_MAXIMAS = 5;
//Recuperamos página
if(isset($_GET["p"])){
	$pagina = $_GET["p"];
} else {
	$pagina = 1;
}
//Cálculo del num de páginas
$inicio = ($pagina-1)*$TAMANO_PAGINA;
$total_paginas = ceil($numRegistros/$TAMANO_PAGINA);
?>