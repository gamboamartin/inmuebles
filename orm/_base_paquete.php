<?php
namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use stdClass;

class _base_paquete{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Integra una descripcion basada en nombres y generales
     * @param array|stdClass $registro Registro en proceso
     * @return string|array
     * @version 1.175.1
     */
    final public function descripcion(array|stdClass $registro): string|array
    {
        if(is_object($registro)){
            $registro = (array)$registro;
        }
        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }
        $descripcion = $registro['nombre'];
        $descripcion .= ' '.$registro['apellido_paterno'];
        $descripcion .= ' '.$registro['apellido_materno'];
        $descripcion .= ' '.$registro['nss'];
        $descripcion .= ' '.$registro['curp'];
        $descripcion .= ' '.$registro['rfc'];
        return $descripcion;
    }

    /**
     * Inicializa la descripcion basada en los keys de relacion
     * @param int $id Id de entidad
     * @param string $key_entidad_base_id Entidad de tipo proceso
     * @param string $key_entidad_id Entidad base de operacion
     * @param _inm_ubicaciones|inm_comprador_etapa|inm_comprador_proceso|_modelo_base_paquete $modelo
     * @return array
     * @version 2.100.0
     */
    final public function init_data_row(int $id, string $key_entidad_base_id, string $key_entidad_id
        , _inm_ubicaciones|inm_comprador_etapa|inm_comprador_proceso|_modelo_base_paquete $modelo): array
    {

        if($id <=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0',data:  $id);
        }
        $registro = $modelo->registro(registro_id: $id, columnas_en_bruto: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro',data:  $registro);
        }

        unset($registro['descripcion']);
        $registro = $modelo->init_row(key_entidad_base_id: $key_entidad_base_id, key_entidad_id: $key_entidad_id,
            registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }
        return $registro;
    }


}
