<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Cuentas.php";
require "clases/Categorias.php";
require "clases/Movimientos.php";
/****************
Leemos la sesión
*****************/
$sesion = new Sesion();
$usuario = $sesion->getUsuario();
$data = Usuarios::leeUsuario($usuario);
$id = $data["id"]; //identificador del usuario
/*****************
Lee movimientos
******************/
$movimiento = new Movimientos();
$movimientos_array = array();
$numRegistros = $movimiento->numRegistros($id);
/*****************************
Lee cuentas (realizamos pagos)
******************************/
$cuenta = new Cuentas();
$cuentas_array = array();
$cuentas_array = $cuenta->leeCuentasUsuario($id);
/******************/
//require "php/paginaArriba.php";
/****************
Variables de trabajo
********************/
$idMovimiento = "";
$idCuenta= "";
$idCategoria= "";
$tipo = "";
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
if (isset($_POST["cuenta"])) {
	$tipo = isset($_POST["tipo"])? $_POST["tipo"] : null;
	$categoria = isset($_POST["categoria"])? $_POST["categoria"] : null;
	$monto = isset($_POST["monto"])? $_POST["monto"] : null;
	$idCuenta = isset($_POST["cuenta"])? $_POST["cuenta"] : null;
	$fecha = isset($_POST["fecha"])? $_POST["fecha"] : null;
	$nota = isset($_POST["nota"])? $_POST["nota"] : null;
	$monto = limpiaNumero($monto);
	//
	if(!validaRequerido($tipo)) $msg[] = "1El campo de 'tipo' es requerido.";
	if(!validaRequerido($categoria)) $msg[] = "1El campo de 'categoría' es requerido.";
	if(!validaRequerido($idCuenta)) $msg[] = "1El campo de 'cuenta' es requerido.";
	if(!validaDecimal($monto)) $msg[] = "1El campo de 'monto' es incorrecto.";
	if($monto<=0) $msg[] = "1El campo de 'monto' no puede ser menor o igual a cero.";
	if(!validaFecha($fecha)) $msg[] = "1El campo de 'fecha' es incorrecto.";
	if (count($msg)==0) {
		$movimiento->altaMovimiento("",$id,"",$tipo, $categoria, $idCuenta, $monto, $fecha,$nota);
		$cuenta->actualizaSaldo($idCuenta,$tipo,$monto);
	} else {
		$m = "A";
	}
}
//Baja definitiva
if($m=="D"){
	$idMovimiento = $_GET["id"];
	//leemos los datos del registro
	$data = $movimiento->leerRegistro($idMovimiento);
	$idCuenta= $data[0]["cuenta"];
	$tipo = $data[0]["tipo"];
	$monto = $data[0]["monto"];
	//Borramos el movimiento
	$movimiento->borrarRegistro($idMovimiento);
	//Actualizamos el saldo
	$cuenta->actualizaSaldo($idCuenta,$tipo,$monto*-1);
	$m = "S";
}
//Consulta o baja (previa) del registro
if($m=="B"){
	$idMovimiento = $_GET["id"];
	$data = $movimiento->leerRegistro($idMovimiento);
	//
	$idCuenta= $data[0]["cuenta"];
	$idCategoria= $data[0]["categoria"];
	$tipo = $data[0]["tipo"];
	$monto = $data[0]["monto"];
	$fecha = $data[0]["fecha"];
	$nota = $data[0]["nota"];
	$cuentaNombre = $data[0]["cuentaNombre"];
	$categoriaNombre = $data[0]["categoriaNombre"];
} else if($m=="S"){
	$movimientos_array = $movimiento->leeMovimientosUsuario($id); //,$inicio,$TAMANO_PAGINA
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Movimientos</title>
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
					window.open("movimientos.php?m=A","_self");
				}
			<?php } 
			
			if($m=="C" || $m=="A"){ ?>
				document.getElementById("regresar").onclick = function(){
					window.open("movimientos.php","_self");
				}
			<?php } 
			
			if($m=="B"){ ?>
				document.getElementById("si").onclick = function(){
					var idMovimiento = <?php print $idMovimiento; ?>;
					window.open("movimientos.php?m=D&id="+idMovimiento,"_self");
				}
				document.getElementById("no").onclick = function(){
					window.open("movimientos.php","_self");
				}
			<?php } else  {?>

				document.getElementById("tipo").onchange = function() {
					var tipo = this.value;
					console.log(tipo);
					despliegaCategorias(tipo);
				}

			<?php } ?>
		}
		function cambiaPagina(p) {
			window.open("movimientos.php?p="+p,"_self");
		}
		function despliegaCategorias(tipo){
			if (tipo=="") return;
			var xmlhttp;
			var usuario = <?php print $id; ?>;
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			} else {
				//IE5 o 6
				xmlhttp = new ActiveXObject("Microsoft.HTMLHTTP");
			}
			xmlhttp.open("GET","php/leeCategorias.php?t="+tipo+"&usuario="+usuario,true);
			xmlhttp.send();
			xmlhttp.onreadystatechange = function() {
				//estado 4 y 200
				if (xmlhttp.readyState==4) {
					if (xmlhttp.status==200) {
						creaCombo(xmlhttp.responseXML);
					} else {
						alert("Error al leer las categorías ".xmlhttp.status);
					}
				}
			}
		}
		function creaCombo(objetoXML) {
			var a = objetoXML.documentElement.getElementsByTagName("categoria");
			var categoria_cb = document.getElementById("categoria");
			//Limpiar el combo
			while(categoria_cb.length) categoria_cb.remove(0);
			//
			var op = document.createElement("option");
			op.innerHTML= "--Selecciona una categoría--";
			op.setAttribute("value","0");
			categoria_cb.appendChild(op);
			//
			for (var i = 0; i < a.length; i++) {
				num = a[i].getElementsByTagName("id");
				numCategoria = num[0].firstChild.nodeValue;
				//
				cat = a[i].getElementsByTagName("nombre");
				nomCategoria = cat[0].firstChild.nodeValue;
				//
				//creamos la etiqueta <option>
				//
				var op = document.createElement("option");
				op.innerHTML= nomCategoria;
				op.setAttribute("value",numCategoria);
				categoria_cb.appendChild(op); 
			}
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
					<input type="button" name="alta" value="Dar de alta un movimiento" class="btn btn-info mt-5" role="button" id="alta">
				<?php } ?>
			</div>
			<div class="col-sm-9 text-center">
				<h2>Movimientos</h2>
				<?php if($m=="C" || $m=="A" || $m=="B") { 
					require "php/mensajes.php";
				?>
					<form action="movimientos.php" method="post">
						<div class="form-group text-left">
							<label for="tipo">* Tipo de cuenta:</label><br>
							<?php
							if($m=="B"){
								print "<input class='form-control' type='text' id='tipo' name='tipo' value='".$tipo."' disabled/>";
							} else { ?>
							<select id="tipo" name="tipo" class="form-control">
								<option value="">Selecciona un tipo de cuenta</option>
								<option value="gasto">Gasto</option>
								<option value="ingreso">Ingreso</option>
							</select>
							<?php } ?>
						</div>
						<div class="form-group text-left">
							<label for="categoria">* Categoria:</label><br>
							<?php 
							if($m=="B"){
								print "<input class='form-control' type='text' id='categoria' name='categoria' value='".$categoriaNombre."' disabled/>";
							} else { ?>
								<select id="categoria" name="categoria" class="form-control">
									<option value="">Selecciona una categoria</option>
								</select>
							<?php } ?>
						</div>
						<div class="form-group text-left">
							<label for="cuenta">* Cuenta:</label><br>
							<select id="cuenta" name="cuenta" class="form-control" <?php print ($m=='B')?'disabled':""; ?>>
								<option value="">* Forma de pago</option>
								<?php
								for ($i=0; $i < count($cuentas_array); $i++) { 
									print "<option ";
									print ($cuentas_array[$i]["id"]==$idCuenta)?"selected":"";
									print " value='".$cuentas_array[$i]["id"]."'>";
									print $cuentas_array[$i]["cuenta"];
									print "</option>";
								}
								?>
							</select>
						</div>
						<div class="form-group text-left">
							<label for="monto">* Monto:</label>
							<input type="text" name="monto" id="monto" required class="form-control" placeholder="Escribe el monto del movimiento" value="<?php print number_format($monto,2); ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="fecha">* Fecha:</label>
							<input type="date" name="fecha" id="fecha" required class="form-control" placeholder="AAAA-MM-DD" value="<?php print $fecha; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<div class="form-group text-left">
							<label for="nota">Nota:</label>
							<input type="text" name="nota" id="nota" class="form-control" placeholder="Escribe una nota" value="<?php print $nota; ?>" <?php print ($m=='B')?'disabled':""; ?>/>
						</div>
						<input type="hidden" id="idCuenta" name="idCuenta" value="<?php print $idCuenta; ?>">
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
							<p>La eliminición de un saldo modificará sus saldos.</p>
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
					print "<th>Fecha</th>";
					print "<th>Cuenta</th>";
					print "<th>Categoria</th>";
					print "<th>Cargo</th>";
					print "<th>Abono</th>";
					print "<th>Nota</th>";
					print "<th>Borrar</th>";
					print "</tr>";
					print "</thead>";
					for ($i=0; $i < count($movimientos_array); $i++) {
						$monto = $movimientos_array[$i]["monto"];
						
						print "<tr>";
						print "<td>".$movimientos_array[$i]["fecha"]."</td>";
						print "<td class='text-left'>".$movimientos_array[$i]["cuentaNombre"]."</td>";
						print "<td>".$movimientos_array[$i]["categoriaNombre"]."</td>";
						if ($movimientos_array[$i]["tipo"]=="gasto") {
							print "<td class='text-right'>".number_format($monto,2)."</td>";
							print "<td>-</td>";
						} else {
							print "<td>-</td>";
							print "<td class='text-right'>".number_format($monto,2)."</td>";
						}
						print "<td class='text-left'>".$movimientos_array[$i]["nota"]."</td>";
						print "<td><a class='btn btn-danger' href='movimientos.php?m=B&id=".$movimientos_array[$i]["id"]."'>Borrar</a></td>";
						print "</tr>";
						
					}
					print "</table>";
					//require "php/paginaBaja.php";
				}
				
				?>
			</div>
			<div class="col-sm-1 sidevar"></div>
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