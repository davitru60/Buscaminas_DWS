<?php
require_once './modelo/JugadorModelo.php';
require_once 'phpMailer.php';
class ControladorJugador{
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

    static function cambiarContrasenia($jugadorID){
        $nuevaContrasenia=self::generarContrasena();
        EnvioCorreo::enviarCorrero($nuevaContrasenia);
        $exito = JugadorModelo::cambiarContrasenia($jugadorID, $nuevaContrasenia);
    
        if ($exito) {
            self::enviarRespuestaJSON(200, ['nuevaContrasenia' => $nuevaContrasenia]);
        } else {
            self::enviarRespuestaJSON(500 ,'Error interno');
        }
    }

    static function generarContrasena() {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $contrasena = '';
    
        for ($i = 0; $i < 8; $i++) {
            $indice = random_int(0, strlen($caracteres) - 1);
            $contrasena .= $caracteres[$indice];
        }
    
        return $contrasena;
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