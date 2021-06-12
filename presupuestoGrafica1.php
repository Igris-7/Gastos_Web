<?php
require "php/variables.php";
require "php/funciones.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
require "clases/Categorias.php";
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
	<title>Control de Gastos | Presupuesto Gráfica de pie</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
  <link rel="shortcut icon" href="imagenes/fox1.png">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	
	<!--Cargar AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">		
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="Estilos/style.css">   

	<script>
		window.onload = function(){
			document.getElementById("tabla").onclick = function() {
				window.open("presupuesto.php","_self");
			}
		}
	</script>
    <script>

      // Carga el API de visualización y el paquete corechart
      google.charts.load('current', {'packages':['corechart']});

      // Define la función callback para crear la gráfica
      google.charts.setOnLoadCallback(grafica);

      // Función para poblar la gráfica
      // iniciar el gráfico, pasa los datos y los dibuja
      function grafica() {

        // Crea la gráfica
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Navegador');
        data.addColumn('number', 'Porciento');
        data.addRows([
          
        <?php
          $n = count($categorias_array);
          for ($i = 0; $i < $n; $i++) {
          	if($categorias_array[$i]["tipo"]=="gasto"){
          		print "['".$categorias_array[$i]["categoria"]."', ".$categorias_array[$i]["presupuesto"]."]";
            	if(($i+1)<$n) print ",";
          	}
          }
        ?>
         
        ]);

        // Opciones de la gráfica
        var options = {'title':'',
                      'is3D':true,
                       'width':600,
                       'height':400};

        // Inicia la gráfica
        var chart = new google.visualization.PieChart(document.getElementById('grafica'));
        chart.draw(data, options);
      }
    </script>
	<style>
	#grafica{
		width:600px;
		margin:0 auto;
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
				<label for="alta"></label>
				<input type="button" name="tabla" value="Tabla" class="btn btn-info mt-5" role="button" id="tabla">
			</div>
			<div class="col-sm-8 text-center">
				<h2>Presupuesto Gráfica</h2>
				<div id="grafica"></div>
			</div>
			<div class="col-sm-2 sidevar"></div>
		</div>
	</div>
	</div>

	<script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>