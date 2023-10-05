<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_usuario;
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
            'adm_usuario'=>'com_agente','dp_calle_pertenece'=>$tabla,'dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_calle'=>'dp_calle_pertenece','dp_colonia'=>'dp_colonia_postal','dp_cp'=>'dp_colonia_postal',
            'dp_municipio'=>'dp_cp','dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado');

        $campos_obligatorios = array('com_prospecto_id','razon_social','dp_calle_pertenece_id','rfc',
            'numero_exterior','numero_interior');

        $columnas_extra= array();


        $adm_usuario = (new adm_usuario(link: $link))->registro(registro_id: $_SESSION['usuario_id'],
            columnas: array('adm_grupo_root'));
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al obtener adm_usuario ',data:  $adm_usuario);
            print_r($error);
            exit;
        }

        $sql = "(SELECT IF(adm_usuario.id = $_SESSION[usuario_id], $_SESSION[usuario_id], -1))";
        if($adm_usuario['adm_grupo_root'] === 'activo'){
            $sql = $_SESSION['usuario_id'];
        }
        $columnas_extra['usuario_permitido_id'] = $sql;

        $renombres = array();

        $atributos_criticos = array('com_prospecto_id','razon_social','dp_calle_pertenece_id','rfc',
            'numero_exterior','numero_interior');


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

        if($this->registro['nss'] === ''){
            $this->registro['nss'] = '99999999999';
        }
        if($this->registro['curp'] === ''){
            $this->registro['curp'] = 'XEXX010101HNEXXXA4';
        }
        if($this->registro['rfc'] === ''){
            $this->registro['rfc'] = 'XAXX010101000';
        }

        if(!isset($this->registro['descripcion'])){
            $descripcion = (new _base_paquete())->descripcion(registro: $this->registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }
            $this->registro['descripcion'] = $descripcion;
        }

        if(!isset($this->registro['dp_calle_pertenece_id'])){
            $dp_calle_pertenece_id = $this->id_preferido_detalle(entidad_preferida: 'dp_calle_pertenece');
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece_id',data:  $dp_calle_pertenece_id);
            }
            if($dp_calle_pertenece_id === -1){
                $dp_calle_pertenece_id = 100;
            }
            $this->registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        }

        if(!isset($this->registro['numero_exterior'])){
            $this->registro['numero_exterior'] = 'SN';
        }
        if(!isset($this->registro['numero_interior'])){
            $this->registro['numero_interior'] = 'SN';
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



    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {


        $r_modifica =  parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $r_modifica);
        }

        if(!isset($r_modifica->registro_actualizado->com_prospecto_rfc)){
            return $this->error->error(mensaje: 'Error al modificar prospecto no existe rfc en com_prospecto',
                data:  $r_modifica);
        }

        $registro = $r_modifica->registro_puro;
        $registro->rfc = $r_modifica->registro_actualizado->com_prospecto_rfc;

        if($registro->nss === ''){
            $registro->nss = '99999999999';
        }
        if($registro->curp === ''){
            $registro->curp = 'XEXX010101HNEXXXA4';
        }

        $descripcion = (new _base_paquete())->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
        }

        $registro_ds['descripcion'] = $descripcion;
        $r_modifica_descripcion =  parent::modifica_bd(registro: $registro_ds,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $r_modifica_descripcion);
        }

        $data_com_prospecto['nombre'] = $registro->nombre;
        $data_com_prospecto['apellido_paterno'] = $registro->apellido_paterno;
        $data_com_prospecto['apellido_materno'] = $registro->apellido_materno;
        $data_com_prospecto['telefono'] = $registro->lada_com.$registro->numero_com;
        $data_com_prospecto['correo'] = $registro->correo_com;
        $data_com_prospecto['razon_social'] = $registro->razon_social;

        $upd = (new com_prospecto(link: $this->link))->modifica_bd(registro: $data_com_prospecto,
            id:  $registro->com_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $upd);
        }


        return $r_modifica;
    }


}