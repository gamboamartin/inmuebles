<?php
namespace gamboamartin\inmuebles\models;

use base\orm\modelo;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_ubicacion;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _prospecto{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Asigna los datos de alta para un prospecto
     * @param inm_prospecto $modelo Modelo en ejecucion
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.196.1
     */
    private function asigna_datos_alta(inm_prospecto $modelo, array $registro): array
    {

        $registro = $this->init_data_default(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar key fiscal',data:  $registro);
        }

        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
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

    /**
     * Asigna la descripcion a un registro
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.187.1
     */
    private function asigna_descripcion(array $registro): array
    {
        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        if(!isset($registro['descripcion'])){
            $descripcion = (new _base_paquete())->descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }
            $registro['descripcion'] = $descripcion;
        }
        return $registro;
    }

    /**
     * Asigna la calle por default
     * @param inm_prospecto $modelo Modelo en ejecucion
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.189.1
     */
    private function asigna_dp_calle_pertenece_id(inm_prospecto $modelo, array $registro): array
    {
        if(!isset($registro['dp_calle_pertenece_id'])){
            $dp_calle_pertenece_id = $this->dp_calle_pertenece_id(modelo: $modelo);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece_id',data:  $dp_calle_pertenece_id);
            }
            $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        }
        return $registro;
    }

    /**
     * Integra los campos de par ala insersion de un prospecto
     * @param array $registro
     * @return array
     * @version 2.191.1
     */
    private function com_prospecto_ins(array $registro): array
    {
        $keys = array('nombre','apellido_paterno','lada_com','numero_com','razon_social','com_agente_id',
            'com_tipo_prospecto_id');

        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data: $valida);
        }

        if(!isset($registro['correo_com'])){
            $registro['correo_com'] = 'pendiente@correo.com';
        }
        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }
        $com_prospecto_ins['nombre'] = trim($registro['nombre']);
        $com_prospecto_ins['apellido_paterno'] = trim($registro['apellido_paterno']);
        $com_prospecto_ins['apellido_materno'] = trim($registro['apellido_materno']);
        $com_prospecto_ins['telefono'] = trim($registro['lada_com'].$registro['numero_com']);
        $com_prospecto_ins['correo'] = trim($registro['correo_com']);
        $com_prospecto_ins['razon_social'] = trim($registro['razon_social']);
        $com_prospecto_ins['com_agente_id'] = $registro['com_agente_id'];
        $com_prospecto_ins['com_tipo_prospecto_id'] = $registro['com_tipo_prospecto_id'];

        return $com_prospecto_ins;
    }

    /**
     * Obtiene la calle de prospecto mas usada
     * @param inm_prospecto $modelo Modelo de prospecto
     * @return array|int
     * @version 2.188.1
     */
    private function  dp_calle_pertenece_id(inm_prospecto $modelo): int|array
    {
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
     * Inicializa los datos default de un registro de tipo prospecto
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.195.1
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
        if(!isset($registro['fecha_nacimiento'])){
            $registro['fecha_nacimiento'] = '1900-01-01';
        }

        $registro = (new _base_paquete())->montos_0(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar montos',data:  $registro);
        }


        return $registro;
    }

    /**
     * Inicializa los datos fiscales base
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.185.1
     */
    private function init_data_fiscal(array $registro): array
    {
        if(!isset($registro['nss'])){
            $registro['nss'] = '99999999999';
        }
        if(!isset($registro['curp'])){
            $registro['curp'] = 'XEXX010101HNEXXXA4';
        }
        if(!isset($registro['rfc'])){
            $registro['rfc'] = 'XAXX010101000';
        }

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

    /**
     * Inicializa elementos para dar de alta un registro
     * @param stdClass $data Datos previos
     * @param array $entidades Entidades de inicializacion
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.200.1
     */
    private function init_entidades_default(stdClass $data, array $entidades, array $registro): array
    {
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
     * @version 2.261.0
     */
    private function init_key(string $key, array $registro): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $registro);
        }
        $valida = (new validacion())->valida_texto_pep_8(txt: $key);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar key',data:  $valida);
        }

        if(!isset($registro[$key])){
            $registro[$key] = '';
        }
        return $registro;
    }

    /**
     * Inicializa los keys id para prospecto
     * @param stdClass $data datos previos
     * @param string $entidad Entidad de integracion
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.198.1
     */
    private function init_key_entidad_id(stdClass $data, string $entidad, array $registro): array
    {
        $entidad = trim($entidad);
        if($entidad === ''){
            return $this->error->error(mensaje: 'Error entidad esta vacia',data:  $entidad);
        }
        $key_id = $entidad.'_id';
        $registro = $this->init_key_id(data: $data,key_id:  $key_id,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }

        return $registro;
    }

    /**
     * Inicializa elementos de infonavit
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.199.1
     */
    private function init_key_entidad_hardcodeo(array $registro): array
    {
        if(!isset($registro['inm_producto_infonavit_id'])){
            $registro['inm_producto_infonavit_id'] = -1;
        }
        if(!isset($registro['inm_attr_tipo_credito_id'])){
            $registro['inm_attr_tipo_credito_id'] = -1;
        }
        if(!isset($registro['inm_plazo_credito_sc_id'])){
            $registro['inm_plazo_credito_sc_id'] = -1;
        }
        if(!isset($registro['inm_tipo_discapacidad_id'])){
            $registro['inm_tipo_discapacidad_id'] = -1;
        }
        if(!isset($registro['inm_persona_discapacidad_id'])){
            $registro['inm_persona_discapacidad_id'] = -1;
        }
        if(!isset($registro['inm_estado_civil_id'])){
            $registro['inm_estado_civil_id'] = -1;
        }
        if(!isset($registro['inm_institucion_hipotecaria_id'])){
            $registro['inm_institucion_hipotecaria_id'] = -1;
        }
        if(!isset($registro['inm_sindicato_id'])){
            $registro['inm_sindicato_id'] = -1;
        }
        if(!isset($registro['inm_destino_credito_id'])){
            $registro['inm_destino_credito_id'] = -1;
        }

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
        if((int)$registro['inm_sindicato_id'] === -1){
            $registro['inm_sindicato_id'] = 1;
        }
        return $registro;
    }

    /**
     * Inicializa un key id para integrarlo al objeto
     * @param stdClass $data Datos
     * @param string $key_id Key a integrar
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.197.1
     */
    private function init_key_id(stdClass $data, string $key_id, array $registro): array
    {
        $key_id = trim($key_id);
        if($key_id === ''){
            return $this->error->error(mensaje: 'Error key_id esta vacio',data:  $key_id);
        }
        if(is_numeric($key_id)){
            return $this->error->error(mensaje: 'Error key_id debe ser un texto',data:  $key_id);
        }
        if(!isset($data->$key_id)){
            $data->$key_id = '';
        }
        if(!isset($registro[$key_id])){
            $registro[$key_id] = $data->$key_id;
        }
        return $registro;
    }

    /**
     * Inicializa los keys de un registro de prospecto
     * @param array $keys Keys inicializar
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.162.0
     */
    private function init_keys(array $keys, array $registro): array
    {
        foreach ($keys as $key){
            $key = trim($key);
            if($key === ''){
                return $this->error->error(mensaje: 'Error key esta vacio',data:  $registro);
            }
            $valida = (new validacion())->valida_texto_pep_8(txt: $key);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al validar key',data:  $valida);
            }

            $registro = $this->init_key(key:  $key,registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al inicializar key registro',data:  $registro);
            }
        }
        return $registro;
    }


    /**
     * Inicializa los keys como vacios
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.170.1
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

    /**
     * Se inicializan datos numbers de domicilios
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.189.1
     */
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

    /**
     * Inserta un com prospecto
     * @param PDO $link Conexion a la base de datos
     * @param array $registro Registro en proceso
     * @return array|stdClass
     */
    private function inserta_com_prospecto(PDO $link, array $registro): array|stdClass
    {
        $valida = $this->valida_alta_prospecto(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


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

    /**
     * Inicializa los key con elementos de mayor uso
     * @param PDO $link Conexion a la base de datos
     * @param array $registro registro en proceso
     * @return array
     * @version 2.200.1
     */
    private function integra_entidades_mayor_uso(PDO $link, array $registro): array
    {
        $entidades = array('inm_producto_infonavit','inm_attr_tipo_credito','inm_destino_credito',
            'inm_plazo_credito_sc','inm_tipo_discapacidad','inm_persona_discapacidad','inm_estado_civil',
            'inm_institucion_hipotecaria','inm_sindicato');
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

    /**
     * Ajusta los elementos necesarios previo a un alta de registro
     * @param inm_prospecto $modelo Modelo de prospecto
     * @param array $registro registro previo de insersion
     * @return array
     */
    final public function previo_alta(inm_prospecto $modelo, array $registro): array
    {


        $valida = $this->valida_alta_prospecto(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

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

        if(!isset($registro['dp_municipio_nacimiento_id'])){
            $registro['dp_municipio_nacimiento_id'] = 2469;

        }

        return $registro;
    }

    /**
     * Verifica que los elementos de un alta sean correctos
     * @param array $registro Registro en proceso
     * @return array|true
     * @version 2.203.1
     */
    private function valida_alta_prospecto(array $registro): bool|array
    {
        $keys = array('nombre','apellido_paterno','lada_com','numero_com','razon_social','com_agente_id',
            'com_tipo_prospecto_id');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys = array('com_agente_id', 'com_tipo_prospecto_id');
        $valida = (new validacion())->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        return true;
    }
}
