<?php
require_once 'Jugador.php';
class Factoria{
    static function crearJugador($ema,$contra,$admin){
        $jugador = new Jugador();
        $jugador->__construct2($ema,$contra,$admin);
        return $jugador;
    }
}