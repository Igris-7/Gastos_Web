<?php
	/**
	* 
	*/
	class Movimientos
	{
		
		function __construct(){}

		/***************************/
		public function leeMovimientosUsuario($id)//, $inicio, $registros
		{
			$db = new dbMySQL();
			$sql = "SELECT m.id as id, m.usuario as usuario, m.tipo as tipo, ";
			$sql .= "m.categoria categoria, m.cuenta as cuenta, m.monto as monto, ";
			$sql .= "m.fecha as fecha, m.nota as nota, ";
			$sql .= "ca.categoria as categoriaNombre, cu.cuenta as cuentaNombre ";
			$sql .= "FROM movimientos as m, cuentas as cu, categorias as ca ";
			$sql .= "WHERE m.categoria=ca.id AND ";
			$sql .= "m.cuenta=cu.id AND ";
			$sql .= "m.usuario=".$id; //LIMIT ".$inicio.", ".$registros;
			return $db->querySelect($sql);
		}
		/***************************/
		public function numRegistros($id)
		{
			$db = new dbMySQL();
			$num = 0;
			$sql = "SELECT count(*) as num FROM movimientos WHERE usuario=".$id;
			$r = $db->querySelect($sql);
			return $r[0]["num"];
		}
		/***************************/
		public function altaMovimiento($id,$idUsuario,$periodo,$tipo, $categoria, $cuenta, $monto, $fecha,$nota)
		{
			$db = new dbMySQL();
			//Alta de una cuenta
			if ($id=="") {
				$sql = "INSERT INTO movimientos VALUES(0,";
				$sql .= $idUsuario.",";
				$sql .= "'".$periodo."',";
				$sql .= "'".$tipo."',";
				$sql .= "".$categoria.",";
				$sql .= "".$cuenta.", ";
				$sql .= "".$monto.", ";
				$sql .= "'".$fecha."', ";
				$sql .= "'".$nota."')";
			} else {
				/*$sql = "UPDATE cuentas SET ";
				$sql .= "cuenta='".$nombre."', ";
				$sql .= "tipo='".$tipo."', ";
				$sql .= "saldo=".$saldo." ";
				$sql .= "WHERE id=".$id;*/
			}
			//Regresamos un valor booleano true- correcto, false incorrecto
			return $db->queryNoSelect($sql);
		}
		/*******************/
		public function leerRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT m.id as id, m.usuario as usuario, m.tipo as tipo, ";
			$sql .= "m.categoria categoria, m.cuenta as cuenta, m.monto as monto, ";
			$sql .= "m.fecha as fecha, m.nota as nota, ";
			$sql .= "ca.categoria as categoriaNombre, cu.cuenta as cuentaNombre ";
			$sql .= "FROM movimientos as m, cuentas as cu, categorias as ca ";
			$sql .= "WHERE m.categoria=ca.id AND ";
			$sql .= "m.cuenta=cu.id AND ";
			$sql .= "m.id=".$id;
			return $db->querySelect($sql);
		}
		/***************************/
		public function borrarRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "DELETE FROM movimientos WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
		/***************************/
		public function montoCategoria($id, $usuario)
		{
			$db = new dbMySQL();
			$sql = "SELECT sum(monto) as suma FROM movimientos WHERE categoria=".$id." AND usuario=".$usuario;
			$data = $db->querySelect($sql);
			return ($data[0]["suma"]==NULL)?0:$data[0]["suma"];
		}
		/***************************/
		public function gastos($id, $traspaso)
		{
			$db = new dbMySQL();
			$sql = "SELECT sum(monto) as suma FROM movimientos ";
			$sql .= "WHERE usuario=".$id." AND categoria!=".$traspaso;
			$sql .= " AND tipo='gasto'";
			$data = $db->querySelect($sql);
			return ($data[0]["suma"]==NULL)?0:$data[0]["suma"];
		}
		/***************************/
	}
?>