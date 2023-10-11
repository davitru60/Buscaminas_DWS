<?php
class Jugador{
    private $id;
    private $contrasenia;
    private $email;
    private $esAdministrador;

	public function __construct(){
		
	}

    public function __construct1($id,$email,$contra,$admin){
        $this->id= $id;
		$this->email=$email;
        $this->contrasenia = $contra;
        $this->esAdministrador=$admin;
    }

	public function __construct2($email,$contra,$admin){
		$this->email=$email;
        $this->contrasenia = $contra;
        $this->esAdministrador=$admin;
	}

	public function getId() {
		return $this->id;
	}

	public function getContrasenia() {
		return $this->contrasenia;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getEsAdministrador() {
		return $this->esAdministrador;
	}
}