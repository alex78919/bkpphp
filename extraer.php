<?php

include 'conexion_mysql.php';

class Separador {

    private $conexion;
    private $nombre_archivo;

    function __construct($nombre_archivo) {
        $this->conexion = Conexion_BD::get_instance();
        $this->nombre_archivo = $nombre_archivo . ".txt";
    }

    private function separar($tupla) {
        $res = "";
        $separacion = "|";
        for ($i = 0; $i < count($tupla); $i++) {
            if ($i == count($tupla) - 1) {
                $res .= $tupla[$i];
            } else {
                $res .= $tupla[$i] . $separacion;
            }
        }

        return $res;
    }

    private function backup_tabla($nombre_tabla) {
        $backup = "TABLA " . $nombre_tabla . "\n";
        $sql = "select * from " . $nombre_tabla;

        $this->conexion->ejecutar_sql($sql);
        while ($tupla = mysql_fetch_row($this->conexion->get_result())) {
            $backup .= $this->separar($tupla, $nombre_tabla);
            $backup .= "\n\n";
        }

        return $backup;
    }
    
    public function backup_tablas($array_tablas) {
        $respaldo = "";
        foreach($array_tablas as $i) {
            
            $respaldo .= $this->backup_tabla($i);
            $respaldo .= "\n\n\n";
        }
        return $respaldo;
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

    public function guardar($contenido_a_guardar) {
        if (!file_exists("bkp_bd")) {
            mkdir("bkp_bd", 0600);
        }

        echo getcwd();
        chdir("bkp_bd");

        $archivo = fopen($this->nombre_archivo, "w");
        if ($archivo) {
            fwrite($archivo, $contenido_a_guardar);
        }
    }

}

$bd_separador = new Separador("separar");
$bd_separador->guardar($bd_separador->backup_base_datos());
