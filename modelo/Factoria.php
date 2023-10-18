<?php
require_once 'Jugador.php';
require_once 'Tablero.php';
require_once 'Partida.php';
class Factoria{
    static function crearJugador($ema,$contra,$admin){
        $jugador = new Jugador();
        $jugador->__construct2($ema,$contra,$admin);
        return $jugador;
    }

    static function crearTableroOculto($tam,$nMinas){
        $tablero= new Tablero();
        $tablero-> __construct2($tam,$nMinas);
        return $tablero;
    }

    static function crearTableroJugador($tam){
        $tablero= new Tablero();
        $tablero-> __construct3($tam);
        return $tablero;
    }

    static function crearPartida($tableroOculto,$tableroJugador){
        return new Partida($tableroOculto,$tableroJugador);
    }
}