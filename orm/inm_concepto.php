<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_concepto extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_concepto';
        $columnas = array($tabla=>false,'inm_tipo_concepto'=>$tabla);

        $campos_obligatorios = array('inm_tipo_concepto_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_tipo_concepto_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Conceptos';
    }


}