<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
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
            if(count($inm_ins) === 1){
                if(isset($inm_ins['inm_comprador_id'])){
                    $aplica_alta = false;
                }
            }
            if(count($inm_ins) === 2){
                if(isset($inm_ins['inm_comprador_id']) && isset($inm_ins['genero'])){
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
     * @return stdClass|array
     * @version 2.71.0
     */
    private function data_relacion(int $indice,array $relaciones): stdClass|array
    {
        if($indice<=0){
            return $this->error->error(mensaje: 'Error indice es menor a 1',data:  $indice);
        }
        if($indice > 2){
            return $this->error->error(mensaje: 'Error indice es mayor a 2',data:  $indice);
        }

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
     private function get_data_relacion(string $name_relacion, int $indice,int $inm_comprador_id,
                                       inm_comprador $modelo_inm_comprador): array|stdClass
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        if($indice<=0){
            return $this->error->error(mensaje: 'Error indice es menor a 1',data:  $indice);
        }
        if($indice > 2){
            return $this->error->error(mensaje: 'Error indice es mayor a 2',data:  $indice);
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
     * Genera un registro de insersion de un co acreditado
     * @param int $inm_co_acreditado_id Co acreditado id
     * @param int $inm_comprador_id Comprador id
     * @return array
     */
    private function inm_rel_co_acreditado_ins(int $inm_co_acreditado_id, int $inm_comprador_id): array
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0', data: $inm_comprador_id);
        }
        if($inm_co_acreditado_id <= 0){
            return $this->error->error(mensaje: 'Error inm_co_acreditado_id es menor a 0', data: $inm_co_acreditado_id);
        }
        $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $inm_co_acreditado_id;
        $inm_rel_co_acred_ins['inm_comprador_id'] = $inm_comprador_id;
        return $inm_rel_co_acred_ins;
    }

    /**
     * Inserta un conjunto de co acreaditados y los liga a un comprador
     * @param array $inm_co_acreditado_ins Conjunto de co acreaditados
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     */
    private function inserta_data_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id,
                                                PDO $link): array|stdClass
    {
        $valida = (new inm_co_acreditado(link: $link))->valida_data_alta(inm_co_acreditado: $inm_co_acreditado_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inm_co_acreditado',data:  $valida);
        }
        $valida = (new inm_co_acreditado(link: $link))->valida_alta(inm_co_acreditado: $inm_co_acreditado_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0', data: $inm_comprador_id);
        }

        $alta_inm_co_acreditado = (new inm_co_acreditado(link: $link))->alta_registro(registro: $inm_co_acreditado_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar co_acreditado', data: $alta_inm_co_acreditado);
        }

        $inm_rel_co_acred_ins = $this->inm_rel_co_acreditado_ins(
            inm_co_acreditado_id: $alta_inm_co_acreditado->registro_id,inm_comprador_id:  $inm_comprador_id);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar inm_rel_co_acred_ins', data: $inm_rel_co_acred_ins);
        }

        $alta_inm_rel_co_acred = (new inm_rel_co_acred(link: $link))->alta_registro(registro: $inm_rel_co_acred_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_inm_rel_co_acred', data: $alta_inm_rel_co_acred);
        }

        $data = new stdClass();
        $data->alta_inm_co_acreditado = $alta_inm_co_acreditado;
        $data->alta_inm_rel_co_acred = $alta_inm_rel_co_acred;
        return $data;
    }

    /**
     * Inserta los elementos de una referencia
     * @param array $inm_referencia_ins Referencia a integrar
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     */
    private function inserta_data_referencia(array $inm_referencia_ins, PDO $link): array|stdClass
    {

        $valida = (new inm_referencia(link: $link))->valida_alta_referencia(registro: $inm_referencia_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

        $alta_inm_referencia = (new inm_referencia(link: $link))->alta_registro(registro: $inm_referencia_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_inm_referencia', data: $alta_inm_referencia);
        }


        $data = new stdClass();
        $data->alta_inm_referencia = $alta_inm_referencia;

        return $data;
    }

    /**
     * Integra un campo de co acreditado para su alta
     * @param string $campo Campo a integrar
     * @param array $inm_ins Registro previo para insersion
     * @param string $key Key de base modifica
     * @param array $registro Registro en proceso
     * @return array
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
     * Genera las transacciones de un co acreditado, ya sea a insercion o modificacion
     * @param array $inm_co_acreditado_ins Co acreditados
     * @param int $inm_comprador_id Comprador id
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     */
    final public function transacciones_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id,
                                                 inm_comprador $modelo_inm_comprador): array|stdClass
    {

        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        $valida = (new inm_co_acreditado(link: $modelo_inm_comprador->link))->valida_data_alta(
            inm_co_acreditado: $inm_co_acreditado_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inm_co_acreditado',data:  $valida);
        }
        $valida = (new inm_co_acreditado(link: $modelo_inm_comprador->link))->valida_alta(
            inm_co_acreditado: $inm_co_acreditado_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $data_result = new stdClass();

        $data_co_acreditado = $this->get_data_relacion(name_relacion: 'inm_con_acreditado',
            indice: 1, inm_comprador_id: $inm_comprador_id, modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        $data_result->data_co_acreditado = $data_co_acreditado;

        if(!$data_co_acreditado->existe_relacion) {
            $data_ins = $this->inserta_data_co_acreditado(
                inm_co_acreditado_ins: $inm_co_acreditado_ins, inm_comprador_id:  $inm_comprador_id,
                link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de co acreditado', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_co_acreditado = (new inm_co_acreditado(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_co_acreditado_ins,id:  $data_co_acreditado->inm_relacion->inm_co_acreditado_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar co acreditado', data: $modifica_co_acreditado);
            }
            $data_result->modifica_co_acreditado = $modifica_co_acreditado;
        }
        return $data_result;

    }

    /**
     * Da de alta las referencias relacionadas con el comprador, si no existe la modifica
     * @param int $indice Indice de referencia 1 o 2
     * @param array $inm_referencia_ins Referencias a insertar
     * @param int $inm_comprador_id Identificador de referencia
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     */
    final public function transacciones_referencia(int $indice,array $inm_referencia_ins, int $inm_comprador_id,
                                              inm_comprador $modelo_inm_comprador): array|stdClass
    {

        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        $valida = (new inm_referencia(link: $modelo_inm_comprador->link))->valida_alta_referencia(
            registro: $inm_referencia_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }
        if($indice<=0){
            return $this->error->error(mensaje: 'Error indice es menor a 1',data:  $indice);
        }
        if($indice > 2){
            return $this->error->error(mensaje: 'Error indice es mayor a 2',data:  $indice);
        }

        $data_result = new stdClass();

        $data_referencia = $this->get_data_relacion(name_relacion: 'inm_referencia',
            indice: $indice, inm_comprador_id: $inm_comprador_id, modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
        }

        $data_result->data_referencia = $data_referencia;

        if(!$data_referencia->existe_relacion) {
            $data_ins = $this->inserta_data_referencia(inm_referencia_ins: $inm_referencia_ins,
                link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de referencia', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_referencia = (new inm_referencia(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_referencia_ins,id:  $data_referencia->inm_relacion->inm_referencia_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar modifica_referencia', data: $modifica_referencia);
            }
            $data_result->modifica_referencia = $modifica_referencia;
        }
        return $data_result;

    }

    /**
     * Valida que los elementos para integrar un campo de insersion en co acreditado sea valido
     * @param string $campo Campo de co_acreditado
     * @param string $key Key a integrar
     * @param array $registro Registro en proceso
     * @return array|true
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