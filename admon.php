<?php
require "php/variables.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
/****************
Leemos la sesión
*****************/
$sesion = new Sesion();
$usuario = $sesion->getUsuario();
$data = Usuarios::leeUsuario($usuario);
$id = $data["id"];
/****************
Modo de la página (CRUD o ABC)
S - Consulta (select)
A - Alta (insert)
B - Borrar (delete)
C - Cambiar (update)
*****************/
if (isset($_GET["m"])) {
	$m = $_GET["m"];
} else {
	$m = "S";
}
/**************
Validacion
**************/
if (isset($_POST["nueva"])) {
	$nueva = $_POST["nueva"];
	$verifica = $_POST["verifica"];
	$m="C";
	//validar
	if ($nueva=="") {
		array_push($msg,"1La clave de acceso no puede estar vacía");
	} else if($verifica==""){
		array_push($msg,"1La clave de acceso de verificación no puede estar vacía");
	} else if($nueva!=$verifica){
		array_push($msg,"1Las claves de acceso no coinciden");
	} else {
		$clave = substr(hash_hmac("sha512",$nueva,"mimamamemima"),0,100);
		$r = Usuarios::cambiaClaveAcceso($usuario,$clave);
		array_push($msg,$r);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos | Admon</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="imagenes/fox1.png">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
	<script>
		window.onload = function(){
			<?php if($m=="C"){ ?>
				document.getElementById("regresar").onclick = function(){
					window.open("admon.php","_self");
				}
			<?php } ?>
		}
	</script>
</head>
<body>
	<nav class="navbar navbar-expand-sm bg-dark navbar-dark">
		<a href="inicio.php" class="navbar-brand">Gastos</a>
		<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
			<li class="nav-item">
				<a href="inicio.php" class="nav-link">Resumen</a>
			</li>
			<li class="nav-item">
				<a href="categorias.php" class="nav-link">Categorías</a>
			</li>
			<li class="nav-item">
				<a href="cuentas.php" class="nav-link">Cuentas</a>
			</li>
			<li class="nav-item">
				<a href="movimientos.php" class="nav-link">Movimientos</a>
			</li>
			<li class="nav-item">
				<a href="traspasos.php" class="nav-link">Traspasos</a>
			</li>
			<li class="nav-item">
				<a href="presupuesto.php" class="nav-link">Presupuesto</a>
			</li>
			<li class="nav-item">
				<a href="cxc.php" class="nav-link">CXC</a>
			</li>
			<li class="nav-item active">
				<a href="admon.php" class="nav-link">Admon</a>
			</li>
		</ul>
		<ul class="navbar-nav">
			<li class="nav-item">
				<a href="salir.php" class="nav-link">Salir</a>
			</li>
		</ul>
	</nav>
	<div class="container-fluid text-center">
		<div class="row content">
			<div class="col-sm-2 sidevar"></div>
			<div class="col-sm-8 text-center">
				<?php if($m=="C") { 
					require "php/mensajes.php";
				?>
					<form action="admon.php" method="post">
						<div class="form-group text-left">
							<label for="usuario">Usuario:</label>
							<input type="text" name="usuario" id="usuario" disabled class="form-control" value="<?php print $usuario; ?>">
						</div>
						<div class="form-group text-left">
							<label for="nueva">Nueva clave de acceso:</label>
							<input type="password" name="nueva" id="nueva" class="form-control" placeholder="Escribe la nueva clave de acceso" required>
						</div>
						<div class="form-group text-left">
							<label for="verifica">Verifica la clave de acceso:</label>
							<input type="password" name="verifica" id="verifica" class="form-control" placeholder="Verifica la clave de acceso" required>
						</div>
						<div class="form-group text-left">
							<label for="enviar"></label>
							<input type="submit" name="enviar" id="enviar" class="btn btn-success" value="Enviar claves"/>

							<label for="regresar"></label>
							<input type="button" name="regresar" id="regresar" class="btn btn-info" value="Regresar" role="button"/>
						</div>
					</form>
				<?php
				}
				if($m=="S"){
					print "<table class='table table-striped' width='100%'>";
					print "<tr>";
					print "<th>id</th>";
					print "<th>Usuario</th>";
					print "<th>Cambiar clave</th>";
					print "</tr>";
					print "<tr>";
					print "<td>".$id."</td>";
					print "<td>".$usuario."</td>";
					print "<td><a class='btn btn-info' href='admon.php?m=C&id=".$id."'>Cambiar clave</a></td>";
					print "</tr>";
					print "</table>";
				}
				?>
			</div>
			<div class="col-sm-2 sidevar"></div>
		</div>
	</div>
</body>
</html>