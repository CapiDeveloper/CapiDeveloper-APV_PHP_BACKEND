<?php

    namespace Model;

    class Veterinarios extends ActiveRecord{

        public static $columnasDB = ['id','nombre','password','email','telefono','web','confirmado','token'];
        public static $tabla = 'veterinarios';

        public function __construct($args=[]){
            $this->id = $args['id'] ?? '';
            $this->nombre = $args['nombre'] ?? '';
            $this->password = $args['password'] ?? '';
            $this->email = $args['email'] ?? '';
            $this->telefono = $args['telefono'] ?? 0;
            $this->web = $args['web'] ?? '';
            $this->confirmado = $args['confirmado'] ?? true;
            $this->token = $args['token'] ?? NULL;
            
        }   
    
            public function generarToken(){
                $this->token = bin2hex(openssl_random_pseudo_bytes(32,$cstrong));
            }
            public function hashearPassoword(){
                $this->password = password_hash($this->password,PASSWORD_BCRYPT);
            }
        }
?>