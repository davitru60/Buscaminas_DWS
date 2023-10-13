<?php
require_once './bbdd/Conexion.php';

class RankingModelo
{

    static function obtenerRanking()
    {
        $conexion = Conectar::conectar();
        $consulta = "SELECT id_jugador, email,partidas_ganadas FROM jugadores ORDER BY partidas_ganadas DESC";
        $stmt = $conexion->prepare($consulta);
        $jugadores=[];

        if ($stmt) {
            $stmt->execute();
            $resultados = $stmt->get_result();

            while ($fila = $resultados->fetch_assoc()) {
                $jugadores[] = $fila;
            }

            $resultados->free_result();
            $stmt->close();
        }

        return $jugadores;
    }
}