<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use stdClass;

class _relaciones_comprador{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();

    }

    /**
     * Valida si aplica o no un alta de co_acreditado
     * @param array $inm_ins Registro a validar
     * @return bool
     * @version 2.70.0
     */
    final public function aplica_alta(array $inm_ins): bool
    {
        $aplica_alta = false;
        if(count($inm_ins)>0){
            $aplica_alta = true;
            if(count($inm_ins) === 1){
                if(isset($inm_ins['genero'])){
                    $aplica_alta = false;
                }
            }
        }
        return $aplica_alta;
    }

    /**
     * Asigna un valor de un campo de referencia para su integracion con otro catalogo
     * @param string $campo Campo a integrar
     * @param array $inm_ins registro previo a insertar
     * @param string $key Key a integrar
     * @param array $registro Registro en proceso
     * @return array
     * @version 1.172.1
     */
    private function asigna_campo(string $campo, array $inm_ins, string $key, array $registro): array
    {
        $campo = trim($campo);
        if($campo === ''){
            return $this->error->error(mensaje: 'Error campo esta vacio',data:  $campo);
        }
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $key);
        }

        $keys = array($key);
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $inm_ins[$campo] = $registro[$key];

        return $inm_ins;

    }

    /**
     * Integra los datos para una transaccion de co acreditado
     * @param int $indice Indice de datos
     * @param array $relaciones Conjunto de co acreditados
     * @return stdClass
     * @version 2.71.0
     */
    private function data_relacion(int $indice,array $relaciones): stdClass
    {
        $existe_relacion = false;
        $inm_relacion = new stdClass();
        if(isset($relaciones[$indice-1])){
            $existe_relacion = true;
            $inm_relacion = (object)$relaciones[$indice-1];
        }

        $data = new stdClass();
        $data->existe_relacion = $existe_relacion;
        $data->inm_relacion = $inm_relacion;

        return $data;
    }

    /**
     * Obtiene los datos de los co acreditados ligados a un comprador
     * @param string $name_relacion
     * @param int $indice
     * @param int $inm_comprador_id Comprador id
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     * @version 2.71.0
     */
    final public function get_data_relacion(string $name_relacion, int $indice,int $inm_comprador_id,
                                       inm_comprador $modelo_inm_comprador): array|stdClass
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }

        if($name_relacion === 'inm_referencia') {
            $relaciones = $modelo_inm_comprador->get_referencias(inm_comprador_id: $inm_comprador_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener referencias', data: $relaciones);
            }
        }
        else{
            $relaciones = $modelo_inm_comprador->get_co_acreditados(inm_comprador_id: $inm_comprador_id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener relaciones',data:  $relaciones);
            }
        }

        $data_relaciones = $this->data_relacion(indice: $indice, relaciones: $relaciones);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_relaciones',data:  $data_relaciones);
        }
        return $data_relaciones;
    }


    /**
     * Integra los campos para insertar un registro de co acreditado
     * @param string $entidad Entidad de relacion
     * @param int $indice Indice de form
     * @param int $inm_comprador_id Comprador
     * @param array $keys Key a integrar
     * @param array $registro registro en proceso
     * @return array
     * @version 2.69.0
     */
    final public function inm_ins(string $entidad, int $indice, int $inm_comprador_id, array $keys,
                                  array $registro): array
    {

        $inm_ins = array();
        foreach ($keys as $campo){
            $inm_ins = $this->integra_value(campo: $campo, entidad: $entidad, indice: $indice,
                inm_ins: $inm_ins, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_ins);
            }
        }
        if(count($inm_ins)>0) {
            $inm_ins['inm_comprador_id'] = $inm_comprador_id;
        }
        return $inm_ins;
    }

    /**
     * Integra un campo de co acreditado para su alta
     * @param string $campo Campo a integrar
     * @param array $inm_ins Registro previo para insersion
     * @param string $key Key de base modifica
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.68.0
     */
    private function integra_campo(string $campo, array $inm_ins, string $key, array $registro): array
    {
        $campo = trim($campo);
        $key = trim($key);

        $valida = $this->valida_data(campo: $campo, key:  $key,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar',data:  $valida);
        }

        $value = trim($registro[$key]);
        if($value !=='') {
            $inm_ins = $this->asigna_campo(campo: $campo, inm_ins:  $inm_ins, key:  $key, registro:  $registro);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar campo',data:  $inm_ins);
            }
        }
        return $inm_ins;
    }

    /**
     * Integra un valor de un campo para insertar un co acreditado
     * @param string $campo Campo a integrar
     * @param int $indice Indice extra de integracion a name input
     * @param array $inm_ins Registro previo cargado
     * @param array $registro Registro en proceso
     * @param string $entidad de relacion
     * @return array
     * @version 2.68.0
     */
    private function integra_value(string $campo, string $entidad, int $indice, array $inm_ins, array $registro): array
    {
        $campo = trim($campo);
        if($campo === ''){
            return $this->error->error(mensaje: 'Error campo esta vacio', data: $campo);
        }

        $key = $entidad.'_'.$campo;
        if($indice>-1){
            $key = $entidad.'_'.$campo.'_'.$indice;
        }
        if(isset($registro[$key])) {
            $inm_ins = $this->integra_campo(campo: $campo, inm_ins: $inm_ins, key: $key, registro: $registro);

            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_ins);
            }
        }
        return $inm_ins;
    }

    /**
     * Valida que los elementos para integrar un campo de insersion en co acreditado sea valido
     * @param string $campo Campo de co_acreditado
     * @param string $key Key a integrar
     * @param array $registro Registro en proceso
     * @return array|true
     * @version 2.67.0
     */
    private function valida_data(string $campo, string $key, array $registro): bool|array
    {
        $campo = trim($campo);
        if($campo === ''){
            return $this->error->error(mensaje: 'Error campo esta vacio',data:  $campo);
        }
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $key);
        }
        $keys = array($key);
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar',data:  $valida);
        }
        return true;
    }
}