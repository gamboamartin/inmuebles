<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_destino_credito extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_destino_credito';
        $columnas = array($tabla=>false);

        $campos_obligatorios = array('x','y');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('x','y');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Destino del credito';
    }


}