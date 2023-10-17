<?php

class PartidaModelo
{
    public function __construct()
    {

    }

    static function esPartidaCreada($jugadorID, $partidaID)
    {

        $resultado = false;
        $conexion = Conectar::conectar();
        $consulta = "SELECT COUNT(id_partida) FROM partidas WHERE id_jugador=? AND id_partida=?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("ii", $jugadorID, $partidaID);
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

    static function obtenerEstadoPartida($jugadorID, $partidaID)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT estado_partida FROM partidas WHERE id_jugador = ? and id_partida=?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("ii", $jugadorID, $partidaID);
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


    static function perderPartida($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE partidas SET estado_partida = -1 WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);

        $ejecucionCorrecta = true;


        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->close();
            $ejecucionCorrecta = true;
        } else {
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }

    static function ganarPartida($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE partidas SET estado_partida = 1 WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);

        $ejecucionCorrecta = true;


        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->close();
            $ejecucionCorrecta = true;
        } else {
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }

    static function actualizarTableroJugador($tableroJugador, $jugadorID, $partidaID)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE partidas SET tablero_jugador=? WHERE id_jugador=? AND id_partida=?";
        $stmt = $conexion->prepare($consulta);
        $ejecucionCorrecta = true;

        if ($stmt) {
            $stmt->bind_param("sii", $tableroJugador, $jugadorID, $partidaID);
            $stmt->execute();
            $stmt->close();
            $ejecucionCorrecta = true;
        } else {
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }

    static function obtenerTableroOculto($jugadorID, $partidaID)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT tablero_oculto FROM partidas WHERE id_jugador = ? and id_partida=?";
        $stmt = $conexion->prepare($consulta);
        $tableroOculto = null;

        if ($stmt) {
            $stmt->bind_param("ii", $jugadorID, $partidaID);
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

    static function obtenerTableroJugador($jugadorID, $partidaID)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT tablero_jugador FROM partidas WHERE id_jugador = ? AND id_partida=?";
        $stmt = $conexion->prepare($consulta);
        $tableroJugador = null;

        if ($stmt) {
            $stmt->bind_param("ii", $jugadorID, $partidaID);
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

    static function obtenerPartidasJugador($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT id_partida, estado_partida FROM partidas WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $resultado = $stmt->get_result();

            // Procesar el resultado y almacenarlo en un array
            $partidas = array();
            while ($fila = $resultado->fetch_assoc()) {
                $partidas[] = $fila;
            }

            $stmt->close();
            $conexion->close();

            return $partidas;
        } else {
            $conexion->close();
            return false;
        }
    }

    static function actualizarPartidasGanadas($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE jugadores SET partidas_jugadas = partidas_jugadas + 1, partidas_ganadas = partidas_ganadas + 1 WHERE id_jugador=?";
        $stmt = $conexion->prepare($consulta);
        $ejecucionCorrecta = true;

        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->close(); // Cierra la consulta

            $ejecucionCorrecta = true;
        } else {
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }

    static function actualizarPartidasJugadas($jugadorID)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE jugadores SET partidas_jugadas = partidas_jugadas + 1 WHERE id_jugador=?";
        $stmt = $conexion->prepare($consulta);
        $ejecucionCorrecta = true;

        if ($stmt) {
            $stmt->bind_param("i", $jugadorID);
            $stmt->execute();
            $stmt->close();
            $ejecucionCorrecta = true;

        } else {
            $ejecucionCorrecta = false;
        }

        return $ejecucionCorrecta;
    }
}