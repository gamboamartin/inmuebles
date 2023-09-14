<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _referencias{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    private function aplica_alta_referencia(array $inm_referencia_ins): bool
    {
        $aplica_alta_referencia = false;
        if(count($inm_referencia_ins)>0){
            $aplica_alta_referencia = true;
        }
        return $aplica_alta_referencia;
    }

    /**
     * Asigna un valor de un campo de referencia para su integracion con otro catalogo
     * @param string $campo_referencia Campo a integrar
     * @param array $inm_referencia_ins registro previo a insertar
     * @param string $key_referencia Key a integrar
     * @param array $registro Registro en proceso
     * @return array
     * @version 1.172.1
     */
    private function asigna_campo_referencia(string $campo_referencia, array $inm_referencia_ins,
                                                string $key_referencia, array $registro): array
    {
        $campo_referencia = trim($campo_referencia);
        if($campo_referencia === ''){
            return $this->error->error(mensaje: 'Error campo_referencia esta vacio',data:  $campo_referencia);
        }
        $key_referencia = trim($key_referencia);
        if($key_referencia === ''){
            return $this->error->error(mensaje: 'Error key_referencia esta vacio',data:  $key_referencia);
        }

        $keys = array($key_referencia);
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $inm_referencia_ins[$campo_referencia] = $registro[$key_referencia];

        return $inm_referencia_ins;

    }

    private function data_referencia(array $referencias): stdClass
    {
        $existe_referencia = false;
        $inm_referencia = new stdClass();
        if(count($referencias) === 1){
            $existe_referencia = true;
            $inm_referencia = (object)$referencias[0];

        }

        $data = new stdClass();
        $data->existe_referencia = $existe_referencia;
        $data->inm_referencia = $inm_referencia;

        return $data;
    }

    private function get_data_referencia(int $inm_comprador_id, inm_comprador $modelo_inm_comprador){
        $referencias = $modelo_inm_comprador->get_referencias(inm_comprador_id: $inm_comprador_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener referencias',data:  $referencias);
        }

        $data_referencias = $this->data_referencia(referencias: $referencias);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencias',data:  $data_referencias);
        }
        return $data_referencias;
    }

    private function inm_referencia_ins(int $indice, int $inm_comprador_id,array $registro){
        $keys_referencia = array('apellido_paterno','nombre', 'lada', 'numero','celular','inm_comprador_id',
            'dp_calle_pertenece_id','numero_dom');

        $inm_referencia_ins = array();
        foreach ($keys_referencia as $campo_referencia){
            $inm_referencia_ins = $this->integra_value_referencia(
                campo_referencia: $campo_referencia, indice: $indice, inm_referencia_ins: $inm_referencia_ins,
                registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_referencia_ins);
            }
        }
        if(count($inm_referencia_ins)>0) {
            $inm_referencia_ins['inm_comprador_id'] = $inm_comprador_id;
        }
        return $inm_referencia_ins;
    }

    private function inserta_data_referencia(array $inm_referencia_ins, PDO $link){
        $alta_inm_referencia = (new inm_referencia(link: $link))->alta_registro(registro: $inm_referencia_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_inm_referencia', data: $alta_inm_referencia);
        }


        $data = new stdClass();
        $data->alta_inm_referencia = $alta_inm_referencia;

        return $data;
    }

    /**
     * @param string $campo_referencia
     * @param array $inm_referencia_ins
     * @param string $key_referencia
     * @param array $registro
     * @return array
     */
    private function integra_campo_referencia(string $campo_referencia, array $inm_referencia_ins,
                                             string $key_referencia, array $registro): array
    {
        $key_referencia = trim($key_referencia);
        if($key_referencia === ''){
            return $this->error->error(mensaje: 'Error key_referencia esta vacio',data:  $key_referencia);
        }
        $campo_referencia = trim($campo_referencia);
        if($campo_referencia === ''){
            return $this->error->error(mensaje: 'Error campo_referencia esta vacio',data:  $campo_referencia);
        }
        $keys = array($key_referencia);
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $value = trim($registro[$key_referencia]);
        if($value !=='') {
            $inm_referencia_ins = $this->asigna_campo_referencia(campo_referencia: $campo_referencia,
                inm_referencia_ins:  $inm_referencia_ins, key_referencia:  $key_referencia,
                registro:  $registro);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar campo',data:  $inm_referencia_ins);
            }
        }
        return $inm_referencia_ins;
    }

    /**
     * @param string $campo_referencia
     * @param int $indice
     * @param array $inm_referencia_ins
     * @param array $registro
     * @return array
     */
    private function integra_value_referencia(string $campo_referencia, int $indice, array $inm_referencia_ins,
                                              array $registro): array
    {
        $campo_referencia = trim($campo_referencia);
        if($campo_referencia === ''){
            return $this->error->error(mensaje: 'Error campo_referencia esta vacio',data:  $campo_referencia);
        }

        $key_referencia = 'inm_referencia_'.$campo_referencia.'_'.$indice;
        if(isset($registro[$key_referencia])) {
            $inm_referencia_ins = $this->integra_campo_referencia(campo_referencia: $campo_referencia,
                inm_referencia_ins: $inm_referencia_ins, key_referencia: $key_referencia,
                registro: $registro);

            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_referencia_ins);
            }
        }
        return $inm_referencia_ins;
    }

    final public function operaciones_referencia(int $indice, int $inm_comprador_id, array $inm_comprador_upd, inm_comprador $modelo_inm_comprador){

        $result = new stdClass();

        $inm_referencia_ins = $this->inm_referencia_ins(indice: $indice, inm_comprador_id: $inm_comprador_id,
            registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_referencia_ins);
        }


        $result->inm_referencia_ins = $inm_referencia_ins;

        $aplica_alta_referencia = $this->aplica_alta_referencia(inm_referencia_ins: $inm_referencia_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta inm_referencia_ins', data: $inm_referencia_ins);
        }
        $result->aplica_alta_referencia = $aplica_alta_referencia;

        if($aplica_alta_referencia) {
            $data_referencia = $this->transacciones_referencia(
                inm_referencia_ins: $inm_referencia_ins,inm_comprador_id:  $inm_comprador_id,modelo_inm_comprador:  $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
            }
            $result->data_referencia = $data_referencia;
        }
        return $result;
    }

    private function transacciones_referencia(array $inm_referencia_ins, int $inm_comprador_id, inm_comprador $modelo_inm_comprador){

        $data_result = new stdClass();

        $data_referencia = $this->get_data_referencia(inm_comprador_id: $inm_comprador_id,modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
        }
        $data_result->data_referencia = $data_referencia;

        if(!$data_referencia->existe_referencia) {
            $data_ins = $this->inserta_data_referencia(inm_referencia_ins: $inm_referencia_ins,
                link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de referencia', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_referencia = (new inm_referencia(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_referencia_ins,id:  $data_referencia->inm_referencia->inm_referencia_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar modifica_referencia', data: $modifica_referencia);
            }
            $data_result->modifica_referencia = $modifica_referencia;
        }
        return $data_result;

    }

}
