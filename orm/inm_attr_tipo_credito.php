<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_attr_tipo_credito extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_attr_tipo_credito';
        $columnas = array($tabla=>false,'inm_tipo_credito'=>$tabla);

        $campos_obligatorios = array('x','y','inm_tipo_credito_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('x','y','inm_tipo_credito_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Atributo Tipo de credito';
    }


}