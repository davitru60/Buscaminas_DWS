<?php
require_once './modelo/JugadorModelo.php';
require_once './modelo/TableroModelo.php';
require_once './modelo/PartidaModelo.php';
require_once './modelo/Factoria.php';

class Controlador{
    static function obtenerJugadores(){
        $jugadores = JugadorModelo::obtenerJugadores();
        if($jugadores){
            self::enviarRespuestaJSON(200,$jugadores);
        }
    
    }

    static function obtenerJugadorPorID($id){
        if (!is_numeric($id)) {
            self::enviarRespuestaJSON(400, 'ID de persona no válido');
        }

        $jugador= JugadorModelo::obtenerJugadoresPorID($id);
        if($jugador){
            self::enviarRespuestaJSON(200, $jugador);
        }else{
            self::enviarRespuestaJSON(404, 'Jugador no encontrado');
        }

    }

    static function aniadirJugador($jugador){
        if(JugadorModelo::registrarJugador($jugador)){
            self::enviarRespuestaJSON(201, 'Jugador creado exitosamente');
        }else{
            self::enviarRespuestaJSON(500, 'Error al agregar jugador');
        }
    }

    static function modificarJugador($jugador){
        if(JugadorModelo::modificarJugador($jugador)){
            self::enviarRespuestaJSON(200, 'Jugador actualizado exitosamente');
        }else{
            self::enviarRespuestaJSON(404, 'Jugador no encontrado o error en la actualización');
        }
    }

    static function eliminarJugador($id){
        if (!is_numeric($id)) {
            self::enviarRespuestaJSON(400, 'ID del jugador no válido');
        }
        
        if (JugadorModelo::eliminarJugador($id)) {
            self::enviarRespuestaJSON(200, 'Jugador eliminado exitosamente');
        } else {
            self::enviarRespuestaJSON(404, 'Jugador no encontrado o error en la eliminación');
        }
    }

    static function esAdministrador($email,$contrasenia){
        return JugadorModelo::esAdministrador($email,$contrasenia);
     }

    static function validarJugador($email,$contrasenia){
        return JugadorModelo::validarJugador($email,$contrasenia);
    }
   
   
    static function obtenerIDJugador($email,$contrasenia){
        return JugadorModelo::obtenerIDJugador($email,$contrasenia);

    }

    static function obtenerRanking(){
        $jugadores = JugadorModelo::obtenerRanking();
        if($jugadores){
            self::enviarRespuestaJSON(200,$jugadores);
        }
    }

    static function crearTablero($tamanio,$numMinas,$jugadorID){
        $tablero=Factoria::crearTablero($tamanio,$numMinas);
        $tableroT=Factoria::crearTableroT($tamanio);

        if (TableroModelo::insertarTablero($jugadorID, $tablero,$tableroT)) {
            self::enviarRespuestaJSON(201, 'Tablero creado y registrado exitosamente');
        } else {
            self::enviarRespuestaJSON(500, 'Error al crear y registrar el tablero');
        }
    }

    static function esPartidaCreada($jugadorID){
        return PartidaModelo::esPartidaCreada($jugadorID);
    }

    static function esPartidaAbierta($jugadorID){
        return PartidaModelo::esPartidaAbierta($jugadorID);
    }
    
    private static function enviarRespuestaJSON($codigo, $mensaje){
        header('Content-Type: application/json');
        http_response_code($codigo);
        $mensajeEstado = self::obtenerMensajeEstadoHTTP($codigo);
        $response = array(
            'codigo' => $codigo,
            'estado' => $mensajeEstado,
            'mensaje' => $mensaje
        );
        echo json_encode($response);
    }

    private static function obtenerMensajeEstadoHTTP($codigo){
        $estadosHttp = array(
            200 => 'OK',
            201 => 'Creado',
            400 => 'Petición incorrecta',
            404 => 'No encontrado',
            500 => 'Error interno'
        );
    
        // Verifica si el código está en el array y devuelve el mensaje correspondiente
        if (isset($estadosHttp[$codigo])) {
            return $estadosHttp[$codigo];
        }
    }
}
