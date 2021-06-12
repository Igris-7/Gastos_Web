<?php
require "../clases/dbMySQL.php";
$db = new dbMySQL();
//Leemos los parametros de la URL
$tipo = (isset($_GET["t"]))?$_GET["t"]:"";
$usuario = (isset($_GET["usuario"]))?$_GET["usuario"]:"";
$sql = "SELECT * FROM categorias WHERE usuario='".$usuario."' AND tipo='".$tipo."'";
$data = array();
$data = $db->querySelect($sql);
//
//Generamos el XML
//
print header("Content-type:text/xml");
print "<?xml version='1.0' encoding='UTF-8'?>";
print "<categorias>";
for ($i=0; $i < count($data); $i++) { 
	$idCategoria = $data[$i]["id"];
	$nombre = $data[$i]["categoria"];
	//nodo
	print "<categoria>";
	print "<id>".$idCategoria."</id>";
	print "<nombre>".$nombre."</nombre>";
	print "</categoria>";
}
print "</categorias>";
?>