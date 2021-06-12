<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Cuentas.php";
require "clases/Categorias.php";
require "clases/Movimientos.php";
require "clases/Traspasos.php";
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
$cuentas_array = $cuenta->leeCuentasUsuario($id);
$cuentasTipos_array = $cuenta->leeCuentasTipos();
/******************
Categorias
*******************/
$categoria = new Categorias();
$categoriaTraspaso = $categoria->categoriaTraspaso($id);
/******************
Movimientos
*******************/
$movimiento = new Movimientos();
/******************
Traspasos
*******************/
$traspaso = new Traspasos();
$traspasos_array = $traspaso->leeTraspasosUsuario($id);
$numRegistros = $traspaso->numRegistros($id);
/******************/
//require "php/paginaArriba.php";
/****************
Variables de trabajo
********************/
$idCuenta= "";
$origen = "";
$destino = "";
$monto = 0;
$fecha = "";
$nota = "";
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
if (isset($_POST["origen"])) {
	$origen = (isset($_POST["origen"]))?$_POST["origen"]:NULL;
	$destino = (isset($_POST["destino"]))?$_POST["destino"]:NULL;
	$monto = $_POST["monto"];
	$fecha = $_POST["fecha"];
	$nota = $_POST["nota"];
	$monto = limpiaNumero($monto);
	//
	//Recuperamos el saldo
	//
	for ($i=0; $i < count($cuentas_array) ; $i++) { 
		if ($cuentas_array[$i]["id"]==$origen) {
			$saldo = $cuentas_array[$i]["saldo"]-$cuentas_array[$i]["cargos"]+$cuentas_array[$i]["abonos"];
			break;
		} 
	}
	//validar
	if ($origen=="") {
		array_push($msg,"1La cuenta origen no puede estar vacía");
	} else if($destino==""){
		array_push($msg,"1La cuenta destino no puede estar vacía");
	} else if($monto<=0){
		array_push($msg,"1El monto no puede ser menor o igual a cero");
	} else if($origen==$destino){
		array_push($msg,"1La cuenta origen debe ser diferente a la cuenta de destino");
	} else if(!validaFecha($fecha)){
		array_push($msg,"1La cuenta origen debe ser diferente a la cuenta de destino");
	} else if(!validaDecimal($monto)){
		array_push($msg,"1El monto solo acepta valores numéricos");
	} else if($monto>$saldo){
		array_push($msg,"1El saldo en la cuenta de origen es insuficiente");
	} else {
		//
		//movimiento de "gasto" (Cargo)
		//
		$movimiento->altaMovimiento("",$id,"","gasto", $categoriaTraspaso, $origen, $monto, $fecha,$nota);
		$cuenta->actualizaSaldo($origen,"gasto",$monto);
		//
		//Movimiento de "ingreso (Abono)"
		//
		$movimiento->altaMovimiento("",$id,"","ingreso", $categoriaTraspaso, $destino, $monto, $fecha,$nota);
		$cuenta->actualizaSaldo($destino,"ingreso",$monto);
		//
		//Registro de traspaso
		//
		$traspaso->altaRegistro("",$id,$origen,$destino,$monto,$fecha,$nota);
	}
	$m="A";
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
function nombreCuenta($cuenta, $cuentas_array){
	$nombre = "";
	for ($i=0; $i < count($cuentas_array); $i++) { 
		if ($cuenta==$cuentas_array[$i]["id"]) {
			$nombre = $cuentas_array[$i]["cuenta"];
			break;
		}
	}
	return $nombre;
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Traspaso entre cuentas</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="imagenes/fox1.png">
	
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
					window.open("traspasos.php?m=A","_self");
				}
			<?php } 
			
			if($m=="C" || $m=="A"){ ?>
				document.getElementById("regresar").onclick = function(){
					window.open("traspasos.php","_self");
				}
			<?php } 
			
			if($m=="B"){ ?>
				document.getElementById("si").onclick = function(){
					var idCuenta = <?php print $idCuenta; ?>;
					window.open("traspasos.php?m=D&id="+idCuenta,"_self");
				}
				document.getElementById("no").onclick = function(){
					window.open("traspasos.php","_self");
				}
			<?php } ?>
			
		}
		function cambiaPagina(p) {
			window.open("traspasos.php?p="+p,"_self");
		}
	</script>
	<style>
	button{ cursor:pointer; }
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
					<input type="button" name="alta" value="Dar de alta un traspaso" class="btn btn-info mt-5" role="button" id="alta">
				<?php } ?>
			</div>
			<div class="col-sm-8 text-center">
				<h2>Traspasos</h2>
				<?php if($m=="C" || $m=="A" || $m=="B") { 
					require "php/mensajes.php";
				?>
					<form action="traspasos.php" method="post">
						<div class="form-group text-left">
							<label for="origen">* Cuenta origen:</label>
							<select id="origen" name="origen" class="form-control" <?php print ($m=='B')?'disabled':""; ?>>
								<option value="">Selecciona la cuenta origen:</option>
								<?php
								for ($i=0; $i < count($cuentas_array); $i++) { 
									$saldo = $cuentas_array[$i]["saldo"]-$cuentas_array[$i]["cargos"]+$cuentas_array[$i]["abonos"];
									print "<option ";
									print " value='".$cuentas_array[$i]["id"]."'>";
									print $cuentas_array[$i]["cuenta"];
									print " ($".number_format($saldo,2).")";
									print "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group text-left">
							<label for="destino">* Cuenta destino:</label><br>
							<select id="destino" name="destino" class="form-control" <?php print ($m=='B')?'disabled':""; ?>>
								<option value="">Selecciona la cuenta destino</option>
								<?php
								for ($i=0; $i < count($cuentas_array); $i++) {
									$saldo = $cuentas_array[$i]["saldo"]-$cuentas_array[$i]["cargos"]+$cuentas_array[$i]["abonos"];
									print "<option ";
									print " value='".$cuentas_array[$i]["id"]."'>";
									print $cuentas_array[$i]["cuenta"];
									print " ($".number_format($saldo,2).")";
									print "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group text-left">
							<label for="monto">Monto:</label>
							<input type="texto" name="monto" id="monto" class="form-control" placeholder="Escribe el monto del traspaso" value="<?php print number_format(0,2); ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="fecha">* Fecha:</label>
							<input type="date" name="fecha" id="fecha" required class="form-control" placeholder="AAAA-MM-DD" value="<?php print $fecha; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="nota">Nota:</label>
							<input type="text" name="nota" id="nota" class="form-control" placeholder="Escribe una nota" value="<?php print $nota; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<?php if($m=="C" || $m=="A") { ?>
						<div class="form-group text-left">
							<label for="enviar"></label>
							<input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar datos"/>

							<label for="regresar"></label>
							<input type="button" name="regresar" id="regresar" class="btn btn-info" value="Regresar" role="button"/>
						</div>
						<?php } else if($m=="B"){?>
						<div class="alert alert-danger">
							<p><b>Advertencia:</b> Una vez borrado el registro, no se podrá recuperar.</p>
							<p>¿Desea borrar el registro?</p>
							<label for="si"></label>
							<input type="button" name="si" id="si" class="btn btn-danger" value="Si"/>

							<label for="No"></label>
							<input type="button" name="no" id="no" class="btn" value="No" role="button"/>
						</div>
						<?php } ?>
					</form>
				<?php
				}
				if($m=="S"){
					print "<table id='tabla' class='table table-striped' width='100%'>";
					print "<thead>";
					print "<tr>";
					print "<th>id</th>";
					print "<th>Cuenta origen</th>";
					print "<th>Cuenta destino</th>";
					print "<th>Monto</th>";
					print "<th>Fecha</th>";
					print "<th>Nota</th>";
					print "</tr>";
					print "</thead>";
					//
					for ($i=0; $i < count($traspasos_array); $i++) { 
						print "<tr>";
						print "<td>".$traspasos_array[$i]["id"]."</td>";
						print "<td class='text-left'>";
						print nombreCuenta($traspasos_array[$i]["origen"], $cuentas_array);
						print "</td>";
						print "<td class='text-left'>";
						print nombreCuenta($traspasos_array[$i]["destino"], $cuentas_array);
						print "</td>";
						print "<td class='text-right'>".number_format($traspasos_array[$i]["monto"],2)."</td>";
						print "<td>".$traspasos_array[$i]["fecha"]."</td>";
						print "<td class='text-left'>".$traspasos_array[$i]["nota"]."</td>";
						print "</tr>";
					}
					//
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