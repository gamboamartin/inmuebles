<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\gastos\models\gt_proveedor;
use PDO;


class inm_notaria extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_notaria';
        $columnas = array($tabla=>false,'dp_municipio'=>$tabla,'gt_proveedor'=>$tabla);

        $columnas_extra= array();
        $renombres= array();


        parent::__construct(link: $link, tabla: $tabla, columnas: $columnas, columnas_extra: $columnas_extra,
            renombres: $renombres);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Notaria';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|\stdClass
    {
        $registro_proveedor = (new gt_proveedor(link: $this->link))->registro(registro_id: $this->registro['gt_proveedor_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $registro_proveedor);
        }

        $registro_municipio = (new dp_municipio(link: $this->link))->registro(registro_id: $this->registro['dp_municipio_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $registro_municipio);
        }

        if (!isset($this->registro['descripcion'])) {
            $descripcion = $registro_proveedor['gt_proveedor_razon_social'];
            $descripcion .= " ".$registro_municipio['dp_municipio_descripcion'];
            $descripcion .= " Notaria ".$this->registro['numero_notaria'];

            $this->registro['descripcion'] = $descripcion;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }

        return $r_alta_bd;
    }

}