<?php
//https://www.espai.es/blog/2022/06/phpmailer-ya-no-envia-correos-a-traves-de-gmail/
//https://codigosdeprogramacion.com/2022/04/05/enviar-correo-electronico-con-phpmailer/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

class EnvioCorreo
{
    static function enviarCorrero($nuevaContrasenia)
    {

        try {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'auxiliardaw2@gmail.com';
            $mail->Password = 'gaxrwhgytqclfiyd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            //Emisor
            $mail->setFrom('auxiliardaw2@gmail.com', 'Servicio Buscaminas');

            //Destinatarios
            $mail->addAddress('mahiro2425@cindalle.com', 'Mahiro Destino');



            //Nombre opcional
            $mail->isHTML(true);
            $mail->Subject = 'Cambio de contraseña';
            $mail->Body = 'Tu nueva contraseña es:' . $nuevaContrasenia;
            $mail->AltBody = 'Este es el cuerpo en texto sin formato para clientes de correo que no son HTML';

            $mail->send(); //Enviar correo eletrónico
        } catch (Exception $e) {
        }


    }

}