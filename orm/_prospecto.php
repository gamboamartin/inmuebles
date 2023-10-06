<?php
namespace gamboamartin\inmuebles\models;

use base\orm\modelo;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_ubicacion;
use PDO;
use stdClass;

class _prospecto{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    private function asigna_datos_alta(inm_prospecto $modelo, array $registro){
        $registro = $this->init_data_default(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar key fiscal',data:  $registro);
        }

        $registro = $this->asigna_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar descripcion',data:  $registro);
        }

        $registro = $this->asigna_dp_calle_pertenece_id(modelo: $modelo,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar dp_calle_pertenece_id',data:  $registro);
        }

        $registro = $this->init_numbers_dom(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }
        return $registro;
    }

    private function asigna_descripcion(array $registro){
        if(!isset($registro['descripcion'])){
            $descripcion = (new _base_paquete())->descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }
            $registro['descripcion'] = $descripcion;
        }
        return $registro;
    }

    private function asigna_dp_calle_pertenece_id(inm_prospecto $modelo, array $registro){
        if(!isset($registro['dp_calle_pertenece_id'])){
            $dp_calle_pertenece_id = $this->dp_calle_pertenece_id(modelo: $modelo);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece_id',data:  $dp_calle_pertenece_id);
            }
            $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        }
        return $registro;
    }

    private function com_prospecto_ins(array $registro): array
    {
        $com_prospecto_ins['nombre'] = $registro['nombre'];
        $com_prospecto_ins['apellido_paterno'] = $registro['apellido_paterno'];
        $com_prospecto_ins['apellido_materno'] = $registro['apellido_materno'];
        $com_prospecto_ins['telefono'] = $registro['lada_com'].$registro['numero_com'];
        $com_prospecto_ins['correo'] = $registro['correo_com'];
        $com_prospecto_ins['razon_social'] = $registro['razon_social'];
        $com_prospecto_ins['com_agente_id'] = $registro['com_agente_id'];
        $com_prospecto_ins['com_tipo_prospecto_id'] = $registro['com_tipo_prospecto_id'];

        return $com_prospecto_ins;
    }

    private function  dp_calle_pertenece_id(inm_prospecto $modelo){
        $dp_calle_pertenece_id = $modelo->id_preferido_detalle(entidad_preferida: 'dp_calle_pertenece');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece_id',data:  $dp_calle_pertenece_id);
        }
        if($dp_calle_pertenece_id === -1){
            $dp_calle_pertenece_id = 100;
        }
        return $dp_calle_pertenece_id;
    }


    /**
     * @param array $registro
     * @return array
     */
    private function init_data_default(array $registro): array
    {
        $registro = $this->init_keys_sin_data(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar key registro',data:  $registro);
        }

        $registro = $this->init_data_fiscal(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar key fiscal',data:  $registro);
        }

        return $registro;
    }

    private function init_data_fiscal(array $registro): array
    {
        if($registro['nss'] === ''){
            $registro['nss'] = '99999999999';
        }
        if($registro['curp'] === ''){
            $registro['curp'] = 'XEXX010101HNEXXXA4';
        }
        if($registro['rfc'] === ''){
            $registro['rfc'] = 'XAXX010101000';
        }
        return $registro;
    }

    private function init_entidades_default(stdClass $data, array $entidades, array $registro){
        foreach ($entidades as $entidad){
            $registro = $this->init_key_entidad_id(data: $data,entidad:  $entidad,registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
            }
        }

        $registro = $this->init_key_entidad_hardcodeo(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }
        return $registro;
    }

    /**
     * Inicializa un key que no existe como vacio
     * @param string $key Key a inicializar
     * @param array $registro Registro
     * @return array
     */
    private function init_key(string $key, array $registro): array
    {
        if(!isset($registro[$key])){
            $registro[$key] = '';
        }
        return $registro;
    }

    private function init_key_entidad_id(stdClass $data, string $entidad, array $registro){
        $key_id = $entidad.'_id';
        $registro = $this->init_key_id(data: $data,key_id:  $key_id,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }

        return $registro;
    }

    private function init_key_entidad_hardcodeo(array $registro): array
    {
        if((int)$registro['inm_producto_infonavit_id'] === -1){
            $registro['inm_producto_infonavit_id'] = 6;
        }
        if((int)$registro['inm_attr_tipo_credito_id'] === -1){
            $registro['inm_attr_tipo_credito_id'] = 8;
        }
        if((int)$registro['inm_destino_credito_id'] === -1){
            $registro['inm_destino_credito_id'] = 8;
        }
        if((int)$registro['inm_plazo_credito_sc_id'] === -1){
            $registro['inm_plazo_credito_sc_id'] = 7;
        }
        if((int)$registro['inm_tipo_discapacidad_id'] === -1){
            $registro['inm_tipo_discapacidad_id'] = 5;
        }
        if((int)$registro['inm_persona_discapacidad_id'] === -1){
            $registro['inm_persona_discapacidad_id'] = 6;
        }
        if((int)$registro['inm_estado_civil_id'] === -1){
            $registro['inm_estado_civil_id'] = 5;
        }
        if((int)$registro['inm_institucion_hipotecaria_id'] === -1){
            $registro['inm_institucion_hipotecaria_id'] = 2;
        }
        return $registro;
    }
    private function init_key_id(stdClass $data, string $key_id, array $registro): array
    {
        if(!isset($registro[$key_id])){
            $registro[$key_id] = $data->$key_id;
        }
        return $registro;
    }

    /**
     * @param array $keys
     * @param array $registro
     * @return array
     */
    private function init_keys(array $keys, array $registro): array
    {
        foreach ($keys as $key){
            $registro = $this->init_key(key:  $key,registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al inicializar key registro',data:  $registro);
            }
        }
        return $registro;
    }


    /**
     * @param array $registro
     * @return array
     */
    private function init_keys_sin_data(array $registro): array
    {
        $keys = array('apellido_materno','nss','curp','rfc');
        $registro = $this->init_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar key registro',data:  $registro);
        }
        return $registro;
    }

    private function init_numbers_dom(array $registro): array
    {
        if(!isset($registro['numero_exterior'])){
            $registro['numero_exterior'] = 'SN';
        }
        if(!isset($registro['numero_interior'])){
            $registro['numero_interior'] = 'SN';
        }
        return $registro;
    }
    
    private function inserta_com_prospecto(PDO $link, array $registro){
        $com_prospecto_ins = $this->com_prospecto_ins(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar com_prospecto',data:  $com_prospecto_ins);
        }

        $r_com_prospecto = (new com_prospecto(link: $link))->alta_registro(registro: $com_prospecto_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar com_prospecto',data:  $r_com_prospecto);
        }
        return $r_com_prospecto;
    }
    
    private function integra_entidades_mayor_uso(PDO $link, array $registro){
        $entidades = array('inm_producto_infonavit','inm_attr_tipo_credito','inm_destino_credito',
            'inm_plazo_credito_sc','inm_tipo_discapacidad','inm_persona_discapacidad','inm_estado_civil',
            'inm_institucion_hipotecaria');
        $modelo_preferido = (new inm_prospecto(link: $link));

        $data = (new _ubicacion())->integra_ids_preferidos(data: new stdClass(),entidades:  $entidades,
            modelo_preferido:  $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos data',data:  $data);
        }


        $registro = $this->init_entidades_default(data: $data,entidades:  $entidades,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }
        return $registro;
    }

    final public function previo_alta(inm_prospecto $modelo, array $registro){
        $registro = $this->asigna_datos_alta(modelo: $modelo,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }

        $r_com_prospecto = $this->inserta_com_prospecto(link: $modelo->link,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar com_prospecto',data:  $r_com_prospecto);
        }

        $registro['com_prospecto_id'] = $r_com_prospecto->registro_id;


        $registro = $this->integra_entidades_mayor_uso(link: $modelo->link,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }
        return $registro;
    }
}
