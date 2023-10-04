<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_ubicacion;
use PDO;
use stdClass;

class inm_prospecto extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_prospecto';
        $columnas = array($tabla=>false,'com_prospecto'=>$tabla,'inm_producto_infonavit'=>$tabla,
            'inm_attr_tipo_credito'=>$tabla,'inm_destino_credito'=>$tabla,'inm_plazo_credito_sc'=>$tabla,
            'inm_tipo_discapacidad'=>$tabla,'inm_persona_discapacidad'=>$tabla,'inm_estado_civil'=>$tabla,
            'inm_institucion_hipotecaria'=>$tabla,'com_agente'=>'com_prospecto','com_tipo_prospecto'=>'com_prospecto',
            'adm_usuario'=>'com_agente');

        $campos_obligatorios = array('com_prospecto_id');

        $columnas_extra= array();

        $columnas_extra['usuario_permitido_id'] = "(SELECT IF(adm_usuario.id = $_SESSION[usuario_id], $_SESSION[usuario_id], -1))";

        $renombres = array();

        $atributos_criticos = array('com_prospecto_id');


        $tipo_campos= array();

        $aplica_seguridad = true;


        parent::__construct(link: $link, tabla: $tabla, aplica_seguridad: $aplica_seguridad,
            campos_obligatorios: $campos_obligatorios, columnas: $columnas, columnas_extra: $columnas_extra,
            renombres: $renombres, tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Prospecto de Vivienda';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $keys = array('nombre','apellido_paterno','numero_com','lada_com','correo_com');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $keys = array('apellido_materno','nss','curp','rfc');

        foreach ($keys as $key){
            if(!isset($this->registro[$key])){
                $this->registro[$key] = '';
            }
        }


        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->registro['nombre'];
            $descripcion .= ' '.$this->registro['apellido_paterno'];
            $descripcion .= ' '.$this->registro['apellido_materno'];
            $descripcion .= ' '.$this->registro['nss'];
            $descripcion .= ' '.$this->registro['curp'];
            $descripcion .= ' '.$this->registro['rfc'];

            $this->registro['descripcion'] = $descripcion;
        }

        $com_prospecto_ins['nombre'] = $this->registro['nombre'];
        $com_prospecto_ins['apellido_paterno'] = $this->registro['apellido_paterno'];
        $com_prospecto_ins['apellido_materno'] = $this->registro['apellido_materno'];
        $com_prospecto_ins['telefono'] = $this->registro['lada_com'].$this->registro['numero_com'];
        $com_prospecto_ins['correo'] = $this->registro['correo_com'];
        $com_prospecto_ins['razon_social'] = $this->registro['razon_social'];
        $com_prospecto_ins['com_agente_id'] = $this->registro['com_agente_id'];
        $com_prospecto_ins['com_tipo_prospecto_id'] = $this->registro['com_tipo_prospecto_id'];

        $r_com_prospecto = (new com_prospecto(link: $this->link))->alta_registro(registro: $com_prospecto_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar com_prospecto',data:  $r_com_prospecto);
        }

        $this->registro['com_prospecto_id'] = $r_com_prospecto->registro_id;

        $entidades = array('inm_producto_infonavit','inm_attr_tipo_credito','inm_destino_credito',
            'inm_plazo_credito_sc','inm_tipo_discapacidad','inm_persona_discapacidad','inm_estado_civil',
            'inm_institucion_hipotecaria');
        $modelo_preferido = (new inm_prospecto(link: $this->link));

        $data = (new _ubicacion())->integra_ids_preferidos(data: new stdClass(),entidades:  $entidades,
            modelo_preferido:  $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos data',data:  $data);
        }

        foreach ($entidades as $entidad){
            $key_id = $entidad.'_id';
            if(!isset($this->registro[$key_id])){
                $this->registro[$key_id] = $data->$key_id;
            }
        }

        if((int)$this->registro['inm_producto_infonavit_id'] === -1){
            $this->registro['inm_producto_infonavit_id'] = 6;
        }
        if((int)$this->registro['inm_attr_tipo_credito_id'] === -1){
            $this->registro['inm_attr_tipo_credito_id'] = 8;
        }
        if((int)$this->registro['inm_destino_credito_id'] === -1){
            $this->registro['inm_destino_credito_id'] = 8;
        }
        if((int)$this->registro['inm_plazo_credito_sc_id'] === -1){
            $this->registro['inm_plazo_credito_sc_id'] = 7;
        }
        if((int)$this->registro['inm_tipo_discapacidad_id'] === -1){
            $this->registro['inm_tipo_discapacidad_id'] = 5;
        }
        if((int)$this->registro['inm_persona_discapacidad_id'] === -1){
            $this->registro['inm_persona_discapacidad_id'] = 6;
        }
        if((int)$this->registro['inm_estado_civil_id'] === -1){
            $this->registro['inm_estado_civil_id'] = 5;
        }
        if((int)$this->registro['inm_institucion_hipotecaria_id'] === -1){
            $this->registro['inm_institucion_hipotecaria_id'] = 2;
        }



        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar prospecto',data:  $r_alta_bd);
        }
        return $r_alta_bd;
    }


}