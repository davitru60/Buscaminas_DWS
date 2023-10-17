<?php
require_once './bbdd/Conexion.php';
require_once 'Jugador.php';
class JugadorModelo
{

    public function __construct()
    {

    }

    static function obtenerJugadores()
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT * FROM jugadores";
        $stmt = $conexion->prepare($consulta);
        $jugadores = [];

        if ($stmt) {
            $stmt->execute();
            $resultados = $stmt->get_result();

            while ($fila = $resultados->fetch_assoc()) {
                $jugadores[] = $fila;
            }

            $resultados->free_result();
            $stmt->close();
        }

        return $jugadores;
    }

    static function obtenerJugadoresPorID($id)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT * FROM jugadores WHERE id_jugador=? ";
        $stmt = $conexion->prepare($consulta);
        $jugador = [];
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resultados = $stmt->get_result();

        if ($resultados->num_rows != 0) {
            while ($fila = $resultados->fetch_assoc()) {
                $jugador[] = $fila;
            }
            $resultados->free_result();
            $stmt->close();
        }

        return $jugador;
    }

    static function registrarJugador(Jugador $jugador)
    {
        $conexion = Conectar::conectar();
        $consulta = "INSERT INTO jugadores (contrasenia,email,es_administrador) VALUES (?,?,?)";
        $stmt = $conexion->prepare($consulta);
        $contrasenia = $jugador->getContrasenia();
        $email = $jugador->getEmail();
        $admin = $jugador->getEsAdministrador();
        $stmt->bind_param("ssi", $contrasenia, $email, $admin);

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

    static function modificarJugador(Jugador $jugador)
    {
        $conexion = Conectar::conectar();
        $consulta = "UPDATE jugadores SET email=?,contrasenia=? WHERE id_jugador=?";
        $stmt = $conexion->prepare($consulta);
        $jugador = new Jugador();

        $email = $jugador->getEmail();
        $contrasenia = $jugador->getContrasenia();
        $id = $jugador->getId();

        $stmt->bind_param("ssi", $email, $contrasenia, $id);

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

    static function eliminarJugador($id)
    {
        $conexion = Conectar::conectar();

        // Verificar si el jugador existe antes de intentar eliminarlo
        $consulta_existencia = "SELECT id_jugador FROM jugadores WHERE id_jugador = ?";
        $stmt_existencia = $conexion->prepare($consulta_existencia);
        $stmt_existencia->bind_param("i", $id);
        $stmt_existencia->execute();
        $stmt_existencia->store_result();

        if ($stmt_existencia->num_rows === 0) {
            // El jugador con el ID proporcionado no existe, no se realiza la eliminación
            $stmt_existencia->close();
            $conexion->close();
            return false;
        }

        // Continuar con la eliminación si el jugador existe
        $stmt_existencia->close();

        $consulta = "DELETE FROM jugadores WHERE id_jugador = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("i", $id);

        $ejecucionCorrecta = $stmt->execute();

        $stmt->close();
        $conexion->close();

        return $ejecucionCorrecta;
    }

    static function esAdministrador($email, $contrasenia)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT es_administrador FROM jugadores WHERE email = ? AND contrasenia = ?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            $stmt->bind_param("ss", $email, $contrasenia);
            $stmt->execute();
            $stmt->bind_result($esAdmin);

            if ($stmt->fetch()) {
                if ($esAdmin == 1) {
                    $stmt->close();
                    $conexion->close();
                    return true;
                }
            }

            $stmt->close();
            $conexion->close();
        }

        return false;
    }


    static function validarJugador($email, $contrasenia)
    {
        // Inicializa una variable para almacenar el resultado
        $resultado = false;

        $conexion = Conectar::conectar();
        $consulta = "SELECT COUNT(id_jugador) FROM jugadores WHERE email=? AND contrasenia=?";
        $stmt = $conexion->prepare($consulta);

        if ($stmt) {
            // Vincula los parámetros y ejecuta la consulta
            $stmt->bind_param("ss", $email, $contrasenia);
            $stmt->execute();

            // Obtiene el resultado
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

    static function obtenerIDJugador($email, $contrasenia)
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT id_jugador FROM jugadores WHERE email = ? AND contrasenia = ?";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param('ss', $email, $contrasenia);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $stmt->bind_result($id_jugador);

        if ($stmt->fetch()) {
            $stmt->close();
            return $id_jugador;
        } else {
            $stmt->close();
            return null;
        }
    }

    static function obtenerRanking()
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT id_jugador, email,partidas_ganadas FROM jugadores ORDER BY partidas_ganadas DESC";
        $stmt = $conexion->prepare($consulta);
        $jugadores=[];

        if ($stmt) {
            $stmt->execute();
            $resultados = $stmt->get_result();

            while ($fila = $resultados->fetch_assoc()) {
                $jugadores[] = $fila;
            }

            $resultados->free_result();
            $stmt->close();
        }
        
        return $jugadores;
    }

    static function cambiarContrasenia($jugadorID,$contrasenia){
        $conexion=Conectar::conectar();
        $consulta="UPDATE jugadores SET contrasenia=? WHERE id_jugador=?";
        $stmt=$conexion->prepare($consulta);
        $stmt->bind_param("si",$contrasenia,$jugadorID);
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
