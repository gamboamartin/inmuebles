<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_estado_vivienda extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_estado_vivienda';
        $columnas = array($tabla=>false);

        $columnas_extra= array();
        $renombres= array();


        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas, columnas_extra: $columnas_extra,
            renombres: $renombres);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Estado Vivienda';
    }


}