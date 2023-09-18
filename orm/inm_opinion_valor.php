<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_opinion_valor extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_opinion_valor';
        $columnas = array($tabla=>false,'inm_ubicacion'=>$tabla,'inm_valuador'=>$tabla);

        $campos_obligatorios = array('inm_ubicacion_id','inm_valuador_id','monto_resultado','fecha','costo');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_ubicacion_id','inm_valuador_id','monto_resultado','fecha','costo');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Opiniones de valor';
    }


}