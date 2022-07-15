<?php

namespace Controller;

use ReallySimpleJWT\Token;

include_once __DIR__.'/../Includes/app.php';

use Model\Veterinarios;
use Clases\Email;

class VeterinarioController{
    public static function registrar(){
      $alertas = [];

      if ($_SERVER["REQUEST_METHOD"] === 'POST') {
          
        $veterinarios = new Veterinarios($_POST);
        $existeVeterinario = Veterinarios::find('email',$_POST['email']);
        if (!$existeVeterinario) {
          // Guardarmos registro
          $veterinarios->generarToken();
          $veterinarios->hashearPassoword();
          $resultado = $veterinarios->guardar();
          $alertas=[
            'mensaje'=> 'Usuario Creado correctamente, revisa tu email',
            'tipo' => false
          ];

          // Enviar email
          $email = new Email($veterinarios->nombre,$veterinarios->email,$veterinarios->token);
          $email->enviarConfirmacion();
        }else{
          $alertas=[
            'mensaje'=> 'El usuario ya existe',
            'tipo' => true
          ];
        }
        echo json_encode($alertas);
      }// fin post
    }
    public static function confirmar(){
      $confirmacion = [];
      $token = $_GET['token'];
      //Encontrar al usuario con este token
      $usuario = Veterinarios::find('token',$token);

      if (empty($usuario)) {
          //No se encontro un usuario con ese token
          $confirmacion = [
            'detalle'=>'Token no valido',
            'valido' => true
          ];
      }else{
        $confirmacion = [
          'detalle'=>'Token valido',
          'valido'=> false
        ];
        //Confirmar la cuenta
        $usuario->confirmado = 1;
        $usuario->token=null;

        //Guardar en la BD
        $usuario->guardar();
      }
          echo json_encode($confirmacion);
    }
    public static function autenticar(){
      if ($_SERVER["REQUEST_METHOD"] === 'POST') {

        $array = [];

        $email = $_POST['email'];
        $password = $_POST['password'];

        //Verificar si existe usuario
        $existeUsuario = Veterinarios::find('email',$email);
        if ($existeUsuario) {
          // verificar confirmacion
          if ($existeUsuario->confirmado == '0') {

            // Confirmar password
              if (password_verify($password,$existeUsuario->password)) {
                
                $data = [
                  'id' => $existeUsuario->id,
                  'nombre' => $existeUsuario->nombre,
                  'exp' => time() + 10
              ];
              
              $secret = 'Hello&MikeFooBar123';
              
              $token = Token::customPayload($data, $secret);

                $array = [
                  'mensaje'=>$token,
                  'error'=>false,
                  'valido'=>true
                ];
                

              }else{
                // passowrd incorrecto
                $array = [
                  'mensaje'=>'Password Incorrecto',
                  'error'=>true
                ];
              }
          }else{
            // No esta confirmado el usuario
            $array = [
              'mensaje'=>'Usuario no esta confirmado',
              'error'=>true
            ];
          }

        }else{
          $array = [
            'mensaje'=>'No existe usuario',
            'error'=>true
          ];
        }
        echo json_encode($array);
        return;
      }
    }
    public static function perfil(){

      $array = [];
      // En caso de que en el better no exista el jwt
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
          header('location: facebook.com');
          echo 'Token not found in request';
          echo $array_error = [
            'error'=>'token no valido'
          ];
          exit;
       }

       //En $jwt esta almacenado el token que tiene el usuario en el navegador
       $jwt = $matches[1];

       //Verificar si el token existe
        if (!$jwt) {
            header('location: http://localhost:3000');
            exit;
        }

