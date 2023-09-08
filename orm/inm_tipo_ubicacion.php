<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_tipo_ubicacion extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_tipo_ubicacion';
        $columnas = array($tabla=>false);

        $campos_obligatorios = array();

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Tipos de Ubicacion';
    }


}