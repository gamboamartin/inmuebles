<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_costo extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_costo';
        $columnas = array($tabla=>false,'inm_concepto'=>$tabla,'inm_ubicacion'=>$tabla);

        $campos_obligatorios = array('inm_concepto_id','inm_ubicacion_id','monto','fecha','referencia');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_ubicacion_id','inm_concepto_id','monto','fecha','referencia');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Costo Ubicaciones';
    }


}