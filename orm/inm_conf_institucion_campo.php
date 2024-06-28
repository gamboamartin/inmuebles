<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_conf_institucion_campo extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conf_institucion_campo';
        $columnas = array($tabla=>false,'inm_institucion_hipoteccaria'=>$tabla,'adm_campo'=>$tabla);

        $campos_obligatorios = array('x','y');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('x','y');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Conf. Institucion Campo';
    }


}