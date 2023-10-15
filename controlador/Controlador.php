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
        $tableroOculto=Factoria::crearTableroOculto($tamanio,$numMinas);
        $tableroJugador=Factoria::crearTableroJugador($tamanio);

        if (TableroModelo::insertarTablero($jugadorID, $tableroOculto,$tableroJugador)) {
            self::enviarRespuestaJSON(201, 'Tablero creado y registrado exitosamente');
        } else {
            self::enviarRespuestaJSON(500, 'Error al crear y registrar el tablero');
        }     
    }

    static function obtenerTableros(){

    }

    static function esPartidaCreada($jugadorID){
        return PartidaModelo::esPartidaCreada($jugadorID);
    }

    static function obtenerEstadoPartida($jugadorID){
        return PartidaModelo::ObtenerEstadoPartida($jugadorID);
    }

    static function jugar($jugadorID,$casilla){
        $tableroOculto=self::obtenerTableroOculto($jugadorID);
        $tableroJugador=self::obtenerTableroJugador($jugadorID);
        

        $partida=Factoria::crearPartida($tableroOculto,$tableroJugador);
        $resultado= $partida->destaparCasilla($casilla);
        $mensaje= $resultado['mensaje'];
        $tableroActualizado= $resultado['tableroJugador'];
    }


     static function obtenerTableroOculto($jugadorID){
        $tableroOculto=PartidaModelo::obtenerTableroOculto($jugadorID);

        if($tableroOculto !==null){
            $tableroOcultoArray = explode(' ', $tableroOculto);
            return $tableroOcultoArray;
        }
    }

    private static function obtenerTableroJugador($jugadorID) {
        $tablero = PartidaModelo::obtenerTableroJugador($jugadorID);
        if ($tablero !== null) {
            $tableroArray = explode(' ',$tablero);
            return $tableroArray;
        }
        return null;
    }
    
    static function rendirse($jugadorID) {
        $respuestas = array();
    
        // Obtener el tablero
        $tablero = self::obtenerTableroJugador($jugadorID);
        if ($tablero !== null) {
            $tableroStr = implode('', $tablero); // Convierte el array en una cadena
            
            $tableroStr = str_replace(["\n", "\r"], '', $tableroStr);
            $respuestas['tablero'] = $tableroStr;
            
        }
    
        // Rendirse
        $partidaActual = PartidaModelo::rendirse($jugadorID);
        if ($partidaActual) {
            $respuestas['estado_partida'] = 'Se ha cerrado la partida';
        } else {
            $respuestas['estado_partida'] = 'Algo fue mal';
        }
    
        // Enviar todas las respuestas en un solo JSON
        self::enviarRespuestaJSON(200, $respuestas);
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
