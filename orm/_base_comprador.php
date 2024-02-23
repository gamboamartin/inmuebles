<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _base_comprador{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }


    /**
     * Obtiene el cliente fiscal asignado al comprador de vivienda
     * @param int $com_cliente_id Identificador de cliente
     * @param PDO $link Conexion a la base de datos
     * @param bool $columnas_en_bruto
     * @param bool $retorno_obj Retorna un objeto en caso de ser verdadero
     * @return array|object
     */
    final public function com_cliente(int $com_cliente_id, PDO $link, bool $columnas_en_bruto = false, bool $retorno_obj = false): object|array
    {

        if($com_cliente_id<=0){
            return $this->error->error(mensaje: 'Error com_cliente_id es menor a 0',data:  $com_cliente_id);
        }

        $filtro['com_cliente.id'] = $com_cliente_id;

        $r_com_cliente = (new com_cliente(link: $link))->filtro_and(columnas_en_bruto: $columnas_en_bruto, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener com_cliente',data:  $r_com_cliente);
        }

        if($r_com_cliente->n_registros === 0){
            return $this->error->error(
                mensaje: 'Error no existe com_cliente',data:  $r_com_cliente);
        }

        if($r_com_cliente->n_registros > 1){
            return $this->error->error(
                mensaje: 'Error de integridad existe mas de un com_cliente',data:  $r_com_cliente);
        }

        $com_cliente = $r_com_cliente->registros[0];
        if($retorno_obj){
            $com_cliente = (object)$com_cliente;
        }

        return $com_cliente;
    }

    /**
     * Inicializa los elementos default posterior upd
     * @param stdClass $r_modifica Resultado de modificacion
     * @return stdClass|array
     */
    final public function data_upd_post(stdClass $r_modifica): stdClass|array
    {

        $valida = $this->valida_r_modifica(r_modifica: $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar r_modifica', data: $valida);
        }


        $row_upd_post = array();
        $aplica_upd_posterior = false;

        if($r_modifica->registro_actualizado->inm_comprador_es_segundo_credito === 'NO'){
            $row_upd_post['inm_plazo_credito_sc_id'] = 7;
            $aplica_upd_posterior = true;

        }

        if($r_modifica->registro_actualizado->inm_comprador_con_discapacidad === 'NO'){
            $row_upd_post['inm_tipo_discapacidad_id'] = 5;
            $row_upd_post['inm_persona_discapacidad_id'] = 6;
            $aplica_upd_posterior = true;
        }

        $data = new stdClass();
        $data->aplica_upd_posterior = $aplica_upd_posterior;
        $data->row_upd_post = $row_upd_post;
        return $data;
    }

    /**
     * Genera la descripcion de un comprador basado en datos del registro a insertar
     * @param array $registro Registro en proceso
     * @return string|array
     * @version 1.176.1
     */
    final public function descripcion(array $registro): string|array
    {
        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }
        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }

        $descripcion = (new _base_paquete())->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion', data: $descripcion);
        }

        return $descripcion;
    }


    /**
     * Obtiene la relacion entre un cliente y un comprador
     * @param int $inm_comprador_id Comprador identificador
     * @param PDO $link Conexion a  la base de datos
     * @return array
     */
    final public function inm_rel_comprador_cliente(int $inm_comprador_id, PDO $link): array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_imp_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $link))->filtro_and(
            filtro:$filtro);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe inm_rel_comprador_com_cliente',
                data:  $r_imp_rel_comprador_com_cliente);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros > 1){
            return $this->error->error(
                mensaje: 'Error de integridad existe mas de un inm_rel_comprador_com_cliente',
                data:  $r_imp_rel_comprador_com_cliente);
        }

        return $r_imp_rel_comprador_com_cliente->registros[0];
    }

    /**
     * Integra una relacion entre un cliente y un comprador
     * @param int $inm_comprador_id id de comprador
     * @param PDO $link Conexion de base de datos
     * @param array $registro_entrada registro de comprador
     * @return array|stdClass
     */
    final public function integra_relacion_com_cliente(int $inm_comprador_id, PDO $link,
                                                       array $registro_entrada): array|stdClass
    {
        $valida = (new _alta_comprador())->valida_transacciones(
            inm_comprador_id: $inm_comprador_id,registro_entrada:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $r_com_cliente = (new _com_cliente())->transacciona_com_cliente(link: $link, registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
        }
        $r_inm_rel_comprador_com_cliente = (new _com_cliente())->inserta_inm_rel_comprador_com_cliente(
            com_cliente_id: $r_com_cliente->registro_id,inm_comprador_id:  $inm_comprador_id,link: $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar r_inm_rel_comprador_com_cliente',
                data: $r_inm_rel_comprador_com_cliente);
        }
        return $r_inm_rel_comprador_com_cliente;
    }


    /**
     * Ejecuta las transacciones de modificacion de un comprador inserta las referencias y los co acreditados o
     * los modifica en su caso
     * @param array $inm_comprador_upd Registro de tipo comprador
     * @param int $inm_comprador_id Identificador de comprador
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @param stdClass $r_modifica Resultado de la modificacion de comprador
     * @return array|stdClass
     */
    final public function transacciones_posterior_upd(array $inm_comprador_upd,int $inm_comprador_id,
                                                      inm_comprador $modelo_inm_comprador,
                                                      stdClass $r_modifica): array|stdClass
    {

        $valida = $this->valida_r_modifica(r_modifica: $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar r_modifica', data: $valida);
        }
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        $valida = (new _com_cliente())->valida_data_cliente(inm_comprador: $r_modifica->registro_actualizado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar r_modifica->inm_comprador',data:  $valida);
        }

        $result = new stdClass();
        $data_upd = $modelo_inm_comprador->upd_post(id: $inm_comprador_id,r_modifica:  $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data_upd);
        }
        $result->data_upd = $data_upd;

        $r_com_cliente = (new _com_cliente())->modifica_com_cliente(inm_comprador: $r_modifica->registro_actualizado,
            link: $modelo_inm_comprador->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
        }
        $result->r_com_cliente = $r_com_cliente;

        $data_co_acreditado = (new _co_acreditado())->operaciones_co_acreditado(inm_comprador_id: $inm_comprador_id,
            inm_comprador_upd:  $inm_comprador_upd,modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        $result->data_co_acreditado = $data_co_acreditado;


        $data_referencias = (new _referencias())->operaciones_referencia(indice: 1, inm_comprador_id: $inm_comprador_id,
            inm_comprador_upd: $inm_comprador_upd, modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencias',data:  $data_referencias);
        }

        $data_referencias = (new _referencias())->operaciones_referencia(indice: 2, inm_comprador_id: $inm_comprador_id,
            inm_comprador_upd: $inm_comprador_upd, modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencias',data:  $data_referencias);
        }


        $result->data_referencias = $data_referencias;

        return $result;
    }

    /**
     * Valida que los elementos base existen en un modificacion de comprador
     * @param stdClass $r_modifica Resultado de modifica_bd
     * @return bool|array
     */
    final public function valida_r_modifica(stdClass $r_modifica): bool|array
    {
        if(!isset($r_modifica->registro_actualizado)){
            return $this->error->error(mensaje: 'Error $r_modifica->registro_actualizado no existe',
                data: $r_modifica);
        }
        if(!is_object($r_modifica->registro_actualizado)){
            return $this->error->error(mensaje: 'Error $r_modifica->registro_actualizado debe ser un obj',
                data: $r_modifica);
        }
        if(!isset($r_modifica->registro_actualizado->inm_comprador_es_segundo_credito)){
            return $this->error->error(
                mensaje: 'Error$r_modifica->registro_actualizado->inm_comprador_es_segundo_credito no existe',
                data: $r_modifica);
        }
        if(!isset($r_modifica->registro_actualizado->inm_comprador_con_discapacidad)){
            return $this->error->error(
                mensaje: 'Error$r_modifica->registro_actualizado->inm_comprador_con_discapacidad no existe',
                data: $r_modifica);
        }
        return true;
    }

}
