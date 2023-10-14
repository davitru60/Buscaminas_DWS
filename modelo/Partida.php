<?php
require_once './modelo/Tablero.php';
class Partida{
    private $tableroJuego;
    private $tableroTapado;
    private $juegoTerminado;

    public function __construct(Tablero $tableroJuego, Tablero $tableroTapado) {
        $this->tableroJuego = $tableroJuego;
        $this->tableroTapado = $tableroTapado;
        $this->juegoTerminado = false;
    }
}