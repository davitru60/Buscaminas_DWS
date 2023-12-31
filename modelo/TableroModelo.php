<?php
require_once './bbdd/Conexion.php';
class TableroModelo{

    static function insertarTablero($jugadorID,$tableroOculto,$tableroJugador){
        $conexion = Conectar::conectar();
        $consulta = "INSERT INTO partidas (id_jugador,tablero_oculto,tablero_jugador) VALUES (?,?,?)";
        $stmt = $conexion->prepare($consulta);
        $stmt-> bind_param("iss",$jugadorID,$tableroOculto,$tableroJugador);

        
        $ejecucionCorrecta = true;

        if ($stmt->execute()) {
            $conexion->close();
            $ejecucionCorrecta = true;
        } else {
            $stmt->close();
            $conexion->close();
            $ejecucionCorrecta = false;
        }
        return $ejecucionCorrecta;
    }
}