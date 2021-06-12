<?php
	/**
	* 
	*/
	class CXC
	{
		
		function __construct(){}

		/***************************/
		public function leeCXCUsuario($id, $inicio=0, $registros=999)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM cxc WHERE usuario=".$id." LIMIT ".$inicio.", ".$registros;
			return $db->querySelect($sql);
		}
		/***************************/
		public function numRegistros($id)
		{
			$db = new dbMySQL();
			$num = 0;
			$sql = "SELECT count(*) as num FROM cxc WHERE usuario=".$id;
			$r = $db->querySelect($sql);
			return $r[0]["num"];
		}
		/***************************/
		public function altaCXC($id,$idUsuario,$periodo,$estado,$cliente,$monto,$nota)
		{
			$db = new dbMySQL();
			//Alta de una cuenta
			if ($id=="") {
				$sql = "INSERT INTO cxc VALUES(0,";
				$sql .= $idUsuario.",";
				$sql .= "'".$periodo."',";
				$sql .= "'".$estado."',";
				$sql .= "'".$cliente."',";
				$sql .= "".$monto.",";
				$sql .= "'".$nota."')";
			} else {
				/*$sql = "UPDATE cxc SET ";
				$sql .= "cuenta='".$nombre."', ";
				$sql .= "tipo='".$tipo."', ";
				$sql .= "saldo=".$saldo." ";
				$sql .= "WHERE id=".$id;*/
			}
			return $db->queryNoSelect($sql);
		}
		/*******************/
		public function leerRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM cxc WHERE id=".$id;
			return $db->querySelect($sql);
		}
		/***************************/
		public function cancelarCXC($id)
		{
			$db = new dbMySQL();
			$sql = "UPDATE cxc SET estado=2 WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
		/***************************/
		public function pagarCXC($id)
		{
			$db = new dbMySQL();
			$sql = "UPDATE cxc SET estado=1 WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
	}
?>