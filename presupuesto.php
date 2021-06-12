<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
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
Lee categorias
******************/
$categoria = new Categorias();
$categorias_array = array();
$numRegistros = $categoria->numRegistros($id);
if($numRegistros==0){
	array_push($msg,"1Error no hay categorías");
} else {
	$categorias_array = $categoria->leeCategoriasUsuario($id,0,$numRegistros);
}
/**********
Movimientos
**********/
$movimiento = new Movimientos();
/**********
Variables
**********/
$tot1 = 0;
$tot2 = 0;
$tot3 = 0;
$ejercido = 0;
$restante = 0;
/****************/
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Presupuesto</title>
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
			document.getElementById("grafica").onclick = function() {
				window.open("presupuestoGrafica1.php","_self");
			}
		}
	</script>
	<style>
	button{ cursor:pointer; }
	.rojo{ color:red; }
	.azul{ color:blue; }
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
				<label for="alta"></label>
				<input type="button" name="grafica" value="Gráfica" class="btn btn-info mt-5" role="button" id="grafica">
			</div>
			<div class="col-sm-8 text-center">
				<h2>Presupuesto</h2>
				<?php require "php/mensajes.php";
				print "<table id='tabla' class='table table-striped' width='100%'>";
				print "<thead>";
				print "<tr>";
				print "<th>Num. cuenta</th>";
				print "<th>Categoría</th>";
				print "<th>Tipo</th>";
				print "<th>Presupuesto</th>";
				print "<th>Ejercido</th>";
				print "<th>Restante</th>";
				print "</tr>";
				print "</thead>";
				for ($i=0; $i < count($categorias_array); $i++) {
					if($categorias_array[$i]["tipo"]=="gasto"){
						$ejercido = $movimiento->montoCategoria($categorias_array[$i]["id"],$id);
						$restante = $categorias_array[$i]["presupuesto"] - $ejercido;
						$tot1 += $categorias_array[$i]["presupuesto"];
						$tot2 += $ejercido;
						$tot3 += $restante;
						print "<tr>";
						print "<td>".$categorias_array[$i]["id"]."</td>";
						print "<td class='text-left'>".$categorias_array[$i]["categoria"]."</td>";
						print "<td>".$categorias_array[$i]["tipo"]."</td>";
						print "<td class='text-right'>".number_format($categorias_array[$i]["presupuesto"],2)."</td>";
						print "<td class='text-right'>".number_format($ejercido,2)."</td>";
						if ($restante>=0) {
							print "<td class='text-right azul'>";
						} else {
							print "<td class='text-right rojo'>";
						}
						print number_format($restante,2)."</td>";
						print "</tr>";
					}
				}
				print "<tr>";
				print "<td>Totales:</td>";
				print "<td class='text-left'></td>";
				print "<td></td>";
				print "<td class='text-right'>".number_format($tot1,2)."</td>";
				print "<td class='text-right'>".number_format($tot2,2)."</td>";
				print "<td class='text-right'>".number_format($tot3,2)."</td>";
				print "</tr>";
				print "</table>";
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