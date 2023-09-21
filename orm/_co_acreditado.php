<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _co_acreditado{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * @param array $inm_co_acreditado_ins
     * @return bool
     */
    private function aplica_alta_co_acreditado(array $inm_co_acreditado_ins): bool
    {
        $aplica_alta_co_acreditado = false;
        if(count($inm_co_acreditado_ins)>0){
            $aplica_alta_co_acreditado = true;
            if(count($inm_co_acreditado_ins) === 1){
                if(isset($inm_co_acreditado_ins['genero'])){
                    $aplica_alta_co_acreditado = false;
                }
            }
        }
        return $aplica_alta_co_acreditado;
    }

    /**
     * Asigna un campo para su alta
     * @param string $campo_co_acreditado Campo  a integrar
     * @param array $inm_co_acreditado_ins Registro previo cargado
     * @param string $key_co_acreditado Key a integrar
     * @param array $registro registro de comprador modifica
     * @return array
     * @version 2.65.0
     */
    private function asigna_campo_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins,
                                                string $key_co_acreditado, array $registro): array
    {
        $campo_co_acreditado = trim($campo_co_acreditado);
        $key_co_acreditado = trim($key_co_acreditado);

        $valida = $this->valida_data_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
            key_co_acreditado:  $key_co_acreditado,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar',data:  $valida);
        }

        $inm_co_acreditado_ins[$campo_co_acreditado] = $registro[$key_co_acreditado];

        return $inm_co_acreditado_ins;

    }

    /**
     * @param array $co_acreditados
     * @return stdClass
     */
    private function data_co_acreditado(array $co_acreditados): stdClass
    {
        $existe_co_acreditado = false;
        $inm_co_acreditado = new stdClass();
        if(count($co_acreditados) === 1){
            $existe_co_acreditado = true;
            $inm_co_acreditado = (object)$co_acreditados[0];

        }

        $data = new stdClass();
        $data->existe_co_acreditado = $existe_co_acreditado;
        $data->inm_co_acreditado = $inm_co_acreditado;

        return $data;
    }

    /**
     * @param int $inm_comprador_id
     * @param inm_comprador $modelo_inm_comprador
     * @return array|stdClass
     */
    private function get_data_co_acreditado(int $inm_comprador_id, inm_comprador $modelo_inm_comprador): array|stdClass
    {
        $co_acreditados = $modelo_inm_comprador->get_co_acreditados(inm_comprador_id: $inm_comprador_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener co_acreditados',data:  $co_acreditados);
        }

        $data_co_acreditado = $this->data_co_acreditado(co_acreditados: $co_acreditados);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        return $data_co_acreditado;
    }

    /**
     * Integra los campos para insertar un registro de co acreditado
     * @param array $registro registro en proceso
     * @return array
     */
    private function inm_co_acreditado_ins(array $registro): array
    {
        $keys_co_acreditado = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $inm_co_acreditado_ins = array();
        foreach ($keys_co_acreditado as $campo_co_acreditado){
            $inm_co_acreditado_ins = $this->integra_value_co_acreditado(
                campo_co_acreditado: $campo_co_acreditado,inm_co_acreditado_ins:  $inm_co_acreditado_ins, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    /**
     * @param int $inm_co_acreditado_id
     * @param int $inm_comprador_id
     * @return array
     */
    private function inm_rel_co_acreditado_ins(int $inm_co_acreditado_id, int $inm_comprador_id): array
    {
        $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $inm_co_acreditado_id;
        $inm_rel_co_acred_ins['inm_comprador_id'] = $inm_comprador_id;
        return $inm_rel_co_acred_ins;
    }

    /**
     * @param array $inm_co_acreditado_ins
     * @param int $inm_comprador_id
     * @param PDO $link
     * @return array|stdClass
     */
    private function inserta_data_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id, PDO $link): array|stdClass
    {
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
     * Integra un campo de co acreditado para su alta
     * @param string $campo_co_acreditado Campo a integrar
     * @param array $inm_co_acreditado_ins Registro previo para insersion
     * @param string $key_co_acreditado Key de base modifica
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.68.0
     */
    private function integra_campo_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins,
                                                 string $key_co_acreditado, array $registro): array
    {
        $campo_co_acreditado = trim($campo_co_acreditado);
        $key_co_acreditado = trim($key_co_acreditado);

        $valida = $this->valida_data_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
            key_co_acreditado:  $key_co_acreditado,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar',data:  $valida);
        }

        $value = trim($registro[$key_co_acreditado]);
        if($value !=='') {
            $inm_co_acreditado_ins = $this->asigna_campo_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
                inm_co_acreditado_ins:  $inm_co_acreditado_ins, key_co_acreditado:  $key_co_acreditado,
                registro:  $registro);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar campo',data:  $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    /**
     * Integra un valor de un campo para insertar un co acreditado
     * @param string $campo_co_acreditado Campo a integrar
     * @param array $inm_co_acreditado_ins Registro previo cargado
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.68.0
     */
    private function integra_value_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins,
                                                 array $registro): array
    {
        $campo_co_acreditado = trim($campo_co_acreditado);
        if($campo_co_acreditado === ''){
            return $this->error->error(mensaje: 'Error campo_co_acreditado esta vacio', data: $campo_co_acreditado);
        }
        $key_co_acreditado = 'inm_co_acreditado_'.$campo_co_acreditado;
        if(isset($registro[$key_co_acreditado])) {
            $inm_co_acreditado_ins = $this->integra_campo_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
                inm_co_acreditado_ins: $inm_co_acreditado_ins, key_co_acreditado: $key_co_acreditado,
                registro: $registro);

            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    /**
     * @param int $inm_comprador_id
     * @param array $inm_comprador_upd
     * @param inm_comprador $modelo_inm_comprador
     * @return array|stdClass
     */
    final public function operaciones_co_acreditado(int $inm_comprador_id, array $inm_comprador_upd, inm_comprador $modelo_inm_comprador): array|stdClass
    {

        $result = new stdClass();

        $inm_co_acreditado_ins = $this->inm_co_acreditado_ins(registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
        }
        $result->inm_co_acreditado_ins = $inm_co_acreditado_ins;

        $aplica_alta_co_acreditado = $this->aplica_alta_co_acreditado(inm_co_acreditado_ins: $inm_co_acreditado_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta co acreditado', data: $inm_co_acreditado_ins);
        }
        $result->aplica_alta_co_acreditado = $aplica_alta_co_acreditado;

        if($aplica_alta_co_acreditado) {
            $data_co_acreditado = $this->transacciones_co_acreditado(
                inm_co_acreditado_ins: $inm_co_acreditado_ins,inm_comprador_id:  $inm_comprador_id,modelo_inm_comprador:  $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
            }
            $result->data_co_acreditado = $data_co_acreditado;
        }
        return $result;
    }

    /**
     * @param array $inm_co_acreditado_ins
     * @param int $inm_comprador_id
     * @param inm_comprador $modelo_inm_comprador
     * @return array|stdClass
     */
    private function transacciones_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id, inm_comprador $modelo_inm_comprador): array|stdClass
    {

        $data_result = new stdClass();

        $data_co_acreditado = $this->get_data_co_acreditado(inm_comprador_id: $inm_comprador_id,modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        $data_result->data_co_acreditado = $data_co_acreditado;

        if(!$data_co_acreditado->existe_co_acreditado) {
            $data_ins = $this->inserta_data_co_acreditado(inm_co_acreditado_ins: $inm_co_acreditado_ins,
                inm_comprador_id:  $inm_comprador_id,link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de co acreditado', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_co_acreditado = (new inm_co_acreditado(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_co_acreditado_ins,id:  $data_co_acreditado->inm_co_acreditado->inm_co_acreditado_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar co acreditado', data: $modifica_co_acreditado);
            }
            $data_result->modifica_co_acreditado = $modifica_co_acreditado;
        }
        return $data_result;

    }

    /**
     * Valida que los elementos para integrar un campo de insersion en co acreditado sea valido
     * @param string $campo_co_acreditado Campo de co_acreditado
     * @param string $key_co_acreditado Key a integrar
     * @param array $registro Registro en proceso
     * @return array|true
     * @version 2.67.0
     */
    private function valida_data_co_acreditado(string $campo_co_acreditado, string $key_co_acreditado,
                                               array $registro): bool|array
    {
        $campo_co_acreditado = trim($campo_co_acreditado);
        if($campo_co_acreditado === ''){
            return $this->error->error(mensaje: 'Error campo_co_acreditado esta vacio',data:  $campo_co_acreditado);
        }
        $key_co_acreditado = trim($key_co_acreditado);
        if($key_co_acreditado === ''){
            return $this->error->error(mensaje: 'Error key_co_acreditado esta vacio',data:  $key_co_acreditado);
        }
        $keys = array($key_co_acreditado);
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar',data:  $valida);
        }
        return true;
    }
}
