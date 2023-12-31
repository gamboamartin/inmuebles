<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_beneficiario extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_beneficiario';
        $columnas = array($tabla=>false,'inm_prospecto'=>$tabla,'inm_tipo_beneficiario'=>$tabla,
            'inm_parentesco'=>$tabla);

        $campos_obligatorios = array('inm_prospecto_id','inm_tipo_beneficiario_id','inm_parentesco_id');

        $columnas_extra= array();

        $renombres = array();

        $atributos_criticos = array('inm_prospecto_id','inm_tipo_beneficiario_id','inm_parentesco_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Beneficiarios';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $keys = array('inm_prospecto_id','inm_tipo_beneficiario_id','inm_parentesco_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->registro['inm_prospecto_id'];
            $descripcion .= ' '.$this->registro['inm_tipo_beneficiario_id'];
            $descripcion .= ' '.$this->registro['inm_parentesco_id'];
            $descripcion .= ' '.$this->registro['nombre'];
            $descripcion .= ' '.$this->registro['apellido_paterno'];
            $this->registro['descripcion'] = $descripcion;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
        }


        return $r_alta_bd;

    }

}