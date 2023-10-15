<?php
require_once './modelo/Tablero.php';
class Partida {
    private $tableroOculto;
    private $tableroJugador;

    public function __construct($tableroOculto, $tableroJugador) {
        $this->tableroOculto = $tableroOculto;
        $this->tableroJugador = $tableroJugador;
    }

    public function destaparCasilla($casilla) {
        $mensaje = array();
        $tableroActualizado = $this->tableroJugador;

    

        if ($tableroActualizado[$casilla] !== 'X') {
            array_push($mensaje, 'La casilla ya ha sido destapada');
        }

        if ($this->tableroOculto[$casilla] === 'X') {
            array_push($mensaje, '¡Has perdido! Había una mina en esta casilla');
        }

        $minasCercanas = $this->contarMinasCercanas($casilla);
        if ($this->tableroOculto[$casilla] === 'X') {
            $tableroActualizado[$casilla] = 'X';
        } else {
            $tableroActualizado[$casilla] = $minasCercanas;
        }
        
        return array(
            'mensaje' => $mensaje,
            'tableroJugador' => $tableroActualizado
        );
    }

    private function contarMinasCercanas($casilla) {
        $minasCercanas = 0;
        $totalCasillas = count($this->tableroOculto);

        for ($i = -1; $i <= 1; $i++) {
            $pos = $casilla + $i;

            if ($pos >= 0 && $pos < $totalCasillas && $this->tableroOculto[$pos] === 'X') {
                $minasCercanas++;
            }
        }

        return $minasCercanas;
    }

}
