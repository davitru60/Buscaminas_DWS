<?php
class Jugador{
    private $id;
    private $contrasenia;
    private $email;
    private $partidasJugadas;
    private $partidasGanadas;
    private $esAdministrador;

    public function __construct($id,$contra,$email,$pj,$pg,$admin){
        $this->id= $id;
        $this->contrasenia = $contra;
        $this->email=$email;
        $this->partidasJugadas=$pj;
        $this->partidasGanadas =$pg;
        $this->esAdministrador=$admin;
    }


}