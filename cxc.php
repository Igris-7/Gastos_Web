<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Cuentas.php";
require "clases/Categorias.php";
require "clases/Movimientos.php";
require "clases/CXC.php";
/****************
Leemos la sesión
*****************/
$sesion = new Sesion();
$usuario = $sesion->getUsuario();
$data = Usuarios::leeUsuario($usuario);
$id = $data["id"]; //identificador del usuario
/*****************************
Lee Cuentas
******************************/
$cuenta = new Cuentas();
/*****************************
Creamos el movimiento
******************************/
$movimiento = new Movimientos();
/*****************************
Creamos el categoria
******************************/
$categoria = new Categorias();
/*****************************
Lee CXC 
******************************/
$cxc = new CXC();
$cxc_array = array();
$cxc_array = $cxc->leeCXCUsuario($id);
$numRegistros = $cxc->numRegistros($id);
/******************/
//require "php/paginaArriba.php";
/****************
Variables de trabajo
********************/
$cliente = "";
$monto = 0;
$fecha = "";
$nota = "";
$estado = "";
$estados_array = array("Pendiente","Pagado","Cancelado");
/****************
Modo de la página (CRUD o ABC)
S - Consulta (select)
A - Alta (insert)
B - Cancelar (update)
C - Cambiar (update)
D - Baja Definitiva
P - Pago (update)
*****************/
if (isset($_GET["m"])) {
	$m = $_GET["m"];
} else {
	$m = "S";
}
/**************
Validacion
**************/
if (isset($_POST["cliente"])) {
	//
	$cliente = isset($_POST["cliente"])? $_POST["cliente"] : null;
	$monto = isset($_POST["monto"])? $_POST["monto"] : null;
	$nota = isset($_POST["nota"])? $_POST["nota"] : null;
	$monto = limpiaNumero($monto);
	$nota = validaCadena($nota);
	//
	if(!validaRequerido($cliente)) $msg[] = "1El campo de 'cliente' es requerido.";
	if(!validaDecimal($monto)) $msg[] = "1El campo de 'monto' es incorrecto.";
	if($monto<=0) $msg[] = "1El campo de 'monto' no puede ser menor o igual a cero.";
	//
	//estado = 0 => pendiente
	//estado = 1 => pagado
	if (count($msg)==0) {
		$cxc->altaCXC("",$id,"",0, $cliente, $monto, $nota);
	} else {
		$m = "A";
	}
	//
}
/***************
Validacion Pago
****************/
if (isset($_POST["cuentaDeposito"])) {
	//
	$idCXC = isset($_POST["idCXC"])? $_POST["idCXC"] : null; 
	$cuentaDeposito = isset($_POST["cuentaDeposito"])? $_POST["cuentaDeposito"] : null; 
	$clientePago = isset($_POST["clientePago"])? $_POST["clientePago"] : null;
	$montoPago = isset($_POST["montoPago"])? $_POST["montoPago"] : null;
	$notaPago = isset($_POST["notaPago"])? $_POST["notaPago"] : null;
	$fecha = isset($_POST["fecha"])? $_POST["fecha"] : null;
	$montoPago = limpiaNumero($montoPago);
	$notaPago = validaCadena($notaPago);
	//
	if(!validaRequerido($clientePago)) $msg[] = "1El campo de 'cliente' es requerido.";
	if(!validaDecimal($montoPago)) $msg[] = "1El campo de 'monto' es incorrecto.";
	if(!validaFecha($fecha)) $msg[] = "1El campo de 'fecha' es incorrecto.";
	if($montoPago<=0) $msg[] = "1El campo de 'monto' no puede ser menor o igual a cero.";
	$idCategoria = $categoria->categoriaCXC($id);
	//
	//estado = 0 => pendiente
	//estado = 1 => pagado
	//estado = 2 => cancelada
	if (count($msg)==0) {
		$cxc->pagarCXC($idCXC);
		$movimiento->altaMovimiento("",$id,"","ingreso", $idCategoria, $cuentaDeposito, $montoPago, $fecha,$notaPago);
		$cuenta->actualizaSaldo($cuentaDeposito, "ingreso", $montoPago);
	} else {
		$m = "P";
	}
	//
}
//Cancelacion
if($m=="D"){
	$idCXC = $_GET["id"];
	$cxc->cancelarCXC($idCXC);
	$m = "S";
}
//Consulta o baja (previa) del registro
if($m=="P" || $m=="B"){
	//Lectura de las cuentas para el pago
	//
	$cuentas_array = $cuenta->leeCuentasUsuario($id);
	//
	$idCXC = $_GET["id"];
	$data = $cxc->leerRegistro($idCXC);
	//
	$idCXC = $data[0]["id"];
	$cliente = $data[0]["cliente"];
	$monto = $data[0]["monto"];
	$nota = $data[0]["nota"];
	$estado = $data[0]["estado"];

} else if($m=="S"){
	$cxc_array = $cxc->leeCXCUsuario($id); //,$inicio,$TAMANO_PAGINA
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Cuentas por cobrar</title>
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
					window.open("cxc.php?m=A","_self");
				}
			<?php } 
			
			if($m=="P" || $m=="A"){ ?>
				document.getElementById("regresar").onclick = function(){
					window.open("cxc.php","_self");
				}
			<?php } 
			
			if($m=="B"){ ?>
				document.getElementById("si").onclick = function(){
					var idCXC = <?php print $idCXC; ?>;
					window.open("cxc.php?m=D&id="+idCXC,"_self");
				}
				document.getElementById("no").onclick = function(){
					window.open("cxc.php","_self");
				}
				document.getElementById("regresaCancelar").onclick = function(){
					window.open("cxc.php","_self");
				}
			<?php } ?>
		}
		function cambiaPagina(p) {
			window.open("cxc.php?p="+p,"_self");
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
					<input type="button" name="alta" value="Dar de alta una CXC" class="btn btn-info mt-5" role="button" id="alta">
				<?php } ?>
			</div>
			<div class="col-sm-8 text-center">
				<h2>Cuentas por cobrar</h2>
				<?php if($m=="C" || $m=="A" || $m=="B") { 
					require "php/mensajes.php";
				?>
					<form action="cxc.php" method="post">
						<div class="form-group text-left">
							<label for="cliente">* Cliente:</label><br>
							<input type="text" name="cliente" id="cliente" required class="form-control" placeholder="Escribe el nombre de tu cliente" value="<?php print $cliente; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="monto">* Monto:</label>
							<input type="text" name="monto" id="monto" required class="form-control" placeholder="Escribe el monto de la cuenta por cobrar" value="<?php print number_format($monto,2); ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="nota">Nota:</label>
							<input type="text" name="nota" id="nota" class="form-control" placeholder="Escribe una nota" value="<?php print $nota; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<input type="hidden" id="idCXC" name="idCXC" value="<?php print $idCXC; ?>">
						<?php if($m=="C" || $m=="A") { ?>
						<div class="form-group text-left">
							<label for="enviar"></label>
							<input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar datos"/>

							<label for="regresar"></label>
							<input type="button" name="regresar" id="regresar" class="btn btn-info" value="Regresar" role="button"/>
						</div>
						<?php } else if($m=="B"){
							//o es pediente, 1 es pagado y 2 es cancelado
							if ($estado==0) { ?>
							<div class="alert alert-danger">
								<p><b>Advertencia:</b> Una vez cancelada la cuenta por cobrar, no se podrá recuperar.</p>
								<p>¿Desea cancelar el registro?</p>
								<label for="si"></label>
								<input type="button" name="si" id="si" class="btn btn-danger" value="Si"/>

								<label for="No"></label>
								<input type="button" name="no" id="no" class="btn" value="No" role="button"/>
								<input type="hidden" name="regresaCancelar" id="regresaCancelar"/>
							</div>
						<?php } else { ?>
						<div class="alert alert-danger">
							<p><b>Advertencia:</b> Sólo puede cancelar cuentas pendientes de pago.</p>
								<label for="regresaCancelar"></label>
								<input type="button" name="regresaCancelar" id="regresaCancelar" class="btn btn-danger" value="Regresar"/>

								<label for="No"></label>
								<input type="hidden" name="no" id="no"/>
								<input type="hidden" name="si" id="si"/>
						</div>
						<?php }
						}?>
					</form>
				<?php
				}
				//
				//realizamos el pago
				//
				if($m=="P") { 
					require "php/mensajes.php";
				?>
					<form action="cxc.php" method="post">
						<div class="form-group text-left">
							<label for="cuentaDeposito">* Cuenta de depósito:</label><br>
							<select id="cuentaDeposito" name="cuentaDeposito" class="form-control">
								<option value="0">Selecciona la cuenta de pago</option>
								<?php
								for ($i=0; $i < count($cuentas_array); $i++) { 
									print "<option ";
									print " value='".$cuentas_array[$i]["id"]."'>";
									print $cuentas_array[$i]["cuenta"];
									print "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group text-left">
							<label for="clientePago1">Cliente:</label><br>
							<input type="text" name="clientePago1" id="clientePago1" class="form-control" value="<?php print $cliente; ?>" disabled/>
						</div>
						<div class="form-group text-left">
							<label for="montoPago1">Monto:</label>
							<input type="text" name="montoPago1" id="montoPago1" class="form-control" value="<?php print number_format($monto,2); ?>" disabled/>
						</div>
						<div class="form-group text-left">
							<label for="notaCXC">Nota de la CXC:</label>
							<input type="text" name="notaCXC" id="notaCXC" class="form-control" value="<?php print $nota; ?>" disabled/>
						</div>
						<div class="form-group text-left">
							<label for="notaPago">Nota sobre el pago:</label>
							<input type="text" name="notaPago" id="notaPago" class="form-control" placeholder="Escribe una nota sobre el pago"/>
						</div>
						<div class="form-group text-left">
							<label for="fecha">* Fecha:</label>
							<input type="date" name="fecha" id="fecha" class="form-control" placeholder="MM/DD/AAAA" value="<?php print $fecha; ?>"/>
						</div>
						<input type="hidden" id="idCXC" name="idCXC" value="<?php print $idCXC; ?>">
						<input type="hidden" id="clientePago" name="clientePago" value="<?php print $cliente; ?>">
						<input type="hidden" id="montoPago" name="montoPago" value="<?php print $monto; ?>">
						<?php if($m=="P") { ?>
						<div class="form-group text-left">
							<label for="enviar"></label>
							<input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar datos"/>

							<label for="regresar"></label>
							<input type="button" name="regresar" id="regresar" class="btn btn-info" value="Regresar" role="button"/>
						</div>
						<?php } ?>
					</form>
				<?php
				}
				if($m=="S"){
					print "<table id='tabla' class='table table-striped' width='100%'>";
					print "<thead>";
					print "<tr>";
					print "<th>Estado</th>";
					print "<th>Cliente</th>";
					print "<th>Monto</th>";
					print "<th>Nota</th>";
					print "<th>Pagar</th>";
					print "<th>Cancelar</th>";
					print "</tr>";
					print "</thead>";
					//
					for ($i=0; $i < count($cxc_array); $i++) {
						$monto = $cxc_array[$i]["monto"];
						$edo = $cxc_array[$i]["estado"];
						print "<tr>";
						print "<td>".$estados_array[$cxc_array[$i]["estado"]]."</td>";
						print "<td class='text-left'>".$cxc_array[$i]["cliente"]."</td>";
						print "<td>$".number_format($monto,2)."</td>";
						print "<td class='text-left'>".$cxc_array[$i]["nota"]."</td>";
						print "<td><a class='btn btn-info";
						print ($edo==1)?" disabled":"";
						print "' href='cxc.php?m=P&id=".$cxc_array[$i]["id"]."'>Pagar</a></td>";
						print "<td><a class='btn btn-danger' href='cxc.php?m=B&id=".$cxc_array[$i]["id"]."'>Cancelar</a></td>";
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