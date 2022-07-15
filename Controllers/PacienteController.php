<?php

namespace Controller;

include_once __DIR__.'/../Includes/app.php';

use Model\Pacientes;
use ReallySimpleJWT\Token;

include_once __DIR__.'/../Includes/app.php';

class PacienteController{
    public static function agregarPaciente(){
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {

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
              $info= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwt)[1]))));
              $_POST['veterinarioId'] = $info->id;
              
              // Guardar paciente
              $paciente = new Pacientes($_POST);
              $resultado = $paciente->guardar();

              if ($resultado['resultado']) {
                $array = [
                  'mensaje'=>$paciente,
                  'valido'=>true,
                  'actualizado'=>$resultado['actualizado']
                ];
              }else{
                $array = [
                  'mensaje'=>'No se guardo el registro, intentelo mas tarde o comunicarse con soporte',
                  'valido'=>true
                ];
              }
            }else{
              $array = [
                'mensaje'=>'Token no valido',
                'valido'=>false
              ];
            }
        echo json_encode($array);
        return;
        }
    }
    public static function obtenerPacientes(){
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
          $info= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $jwt)[1]))));
          
          $idVeterinario = $info->id;
          
          // select pacientes
          $resultado = Pacientes::where('veterinarioId',$idVeterinario);
          $array = [
            'mensaje'=>$resultado,
            'valido'=>true
          ];
          
        }else{
          $array = [
            'mensaje'=>'Token no valido',
            'valido'=>false
          ];
        }
    echo json_encode($array);
    return;
    }

    public static function eliminarPacientes(){
      if ($_SERVER["REQUEST_METHOD"] === 'POST') {
        $array = [];
        $resultado = Pacientes::eliminar($_POST['id']);
        if ($resultado) {
          $array = [
            'mensaje'=>'Eliminado correctamente',
            'valido'=>true
          ];
        }else{
          $array = [
            'mensaje'=>'Problemas al eliminar el registro, consulte con soporte',
            'valido'=>false
          ];
        }
        echo json_encode($array);
        return;
      }
    }
}