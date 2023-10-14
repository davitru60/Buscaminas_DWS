<?php

class PartidaModelo{
    public function __construct()
    {

    }


    static function esPartidaCreada($jugadorID){

        $resultado = false;
        $conexion = Conectar::conectar();
        $consulta = "SELECT COUNT(id_partida) FROM partidas WHERE id_jugador=?";
        $stmt = $conexion->prepare($consulta);

        if($stmt){
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

        static function esPartidaAbierta($jugadorID){
            $conexion = Conectar::conectar();
            $consulta = "SELECT estado_partida FROM partidas WHERE id_jugador = ?";
            $stmt = $conexion->prepare($consulta);
        
            if($stmt){
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
}