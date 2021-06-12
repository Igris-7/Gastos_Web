<?php
	/**
	* 
	*/
	class Cuentas
	{
		
		function __construct(){}

		/***************************/
		public function leeCuentasUsuario($id, $inicio=0, $registros=999)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM cuentas WHERE usuario=".$id." LIMIT ".$inicio.", ".$registros;
			return $db->querySelect($sql);
		}
		/***************************/
		public function numRegistros($id)
		{
			$db = new dbMySQL();
			$num = 0;
			$sql = "SELECT count(*) as num FROM cuentas WHERE usuario=".$id;
			$r = $db->querySelect($sql);
			return $r[0]["num"];
		}
		/***************************/
		public function leeCuentasTipos()
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM cuentastipos";
			return $db->querySelect($sql);
		}
		/***************************/
		public function altaCuenta($id,$idUsuario,$periodo,$nombre,$tipo,$saldo)
		{
			$db = new dbMySQL();
			//Alta de una cuenta
			if ($id=="") {
				$cargos = 0;
				$abonos = 0;
				$sql = "INSERT INTO cuentas VALUES(0,";
				$sql .= $idUsuario.",";
				$sql .= "'".$periodo."',";
				$sql .= "'".$nombre."',";
				$sql .= "'".$tipo."',";
				$sql .= "".$saldo.",";
				$sql .= "".$cargos.", ";
				$sql .= "".$abonos.")";
			} else {
				$sql = "UPDATE cuentas SET ";
				$sql .= "cuenta='".$nombre."', ";
				$sql .= "tipo='".$tipo."', ";
				$sql .= "saldo=".$saldo." ";
				$sql .= "WHERE id=".$id;
			}
			return $db->queryNoSelect($sql);
		}
		/*******************/
		public function leerRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM cuentas WHERE id=".$id;
			return $db->querySelect($sql);
		}
		/***************************/
		public function borrarCuenta($id)
		{
			$db = new dbMySQL();
			$sql = "DELETE FROM cuentas WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
		/***************************/
		public function actualizaSaldo($cuenta, $tipo, $monto)
		{
			$db = new dbMySQL();
			$sql = "SELECT saldo, cargos, abonos FROM cuentas WHERE id=".$cuenta;
			$data = $db->query($sql);
			$saldoInicial = $data["saldo"];
			$cargos = $data["cargos"];
			$abonos = $data["abonos"];
			//
			if ($tipo=="gasto") {
				$cargos += $monto;
				$sql = "UPDATE cuentas SET cargos=".$cargos." WHERE id=".$cuenta;
			} else {
				$abonos += $monto;
				$sql = "UPDATE cuentas SET abonos=".$abonos." WHERE id=".$cuenta;
			}
			return $db->queryNoSelect($sql);
		}
		/***************************/
		public function sePuedeBorrar($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT count(*) as num FROM movimientos WHERE cuenta=".$id;
			$cuenta = $db->query($sql);
			return ($cuenta["num"]==0);
		}
	}
?>