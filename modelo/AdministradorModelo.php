<?php
require_once './bbdd/Conexion.php';
require_once 'Jugador.php';
class JugadorModelo
{
    private $conexion;
    private $jugadores;

    public function __construct()
    {
        $this->conexion = Conectar::conectar();
        $this->jugadores = [];

    }

    public function obtenerJugadores()
    {
        $consulta = "SELECT * FROM jugadores";
        $stmt = self::$conexion->prepare($consulta);

        if ($stmt) {
            $stmt->execute();
            $resultados = $stmt->get_result();

            while ($fila = $resultados->fetch_assoc()) {
                self::$jugadores[] = $fila;
            }

            $resultados->free_result();
            $stmt->close();
        }

        return self::$jugadores;
    }

    public function obtenerJugadoresPorID($id)
    {
        $consulta = "SELECT * FROM jugadores WHERE id_jugador=? ";
        $stmt = self::$conexion->prepare($consulta);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resultados = $stmt->get_result();

        if ($resultados->num_rows != 0) {
            while ($fila = $resultados->fetch_assoc()) {
                self::$jugadores[] = $fila;
            }
            $resultados->free_result();
            $stmt->close();
        }

        return  self::$jugadores;
    }

    public function registrarJugador(Jugador $jugador){
        $consulta= "INSERT INTO jugadores (contraseña,email,es_adminstrador) VALUES (?,?,?)";
        $stmt = $this->conexion->prepare($consulta);
        $contrasenia= $jugador->getContrasenia();
        $email= $jugador-> getEmail();
        $admin = $jugador -> getEsAdministrador();
        $stmt->bind_param("ssi",$contrasenia,$email,$admin);

        $ejecucionCorrecta=true;

        if($stmt->execute()){
            self::$conexion->close();
            $ejecucionCorrecta= true;
        }else{
            $stmt->close();
            self::$conexion->close();
            $ejecucionCorrecta=false;
        }
        return $ejecucionCorrecta;
    }

    public function modificarJugador(Jugador $jugador){
        $consulta = "UPDATE jugadores SET email=?,contrasenia=? WHERE id_jugador=?";
        $stmt = $this->conexion->prepare($consulta);
        $email= $jugador-> getEmail();
        $contrasenia= $jugador->getContrasenia();
        $id = $jugador-> getId();

        $stmt-> bind_param("ssi",$email,$contrasenia,$id);

        $ejecucionCorrecta=true;

        if($stmt->execute()){
            self::$conexion->close();
            $ejecucionCorrecta= true;
        }else{
            $stmt->close();
            self::$conexion->close();
            $ejecucionCorrecta=false;
        }
        return $ejecucionCorrecta;

    }

    public function eliminarPersona($id){
        $consulta = "DELETE FROM jugadores WHERE id_jugador = ?";
        $stmt = $this->conexion->prepare($consulta);
        $stmt->bind_param("i", $id);

        
        $ejecucionCorrecta=true;

        if($stmt->execute()){
            self::$conexion->close();
            $ejecucionCorrecta= true;
        }else{
            $stmt->close();
            self::$conexion->close();
            $ejecucionCorrecta=false;
        }
        return $ejecucionCorrecta;


    }
}