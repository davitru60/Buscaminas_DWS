CREATE TABLE jugadores (
    id_jugador INT AUTO_INCREMENT PRIMARY KEY,
    contraseña VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    partidas_jugadas INT DEFAULT 0,
    partidas_ganadas INT DEFAULT 0,
    es_administrador BOOLEAN NOT NULL
);


CREATE TABLE partidas (
    id_partida INT AUTO_INCREMENT PRIMARY KEY,
    id_jugador INT,
    tablero_oculto VARCHAR(255),
    tablero_jugador VARCHAR(255),
    estado_partida INT DEFAULT 0, -- -1: perdida, 0: en juego, 1: ganada
    FOREIGN KEY (id_jugador) REFERENCES jugadores(id_jugador)
);
