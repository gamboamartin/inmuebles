<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_conf_docs_prospecto extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conf_docs_prospecto';
        $columnas = array($tabla=>false,'doc_tipo_documento'=>$tabla,'inm_attr_tipo_credito'=>$tabla,
            'inm_destino_credito'=>$tabla, 'inm_producto_infonavit'=>$tabla,'pr_sub_proceso'=>$tabla,
            'pr_proceso'=>'pr_sub_proceso');

        $campos_obligatorios = array('doc_tipo_documento_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'inm_producto_infonavit_id','es_obligatorio','pr_sub_proceso_id');

        $columnas_extra= array();

        $atributos_criticos = array('doc_tipo_documento_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'inm_producto_infonavit_id','es_obligatorio','pr_sub_proceso_id');

        $renombres = array();

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Conf de tipos de documentos';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->descripcion(registro: $this->registro );
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }
            $this->registro['descripcion'] = $descripcion;
        }
        if(!isset($this->registro['es_obligatorio'])){
            $this->registro['es_obligatorio'] = 'activo';
        }


        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }


        return $r_alta_bd;

    }

    /**
     * Genera la descripcion de un prospecto basado en datos del registro a insertar
     * @param array $registro Registro en proceso
     * @return string
     */
    private function descripcion(array $registro): string
    {
        $descripcion = $registro['doc_tipo_documento_id'];
        $descripcion .= $registro['inm_attr_tipo_credito_id'];
        $descripcion .= $registro['inm_destino_credito_id'];
        $descripcion .= $registro['inm_producto_infonavit_id'];
        $descripcion .= $registro['pr_sub_proceso_id'];
        $descripcion .= mt_rand(1000,9999);
        return $descripcion;
    }


}