<?php

class Conexion_BD {

    private static $servidor = 'localhost';
    private static $usuario = 'root';
    private static $password = '';
    private $base_datos = 'telmax';
    private $link;
    private $stmt;
    private $array;
    static $_instance;

    private function __construct() {
        $this->conectar();
    }
    
    private function conectar() {
        $this->link = mysql_connect(self::$servidor, self::$usuario, self::$password);
        mysql_select_db($this->base_datos);
        
    }

    public static function get_instance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public function ejecutar_sql($sql) {
        $this->stmt = mysql_query($sql);
    }

    public function cantidad_registros() {
        return mysql_num_rows($this->stmt);
    }
    
    public function get_tuplas() {
        
        $this->array = array();
	$i = 0;
	while($tupla = mysql_fetch_row($this->stmt)){
            $this->array[$i] = $tupla;
		$i ++;
	}
	return $this->array;
    }
    
    public function get_result() {
        return $this->stmt;
    }
    
    public function get_bd() {
        return $this->base_datos;
    }
}

?>