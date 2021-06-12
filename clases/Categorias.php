<?php
	/**
	* 
	*/
	class Categorias
	{
		
		function __construct(){}

		/***************************/
		public function leeCategoriasUsuario($id, $inicio=0, $registros=999)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM categorias WHERE usuario=".$id." LIMIT ".$inicio.", ".$registros;
			return $db->querySelect($sql);
		}
		/***************************/
		public function numRegistros($id)
		{
			$db = new dbMySQL();
			$num = 0;
			$sql = "SELECT count(*) as num FROM categorias WHERE usuario=".$id;
			$r = $db->querySelect($sql);
			return $r[0]["num"];
		}
		/***************************/
		public function leeCategoriasTipos()
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM categoriastipos";
			return $db->querySelect($sql);
		}
		/***************************/
		public function crearCategoriasBasicas($id)
		{
			$db = new dbMySQL();
			$sql = "INSERT INTO categorias VALUES(0,".$id.",'gasto','Casa',0,''),";
			$sql .= "(0,".$id.",'gasto','Comida',0,''),";
			$sql .= "(0,".$id.",'gasto','Salud',0,''),";
			$sql .= "(0,".$id.",'gasto','Transporte',0,''),";
			$sql .= "(0,".$id.",'gasto','Diversión',0,''),";
			$sql .= "(0,".$id.",'gasto','Escuela',0,''),";
			$sql .= "(0,".$id.",'gasto','Ahorro',0,''),";
			$sql .= "(0,".$id.",'gasto','Varios',0,''),";
			$sql .= "(0,".$id.",'ingreso','Ingresos',0,''),";
			$sql .= "(0,".$id.",'traspaso','Traspasos',0,''),";
			$sql .= "(0,".$id.",'ingreso','CXC',0,''),";
			$sql .= "(0,".$id.",'gasto','Préstamo',0,''),";
			$sql .= "(0,".$id.",'ingreso','Pago préstamo',0,'')";
			return $db->queryNoSelect($sql);
		}
		public function altaCategoria($id,$idUsuario,$nombreCategoria,$tipo,$presupuesto,$nota)
		{
			$db = new dbMySQL();
			if ($id=="") {
				$sql = "INSERT INTO categorias VALUES(0,";
				$sql .= $idUsuario.",";
				$sql .= "'".$tipo."',";
				$sql .= "'".$nombreCategoria."',";
				$sql .= "".$presupuesto.",";
				$sql .= "'".$nota."')";
			} else {
				$sql = "UPDATE categorias SET ";
				$sql .= "categoria='".$nombreCategoria."', ";
				$sql .= "tipo='".$tipo."', ";
				$sql .= "presupuesto=".$presupuesto.", ";
				$sql .= "nota='".$nota."' ";
				$sql .= "WHERE id=".$id;
			}
			return $db->queryNoSelect($sql);
		}
		/*******************/
		public function leerRegistro($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT * FROM categorias WHERE id=".$id;
			return $db->querySelect($sql);
		}
		/***************************/
		public function borrarCategoria($id)
		{
			$db = new dbMySQL();
			$sql = "DELETE FROM categorias WHERE id=".$id;
			return $db->queryNoSelect($sql);
		}
		/***************************/
		public function categoriaTraspaso($id)
		{
			$db = new dbMySQL();
			$cuenta = "";
			$sql = "SELECT id FROM categorias WHERE usuario=".$id." AND tipo='traspaso'";
			$cuenta = $db->query($sql);
			return $cuenta["id"];
		}
		/***************************/
		public function categoriaCXC($id)
		{
			$db = new dbMySQL();
			$cuenta = "";
			$sql = "SELECT id FROM categorias WHERE usuario=".$id." AND categoria='CXC'";
			$cuenta = $db->query($sql);
			return $cuenta["id"];
		}
		/***************************/
		public function sePuedeBorrar($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT count(*) as num FROM movimientos WHERE categoria=".$id;
			$cuenta = $db->query($sql);
			if ($cuenta["num"]==0) {
				return true;
			} else {
				return false;
			}
		}
		/***************************/
		public function presupuesto($id)
		{
			$db = new dbMySQL();
			$sql = "SELECT sum(presupuesto) as suma FROM categorias WHERE usuario=".$id;
			$data = $db->query($sql);
			if ($data["suma"]==NULL) {
				return 0;
			} else {
				return $data["suma"];
			}
		}
	}
?>