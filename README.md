# Primer desafío de Desarrollo Web en Entorno Servidor

¿

Para poder iniciar la aplicación tendremos que proporcionar un usuario en formato JSON, he aquí un ejemplo.
```json
{
   "email": "jugador1@example.com",
   "contrasenia": "contrasenia1"
}
```

Este usuario se añadirá en el cuerpo de la petición, para manejar todas las peticiones usaremos ThunderClient. <br>
<p align="center">
  <img src="https://github.com/davitru60/Buscaminas_DWS/assets/84265707/6637f9ff-d5b8-4c57-98c0-10ce0f6d5872" width="700">
</p>

## Rutas para los administradores
El administrador gestionará: altas, bajas, modificaciones, activaciones y accesos de los usuarios. A estas rutas solo tendrán acceso los administradores, por lo tanto cualquier otro usuario que no sea administrador no tendrá permisos para acceder.

`GET /admin`: Con esta ruta el administrador obtiene todos los jugadores de la base de datos
