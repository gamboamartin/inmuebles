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
     * TOTAL
     * Integra una descripcion basada en nombres y generales
     * @param array|stdClass $registro Registro en proceso
     * @return string|array
     * @version 1.175.1
     * @url https://github.com/gamboamartin/inmuebles/wiki/orm._base_paquete.descripcion.4.31.0
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
        $descripcion .= ' '.date('Y-m-d-H-i-s');
        return $descripcion;
    }

    /**
     * Inicializa los elementos de domicilio
     * @param array $init_data Params previos
     * @return array
     * @version 2.250.2
     */
    final public function init_data_domicilio(array $init_data): array
    {
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";

        return $init_data;
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

    /**
     * Inicializa los montos de un registro
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.194.1
     */
    final public function montos_0(array $registro): array
    {
        if(!isset($registro['monto_final'])){
            $registro['monto_final'] = 0;
        }
        if(!isset($registro['sub_cuenta'])){
            $registro['sub_cuenta'] = 0;
        }
        if(!isset($registro['descuento'])){
            $registro['descuento'] = 0;
        }
        if(!isset($registro['puntos'])){
            $registro['puntos'] = 0;
        }
        return $registro;
    }

    /**
     * Integra las relaciones entre entidades de nacimiento
     * @param string $enlace Enlace base
     * @param array $renombres Tablas previas
     * @return array
     */
    final public function rename_data_nac(string $enlace, array $renombres): array
    {
        $renombres['dp_municipio_nacimiento']['nombre_original']= 'dp_municipio';
        $renombres['dp_municipio_nacimiento']['enlace']= $enlace;
        $renombres['dp_municipio_nacimiento']['key']= 'id';
        $renombres['dp_municipio_nacimiento']['key_enlace']= 'dp_municipio_nacimiento_id';

        $renombres = $this->rename_estado(renombres: $renombres);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar rename', data: $renombres);
        }
        return $renombres;
    }

    /**
     * POR DOCUMENTAR EN WIKI
     * Este método private se llama rename_estado.
     *
     * @param array $renombres Recibe un array como parámetro.
     * @return array Devuelve un array.
     *
     * En este método, se reestructura el array de argumento con respecto al estado de nacimiento ('dp_estado_nacimiento').
     * En esta reestructuración se asignan o se renombran las siguientes claves:
     *
     * - 'nombre_original' se establece como 'dp_estado'.
     * - 'enlace' se establece como 'dp_municipio_nacimiento'.
     * - 'key' se establece como 'id'.
     * - 'key_enlace' se establece como 'dp_estado_id'.
     *
     * Estas claves se pueden interpretar como una forma de mapear o renombrar las variables usadas en el sistema.
     * Luego, el array $renombres modificado se devuelve como resultado.
     *
     * En resumen, este método se utiliza para renombrar o reasignar valores a determinadas claves de un array,
     * específicamente relacionadas con el estado de nacimiento ('dp_estado_nacimiento').
     * @version 3.7.0
     */
    private function rename_estado(array $renombres): array
    {
        $renombres['dp_estado_nacimiento']['nombre_original']= 'dp_estado';
        $renombres['dp_estado_nacimiento']['enlace']= 'dp_municipio_nacimiento';
        $renombres['dp_estado_nacimiento']['key']= 'id';
        $renombres['dp_estado_nacimiento']['key_enlace']= 'dp_estado_id';
        return $renombres;
    }


}
