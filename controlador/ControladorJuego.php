<?php
require_once './modelo/TableroModelo.php';
require_once './modelo/PartidaModelo.php';
require_once './modelo/Factoria.php';
class ControladorJuego{
    static function crearTablero($tamanio,$numMinas,$jugadorID){
        $tableroOculto=Factoria::crearTableroOculto($tamanio,$numMinas);
        $tableroJugador=Factoria::crearTableroJugador($tamanio);

        if (TableroModelo::insertarTablero($jugadorID, $tableroOculto,$tableroJugador)) {
            self::enviarRespuestaJSON(201, 'Tablero creado y registrado exitosamente');
        } else {
            self::enviarRespuestaJSON(500, 'Error al crear y registrar el tablero');
        }     
    }

    static function esPartidaCreada($jugadorID,$partidaID){
        return PartidaModelo::esPartidaCreada($jugadorID,$partidaID);
    }

    static function obtenerEstadoPartida($jugadorID,$partidaID){
        return PartidaModelo::ObtenerEstadoPartida($jugadorID,$partidaID);
    }


    static function jugar($jugadorID,$partidaID,$casilla){
        $tableroOculto=self::obtenerTableroOculto($jugadorID,$partidaID);
        $tableroJugador=self::obtenerTableroJugador($jugadorID,$partidaID);
        

        $partida=Factoria::crearPartida($tableroOculto,$tableroJugador);
        $resultado= $partida->destaparCasilla($casilla);
        $mensaje= $resultado['mensaje'];
        $tableroActualizado= $resultado['tableroJugador'];
        $estadoPartida= $resultado['estadoPartida'];

        $mensajeStr =  self::convertirArrayACadena($mensaje);
        $tableroActualizadoStr = self::convertirArrayACadena($tableroActualizado);

        
        if ($estadoPartida == 0) {
            PartidaModelo::actualizarTableroJugador($tableroActualizadoStr, $jugadorID,$partidaID);
        } elseif ($estadoPartida === -1) {
            PartidaModelo::perderPartida($jugadorID);
            PartidaModelo::actualizarTableroJugador($tableroActualizadoStr, $jugadorID,$partidaID);
            PartidaModelo::actualizarPartidasJugadas($jugadorID);
          
        }else if($estadoPartida === 1){
            PartidaModelo::ganarPartida($jugadorID);
            PartidaModelo::actualizarTableroJugador($tableroActualizadoStr, $jugadorID,$partidaID);
            PartidaModelo::actualizarPartidasGanadas($jugadorID);
           
        }

        $respuesta = array(
            'pista' => $mensajeStr,
            'tableroActualizado' => $tableroActualizadoStr,
            'estadoPartida' => $estadoPartida
        );

        self::enviarRespuestaJSON(200, $respuesta);
    }

    static function obtenerPartidasJugador($jugadorID){
        $partidas= PartidaModelo::obtenerPartidasJugador($jugadorID);
        if($partidas){
            self::enviarRespuestaJSON(200, $partidas);
        }else{
            self::enviarRespuestaJSON(404, 'El jugador no tiene ninguna partida'); 
        }

    }

    static function obtenerTableroOculto($jugadorID,$partidaID) {
        $tableroOculto = PartidaModelo::obtenerTableroOculto($jugadorID,$partidaID);
        return self::convertirCadenaAArray($tableroOculto);
    }

    private static function obtenerTableroJugador($jugadorID,$partidaID) {
        $tablero = PartidaModelo::obtenerTableroJugador($jugadorID,$partidaID);
        return self::convertirCadenaAArray($tablero);
    }

    static function rendirse($jugadorID,$partidaID) {
        $respuestas = array();

        // Obtener el tablero
        $tablero = self::obtenerTableroJugador($jugadorID,$partidaID);
        if ($tablero !== null) {
            $tableroStr = self::convertirArrayACadena($tablero);
            $respuestas['tablero'] = $tableroStr;
        }

        // Rendirse
        $partidaActual = PartidaModelo::perderPartida($jugadorID);
        PartidaModelo::actualizarTableroJugador($tableroStr, $jugadorID,$partidaID);
        if ($partidaActual) {
            $respuestas['estado_partida'] = 'Se ha cerrado la partida';
            PartidaModelo::actualizarPartidasJugadas($jugadorID);
        } else {
            $respuestas['estado_partida'] = 'Algo fue mal';
        }

    
        self::enviarRespuestaJSON(200, $respuestas);
    }

    // Función para convertir una cadena en un array
    private static function convertirCadenaAArray($cadena) {
        if ($cadena !== null) {
            return explode(' ', $cadena);
        }
        return null;
    }

    // Función para convertir un array en una cadena
    private static function convertirArrayACadena($array) {
        if ($array !== null) {
            $cadena = implode(' ', $array);
            return str_replace(["\n", "\r"], '', $cadena);
        }
        return null;
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