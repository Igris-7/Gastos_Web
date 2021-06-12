<?php
require "clases/Sesion.php";
$sesion = new Sesion();
$sesion->finLogin();
header("location:index.php");
?>