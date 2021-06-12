<?php
	/**
	* 
	*/
	class Traspasos
	{
		
		function __construct(){}

		/***************************/
		public function leeTraspasosUsuario($id, $inicio=0, $registros=999)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM traspasos WHERE usuario=".$id." LIMIT ".$inicio.", ".$registros;
			return $db->querySelect($sql);
		}
		/***************************/
		public function numRegistros($id)
		{
			$db = new dbMySQL();
			$num = 0;
			$sql = "SELECT count(*) as num FROM traspasos WHERE usuario=".$id;
			$r = $db->querySelect($sql);

			if(!$r){
				echo "No hay registros";
			}else{
				return $r[0]["num"];
			}
		}
		/***************************/
		public function altaRegistro($id,$idUsuario,$origen,$destino,$monto,$fecha,$nota)
		{
			$db = new dbMySQL();
			//Alta de una cuenta
			if ($id=="") {
				$sql = "INSERT INTO traspasos VALUES(0,";
				$sql .= $idUsuario.",";
				$sql .= $origen.",";
				$sql .= "".$destino.",";
				$sql .= "".$monto.",";
				$sql .= "'".$fecha."',";
				$sql .= "'".$nota."')";
			} else {
				/*$sql = "UPDATE cuentas SET ";
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
			$sql = "SELECT * FROM cuentas WHERE id=".$id;
			return $db->querySelect($sql);
		}
		/***************************/
		public function borrarRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "DELETE FROM cuentas WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
		/***************************/
	}
?>