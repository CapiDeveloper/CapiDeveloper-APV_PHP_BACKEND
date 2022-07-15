<?php
namespace Clases;

use PHPMailer\PHPMailer\PHPMailer;

class Email{
    protected $nombre;
    protected $email;
    protected $token;
    public function __construct($nombre,$email,$token)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    // Crear una cuenta
    public function enviarConfirmacion(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ba7570fe5dba57';
        $mail->Password = '3a6512a9f4a52f';

        $mail->setFrom('cuentas@capideveloper.com');
        $mail->addAddress('cuentas@capideveloper.com', 'developer.com');     
        $mail->Subject='Confirma tu cuenta';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = '<html>';
        $contenido .= "<p>Hola <strong>". $this->nombre." </strong>, has creado tu cuenta en nuestra Veterinaria, solo debes
        hacer click en el siguiente enlace para confirmarla</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar-cuenta/" . $this->token . "'>Confirmar Cuenta</a>"; 
        $contenido .= "<p>Si tu no creaste tu cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }

    // Ovide password
    public function enviarInstrucciones(){
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ba7570fe5dba57';
        $mail->Password = '3a6512a9f4a52f';

        $mail->setFrom('cuentas@uptask.com');
        $mail->addAddress('cuentas@uptask.com', 'uptask.com');     
        $mail->Subject='Reestablece tu password';

        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = '<html>';
        $contenido .= "<p>Hola <strong>". $this->nombre." </strong>, has olvidado tu password en UpTask, solo sigue el siguiente
        enlace para recuperarlo</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/recuperar-password/" . $this->token . "'>Reestablecer passoword</a>"; 
        $contenido .= "<p>Si tu no creaste tu cuenta, puedes ignorar este mensaje</p>";
        $contenido .= '</html>';

        $mail->Body = $contenido;

        $mail->send();
    }
}