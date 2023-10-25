<?php

namespace App\Models;

class BaseModel {

    // DataBase
    protected $id;
    protected static $db;
    protected static $table = '';
    protected static $DBColumns = [];

    // Errors and Messages
    protected static $errors = [];
    
    // Set connection with the DataBase
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setError($type, $message) {
        static::$errors[$type][] = $message;
    }

    // Validación
    public static function getErrors() {
        return static::$errors;
    }

    public function validar() {
        static::$errors = [];
        return static::$errors;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function SQLQuery($query) {
        // Query to database
        $result = self::$db->query($query);

        // Iterate to the results
        $array = [];
        while($register = $result->fetch_assoc()) {
            $array[] = static::createObject($register);
        }

        // free memory
        $result->free();

        // return results
        return $array;
    }

    //Create a object in memory that is equals to the database
    protected static function createObject($register) {
        $objeto = new static;

        foreach($register as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identify and join attributes of the database
    public function attributes() {
        $attributes = [];
        foreach(static::$DBColumns as $columna) {
            if($columna === 'id') continue;
            $attributes[$columna] = $this->$columna;
        }
        return $attributes;
    }

    // Sanitizar los datos antes de guardarlos en la BD Sanitize data before to saving it in the database
    public function sanitizeAttributes() {
        $attributes = $this->attributes();
        $sanitized = [];
        foreach($attributes as $key => $value ) {
            $sanitized[$key] = self::$db->escape_string($value);
        }
        return $sanitized;
    }

    //Sync the database with objects in memory
    public function sync($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // registers - CRUD
    public function save() {
        $result = '';
        if(!is_null($this->id)) {
            // actualizar
            $result = $this->update();
        } else {
            // Creando un nuevo register
            $result = $this->create();
        }
        return $result;
    }

    // All registers
    public static function all() {
        $query = "SELECT * FROM " . static::$table;
        $result = self::SQLQuery($query);
        return $result;
    }

    // Find a register in the database
    public static function find($id) {
        $query = "SELECT * FROM " . static::$table  ." WHERE id = $id";
        $result = self::SQLQuery($query);
        return array_shift( $result ) ;
    }

    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$table  ." WHERE $columna = '$valor';";
        $result = self::SQLQuery($query);
        return array_shift( $result ) ;
    }

    //Retrieve records with a specific amount.
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$table . " LIMIT $limite";
        $result = self::SQLQuery($query);
        return array_shift( $result ) ;
    }

    // Query flat of SQL
    public static function SQL($query) {
        $result = self::SQLQuery($query);
        return $result;
    }

    // Create a new register
    public function create() {
        // Sanitize data
        $attributes = $this->sanitizeAttributes();
        // Insert in the database
        $query = " INSERT INTO " . static::$table . " ( ";
        $query .= join(', ', array_keys($attributes));
        $query .= " ) VALUES ('"; 
        $query .= join("', '", array_values($attributes));
        $query .= "') ";

        // result of query
        $result = self::$db->query($query);
        return [
           'result' =>  $result,
           'id' => self::$db->insert_id
        ];
    }

    // Update the register
    public function update() {
        // Sanitizar los datos
        $attributes = $this->sanitizeAttributes();

        // Iterate to add each field of the DB
        $values = [];
        foreach($attributes as $key => $value) {
            $values[] = "{$key}='{$value}'";
        }

        // SQL query
        $query = "UPDATE " . static::$table ." SET ";
        $query .=  join(', ', $values );
        $query .= " WHERE id = '" . self::$db->escape_string($this->id) . "' ";
        $query .= " LIMIT 1 "; 

        // update database
        $result = self::$db->query($query);
        return $result;
    }

    // Delete a register by id
    public function eliminar() {
        $query = "DELETE FROM "  . static::$table . " WHERE id = " . self::$db->escape_string($this->id) . " LIMIT 1";
        $result = self::$db->query($query);
        return $result;
    }

}