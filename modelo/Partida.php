<?php
require_once './modelo/Tablero.php';
class Partida {
    private $tableroOculto;
    private $tableroJugador;
    private $estadoPartida;

    public function __construct($tableroOculto, $tableroJugador) {
        $this->tableroOculto = $tableroOculto;
        $this->tableroJugador = $tableroJugador;
    }

    public function destaparCasilla($casilla) {
        $mensaje = array();
        $tableroActualizado = $this->tableroJugador;
        $estadoPartida = 0; // Inicialmente, no se ha ganado ni perdido

        if ($tableroActualizado[$casilla] !== 't') {
            array_push($mensaje, 'La casilla ya ha sido destapada');
        } elseif ($this->tableroOculto[$casilla] === 'X') {
            array_push($mensaje, '¡Has perdido! Había una mina en esta casilla');
            $tableroActualizado = $this->tableroOculto;
            $estadoPartida = -1; // El jugador ha perdido
        } else {
            $this->revelarCasillas($casilla, $tableroActualizado);

            if ($this->haGanado()) {
                $estadoPartida = 1; // El jugador ha ganado
                array_push($mensaje, '¡Has ganado! Todas las minas han sido marcadas');
            }
        }

        return array(
            'mensaje' => $mensaje,
            'tableroJugador' => $tableroActualizado,
            'estadoPartida' => $estadoPartida
        );
    }


    private function haGanado() {
        return !in_array('t', $this->tableroJugador);
    }

    private function revelarCasillas($casilla, &$tableroActualizado) {
        $totalCasillas = count($this->tableroOculto);

        if ($tableroActualizado[$casilla] !== 't') {
            return;
        }

        $minasCercanas = $this->contarMinasCercanas($casilla);
        $tableroActualizado[$casilla] = $minasCercanas;

        if ($minasCercanas === 0) {
            for ($i = -1; $i <= 1; $i++) {
                $pos = $casilla + $i;

                if ($pos >= 0 && $pos < $totalCasillas) {
                    $this->revelarCasillas($pos, $tableroActualizado);
                }
            }
        }
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