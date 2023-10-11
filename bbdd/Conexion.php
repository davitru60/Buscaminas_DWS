<?php
require_once 'Constantes.php';
class Conectar
{
    static function conectar()
    {
        try {
            $conexion = new mysqli(Constantes::$SERVIDOR, Constantes::$USUARIO, Constantes::$CLAVE, Constantes::$BBDD);
            return $conexion;
        } catch (Exception $e) {

        }

    }

}
