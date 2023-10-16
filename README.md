# Primer desafío de Desarrollo Web en Entorno Servidor
## 🧩 Introducción al proyecto
Este proyecto se centra en el desarrollo de una API REST realizada en PHP, que proporciona una plataforma que permite a los usuarios acceder a una variedad de funciones relacionadas con el juego y la administración de jugadores. Este juego se trata de un buscaminas simplificado hecho de forma unidimensional.

## 🛠️ Requisitos
- Tener instalado y configurado correctamente XAMPP.
- Tener instalado el editor de texto Visual Studio Code para usar la extensión ThunderClient (instalar Visual Studio Code no es completamente necesario si empleamos un cliente externo como es Postman,
 aunque para los ejemplos se empleará ThunderClient).

## ⌨️ Puesta en marcha del servicio 
- Primero será necesario que iniciemos un servidor local para poder realizar las peticiones HTTP, para ello en la terminal escribimos lo siguiente:
  `php -S localhost:9000` (El puerto puede ser cualquier otro numero distinto a 9000).
  
- Después, para poder acceder a las rutas del servicio tendremos que proporcionar un usuario correcto en formato JSON, he aquí un ejemplo:
```json
{
   "email": "jugador1@example.com",
   "contrasenia": "contrasenia1"
}
```

Este usuario se añadirá en el cuerpo de la petición. <br>
<p align="center">
  <img src="https://github.com/davitru60/Buscaminas_DWS/assets/84265707/6637f9ff-d5b8-4c57-98c0-10ce0f6d5872" width="700">
</p>

## Rutas para los administradores
El administrador gestionará: altas, bajas, modificaciones, activaciones y accesos de los usuarios. A estas rutas solo tendrán acceso los administradores, por lo tanto cualquier otro usuario que no sea administrador no tendrá permisos para acceder.

### Peticiones GET
- `GET /admin`: Con esta ruta el administrador obtiene los datos de todos los jugadores de la base de datos. <br>
- `GET /admin/jugador/{id}`: Con esta ruta el administrador obtiene los datos de un usuario en concreto de la base de datos dada su id. Por ejemplo: /admin/jugador/12 (Obtiene los datos del usuario con id=12).

### Peticiones POST
Para que el administrador pueda añadir nuevos usuarios a la base de datos, necesitamos un JSON con el siguiente formato:
```json
{
   "email": "jugador1@example.com",
   "contrasenia": "contrasenia1",
   "jugador": [
      {
        "email":"jugadorPrueba@example.com",
        "contrasenia":"contrasenia123"
      } 
    ]
}
```

### Peticiones POST

Mantenemos el email y la contraseña del administrador para poder estar validados y acceder a las rutas. Después si queremos añadir un usuario creamos una línea más en el JSON con los datos del usuario, en este caso con tan solo añadir el email y la contraseñia bastaría ya que el resto de cosas se añaden como datos por defecto en la base de datos.

- `POST /admin/agregarJugador`: Esta es la ruta que necesitará el administrador para poder añadir un nuevo registro.

### Peticiones DELETE
- `DELETE /admin/jugador/{id}`: Con esta ruta el administrador elimina un jugador de la base datos dada su id. Por ejemplo  /admin/jugador/14 (Elimina al usuario con id=14).

## Rutas para todos los jugadores

### Peticiones POST
- `POST /crearPartida`: Con esta ruta los jugadores podrán crear una partida de buscaminas. Si no se especifica ningún argumento más el tablero será de un tamaño de 20 casillas e incluirá 3 bombas

