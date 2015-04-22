<?php
include 'conexion_mysql.php';
class Respaldo {

    private $conexion;
    
    private $nombre_archivo;
    
    function __construct($nombre_archivo) {
        $this->conexion = Conexion_BD::get_instance();
        $this->nombre_archivo = $nombre_archivo . ".sql";
    }

    private function backup_tabla($nombre_tabla) {
        $backup = "";
        $sql = "select * from " . $nombre_tabla;
        
        $this->conexion->ejecutar_sql($sql);
        while($tupla = mysql_fetch_row($this->conexion->get_result())) {
            $backup .= $this->generar_insert($tupla, $nombre_tabla);
            $backup .= "\n\n";
        }
        
        return $backup;
    }

    private function generar_insert($tupla, $nombre_tabla) {
        $res = "INSERT INTO " . $nombre_tabla . " VALUES(";
        for($i = 0; $i < count($tupla); $i++) {
            if($i == count($tupla) -1){
                $res .= "'" . $tupla[$i] . "'";
            }
            else{
                $res .= "'" . $tupla[$i] . "',";
            }
        }
        $res .= ")";
        return $res;
    }
    
    public function backup_base_datos() {
        $respaldo = "";
        $sql = "SHOW TABLES FROM " . $this->conexion->get_bd();
        $this->conexion->ejecutar_sql($sql);
        $tablas = $this->conexion->get_tuplas();
        foreach($tablas as $i) {
            $respaldo .= $this->backup_tabla($i[0]);
            $respaldo .= "\n\n\n";
        }
        return $respaldo;
    }
    
    public function backup_tablas($array_tablas) {
        $respaldo = "";
        foreach($array_tablas as $i) {
            $respaldo .= $this->backup_tabla($i);
            $respaldo .= "\n\n\n";
        }
        return $respaldo;
    }
    
    public function guardar($contenido_a_guardar) {
        if(!file_exists("bkp_bd")) {
            mkdir("bkp_bd", 0600);
            
        }
        
        echo getcwd();
        chdir("bkp_bd");
        
        $archivo = fopen($this->nombre_archivo, "w");
        if($archivo) {
            fwrite($archivo, $contenido_a_guardar);
        }
    }
    
}

/*$respaldo = new Respaldo("bakcup_basededatos");
//$respaldo->backup_base_datos();
$respaldo->guardar($respaldo->backup_tablas(["facturas"]));
$respaldo1 = new Respaldo("otrobackup");
$respaldo1->guardar($respaldo1->backup_base_datos());
*/