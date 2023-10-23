<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_conyuge extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conyuge';
        $columnas = array($tabla=>false,'inm_ocupacion'=>$tabla,'inm_nacionalidad'=>$tabla,'dp_municipio'=>$tabla,
            'dp_estado'=>'dp_municipio');
        $campos_obligatorios = array('nombre','apellido_paterno','dp_municipio_id','inm_nacionalidad_id','curp','rfc',
            'inm_ocupacion_id','telefono_casa','telefono_celular','fecha_nacimiento');
        $atributos_criticos = array('nombre','apellido_paterno','dp_municipio_id','inm_nacionalidad_id','curp','rfc',
            'inm_ocupacion_id','telefono_casa','telefono_celular','fecha_nacimiento');
        $columnas_extra= array();
        $renombres= array();

        $tipo_campos['dp_municipio_id'] = 'id';
        $tipo_campos['inm_nacionalidad_id'] = 'id';
        $tipo_campos['curp'] = 'curp';
        $tipo_campos['rfc'] = 'rfc';
        $tipo_campos['inm_ocupacion_id'] = 'id';
        $tipo_campos['telefono_casa'] = 'telefono_mx';
        $tipo_campos['telefono_celular'] = 'telefono_mx';
        $tipo_campos['fecha_nacimiento'] = 'fecha';


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios, columnas: $columnas,
            columnas_extra: $columnas_extra, renombres: $renombres, tipo_campos: $tipo_campos,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Conyuge';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->registro['nombre'];
            $descripcion .= " ".$this->registro['apellido_paterno'];
            $descripcion .= " ".$this->registro['apellido_materno'];
            $descripcion .= " ".$this->registro['curp'];
            $descripcion .= " ".$this->registro['rfc'];
            $this->registro['descripcion'] = $descripcion;
        }
        $r_alta_bd =  parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }
        return $r_alta_bd;
    }


}