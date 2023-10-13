<?php
require_once './modelo/AdministradorModelo.php';
require_once './modelo/AuntenticacionModelo.php';
require_once './modelo/RankingModelo.php';
require_once './modelo/TableroModelo.php';
require_once './modelo/Factoria.php';

class Controlador{
    static function obtenerJugadores(){
        $jugadores = AdministradorModelo::obtenerJugadores();
        if($jugadores){
            self::enviarRespuestaJSON(200,$jugadores);
        }
    
    }

    static function obtenerJugadorPorID($id){
        if (!is_numeric($id)) {
            self::enviarRespuestaJSON(400, 'ID de persona no válido');
        }

        $jugador= AdministradorModelo::obtenerJugadoresPorID($id);
        if($jugador){
            self::enviarRespuestaJSON(200, $jugador);
        }else{
            self::enviarRespuestaJSON(404, 'Jugador no encontrado');
        }

    }

    static function aniadirJugador($jugador){
        if(AdministradorModelo::registrarJugador($jugador)){
            self::enviarRespuestaJSON(201, 'Jugador creado exitosamente');
        }else{
            self::enviarRespuestaJSON(500, 'Error al agregar jugador');
        }
    }

    static function modificarJugador($jugador){
        if(AdministradorModelo::modificarJugador($jugador)){
            self::enviarRespuestaJSON(200, 'Jugador actualizado exitosamente');
        }else{
            self::enviarRespuestaJSON(404, 'Jugador no encontrado o error en la actualización');
        }
    }

    static function eliminarJugador($id){
        if (!is_numeric($id)) {
            self::enviarRespuestaJSON(400, 'ID del jugador no válido');
        }
        
        if (AdministradorModelo::eliminarJugador($id)) {
            self::enviarRespuestaJSON(200, 'Jugador eliminado exitosamente');
        } else {
            self::enviarRespuestaJSON(404, 'Jugador no encontrado o error en la eliminación');
        }
    }

    static function validarJugador($email,$contrasenia){
        return AuntenticacionModelo::validarJugador($email,$contrasenia);
    }
   
    static function esAdministrador($email,$contrasenia){
       return AdministradorModelo::esAdministrador($email,$contrasenia);
    }

    static function obtenerIDJugador($email,$contrasenia){
        return AuntenticacionModelo::obtenerIDJugador($email,$contrasenia);

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

    static function obtenerRanking(){
        $jugadores = RankingModelo::obtenerRanking();
        if($jugadores){
            self::enviarRespuestaJSON(200,$jugadores);
        }
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
