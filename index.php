<?php
// Creación de las rutas
require_once './controlador/Controlador.php';
require_once './controlador/ControladorJugador.php';
require_once './controlador/ControladorJuego.php';
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
        $contraseniaHash = md5($contrasenia);
        $usuario = ControladorJugador::validarJugador($email, $contraseniaHash);

        if ($usuario) {
            switch ($args[1]) {
                case 'admin':
                    $esAdministrador = ControladorJugador::esAdministrador($email, $contraseniaHash);
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

                case 'rendirse':
                    peticionRendicion($args, $datosDecodificados);
                    break;


                case 'ranking':
                    peticionRanking();
                    break;

                case 'cambiarContrasenia':
                    peticionesContrasenia($datosDecodificados);
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

    } elseif ($metodoPeticion === 'PUT') {
        metodoPutAdmin($args, $datosDecodificados);
    } elseif ($metodoPeticion === 'DELETE') {
        metodoDeleteAdmin($args);
    } else {
        enviarRespuesta(405, 'Método no permitido');
    }
}

function metodoGetAdmin($args)
{
    if (count($args) == 1) {
        ControladorJugador::obtenerJugadores();
    } elseif (count($args) == 3 && $args[2] === 'jugador' && is_numeric($args[3])) {
        $jugadorID = (int) $args[3];
        ControladorJugador::obtenerJugadorPorID($jugadorID);
    } else {
        enviarRespuesta(400, 'Solicitud GET incorrecta');
    }
}

function metodoPostAdmin($args, $datosDecodificados)
{
    if (count($args) == 2 && $args[2] == 'agregarJugador') {
        $datosJugador = $datosDecodificados['jugador'][0];
        $email = $datosJugador['email'];
        $contrasenia = $datosJugador['contrasenia'];
        $contraseniaHash = md5($contrasenia);
        $administrador = $datosJugador['es_administrador'];

        if (
            isset($email) && isset($contrasenia) && filter_var($email, FILTER_VALIDATE_EMAIL) &&
            !empty($contrasenia)
        ) {
            $esAdmin = isset($administrador) ? $administrador : false;
            $jugador = Factoria::crearJugador($email, $contraseniaHash, $esAdmin);
            ControladorJugador::aniadirJugador($jugador);
        } else {
            enviarRespuesta(400, 'Datos de jugador incorrectos para POST');
        }
    } else {
        enviarRespuesta(400, 'Solicitud POST incorrecta');
    }
}

function metodoPutAdmin($args, $datosDecodificados)
{

    if (count($args) == 2 && $args[2] == 'modificarJugador') {
        $datosJugador = $datosDecodificados['jugador'][0];
        $emailActual = $datosJugador['emailActual'];
        $contraseniaActual = $datosJugador['contraseniaActual'];
        $contraseniaActualHash = md5($contraseniaActual);
        $emailActualizado = $datosJugador['emailActualizado'];
        $contraseniaActualizada = $datosJugador['contraseniaActualizada'];
        $contraseniaActualizadaHash= md5($contraseniaActualizada);

        if (
            isset($emailActual) && isset($contraseniaActualHash) && filter_var($emailActual, FILTER_VALIDATE_EMAIL) &&
            !empty($contraseniaActualHash)
        ) {
            $jugadorID = ControladorJugador::obtenerIDJugador($emailActual, $contraseniaActualHash);
            ControladorJugador::modificarJugador($emailActualizado, $contraseniaActualizadaHash, $jugadorID);
        } else {
            enviarRespuesta(400, 'Datos de jugador incorrectos para PUT');
        }

    } else {
        enviarRespuesta(400, 'Solicitud PUT incorrecta');
    }
}

function metodoDeleteAdmin($args)
{
    if (count($args) == 3 && $args[2] === 'jugador' && is_numeric($args[3])) {
        $jugadorID = (int) $args[3];
        ControladorJugador::eliminarJugador($jugadorID);
    } else {
        enviarRespuesta(400, 'Solicitud DELETE incorrecta');
    }
}

function peticionCrearPartida($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $contraseniaHash = md5($contrasenia);
    $jugadorID = ControladorJugador::obtenerIDJugador($email, $contraseniaHash);


    if ($metodoPeticion === 'POST') {
        if (isset($args[2]) && isset($args[3])) {
            $tamanio = (int) $args[2];
            $nMinas = (int) $args[3];
            ControladorJuego::crearTablero($tamanio, $nMinas, $jugadorID);
        } else {
            ControladorJuego::crearTablero(Constantes::$TAM_TABLERO, Constantes::$MINAS, $jugadorID);
        }
    } else {
        enviarRespuesta(400, 'Solicitud incorrecta');
    }
}

function peticionesJugar($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $contraseniaHash = md5($contrasenia);
    $casilla = $datosDecodificados['casilla'];
    $jugadorID = ControladorJugador::obtenerIDJugador($email, $contraseniaHash);


    if ($metodoPeticion === 'GET') {
        if (count($args) == 1) {
            ControladorJuego::obtenerPartidasJugador($jugadorID);
        } else {
            enviarRespuesta(400, 'Solicitud GET incorrecta');
        }
    } elseif ($metodoPeticion === 'POST') {
        if (isset($args[2])) {
            $partidaID = $args[2];
            $esPartidaCreada = ControladorJuego::esPartidaCreada($jugadorID, $partidaID);
            $partidaAbierta = ControladorJuego::obtenerEstadoPartida($jugadorID, $partidaID);

            if ($esPartidaCreada) {
                if ($partidaAbierta === 0) {
                    ControladorJuego::jugar($jugadorID, $partidaID, $casilla);
                } elseif ($partidaAbierta === -1) {
                    enviarRespuesta(200, 'Partida finalizada y perdida.');
                } elseif ($partidaAbierta === 1) {
                    enviarRespuesta(200, 'Partida finalizada y ganada');
                }
            } else {
                enviarRespuesta(404, 'No tienes partidas creadas');

            }
        } else {
            enviarRespuesta(400, 'Solicitud POST incorrecta');
        }
    } else {
        enviarRespuesta(405, 'Método no permitido');
    }

}

function peticionRendicion($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $contraseniaHash = md5($contrasenia);
    $jugadorID = ControladorJugador::obtenerIDJugador($email, $contraseniaHash);



    if ($metodoPeticion === 'POST') {
        if (isset($args[2])) {
            $partidaID = $args[2];
            $partidaAbierta = ControladorJuego::obtenerEstadoPartida($jugadorID, $partidaID);
            $esPartidaCreada = ControladorJuego::esPartidaCreada($jugadorID, $partidaID);
            if ($esPartidaCreada && $partidaAbierta !== -1) {
                ControladorJuego::rendirse($jugadorID, $partidaID);
            } else {
                enviarRespuesta(404, 'No tienes partidas creadas');
            }
        }

    } else {
        enviarRespuesta(405, 'Método no permitido');
    }

}

function peticionRanking()
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    if ($metodoPeticion === 'GET') {
        ControladorJugador::obtenerRanking();
    } else {
        enviarRespuesta(405, 'Metodo no permitido');
    }
}

function peticionesContrasenia($datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];

    if ($metodoPeticion === 'POST') {
        $email = $datosDecodificados['email'];
        $contrasenia = $datosDecodificados['contrasenia'];
        $contraseniaHash = md5($contrasenia);
        $jugadorID = ControladorJugador::obtenerIDJugador($email, $contraseniaHash);
        ControladorJugador::cambiarContrasenia($jugadorID);
    } else {
        enviarRespuesta(405, 'Metodo no permitido');
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