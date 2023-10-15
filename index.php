<?php
// Creación de las rutas
require_once './controlador/Controlador.php';
require_once './modelo/JugadorModelo.php';
require_once './modelo/Factoria.php';

manejadorSolicitudes();

function manejadorSolicitudes()
{

    $rutas = $_SERVER['REQUEST_URI'];
    $args = explode('/', $rutas);
    unset($args[0]);

    $datosRecibidos = file_get_contents('php://input');
    $datosDecodificados = json_decode($datosRecibidos, true);

    if ($datosDecodificados) {
        $email = $datosDecodificados['email'];
        $contrasenia = $datosDecodificados['contrasenia'];
        $usuario = Controlador::validarJugador($email, $contrasenia);

        if ($usuario) {
            switch ($args[1]) {
                case 'admin':
                    $esAdministrador = Controlador::esAdministrador($email, $contrasenia);
                    if ($esAdministrador) {
                        peticionesAdministrador($args, $datosDecodificados);
                    } else {
                        enviarRespuesta(403, 'No tienes permisos de administrador');
                    }
                    break;

                case 'crearPartida':
                    peticionCrearPartida($args, $datosDecodificados);
                    break;


                case 'jugar':
                    peticionesJugar($args, $datosDecodificados);
                    break;

                case 'rendicion':
                    peticionRendicion($datosDecodificados);
                    break;


                case 'ranking':
                    peticionRanking();
                    break;

                default:
                    enviarRespuesta(400, 'Ruta incorrecta');
                    break;

            }
        } else {
            enviarRespuesta(401, 'Credenciales de usuario incorrectas');
        }
    } else {
        enviarRespuesta(400, 'Solicitud incorrecta o datos inválidos');
    }
}

function peticionesAdministrador($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    if ($metodoPeticion === 'GET') {
        metodoGetAdmin($args);
    } elseif ($metodoPeticion === 'POST') {
        metodoPostAdmin($args, $datosDecodificados);
    } elseif ($metodoPeticion === 'DELETE') {
        metodoDeleteAdmin($args);
    } else {
        enviarRespuesta(405, 'Método no permitido');
    }
}

function metodoGetAdmin($args)
{
    if (count($args) == 1) {
        Controlador::obtenerJugadores();
    } elseif (count($args) == 3 && $args[2] === 'jugador' && is_numeric($args[3])) {
        $jugadorID = (int) $args[3];
        Controlador::obtenerJugadorPorID($jugadorID);
    } else {
        enviarRespuesta(400, 'Solicitud GET incorrecta');
    }
}

function metodoPostAdmin($args, $datosDecodificados)
{
    if (count($args) == 2 && $args[2] == 'agregarJugador') {
        $datosJugador = $datosDecodificados['jugador'][0];

        if (
            isset($datosJugador['email']) && isset($datosJugador['contrasenia']) &&
            filter_var($datosJugador['email'], FILTER_VALIDATE_EMAIL) &&
            !empty($datosJugador['contrasenia'])
        ) {
            $esAdmin = isset($datosJugador['es_administrador']) ? $datosJugador['es_administrador'] : false;
            $jugador = Factoria::crearJugador($datosJugador['email'], $datosJugador['contrasenia'], $esAdmin);
            Controlador::aniadirJugador($jugador);
        } else {
            enviarRespuesta(400, 'Datos de jugador incorrectos para POST');
        }
    } else {
        enviarRespuesta(400, 'Solicitud POST incorrecta');
    }
}

function metodoDeleteAdmin($args)
{
    if (count($args) == 3 && $args[2] === 'jugador' && is_numeric($args[3])) {
        $jugadorID = (int) $args[3];
        Controlador::eliminarJugador($jugadorID);
    } else {
        enviarRespuesta(400, 'Solicitud DELETE incorrecta');
    }
}

function peticionCrearPartida($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $jugadorID = Controlador::obtenerIDJugador($email, $contrasenia);


    if ($metodoPeticion === 'POST') {
        if (isset($args[2]) && isset($args[3])) {
            $tamanio = (int) $args[2];
            $nMinas = (int) $args[3];
            Controlador::crearTablero($tamanio, $nMinas, $jugadorID);
        } else {
            Controlador::crearTablero(Constantes::$TAM_TABLERO, Constantes::$MINAS, $jugadorID);
        }
    }else{
        enviarRespuesta(400, 'Solicitud POST incorrecta');
    }
}

function peticionesJugar($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $casilla= $datosDecodificados['casilla'];
    $jugadorID = Controlador::obtenerIDJugador($email, $contrasenia);

    $esPartidaCreada = Controlador::esPartidaCreada($jugadorID);
    $partidaAbierta = Controlador::obtenerEstadoPartida($jugadorID);

    if ($esPartidaCreada) {
        if ($partidaAbierta === 0) {
            Controlador::jugar($jugadorID,$casilla);


        } elseif ($partidaAbierta === -1) {
            enviarRespuesta(200, 'La partida está perdida.');
        } elseif ($partidaAbierta === 1) {
            enviarRespuesta(200, '¡Has ganado la partida!');
        }
    } else {
        enviarRespuesta(404, 'No tienes partidas creadas');
       
    }
}

function peticionRendicion($datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $jugadorID = Controlador::obtenerIDJugador($email, $contrasenia);

    if ($metodoPeticion === 'PUT') {
        $esPartidaCreada = Controlador::esPartidaCreada($jugadorID);
        if($esPartidaCreada){
            Controlador::rendirse($jugadorID);
        }else{
            enviarRespuesta(404, 'No tienes partidas creadas');
        }
    } else {
        enviarRespuesta(405, 'Método no permitido');
    }

}

function peticionRanking()
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    if ($metodoPeticion == 'GET') {
        Controlador::obtenerRanking();
    }
}

function enviarRespuesta($codigo, $mensaje)
{
    header('Content-Type: application/json');
    http_response_code($codigo);
    $response = array(
        'codigo' => $codigo,
        'mensaje' => $mensaje
    );
    echo json_encode($response);
}