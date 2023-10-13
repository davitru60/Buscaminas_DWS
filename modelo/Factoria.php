<?php
require_once 'Jugador.php';
require_once 'Tablero.php';
class Factoria{
    static function crearJugador($ema,$contra,$admin){
        $jugador = new Jugador();
        $jugador->__construct2($ema,$contra,$admin);
        return $jugador;
    }

    static function crearTablero($tam,$nMinas){
        $tablero= new Tablero();
        $tablero-> __construct2($tam,$nMinas);
        return $tablero;
    }

    static function crearTableroT($tam){
        $tablero= new Tablero();
        $tablero-> __construct3($tam);
        return $tablero;
    }
    


}