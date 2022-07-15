<?php
    namespace Model;

    class Pacientes extends ActiveRecord{

        public static $columnasDB = ['id','nombre','propietario','email','telefono','fecha','hora','sintomas','veterinarioId'];

        public static $tabla = 'pacientes';

        public function __construct($args=[]){
            $this->id = $args['id'] ?? '';
            $this->nombre = $args['nombre'] ?? '';
            $this->propietario = $args['propietario'] ?? '';
            $this->email = $args['email'] ?? '';
            $this->telefono = $args['telefono'] ?? '';
            $this->fecha = $args['fecha'] ?? '';
            $this->hora = $args['hora'] ?? '';
            $this->sintomas = $args['sintomas'] ?? '';
            $this->veterinarioId = $args['veterinarioId'] ?? '';
        }   
    }
?>