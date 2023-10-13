<?php
require_once './bbdd/Conexion.php';

class AuntenticacionModelo{
    public function __construct(){

    }

    static function validarJugador($email, $contrasenia) {
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

    static function obtenerIDJugador($email, $contrasenia) {
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
}

