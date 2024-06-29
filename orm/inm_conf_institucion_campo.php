<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use PDO;


class inm_conf_institucion_campo extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conf_institucion_campo';
        $columnas = array($tabla=>false,'inm_institucion_hipotecaria'=>$tabla,'adm_campo'=>$tabla);

        $columnas_extra= array();
        $renombres= array();

        parent::__construct(link: $link, tabla: $tabla,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Conf. Institucion Campo';
    }


}