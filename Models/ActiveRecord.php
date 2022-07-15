<?php
namespace Model;
class ActiveRecord{
    public static $db;
    public static $tabla = '';
    public static $columnasDB = [];
    public static function conectarDB($dataBase){
        self::$db = $dataBase;
    }
    public function guardar(){
        $resultado = '';
        if ($this->id === '') {
            // Crear
            $resultado = $this->crear();    
        }else{
            $resultado = $this->Actualizar();
        }
    return $resultado;
    }

    public function crear(){
        $atributos = $this->sanitizar();

        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES (' "; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        //Guardamos en la BD
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id,
           'actualizado'=> false
        ];
    }

    public function actualizar(){
        $atributos = $this->sanitizar();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            $valores[] = "{$key}='{$value}'";
        }

        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 ";
        
        $resultado = self::$db->query($query);
        return [
            'resultado' =>  $resultado,
           'actualizado'=> true
        ];
    }
    
    public function sincronizar($atributos){
        foreach ($atributos as $key => $value) {
            if(property_exists($key,$value) && !is_null($value));
            $this->$key = $value;
        }
    }

    public static function eliminar($id){
        $query = "DELETE FROM ". static::$tabla ." WHERE id = ${id}";
        
        $resultado = self::$db->query($query);
        return $resultado;
    }

    
    public static function where($atributo,$condicion){
        $query = "SELECT *FROM ".static::$tabla." WHERE ${atributo} = ${condicion}";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    public static function find($atributo,$email){
        $query = "SELECT  *FROM ".static::$tabla." WHERE ${atributo} = '${email}' LIMIT 1";
        $resultado =  self::consultarSQL($query);

        return array_shift($resultado);
    }

    public static function consultarSQL($query){
        $resultados = self::$db->query($query); 
        $registros = [];
        while ($registro = $resultados->fetch_assoc()) {
            $registros[] = self::crearObjeto($registro);  
        }
        //liberar memoria
        $resultados->free();

        return $registros;
    }

    protected static function crearObjeto($registro){
        $objeto = new static;
        foreach ($registro as $key => $value) {
            if(property_exists($objeto,$key));
            $objeto->$key = $value;
        }
        return $objeto;
    }

    public function sanitizar(){
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string($value);
        }
        return $sanitizado;
    }
    public function atributos(){
        $atributos = [];
        foreach (static::$columnasDB as $key) {
            if($key == 'id') continue;
            $atributos[$key] = $this->$key;
        }
        return $atributos;
    }
}
?>