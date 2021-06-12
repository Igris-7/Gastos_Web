<?php
/**
* 
*/
class Sesion
{
	private $login = false;
	private $usuario;
	
	function __construct()
	{
		session_start();
		$this->verificaLogin();
		
	}

	private function verificaLogin()
	{
		if (isset($_SESSION["usuario"])) {
			$this->usuario = $_SESSION["usuario"];
			$this->login = true;
		} else {
			unset($this->usuario);
			$this->login = false;
		}
		
	}

	public function inicioLogin($usuario)
	{
		if ($usuario) {
			$this->usuario = $_SESSION["usuario"] = $usuario;
			$this->login = true;
		} 
	}

	public function finLogin()
	{
		unset($_SESSION["usuario"]);
		unset($this->usuario);
		$this->login = false;
		session_destroy();
	}

	public function estadoLogin()
	{
		return $this->login;
	}

	public function getUsuario()
	{
		return $this->usuario;
	}
}
?>