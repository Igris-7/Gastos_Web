<?php
/**
 * 
 */
class dbMySQL {
	private $host = "localhost";
	private $usuario = "root";
	private $clave = ""; //root es la clave en MAMP
	private $db = "gastos";
	private $puerto = "3306"; //MAMP en windows
	private $conn;

	 function __construct(){
		$this->conn = mysqli_connect(
			$this->host,
			$this->usuario,
			$this->clave,
			$this->db,
			$this->puerto //MAMP en Windows
		);
		if (mysqli_connect_error()) {
			printf("Error en la conexión: %d",mysqli_connect_error());
		} else {
			//print "Conexión exitosa";
		}
	}

	public function query($q)
	{
		$data = array();
		if($q!=""){
			if ($r=mysqli_query($this->conn,$q)) {
				$data = mysqli_fetch_assoc($r);
			}
		}
		return $data;
	}

	public function querySelect($q)
	{
		$data = array();
		if($q!=""){
			if ($r=mysqli_query($this->conn,$q)) {
				while ($row = mysqli_fetch_assoc($r)) {
					array_push($data, $row);
				}
				
			}
		}
		return $data;
	}

	public function queryNoSelect($q)
	{
		//U,I,D regresa un valor booleano
		$r=false;
		if($q!=""){
			$r=mysqli_query($this->conn,$q);
		}
		return $r;
	}

	public function close(){
		mysqli_close($this->conn);
	}
}
?>