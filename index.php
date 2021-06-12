<?php
require "php/variables.php";
require "clases/Sesion.php";
require "clases/dbMySQL.php";
require "clases/Usuarios.php";
/****************
Leemos la sesión
*****************/
$sesion = new Sesion();
/*********************
Validación del usuario
**********************/
if (isset($_POST["usuario"])) {
	$usuario = $_POST["usuario"];
	$clave = $_POST["clave"];
	//$clave = substr(hash_hmac("sha512",$clave,"mimamamemima"),0,100);
	//
	if (Usuarios::buscaUsuario($usuario, $clave)) {
		$sesion->inicioLogin($usuario);
		header("location:inicio.php");
		exit;
	} else {
		array_push($msg, "1Clave de acceso o usuario inválidos");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Control de Gastos</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1"/>
	<link rel="shortcut icon" href="imagenes/fox1.png">
	<!-- Bootstrap -->
	<link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    
	 <link href="Estilos/estilosLogin.css" rel="stylesheet" type="text/css"/>
</head>

<body>
	
	<div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: 
	url('https://unsplash.com/photos/mq6GbT4E8e8/download?force=true');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <h3>Iniciar sesión</h3>
			<?php require "php/mensajes.php"; ?>
            <p class="mb-4">Sistema para el control y gestión de gastos e ingresos.</p>
            <form action="#" method="post">
              <div class="form-group first">
                <label for="usuario">Correo</label>
				<input type="text" name="usuario" id="usuario" class="form-control" 
					   required placeholder="correo@upn.pe"/>
              </div>
              <div class="form-group last mb-3">
                <label for="clave">Contraseña</label>
				<input type="password" name="clave" id="clave" class="form-control" 
					   required placeholder="Contraseña"/>
              </div>

			  <input type="submit" name="entrar" id="entrar" class="btn btn-block btn-success" value="Entrar" />

            </form>
          </div>
        </div>
      </div>
    </div>

    
  </div>

	<script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>