        // ** Comprobar si el token es valido o no y enviar al frontend
        $tokenValido = TOKEN::validate($jwt,'Hello&MikeFooBar123');    
        if ($tokenValido) {
          // **Token Valido
          // decodificacion JWT
          $info= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwt)[1]))));
          $usuario = Veterinarios::find('id',$info->id);
          unset($usuario->password);
          unset($usuario->token);
          unset($usuario->confirmado);
          
          
          $array = [
            'mensaje'=> $usuario,
            'valido'=>true
          ];
          
        }else{
          $array = [
            'mensaje'=>'Token No valido',
            'valido'=>false
          ];
        }

        echo json_encode($array);
        return;
      }

      public static function olvidePassword(){
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
          $alerta = [];
          $usuario =  Veterinarios::find('email',$_POST['email']);

          // Si no existe agregar mensaje de errores
          if (!$usuario) {
            //error no existe usuario
            $alerta =[
              'mensaje' => 'No existe usuario',
              'error' => true
            ];
            echo json_encode($alerta);
            return;
          }

          // Generar token en BD
          $usuario->generarToken();

          $resultado =  $usuario->guardar();
          
          $alerta = [
            'mensaje'=> 'Usuario existe, revide su email para recuperarla',
            'error'=> false
          ];

          $email = new Email($usuario->nombre,$usuario->email,$usuario->token);
          $email->enviarInstrucciones();
          echo json_encode($alerta);
        }

      }
      public static function comprobarToken(){
        $array = [];

            $token = $_GET['token'];
            if(!$token) header('location: http://localhost:3000/');

            $existeUsuario = Veterinarios::find('token',$token);
            if (!$existeUsuario) {
              // debuguear('El usuario no existe');
              $array = [
                'mensaje'=>'El usuario no existe',
                'error'=>true
              ];
              echo json_encode($array);
            }else{
              $array = [
                'mensaje'=>'Coloca tu nuevo password',
                'error'=>false
              ];
              echo json_encode($array);
            }
      }

      public static function nuevoPassword(){
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
          $password = $_POST['password'];
          $existeVeterinario = Veterinarios::find('token',$_POST['token']);
          // echo json_encode($existeUsuario);
          // return;
          if ($existeVeterinario) {
            
            $existeVeterinario->sincronizar($_POST);
            // Eliminamos el token
            $existeVeterinario->token = null;
            // hasheamos password
            $existeVeterinario->hashearPassoword();
            // guardando
            $resultado = $existeVeterinario->guardar();

            $resultado = [
              'mensaje'=>'Password Modificado correctamente',
              'error'=>false
            ];
            echo json_encode($resultado);
          };
        }
      }
      public static function actualizarPerfil(){
        $array = [];
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
          $email = $_POST['email'];
          $existeVeterinario = Veterinarios::find('email',$email);
          // Verificar que exista el veterinario
          if ($_POST['id'] == $existeVeterinario->id) {

              $existeVeterinario->sincronizar($_POST);
              $resultado = $existeVeterinario->guardar();
              if ($resultado) {
                $array = [
                  'mensaje'=>'Guardado Correctamente',
                  'valido'=>false
                ];
                
              }else{
                $array = [
                  'mensaje'=>'Hubo un error en la actualizacion, revise bien los datos',
                  'valido'=>true
                ];
              }
          }else{
            $array = [
              'mensaje'=>'Este email ya esta en uso',
              'valido'=>true
            ];
          }
          echo json_encode($array);
          return;
        }
      }
      public static function actualizarPassword(){

        $array = [];

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {

          // leer datos
          $passwordActual = $_POST['actual'];
          $passwordNuevo = $_POST['nuevo'];
          $idVeterinario = $_POST['id'];

          // Comprobar que el vet exista
          $existeVeterinario = Veterinarios::find('id',$idVeterinario);
          if($existeVeterinario->id === $idVeterinario){
            
            // Comprobar su password
            if(password_verify($passwordActual,$existeVeterinario->password)){

              // Almacenar nuevo password
              $existeVeterinario->password = $passwordNuevo;
              $existeVeterinario->hashearPassoword();
              $resultado = $existeVeterinario->guardar();

              if ($resultado) {
                //Guardado correctamente
                $array = [
                  'mensaje'=>'Guardado correctamente',
                  'valido'=>false
                ];
              }else{
                // No se guardo el password
                $array = [
                  'mensaje'=>'Hubo un error, comunicarse con soporte',
                  'valido'=>true
                ];
              }
            }else{
              // Password incorrecto
              $array = [
                'mensaje'=>'Password actual incorrecto',
                'valido'=>true
              ];
            }
          }else{
            // No tiene permisos para cambiar password
            $array = [
              'mensaje'=>'No tiene permisos para cambiar el password',
              'valido'=>true
            ];
          }
          echo json_encode($array);
          return;
        }
      }
  }
?>