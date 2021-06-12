<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Cuentas.php";
/****************
Leemos la sesión
*****************/
$sesion = new Sesion();
$usuario = $sesion->getUsuario();
$data = Usuarios::leeUsuario($usuario);
$id = $data["id"]; //identificador del usuario
/*****************
Lee cuentas
******************/
$cuenta = new Cuentas();
$cuentas_array = array();
$numRegistros = $cuenta->numRegistros($id);
$cuentasTipos_array = $cuenta->leeCuentasTipos();
/******************/
//require "php/paginaArriba.php";
/****************
Variables de trabajo
********************/
$idCuenta= "";
$nombreCuenta = "";
$saldo = 0;
$tipo = "";
/****************
Modo de la página (CRUD o ABC)
S - Consulta (select)
A - Alta (insert)
B - Borrar (delete)
C - Cambiar (update)
D - Baja Definitiva
*****************/
if (isset($_GET["m"])) {
	$m = $_GET["m"];
} else {
	$m = "S";
}
/**************
Validacion
**************/
if (isset($_POST["cuenta"])) {
	$idCuenta = (isset($_POST["idCuenta"]))?$_POST["idCuenta"]:"";
	$nombreCuenta = $_POST["cuenta"];
	$tipo = $_POST["tipo"];
	$saldo = $_POST["saldo"];
	$m="S";
	//validar
	if ($nombreCuenta=="") {
		array_push($msg,"1La cuenta no puede estar vacía");
	} else if($tipo=="0"){
		array_push($msg,"1Debes seleccionar un tipo de categoría");
	} else if($saldo<0){
		array_push($msg,"1El saldo no puede ser menor a cero");
	} else {
		$saldo = limpiaNumero($saldo);
		if($cuenta->altaCuenta($idCuenta,$id,"",$nombreCuenta,$tipo,$saldo)){
			array_push($msg,"0Alta exitosa");
		} else {
			array_push($msg,"1Error al insetar el registro");
		}
	}
}
//Baja definitiva
if($m=="D"){
	$idCuenta = $_GET["id"];
	$cuenta->borrarCuenta($idCuenta);
	$m = "S";
}
//Consulta o baja (previa) del registro
if($m=="C" || $m=="B"){
	$idCuenta = $_GET["id"];
	$data = $cuenta->leerRegistro($idCuenta);
	//
	$idCuenta = $data[0]["id"];
	$nombreCuenta = $data[0]["cuenta"];
	$tipo = $data[0]["tipo"];
	$saldo = $data[0]["saldo"];
} else if($m=="S"){
	$cuentas_array = $cuenta->leeCuentasUsuario($id);//,$inicio,$TAMANO_PAGINA
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Cuentas</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="imagenes/fox1.png">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	
	<!-- Bootstrap -->
	<link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        
    <!--CSS Propio-->
    <link href="Estilos/main.css" rel="stylesheet" type="text/css"/>
        
	<!-- Datatables -->
    <link href="DataTable/datatables.min.css" rel="stylesheet" type="text/css"/>
    <link href="DataTable/DataTables-1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css"/>

    <!--font awesome con CDN-->  
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" 
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" 
          crossorigin="anonymous">

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">		
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="Estilos/style.css">
	
	<script>
        	$(document).ready(function() {
            $('#tabla').DataTable(
			);
	} );
	</script>

	<script>
		window.onload = function(){
			<?php if($m=="S"){ ?>
				document.getElementById("alta").onclick = function(){
					window.open("cuentas.php?m=A","_self");
				}
			<?php } 
			
			if($m=="C" || $m=="A"){ ?>
				document.getElementById("regresar").onclick = function(){
					window.open("cuentas.php","_self");
				}
			<?php } 
			
			if($m=="B"){ ?>
				document.getElementById("si").onclick = function(){
					var idCuenta = <?php print $idCuenta; ?>;
					window.open("cuentas.php?m=D&id="+idCuenta,"_self");
				}
				document.getElementById("no").onclick = function(){
					window.open("cuentas.php","_self");
				}
				document.getElementById("regresaBorrar").onclick = function(){
					window.open("cuentas.php","_self");
				}
			<?php } ?>
			
		}
		function cambiaPagina(p) {
			window.open("cuentas.php?p="+p,"_self");
		}
	</script>
	<style>
	button{ cursor:pointer; }
	.rojo{ color:red; }
	.verde{ color:green; }
	</style>
</head>
<body>
	
	<div class="wrapper d-flex align-items-stretch">
	<nav id="sidebar">
				<div class="custom-menu">
					<button type="button" id="sidebarCollapse" class="btn btn-primary">
	        </button>
        </div>
	  		<div class="img bg-wrap text-center py-4" style="background-image: url(imagenes/bg_1.jpg);">
	  			<div class="user-logo">
	  				<div class="img" style="background-image: url(imagenes/perro.jpg);"></div>
	  				<h3>Eyner Torres</h3>
	  			</div>
	  		</div>
        <ul class="list-unstyled components mb-5">
          <li class="active">
            <a href="inicio.php"><span class="fa fa-home mr-3"></span> Resumen</a>
          </li>
          <li>
            <a href="categorias.php"><span class="fa fa-gift mr-3"></span> Categorías</a>
          </li>
          <li>
            <a href="cuentas.php"><span class="fa fa-trophy mr-3"></span> Cuentas</a>
          </li>
          <li>
            <a href="movimientos.php"><span class="fa fa-cog mr-3"></span> Movimientos</a>
          </li>
          <li>
            <a href="traspasos.php"><span class="fa fa-support mr-3"></span> Traspasos</a>
          </li>
          <li>
            <a href="presupuesto.php"><span class="fa fa-support mr-3"></span> Presupuesto</a>
          </li>
          <li>
            <a href="cxc.php"><span class="fa fa-support mr-3"></span> Cuentas por cobrar</a>
          </li>
          <li>
            <a href="salir.php"><span class="fa fa-sign-out mr-3"></span> Salir</a>
          </li>
        </ul>

    </nav>

	<div id="content" class="p-4 p-md-5 pt-5">
	<div class="container-fluid text-center">
		<div class="row content">
			<div class="col-sm-2 sidevar">
				<?php if ($m=="S") { ?>
					<label for="alta"></label>
					<input type="button" name="alta" value="Dar de alta una cuenta" class="btn btn-info mt-5" role="button" id="alta">
				<?php } ?>
			</div>
			<div class="col-sm-8 text-center">
				<h2>Cuentas</h2>
				<?php if($m=="C" || $m=="A" || $m=="B") { 
					require "php/mensajes.php";
				?>
					<form action="cuentas.php" method="post">
						<div class="form-group text-left">
							<label for="cuenta">* Cuenta:</label>
							<input type="text" name="cuenta" id="cuenta" required class="form-control" placeholder="Escribe el nombre de la cuenta" value="<?php print $nombreCuenta;?>" <?php print ($m=='B')?'disabled':""; ?> />
						</div>
						<div class="form-group text-left">
							<label for="tipo">* Tipo de cuenta:</label><br>
							<select id="tipo" name="tipo" class="form-control" <?php print ($m=='B')?'disabled':""; ?>>
								<option value="0">Selecciona un tipo de cuenta</option>
								<option value="Efectivo">Efectivo</option>
								<option value="Credito">Crédito</option>
								<?php
								for ($i=0; $i < count($cuentasTipos_array); $i++) { 
									print "<option ";
									print ($cuentasTipos_array[$i]["cuenta"]==$tipo)?"selected":"";
									print " value='".$cuentasTipos_array[$i]["cuenta"]."'>";
									print $cuentasTipos_array[$i]["cuenta"];
									print "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group text-left">
							<label for="saldo">Saldo:</label>
							<input type="texto" name="saldo" id="saldo" class="form-control" placeholder="Escribe el saldo inicial de la cuenta" value="<?php print number_format($saldo,2); ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<input type="hidden" id="idCuenta" name="idCuenta" value="<?php print $idCuenta; ?>">
						<?php if($m=="C" || $m=="A") { ?>
						<div class="form-group text-left">
							<label for="enviar"></label>
							<input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar datos"/>

							<label for="regresar"></label>
							<input type="button" name="regresar" id="regresar" class="btn btn-info" value="Regresar" role="button"/>
						</div>
						<?php } else if($m=="B"){
							if($cuenta->sePuedeBorrar($idCuenta)){?>
								<div class="alert alert-danger">
									<p><b>Advertencia:</b> Una vez borrado el registro, no se podrá recuperar.</p>
									<p>¿Desea borrar el registro?</p>
									<label for="si"></label>
									<input type="button" name="si" id="si" class="btn btn-danger" value="Si"/>

									<label for="No"></label>
									<input type="button" name="no" id="no" class="btn" value="No" role="button"/>
									<input type="hidden" id="regresaBorrar" name="regresaBorrar">
								</div>
						<?php } else {?>
								<div class="alert alert-danger">
									<p><b>Advertencia:</b> esta cuenta tiene movimientos y no se puede borrar.</p>
									<label for="regresaBorrar"></label>
									<input type="button" name="regresaBorrar" id="regresaBorrar" class="btn btn-danger" value="Regresa"/>
									<input type="hidden" id="si" name="si">
									<input type="hidden" id="no" name="no">
								</div>
						<?php }
					} ?>
					</form>
				<?php
				}
				if($m=="S"){
					print "<table id='tabla' class='table table-striped' width='100%'>";
					print "<thead>";
					print "<tr>";
					print "<th>id</th>";
					print "<th>Cuentas</th>";
					print "<th>Tipo</th>";
					print "<th>Saldo incial</th>";
					print "<th>Cargos</th>";
					print "<th>Abonos</th>";
					print "<th>Saldo final</th>";
					print "<th>Modificar</th>";
					print "<th>Borrar</th>";
					print "</tr>";
					print "</thead>";
					for ($i=0; $i < count($cuentas_array); $i++) { 
						$saldo = $cuentas_array[$i]["saldo"] - $cuentas_array[$i]["cargos"] + $cuentas_array[$i]["abonos"]; 
						print "<tr>";
						print "<td>".$cuentas_array[$i]["id"]."</td>";
						print "<td class='text-left'>".$cuentas_array[$i]["cuenta"]."</td>";
						print "<td>".$cuentas_array[$i]["tipo"]."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["saldo"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["cargos"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["abonos"],2)."</td>";
						if($cuentas_array[$i]["tipo"]=="Credito"){
							print "<td class='text-right rojo'>(".number_format(abs($saldo),2).")</td>";
						} else {
							print "<td class='text-right verde'>".number_format($saldo,2)."</td>";
						}
						print "<td><a class='btn btn-info' href='cuentas.php?m=C&id=".$cuentas_array[$i]["id"]."'>Modificar</a></td>";
						print "<td><a class='btn btn-danger' href='cuentas.php?m=B&id=".$cuentas_array[$i]["id"]."'>Borrar</a></td>";
						print "</tr>";
					}
					print "</table>";
					//require "php/paginaBaja.php";
				}
				?>
			</div>
			<div class="col-sm-2 sidevar"></div>
		</div>
	</div>
	</div>

	<script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
                  
     <!--DataTables-->
    <script src="DataTable/datatables.min.js" type="text/javascript"></script>
                
    <!--Botones -->
    <script src="DataTable/Buttons-1.6.5/js/buttons.html5.min.js" type="text/javascript"></script>
    <script src="DataTable/Buttons-1.6.5/js/dataTables.buttons.min.js" type="text/javascript"></script>
    <script src="DataTable/JSZip-2.5.0/jszip.min.js" type="text/javascript"></script>
    <script src="DataTable/pdfmake-0.1.36/pdfmake.min.js" type="text/javascript"></script>
    <script src="DataTable/pdfmake-0.1.36/vfs_fonts.js" type="text/javascript"></script>
                
    <!--Script propio-->
    <script src="Estilos/main.js" type="text/javascript"></script>		
</body>
</html>