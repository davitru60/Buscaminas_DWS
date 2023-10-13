<?php
// Creación de las rutas
require_once './controlador/Controlador.php';
require_once './modelo/AuntenticacionModelo.php';
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
                        metodosAdministrador($args, $datosDecodificados);
                    }else{
                        enviarRespuestaError(403, 'No tienes permisos de administrador');
                    }
                    break;

                case 'jugar':
                    metodosJuego($args,$datosDecodificados);
                    break;

                case 'ranking':
                    metodosRanking();
                    break;

            }
        }else{
            enviarRespuestaError(401, 'Credenciales de usuario incorrectas');
        }
    }else{
        enviarRespuestaError(400, 'Solicitud incorrecta o datos inválidos');
    }
}

function metodosAdministrador($args, $datosDecodificados)
{
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    if ($metodoPeticion === 'GET') {
        if (count($args) > 2 && $args[2] === 'jugador' && isset($args[3])) {
            $jugadorID = $args[3];
            Controlador::obtenerJugadorPorID($jugadorID);
        } else {
            Controlador::obtenerJugadores();
        }
    } elseif ($metodoPeticion === 'POST') {
        if (count($args) == 2 && $args[2] == 'agregarJugador') {
            $datosJugador = $datosDecodificados['jugador'][0]; // El índice 0 accede al primer jugador en la lista
            $email = $datosJugador['email'];
            $contrasenia = $datosJugador['contrasenia'];
            $esAdmin = $datosJugador['es_administrador'];

            $jugador = Factoria::crearJugador($email, $contrasenia, $esAdmin);
            Controlador::aniadirJugador($jugador);
        }


    } elseif ($metodoPeticion === 'PUT') {



    } elseif ($metodoPeticion == 'DELETE') {
        if (count($args) == 3 && $args[2] === 'jugador' && isset($args[3])) {
            $jugadorID = $args[3];
            Controlador::eliminarJugador($jugadorID);
        }
    }
}

function metodosJuego($args,$datosDecodificados){
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    $email = $datosDecodificados['email'];
    $contrasenia = $datosDecodificados['contrasenia'];
    $jugadorID= Controlador::obtenerIDJugador($email,$contrasenia);

    if($metodoPeticion==='POST'){
        if(isset($args[2]) && isset($args[3])){
            $tamanio= (int)$args[2];
            $nMinas= (int)$args[3];
            Controlador::crearTablero($tamanio,$nMinas,$jugadorID);
        }else{
            Controlador::crearTablero(Constantes::$TAM_TABLERO,Constantes::$MINAS,$jugadorID);
        }
    }
}

function metodosRanking(){
    $metodoPeticion = $_SERVER['REQUEST_METHOD'];
    if($metodoPeticion=='GET'){
        Controlador::obtenerRanking();
    }
}

function enviarRespuestaError($codigo, $mensaje) {
    header('Content-Type: application/json');
    http_response_code($codigo);
    $response = array(
        'codigo' => $codigo,
        'mensaje' => $mensaje
    );
    echo json_encode($response);
}