<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\documento\models\doc_documento;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_doc_prospecto extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_doc_prospecto';
        $columnas = array($tabla=>false,'inm_prospecto'=>$tabla,'doc_documento'=>$tabla,
            'doc_tipo_documento'=>'doc_documento','doc_extension'=>'doc_documento');

        $campos_obligatorios = array('inm_prospecto_id','doc_documento_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_prospecto_id','doc_documento_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Documentos de prospecto';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro_doc['doc_tipo_documento_id'] = $this->registro['doc_tipo_documento_id'];
        $file = $_FILES['documento'];



        $r_alta_doc = (new doc_documento(link: $this->link))->alta_documento(registro: $registro_doc,file: $file);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar doc',data:  $r_alta_doc);
        }

        $this->registro['doc_documento_id'] = $r_alta_doc->registro_id;

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->descripcion(r_alta_doc: $r_alta_doc,registro:  $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }
            $this->registro['descripcion'] = $descripcion;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }


        return $r_alta_bd;
    }

    private function descripcion(stdClass $r_alta_doc, array $registro): string
    {
        $descripcion = $r_alta_doc->registro_id;
        $descripcion .= ' '.$registro['doc_tipo_documento_id'];
        $descripcion .= ' '.$r_alta_doc->registro_obj->doc_tipo_documento_descripcion;
        $descripcion .= ' '.$r_alta_doc->registro_obj->doc_extension_descripcion;
        $descripcion .= ' '.$registro['inm_prospecto_id'];
        $descripcion .= ' '.$r_alta_doc->registro_obj->doc_documento_nombre;
        return $descripcion;
    }

    final public function inm_docs_prospecto(int $inm_prospecto_id){

        $filtro['inm_prospecto.id'] = $inm_prospecto_id;
        $r_inm_doc_prospecto = $this->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener documentos',data:  $r_inm_doc_prospecto);
        }
        return $r_inm_doc_prospecto->registros;
    }


}