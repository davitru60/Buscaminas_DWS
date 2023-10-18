<?php

class Tablero {  
    private $tablero;

    public function __construct(){

    }
    public function __construct2($tamanio, $numMinas) {
        $this->tablero = $this->inicializarTablero($tamanio, $numMinas);
    }

    public function __construct3($tamanio){
        $this->tablero = array_fill(0, $tamanio, 't');
    }


    private function inicializarTablero($tamanio, $numMinas) {
        $tablero = array_fill(0, $tamanio, 0);
        
        // Coloca minas aleatoriamente
        for ($i = 0; $i < $numMinas; $i++) {
            $posicion = rand(0, $tamanio - 1);
            
            // Verifica si ya hay una mina en esa posición
            while ($tablero[$posicion] === 'X') {
                $posicion = rand(0, $tamanio - 1);
            }
            
            // Coloca una mina ('X')
            $tablero[$posicion] = 'X';
        }
        
        // Calcula los números alrededor de las minas
        for ($i = 0; $i < $tamanio; $i++) {
            if ($tablero[$i] !== 'X') {
                $minasAlrededor = $this->contarMinasAlrededor($tablero, $i, $tamanio);
                $tablero[$i] = $minasAlrededor;
            }
        }
        
        return $tablero;
    }

    private function contarMinasAlrededor($tablero, $posicion, $tamanio) {
        $minas = 0;
        
        // Verifica si hay una mina a la izquierda
        if ($posicion > 0 && $tablero[$posicion - 1] === 'X') {
            $minas++;
        }
        
        // Verifica si hay una mina a la derecha
        if ($posicion < $tamanio - 1 && $tablero[$posicion + 1] === 'X') {
            $minas++;
        }
        
        return $minas;
    }

    public function __toString() {
        return implode(' ', $this->tablero) . PHP_EOL;
    }
}
