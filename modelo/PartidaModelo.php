<?php

class PartidaModelo
{
    public function __construct()
    {

    }

    static function esPartidaCreada($jugadorID)
    {

        $resultado = false;
        $conexion = Conectar::conectar();
        $consulta = "SELECT COUNT(id_partida) FROM partidas WHERE id_jugador=?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();

            $stmt->bind_result($numFilas);
            $stmt->fetch();

            $stmt->close();
            $conexion->close();

            if ($numFilas > 0) {
                $resultado = true;
            } else {
                $resultado = false;
            }
        } else {
            $conexion->close();
        }

        return $resultado;
    }

    static function obtenerEstadoPartida($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT estado_partida FROM partidas WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->bind_result($estadoPartida);

            if ($stmt->fetch()) {
                return $estadoPartida;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

 
    static function rendirse($jugadorID){
        $conexion = Conectar::conectar();
        $consulta = "UPDATE partidas SET estado_partida = -1 WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);

        $ejecucionCorrecta = true;

    
        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->close();
            $ejecucionCorrecta = true;
        }else{
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }
    
    static function obtenerTableroOculto($jugadorID){
        $conexion = Conectar::conectar();
        $consulta = "SELECT tablero_oculto FROM partidas WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);
        $tableroOculto = null;
    
        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
    
            $stmt->bind_result($tableroOculto);
    
            if ($stmt->fetch()) {
                $stmt->close();
                return $tableroOculto;
            } else {
                $stmt->close();
                return null;
            }
        } else {
            return null;
        }
    }

    static function obtenerTableroJugador($jugadorID) {
        $conexion = Conectar::conectar();
        $consulta = "SELECT tablero_jugador FROM partidas WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);
        $tableroJugador = null;
    
        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
    
            $stmt->bind_result($tableroJugador);
    
            if ($stmt->fetch()) {
                $stmt->close();
                return $tableroJugador;
            } else {
                $stmt->close();
                return null;
            }
        } else {
            return null;
        }
    }
    
}