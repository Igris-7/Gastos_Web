<?php
require "php/variables.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Cuentas.php";
require "clases/CXC.php";
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
Lee cuentas
******************/
$cuenta = new Cuentas();
$cuentas_array = $cuenta->leeCuentasUsuario($id);
$cuentasTipos_array = $cuenta->leeCuentasTipos();
/*****************************
Lee CXC 
******************************/
$cxc = new CXC();
$cxc_array = $cxc->leeCXCUsuario($id);
/*****************************
Lee presupuesto 
******************************/
$categoria = new Categorias();
$presupuesto = $categoria->presupuesto($id);
$traspaso = $categoria->categoriaTraspaso($id);
/*****************************
Lee movimientos 
******************************/
$movimiento = new Movimientos();
$gastos = $movimiento->gastos($id,$traspaso);
if ($presupuesto==0) {
	$porcien = 0;
} else {
	$porcien = $gastos/$presupuesto*100;
}
/*****************************/
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Inicio</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="imagenes/fox1.png">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">		
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="Estilos/style.css">

	<script>
      // Carga el API de visualización y el paquete corechart
      google.charts.load('current', {'packages':['gauge','bar']});

      // Define la función callback para crear la gráfica
      google.charts.setOnLoadCallback(grafica);
      google.charts.setOnLoadCallback(barras);

      // Función para poblar la gráfica
      // iniciar el gráfico, pasa los datos y los dibuja
      function grafica() {
        // Crea la gráfica
        var data = new google.visualization.arrayToDataTable([
        	['Etiqueta','Valor'],
        	<?php
        		print "['Gastos',".$porcien."]";
        	?>
        ]);
        // Opciones de la gráfica
        var opciones = {
        	width:400,
        	height: 120,
        	redFrom: 81,
        	redTo:100,
        	greenFrom:0,
        	greenTo:60,
        	yellowFrom:61,
        	yellowTo:80,
        	min:0,
        	max:100,
        	minorTicks:5 
        };

        // Inicia la gráfica
        var chart = new google.visualization.Gauge(document.getElementById('gastos'));
        chart.draw(data, opciones);
      }

      function barras() {
      	var dataColumnas = google.visualization.arrayToDataTable([
      	["Cuentas","Movimientos"],
      	<?php
      		$n = count($cuentas_array);
      		for($i=0; $i<$n; $i++){
      			print "['".$cuentas_array[$i]["cuenta"]."',".$cuentas_array[$i]["cargos"]."]";
      			if(($i+1)<$n) print ",";
      		}
      	?>
      	]);
      	//
      	var opcionesColumnas = {
      		chart: {title:'Movimientos', subtitle: 'Cuentas'},
      		colors:['orange'],
      		bars: 'horizontal',
      		fontSize:15,
      		fontName:'Times',
      		hAxis: {title:'$',titleTextStyle:{color:'blue', fontSize:10},textPosition:'out',textStyle:{color:'blue', fontSize:10, fontName:"Times"}},
      		vAxis: {title:'',titleTextStyle:{color:'blue', fontSize:10}},
      		legend: {position:'none'},
      		titleTextStyle: {color:'gray',fontSize:20,italic:true},
      		height:200
      	};
      	var chartColumnas = new google.charts.Bar(document.getElementById('columnas'));
      	chartColumnas.draw(dataColumnas,google.charts.Bar.convertOptions(opcionesColumnas));
      }
    </script>
	<style>
		#gastos{
			width: 100px;
			margin:20px auto;
		}
		#columnas{
			width: 200px;
			margin:20px auto;
		}
		p{
			text-align: left;
			margin:0;
		}

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
			<div id="gastos"></div>
			<h6>Gastos vs. presupuesto</h6>
			<p>Presupuesto: $<?php print number_format($presupuesto,2); ?></p>
			<p>Ejercido: $<?php print number_format($gastos,2); ?></p>
			<p>Porcentaje: <?php print number_format($porcien,2); ?>%</p>

			</div>
			<div class="col-sm-8 text-center">
			<h2>Resumen de efectivo</h2>
			<?php
				print "<table class='table table-striped' width='100%'>";
				print "<tr>";
				print "<th>id</th>";
				print "<th>Cuentas</th>";
				print "<th>Tipo</th>";
				print "<th>Saldo incial</th>";
				print "<th>Cargos</th>";
				print "<th>Abonos</th>";
				print "<th>Saldo final</th>";
				print "</tr>";
				$tot1 = 0;
				$tot2 = 0;
				$tot3 = 0;
				$tot4 = 0;
				for ($i=0; $i < count($cuentas_array); $i++) { 
					if($cuentas_array[$i]["tipo"]=="Efectivo"){
						$saldo = $cuentas_array[$i]["saldo"] - $cuentas_array[$i]["cargos"] + $cuentas_array[$i]["abonos"]; 
						print "<tr>";
						print "<td>".$cuentas_array[$i]["id"]."</td>";
						print "<td class='text-left'>".$cuentas_array[$i]["cuenta"]."</td>";
						print "<td>".$cuentas_array[$i]["tipo"]."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["saldo"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["cargos"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["abonos"],2)."</td>";
						
							print "<td class='text-right rojo'>".number_format(abs($saldo),2)."</td>";
						print "</tr>";
						$tot1 += $cuentas_array[$i]["saldo"];
						$tot2 += $cuentas_array[$i]["cargos"];
						$tot3 += $cuentas_array[$i]["abonos"];
						$tot4 += $saldo;
					}
				}
				print "<tr>";
				print "<td>Totales:</td>";
				print "<td></td>";
				print "<td></td>";
				print "<td class='text-right'>".number_format($tot1,2)."</td>";
				print "<td class='text-right'>".number_format($tot2,2)."</td>";
				print "<td class='text-right'>".number_format($tot3,2)."</td>";
				print "<td class='text-right'>".number_format($tot4,2)."</td>";
				print "</tr>";
				print "</table>";
			?>	
			<h2>Deudas de tarjeta de crédito</h2>
			<?php
				print "<table class='table table-striped' width='100%'>";
				print "<tr>";
				print "<th>id</th>";
				print "<th>Cuentas</th>";
				print "<th>Tipo</th>";
				print "<th>Saldo incial</th>";
				print "<th>Cargos</th>";
				print "<th>Abonos</th>";
				print "<th>Saldo final</th>";
				print "</tr>";
				$tot1 = 0;
				$tot2 = 0;
				$tot3 = 0;
				$tot4 = 0;
				for ($i=0; $i < count($cuentas_array); $i++) { 
					if($cuentas_array[$i]["tipo"]=="Credito"){
						$saldo = $cuentas_array[$i]["saldo"] - $cuentas_array[$i]["cargos"] + $cuentas_array[$i]["abonos"]; 
						print "<tr>";
						print "<td>".$cuentas_array[$i]["id"]."</td>";
						print "<td class='text-left'>".$cuentas_array[$i]["cuenta"]."</td>";
						print "<td>".$cuentas_array[$i]["tipo"]."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["saldo"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["cargos"],2)."</td>";
						print "<td class='text-right'>".number_format($cuentas_array[$i]["abonos"],2)."</td>";
						
							print "<td class='text-right rojo'>(".number_format(abs($saldo),2).")</td>";
						print "</tr>";
						$tot1 += $cuentas_array[$i]["saldo"];
						$tot2 += $cuentas_array[$i]["cargos"];
						$tot3 += $cuentas_array[$i]["abonos"];
						$tot4 += $saldo;
					}
				}
				print "<tr>";
				print "<td>Totales:</td>";
				print "<td></td>";
				print "<td></td>";
				print "<td class='text-right'>".number_format($tot1,2)."</td>";
				print "<td class='text-right'>".number_format($tot2,2)."</td>";
				print "<td class='text-right'>".number_format($tot3,2)."</td>";
				print "<td class='text-right'>(".number_format(abs($tot4),2).")</td>";
				print "</tr>";
				print "</table>";
			?>
			<h2>Cuentas por cobrar pendientes</h2>
			<?php
			print "<table class='table table-striped' width='100%'>";
			print "<tr>";
			print "<th>Cliente</th>";
			print "<th>Nota</th>";
			print "<th>Monto</th>";
			print "</tr>";
			//
			for ($i=0; $i < count($cxc_array); $i++) {
				if ($cxc_array[$i]["estado"]==0) {
					$monto = $cxc_array[$i]["monto"];
					print "<tr>";
					print "<td class='text-left'>".$cxc_array[$i]["cliente"]."</td>";
					print "<td class='text-left'>".$cxc_array[$i]["nota"]."</td>";
					print "<td class='text-right'>$".number_format($monto,2)."</td>";
					print "</tr>";
				}
			}
			//
			print "</table>";
			?>
			</div>
			<div class="col-sm-2 sidevar">
				<div id="columnas"></div>
			</div>
		</div>
	</div>
    </div>
</div>		
	<script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>