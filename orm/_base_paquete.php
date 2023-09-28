<?php
namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;

class _base_paquete{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Integra una descripcion basada en nombres y generales
     * @param array $registro Registro en proceso
     * @return string|array
     * @version 1.175.1
     */
    final public function descripcion(array $registro): string|array
    {
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
     * @param int $id
     * @param string $key_entidad_base_id
     * @param string $key_entidad_id
     * @param _inm_ubicaciones|inm_comprador_etapa|inm_comprador_proceso|_modelo_base_paquete $modelo
     * @return array
     */
    final public function init_data_row(int $id, string $key_entidad_base_id, string $key_entidad_id
        , _inm_ubicaciones|inm_comprador_etapa|inm_comprador_proceso|_modelo_base_paquete $modelo): array
    {

        $registro = $modelo->registro(registro_id: $id, columnas_en_bruto: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro',data:  $registro);
        }

        unset($registro['descripcion']);
        $registro = $modelo->init_row(registro: $registro,key_entidad_base_id: $key_entidad_base_id,key_entidad_id: $key_entidad_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }
        return $registro;
    }


}
