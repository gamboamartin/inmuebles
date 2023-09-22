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
     * Valida si aplica o no un alta de co_acreditado
     * @param array $inm_co_acreditado_ins Registro a validar
     * @return bool
     * @version 2.70.0
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
     * Integra los datos para una transaccion de co acreditado
     * @param array $co_acreditados Conjunto de co acreditados
     * @return stdClass
     * @version 2.71.0
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
     * Obtiene los datos de los co acreditados ligados a un comprador
     * @param int $inm_comprador_id Comprador id
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     * @version 2.71.0
     */
    private function get_data_co_acreditado(int $inm_comprador_id, inm_comprador $modelo_inm_comprador): array|stdClass
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }

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
     * Genera un registro de insersion de un co acreditado
     * @param int $inm_co_acreditado_id Co acreditado id
     * @param int $inm_comprador_id Comprador id
     * @return array
     * @version 2.72.0
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
     * @version 2.73.0
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
     * Transacciones datos de comprador, inserta un co acreditado y una relacion
     * @param int $inm_comprador_id Comprador a integrar
     * @param array $inm_comprador_upd datos de registro en proceso
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     * @version 2.75.0
     */
    final public function operaciones_co_acreditado(int $inm_comprador_id, array $inm_comprador_upd,
                                                    inm_comprador $modelo_inm_comprador): array|stdClass
    {

        $result = new stdClass();

        $keys = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $inm_co_acreditado_ins = (new _relaciones_comprador())->inm_ins(entidad: 'inm_co_acreditado', indice: -1,
            inm_comprador_id: $inm_comprador_id, keys: $keys, registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
        }
        $result->inm_co_acreditado_ins = $inm_co_acreditado_ins;

        $aplica_alta_co_acreditado = $this->aplica_alta_co_acreditado(inm_co_acreditado_ins: $inm_co_acreditado_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta co acreditado',
                data: $inm_co_acreditado_ins);
        }
        $result->aplica_alta_co_acreditado = $aplica_alta_co_acreditado;

        if($aplica_alta_co_acreditado) {
            $data_co_acreditado = $this->transacciones_co_acreditado(
                inm_co_acreditado_ins: $inm_co_acreditado_ins,inm_comprador_id:  $inm_comprador_id,
                modelo_inm_comprador:  $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
            }
            $result->data_co_acreditado = $data_co_acreditado;
        }
        return $result;
    }

    /**
     * Genera las transacciones de un co acreditado, ya sea a insercion o modificacion
     * @param array $inm_co_acreditado_ins Co acreditados
     * @param int $inm_comprador_id Comprador id
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     * @version 2.74.0
     */
    private function transacciones_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id,
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

        $data_co_acreditado = $this->get_data_co_acreditado(inm_comprador_id: $inm_comprador_id,
            modelo_inm_comprador: $modelo_inm_comprador);
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


}
