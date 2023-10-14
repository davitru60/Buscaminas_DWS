# Primer desafío de Desarrollo Web en Entorno Servidor
## 🧩 Introducción al proyecto
Este proyecto se centra en el desarrollo de una API REST realizada en PHP, que proporciona una plataforma que permite a los usuarios acceder a una variedad de funciones relacionadas con el juego y la administración de jugadores. Este juego se trata de un buscaminas simplificado hecho de forma unidimensional.

## 🛠️ Requisitos
- Tener instalado y configurado correctamente XAMPP.
- Tener instalado el editor de texto Visual Studio Code para usar la extensión ThunderClient (instalar Visual Studio Code no es completamente necesario si empleamos un cliente externo como es Postman,
 aunque para los ejemplos se empleará ThunderClient).

Para poder acceder a las rutas del servicio tendremos que proporcionar un usuario correcto en formato JSON, he aquí un ejemplo.
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

`GET /admin`: Con esta ruta el administrador obtiene todos los jugadores de la base de datos
