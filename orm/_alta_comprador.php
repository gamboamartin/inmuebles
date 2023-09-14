<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_sub_proceso;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _alta_comprador{
    private validacion $validacion;
    private errores $error;

    public function __construct(){
        $this->validacion = new validacion();
        $this->error = new errores();
    }

    /**
     * Integra los elementos de alta default
     * @param array $registro Registro en proceso
     * @return array
     * @version 1.179.1
     */
    private function default_infonavit(array $registro): array
    {
        if(!isset($registro['inm_plazo_credito_sc_id'])){
            $registro['inm_plazo_credito_sc_id'] = 7;
        }
        if(!isset($registro['inm_tipo_discapacidad_id'])){
            $registro['inm_tipo_discapacidad_id'] = 5;
        }
        if(!isset($registro['inm_persona_discapacidad_id'])){
            $registro['inm_persona_discapacidad_id'] = 6;
        }
        return $registro;
    }

    final public function init_row_alta(array $registro){
        $registro = $this->integra_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $registro = $this->default_infonavit(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error integrar data default',data:  $registro);
        }

        $valida = $this->valida_base_comprador(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error validar registro',data:  $valida);
        }

        return $registro;
    }

    private function inserta_sub_proceso(int $inm_comprador_id, PDO $link, int $pr_sub_proceso_id){
        $inm_comprador_proceso_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_comprador_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso_id;
        $inm_comprador_proceso_ins['fecha'] = date('Y-m-d');
        $r_alta_sp = (new inm_comprador_proceso(link: $link))->alta_registro(registro: $inm_comprador_proceso_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sub proceso en comprador', data: $r_alta_sp);
        }
        return $r_alta_sp;
    }

    /**
     * Integra la descripcion en un registro de alta
     * @param array $registro Registro en proceso
     * @return array
     * @version 1.178.1
     */
    private function integra_descripcion(array $registro): array
    {
        if(!isset($registro['descripcion'])){
            $keys = array('nombre','apellido_paterno','nss','curp','rfc');
            $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
            }

            $descripcion = (new _base_comprador())->descripcion(registro: $registro );
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }

            $registro['descripcion'] = $descripcion;
        }
        return $registro;
    }

    private function numero_completo(string $key_lada, string $key_numero, array $registro){


        $valida = $this->numero_completo_base(key_lada: $key_lada,key_numero:  $key_numero,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero',data:  $valida);
        }

        $numero_completo = $registro[$key_lada].$registro[$key_numero];

        $numero_completo = trim($numero_completo);
        if($numero_completo === ''){
            return $this->error->error(mensaje: 'Error numero_completo esta vacio',data:  $numero_completo);
        }

        if(strlen($numero_completo)!==10){
            return $this->error->error(mensaje: 'Error numero_completo no es de 10 digitos',data:  $numero_completo);
        }
        return $numero_completo;
    }

    /**
     * Valida que un numero telefonico con lada sea valido
     * @param string $key_lada Key del campo lada
     * @param string $key_numero Key del campo numero
     * @param array $registro Registro en proceso de validacion
     * @return array|true
     */
    private function numero_completo_base(string $key_lada, string $key_numero, array $registro): bool|array
    {
        $keys = array($key_lada,$key_numero);
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data:  $valida);
        }

        $lada = $registro[$key_lada];
        $lada = trim($lada);
        $valida = $this->validacion->valida_lada(lada: $lada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar lada',data:  $valida);
        }

        $numero = $registro[$key_numero];
        $numero = trim($numero);
        $valida = $this->validacion->valida_numero_sin_lada(tel: $numero);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero',data:  $valida);
        }
        return true;
    }

    private function numero_completo_com(array $registro): array|string
    {
        $numero_completo_com = $this->numero_completo(key_lada:'lada_com',key_numero:  'numero_com',
            registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error numero_completo_com invalido',data:  $numero_completo_com);
        }
        return $numero_completo_com;
    }

    /**
     * Obtiene el numero completo con lada y numero
     * @param array $registro Registro en proceso
     * @return array|string
     * @version 1.180.1
     */
    private function numero_completo_nep(array $registro): array|string
    {
        $numero_completo_nep = $this->numero_completo(key_lada:'lada_nep',key_numero:  'numero_nep',
            registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error numero_completo_nep invalido',data:  $numero_completo_nep);
        }
        return $numero_completo_nep;
    }

    final public function posterior_alta(int $inm_comprador_id, PDO $link, array $registro_entrada, string $tabla){
        $integra_relacion_com_cliente = (new _base_comprador())->integra_relacion_com_cliente(inm_comprador_id: $inm_comprador_id,
            link: $link, registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $integra_relacion_com_cliente);
        }

        $sub_proceso = $this->sub_proceso(inm_comprador_id: $inm_comprador_id,
            link: $link, pr_proceso_descripcion: 'INMOBILIARIA CLIENTES', pr_sub_proceso_descripcion: 'ALTA', tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sub proceso', data: $sub_proceso);
        }

        $data = new stdClass();
        $data->integra_relacion_com_cliente = $integra_relacion_com_cliente;
        $data->sub_proceso = $sub_proceso;
        return $data;
    }

    private function pr_sub_proceso(PDO $link, string $pr_proceso_descripcion, string $pr_sub_proceso_descripcion,string $tabla){
        $filtro['adm_seccion.descripcion'] = $tabla;
        $filtro['pr_sub_proceso.descripcion'] = $pr_sub_proceso_descripcion;
        $filtro['pr_proceso.descripcion'] =$pr_proceso_descripcion;
        $existe = (new pr_sub_proceso(link: $link))->existe(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si existe sub proceso', data: $existe);
        }
        if(!$existe){
            return $this->error->error(mensaje: 'Error no existe sub proceso definido', data: $filtro);
        }

        $r_pr_sub_proceso = (new pr_sub_proceso(link: $link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sub proceso', data: $r_pr_sub_proceso);
        }
        if($r_pr_sub_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad', data: $r_pr_sub_proceso);
        }
        if($r_pr_sub_proceso->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe sub proceso', data: $r_pr_sub_proceso);
        }

        return $r_pr_sub_proceso->registros[0];
    }

    private function sub_proceso(int $inm_comprador_id, PDO $link, string $pr_proceso_descripcion, string $pr_sub_proceso_descripcion, string $tabla){
        $pr_sub_proceso = $this->pr_sub_proceso(link: $link,
            pr_proceso_descripcion: $pr_proceso_descripcion, pr_sub_proceso_descripcion: $pr_sub_proceso_descripcion, tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sub proceso', data: $pr_sub_proceso);
        }

        $sub_proceso_ins = $this->inserta_sub_proceso(inm_comprador_id: $inm_comprador_id,
            link: $link, pr_sub_proceso_id: $pr_sub_proceso['pr_sub_proceso_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sub proceso', data: $sub_proceso_ins);
        }
        return $pr_sub_proceso;
    }

    private function valida_base_comprador(array $registro){
        $keys = array('lada_nep','numero_nep','lada_com','numero_com');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $numero_completo_nep = $this->numero_completo_nep(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero_completo_nep',data:  $numero_completo_nep);
        }

        $numero_completo_com = $this->numero_completo_com(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero_completo_com',data:  $numero_completo_com);
        }

        $valida = $this->validacion->valida_rfc(key: 'rfc',registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar rfc',data:  $valida);
        }

        return true;
    }
}
