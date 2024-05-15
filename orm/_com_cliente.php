<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\comercial\models\com_cliente;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _com_cliente{
    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    /**
     * Actualiza un registro de tipo cliente
     * @param array $com_cliente_upd Registro con datos a actualizar
     * @param int $inm_comprador_id Comprador ligado a cliente
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     */
    private function actualiza_com_cliente(array $com_cliente_upd, int $inm_comprador_id, PDO $link): array|stdClass
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $com_cliente_id = $this->com_cliente_id(inm_comprador_id: $inm_comprador_id,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_id',data:  $com_cliente_id);
        }

        $r_com_cliente = (new com_cliente(link: $link))->modifica_bd(registro: $com_cliente_upd,id:  $com_cliente_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
        }
        return $r_com_cliente;
    }

    /**
     * Obtiene el id de un cliente con referencia a su comprador
     * @param int $inm_comprador_id Id de comprador
     * @param PDO $link Conexion a la base de datos
     * @return array|int
     */
    private function com_cliente_id(int $inm_comprador_id, PDO $link): int|array
    {
        if($inm_comprador_id <=0 ){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_im_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener relacion',data:  $r_im_rel_comprador_com_cliente);
        }
        if($r_im_rel_comprador_com_cliente->n_registros === 0){
            return $this->error->error(mensaje: 'Error inm_rel_comprador_com_cliente no existe',
                data:  $r_im_rel_comprador_com_cliente);
        }
        if($r_im_rel_comprador_com_cliente->n_registros > 1){
            return $this->error->error(
                mensaje: 'Error de integridad inm_rel_comprador_com_cliente tiene mas de un registro',
                data:  $r_im_rel_comprador_com_cliente);
        }
        $inm_rel_comprador_com_cliente = $r_im_rel_comprador_com_cliente->registros[0];
        return (int)$inm_rel_comprador_com_cliente['com_cliente_id'];
    }

    /**
     * Integra los elementos de un registro de insersion de cliente
     * @param string $numero_interior Numero interior del domicilio
     * @param string $razon_social Razon social de comprador
     * @param array $registro_entrada Registro en proceso
     * @return array
     */
    private function com_cliente_ins(string $numero_interior, string $razon_social, array $registro_entrada): array
    {

        $valida = $this->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $razon_social = trim($razon_social);
        if($razon_social === ''){
            return $this->error->error(mensaje: 'Error razon_social vacia',data:  $razon_social);
        }

        $telefono = trim($registro_entrada['lada_com']).trim($registro_entrada['numero_com']);


        $com_cliente_ins = $this->com_cliente_data_transaccion(numero_interior: $numero_interior,
            razon_social: $razon_social, registro: $registro_entrada, telefono: $telefono);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row', data: $com_cliente_ins);
        }

        return $com_cliente_ins;
    }

    /**
     * Ajusta un row para actualizar un cliente
     * @param PDO $link
     * @param stdClass $registro Registro de tipo comprador
     * @return array
     */
    private function com_cliente_upd(PDO $link, stdClass $registro): array
    {

        $keys = array('inm_comprador_nombre','inm_comprador_apellido_paterno','dp_calle_pertenece_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar $registro',data:  $valida);
        }

        $com_cliente_upd = array();

        $razon_social = $this->razon_social(con_prefijo: true, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener razon_social',data:  $razon_social);
        }
        $com_cliente_upd['razon_social'] = $razon_social;


        $com_cliente_upd = $this->init_keys_com_cliente(com_cliente_upd: $com_cliente_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_upd',data:  $com_cliente_upd);
        }

        $dp_calle_pertenece = (new dp_calle_pertenece(link: $link))->registro(
            registro_id: $registro->dp_calle_pertenece_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece',data:  $dp_calle_pertenece);
        }

        $keys_dom = array('pais','estado','municipio','colonia','calle','cp');
        foreach ($keys_dom as $key_dom){
            $key_row = 'dp_'.$key_dom.'_descripcion';
            $com_cliente_upd[$key_dom] = $dp_calle_pertenece->$key_row;
        }
        $com_cliente_upd['dp_municipio_id'] = $dp_calle_pertenece->dp_municipio_id;

        return $com_cliente_upd;
    }

    /**
     * Integra los elementos de un array para transaccionar con com cliente
     * @param string $numero_interior Numero interior de domicilio
     * @param string $razon_social razon social del cliente
     * @param array $registro Registro en proceso de tipo inm_comprador
     * @param string $telefono Telefono ajustado a 10 digitos
     * @return array
     */
    private function com_cliente_data_transaccion(string $numero_interior, string $razon_social, array $registro,
                                                  string $telefono): array
    {
        $numero_interior = trim($numero_interior);
        $razon_social = trim($razon_social);
        if($razon_social === ''){
            return $this->error->error(mensaje: 'Error razon_social esta vacia',data:  $razon_social);
        }
        $telefono = trim($telefono);
        if($telefono === ''){
            return $this->error->error(mensaje: 'Error telefono esta vacio',data:  $telefono);
        }


        $valida = $this->valida_data_transaccion_cliente(registro_entrada: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $com_cliente_data['razon_social'] = trim($razon_social);
        $com_cliente_data['rfc'] = $registro['rfc'];
        $com_cliente_data['numero_exterior'] = $registro['numero_exterior'];
        $com_cliente_data['numero_interior'] = $numero_interior;
        $com_cliente_data['telefono'] = $telefono;
        $com_cliente_data['cat_sat_regimen_fiscal_id'] = $registro['cat_sat_regimen_fiscal_id'];
        $com_cliente_data['cat_sat_moneda_id'] = $registro['cat_sat_moneda_id'];
        $com_cliente_data['cat_sat_forma_pago_id'] = $registro['cat_sat_forma_pago_id'];
        $com_cliente_data['cat_sat_metodo_pago_id'] = $registro['cat_sat_metodo_pago_id'];
        $com_cliente_data['cat_sat_uso_cfdi_id'] = $registro['cat_sat_uso_cfdi_id'];
        $com_cliente_data['codigo'] = $registro['rfc'];
        $com_cliente_data['com_tipo_cliente_id'] = $registro['com_tipo_cliente_id'];
        $com_cliente_data['cat_sat_tipo_persona_id'] = $registro['cat_sat_tipo_persona_id'];
        $com_cliente_data['cat_sat_tipo_de_comprobante_id'] = 1;
        $com_cliente_data['dp_municipio_id'] = $registro['dp_municipio_id'];
        return $com_cliente_data;
    }

    /**
     * Obtiene el id de un cliente filtrado
     * @param PDO $link Conexion a la base de datos
     * @param array $filtro Filtro de datos
     * @return array|int
     * @version 2.27.0
     */
    private function com_cliente_id_filtrado(PDO $link, array $filtro): int|array
    {
        if(count($filtro) === 0){
            return $this->error->error(mensaje: 'Error filtro esta vacio', data: $filtro);
        }
        $r_com_cliente_f = (new com_cliente(link: $link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente_f);
        }
        if($r_com_cliente_f->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe cliente', data: $r_com_cliente_f);
        }
        if($r_com_cliente_f->n_registros > 1){
            return $this->error->error(mensaje: 'Error existe mas de un cliente', data: $r_com_cliente_f);
        }
        return (int)$r_com_cliente_f->registros[0]['com_cliente_id'];
    }

    /**
     * Obtiene los datos de relacion previo a la transaccion de integracion
     * @param int $com_cliente_id Cliente id
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.35.0
     */
    private function data_rel(int $com_cliente_id, int $inm_comprador_id, PDO $link): array|stdClass
    {
        if($inm_comprador_id <=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        if($com_cliente_id <=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0',data:  $com_cliente_id);
        }

        $inm_rel_comprador_com_cliente_ins = $this->inm_rel_com_cliente_ins(com_cliente_id: $com_cliente_id,
            inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar row rel com cliente',
                data: $inm_rel_comprador_com_cliente_ins);
        }


        $existe = $this->existe_relacion(com_cliente_id: $com_cliente_id,inm_comprador_id:  $inm_comprador_id,
            link:  $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si existe relacion', data: $existe);
        }

        $data = new stdClass();
        $data->inm_rel_comprador_com_cliente_ins = $inm_rel_comprador_com_cliente_ins;
        $data->existe = $existe;

        return $data;
    }

    /**
     * Integra los datos para una actualizacion de cliente
     * @param array|stdClass $registro_entrada Registro previo
     * @return array|stdClass
     * @version 2.27.0
     */
    private function data_upd(array|stdClass $registro_entrada): array|stdClass
    {
        $keys = array('lada_com','numero_com','nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro entrada', data: $valida);
        }

        $razon_social = $this->razon_social(con_prefijo: false,registro: (object) $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar razon_social', data: $razon_social);
        }
        $telefono = trim($registro_entrada['lada_com']).trim($registro_entrada['numero_com']);

        $numero_interior = '';

        if(isset($registro_entrada['numero_interior'])) {
            $numero_interior = trim($registro_entrada['numero_interior']);
        }

        $data = new stdClass();
        $data->razon_social = $razon_social;
        $data->telefono = $telefono;
        $data->numero_interior = $numero_interior;

        return $data;
    }

    /**
     * Valida si existe una relacion entre cliente y comprador
     * @param int $com_cliente_id Identificador de cliente
     * @param int $inm_comprador_id Identificador de comprador
     * @param PDO $link Conexion a la base de datos
     * @return array|bool
     * @version 2.34.0
     */
    private function existe_relacion(int $com_cliente_id, int $inm_comprador_id, PDO $link): bool|array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }
        if($com_cliente_id<=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0', data: $com_cliente_id);
        }
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $filtro['com_cliente.id'] = $com_cliente_id;

        $existe = (new inm_rel_comprador_com_cliente(link: $link))->existe(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si existe dato', data: $existe);
        }
        return $existe;

    }

    /**
     * Obtiene la relacion entre un cliente y un comprador
     * @param int $com_cliente_id Identificador de cliente
     * @param int $inm_comprador_id Identificador de comprador
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.36.0
     */
    private function get_relacion(int $com_cliente_id, int $inm_comprador_id, PDO $link): array|stdClass
    {
        if($inm_comprador_id <=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        if($com_cliente_id <=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0',data:  $com_cliente_id);
        }


        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $filtro['com_cliente.id'] = $com_cliente_id;

        $r_inm_rel_comprador_com_cliente_ins = (new inm_rel_comprador_com_cliente(link: $link))->filtro_and(
            filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener relacion',
                data: $r_inm_rel_comprador_com_cliente_ins);
        }
        return $r_inm_rel_comprador_com_cliente_ins;
    }

    /**
     * Inicializa los keys de un cliente
     * @param array $com_cliente_upd Cliente a ajustar
     * @return array
     */
    private function init_keys_com_cliente(array $com_cliente_upd): array
    {
        $keys_com_cliente = $this->key_com_cliente();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_com_cliente',data:  $keys_com_cliente);
        }

        foreach ($keys_com_cliente as $key_com_cliente){
            if(isset($registro[$key_com_cliente])){
                $com_cliente_upd[$key_com_cliente] = $registro[$key_com_cliente];
            }
        }

        return $com_cliente_upd;
    }

    /**
     * Genera el registro de insersion para la relacion de cliente con comprador
     * @param int $com_cliente_id Identificador de cliente
     * @param int $inm_comprador_id Identificador de comprador
     * @return array
     */
    private function inm_rel_com_cliente_ins(int $com_cliente_id, int $inm_comprador_id): array
    {
        if($inm_comprador_id <=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        if($com_cliente_id <=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0',data:  $com_cliente_id);
        }
        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = $com_cliente_id;
        return $inm_rel_comprador_com_cliente_ins;
    }

    /**
     * Inserta un cliente con datos de inm_comprador
     * @param PDO $link Conexion a base de datos
     * @param array $registro_entrada registro en proceso
     * @return array|stdClass
     */
    private function inserta_com_cliente(PDO $link, array $registro_entrada): array|stdClass
    {

        $valida = $this->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $com_cliente_ins = $this->row_com_cliente_ins(registro_entrada: $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }

        $r_com_cliente = (new com_cliente(link: $link))->alta_registro(registro: $com_cliente_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }

    /**
     * Inserta la relacion entre un cliente y un comprador
     * @param int $com_cliente_id Cliente id
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     */
    final public function inserta_inm_rel_comprador_com_cliente(int $com_cliente_id, int $inm_comprador_id,
                                                                PDO $link): array|stdClass
    {

        if($inm_comprador_id <=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        if($com_cliente_id <=0){
            return $this->error->error(mensaje: 'Error com_cliente_id debe ser mayor a 0',data:  $com_cliente_id);
        }


        $data_rel = $this->data_rel(com_cliente_id: $com_cliente_id,inm_comprador_id:  $inm_comprador_id,link:  $link);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener datos de relacion', data: $data_rel);
        }

        $r_inm_rel_comprador_com_cliente_ins = $this->result_relacion(existe: $data_rel->existe,
            inm_rel_comprador_com_cliente_ins:  $data_rel->inm_rel_comprador_com_cliente_ins, link: $link);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener relacion', data: $r_inm_rel_comprador_com_cliente_ins);
        }


        return $r_inm_rel_comprador_com_cliente_ins;
    }

    /**
     * Integra los keys de un cliente
     * @return string[]
     */
    private function key_com_cliente(): array
    {
        return array('com_tipo_cliente_id','rfc','dp_calle_pertenece_id','numero_exterior',
            'numero_interior','telefono','cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id',
            'cat_sat_metodo_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_persona_id');
    }

    private function keys_name_cliente(bool $con_prefijo): stdClass
    {
        $key_nombre = 'nombre';
        $key_apellido_paterno = 'apellido_paterno';
        $key_apellido_materno = 'apellido_materno';

        if($con_prefijo){
            $key_nombre = 'inm_comprador_'.$key_nombre;
            $key_apellido_paterno = 'inm_comprador_'.$key_apellido_paterno;
            $key_apellido_materno = 'inm_comprador_'.$key_apellido_materno;
        }

        $data = new stdClass();
        $data->key_nombre = $key_nombre;
        $data->key_apellido_paterno = $key_apellido_paterno;
        $data->key_apellido_materno = $key_apellido_materno;

        return $data;

    }

    /**
     * Modifica los datos de un cliente relacionado con el comprador
     * @param stdClass $inm_comprador Registro de comprador
     * @param PDO $link Conexion de base de datos
     * @return array|stdClass
     */
    final public function modifica_com_cliente(stdClass $inm_comprador, PDO $link): array|stdClass
    {

        $valida = $this->valida_data_cliente(inm_comprador: $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar inm_comprador',data:  $valida);
        }


        $com_cliente_upd = $this->com_cliente_upd(link: $link, registro: $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_upd',data:  $com_cliente_upd);
        }
        $r_com_cliente = new stdClass();
        if(count($com_cliente_upd) > 0){

            $r_com_cliente = $this->actualiza_com_cliente(com_cliente_upd: $com_cliente_upd,
                inm_comprador_id: $inm_comprador->inm_comprador_id,link: $link);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
            }
        }

        return $r_com_cliente;
    }

    /**
     * Integra el numero interior ajustado en caso de no existir
     * @param array $registro_entrada Registro en proceso
     * @return string
     */
    private function numero_interior(array $registro_entrada): string
    {
        $numero_interior = '';
        if(isset($registro_entrada['numero_interior'])){
            $numero_interior = trim($registro_entrada['numero_interior']);
        }
        return $numero_interior;
    }

    /**
     * Integra el resultado de transaccion de un cliente conforme al inm_comprador
     * @param array $filtro Filtro de validacion
     * @param PDO $link Conexion a la base de datos
     * @param array $registro_entrada Registro en proceso
     * @return array|stdClass
     */
    private function r_com_cliente(array $filtro, PDO $link, array $registro_entrada): array|stdClass
    {

        if(count($filtro) === 0){
            return $this->error->error(mensaje: 'Error filtro esta vacio', data: $filtro);
        }
        $valida = $this->valida_data_result_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $com_cliente_id_filtrado = $this->com_cliente_id_filtrado(link: $link,filtro:  $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_id_filtrado',
                data: $com_cliente_id_filtrado);
        }

        $r_com_cliente = new stdClass();
        $r_com_cliente->registro_id = $com_cliente_id_filtrado;


        $row_upd = $this->row_upd(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row', data: $row_upd);
        }

        $r_com_cliente_upd = (new com_cliente(link: $link))->modifica_bd(registro: $row_upd,
            id:  $r_com_cliente->registro_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al actualizar cliente', data: $r_com_cliente_upd);
        }

        return $r_com_cliente;

    }

    /**
     * Integra la razon social para el alta de com cliente
     * @param bool $con_prefijo Si integra prefijo o no de inm_comprador
     * @param stdClass $registro Registro en proceso
     * @return string|array
     */
    private function razon_social(bool $con_prefijo, stdClass $registro): string|array
    {


        $keys = $this->keys_name_cliente(con_prefijo: $con_prefijo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys',data:  $keys);
        }

        $key_apellido_materno = $keys->key_apellido_materno;
        $key_nombre = $keys->key_nombre;
        $key_apellido_paterno = $keys->key_apellido_paterno;

        if(!isset($registro->$key_apellido_materno)){
            $registro->$key_apellido_materno = '';
        }

        $keys = array($key_nombre,$key_apellido_paterno,$key_apellido_materno);
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro, valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }

        $keys = array($key_nombre,$key_apellido_paterno);
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }

        $razon_social = $this->razon_social_base(key_apellido_materno: $key_apellido_materno,
            key_apellido_paterno:  $key_apellido_paterno,key_nombre:  $key_nombre,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar razon_social',data:  $valida);
        }

        return trim($razon_social);
    }

    private function razon_social_base(string $key_apellido_materno, string $key_apellido_paterno,
                                       string $key_nombre, stdClass $registro): string
    {
        $razon_social = trim($registro->$key_nombre);
        $razon_social .= ' '.trim($registro->$key_apellido_paterno);
        $razon_social .= ' '.trim($registro->$key_apellido_materno);

        return trim($razon_social);

    }

    /**
     * Obtiene el resultado de la relacion con cliente
     * @param bool $existe_cliente Si existe cliente actualiza si no inserta
     * @param array $filtro Filtro de datos
     * @param PDO $link conexion a la base de datos
     * @param array $registro_entrada registro de entrada de comprador
     * @return array|stdClass
     */
    private function result_com_cliente(bool $existe_cliente, array $filtro, PDO $link,
                                        array $registro_entrada): array|stdClass
    {
        $valida = $this->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        if(count($filtro) === 0){
            return $this->error->error(mensaje: 'Error filtro esta vacio', data: $filtro);
        }
        $valida = $this->valida_data_result_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        if(!$existe_cliente) {
            $r_com_cliente = $this->inserta_com_cliente(link: $link, registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
            }
        }
        else{
            $r_com_cliente = $this->r_com_cliente(filtro: $filtro, link: $link, registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
            }
        }
        return $r_com_cliente;
    }

    /**
     * Integra las relaciones entre un cliente y un comprador
     * @param bool $existe Si no existe inserta sino obtiene
     * @param array $inm_rel_comprador_com_cliente_ins Registro a validar
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.37.0
     */
    private function result_relacion(bool $existe, array $inm_rel_comprador_com_cliente_ins, PDO $link): array|stdClass
    {
        if(!$existe) {
            $r_inm_rel_comprador_com_cliente_ins = (new inm_rel_comprador_com_cliente(link: $link))->alta_registro(
                registro: $inm_rel_comprador_com_cliente_ins);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar relacion',
                    data: $r_inm_rel_comprador_com_cliente_ins);
            }
        }
        else{

            $r_inm_rel_comprador_com_cliente_ins = $this->get_relacion(
                com_cliente_id: $inm_rel_comprador_com_cliente_ins['com_cliente_id'],
                inm_comprador_id:  $inm_rel_comprador_com_cliente_ins['inm_comprador_id'],link:  $link);

            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener relacion',
                    data: $r_inm_rel_comprador_com_cliente_ins);
            }
        }
        return $r_inm_rel_comprador_com_cliente_ins;
    }

    /**
     * Integra un registro de tipo cliente para su insersion
     * @param array $registro_entrada Registro de tipo inm_comprador
     * @return array
     */
    private function row_com_cliente_ins(array $registro_entrada): array
    {

        $valida = $this->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }


        $razon_social = $this->razon_social(con_prefijo: false, registro: (object)$registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar razon social',data:  $razon_social);
        }

        $numero_interior = $this->numero_interior(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar numero_interior',data:  $numero_interior);
        }

        $com_cliente_ins = $this->com_cliente_ins(numero_interior: $numero_interior, razon_social: $razon_social,
            registro_entrada: $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }
        return $com_cliente_ins;
    }

    /**
     * Ajusta los campos para una actualizacion de un cliente
     * @param array|stdClass $registro_entrada Registro en proceso
     * @return array
     */
    private function row_upd(array|stdClass $registro_entrada): array
    {


        $valida = $this->valida_data_result_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $data_upd = $this->data_upd(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos', data: $data_upd);
        }

        $row_upd = $this->com_cliente_data_transaccion(numero_interior: $data_upd->numero_interior,
            razon_social: $data_upd->razon_social, registro: $registro_entrada, telefono: $data_upd->telefono);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row', data: $row_upd);
        }
        return $row_upd;
    }

    /**
     * Genera las transacciones de relacion con un cliente
     * @param PDO $link Conexion a la base de datos
     * @param array $registro_entrada Registro de inm_comprador
     * @return array|stdClass
     */
    final public function transacciona_com_cliente(PDO $link, array $registro_entrada): array|stdClass
    {
        $valida = $this->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        $valida = $this->valida_data_result_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $filtro['com_cliente.rfc'] = $registro_entrada['rfc'];
        $existe_cliente = (new com_cliente(link: $link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe_cliente);
        }

        $r_com_cliente = $this->result_com_cliente(existe_cliente: $existe_cliente, filtro: $filtro, link: $link,
            registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }

    /**
     * Valida los elementos base que debe tener un comprador para hacer la transaccion con com_cliente
     * @param array|stdClass $registro_entrada Registro en proceso
     * @return array|true
     */
    final public function valida_base_com(array|stdClass $registro_entrada): bool|array
    {
        $valida = $this->valida_existencia_keys_com(registro_entrada: $registro_entrada);;
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $valida = $this->valida_ids_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        return true;
    }

    /**
     * Valida que los datos integrados de un comprador sean validos par ala integracion de un cliente
     * @param array|stdClass $inm_comprador Registro de tipo comprador
     * @return array|true
     * @version 2.91.0
     */
    final public function valida_data_cliente(array|stdClass $inm_comprador): bool|array
    {
        $keys = array('inm_comprador_nombre','inm_comprador_apellido_paterno','inm_comprador_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar inm_comprador',data:  $valida);
        }
        $keys = array('inm_comprador_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar inm_comprador',data:  $valida);
        }
        return true;
    }

    /**
     * Valida los elementos de alta de un cliente
     * @param array|stdClass $registro_entrada registro de comprador
     * @return array|true
     */
    final public function valida_data_result_cliente(array|stdClass $registro_entrada): bool|array
    {
        $keys = array('lada_com','numero_com','nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro entrada', data: $valida);
        }

        $valida = $this->valida_data_transaccion_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        return true;
    }

    /**
     * Valida los datos previos a una transaccion con cliente
     * @param array|stdClass $registro_entrada Registro de comprador
     * @return array|true
     */
    private function valida_data_transaccion_cliente(array|stdClass $registro_entrada): bool|array
    {
        $keys = array('cat_sat_forma_pago_id','cat_sat_metodo_pago_id','cat_sat_moneda_id','cat_sat_regimen_fiscal_id',
            'cat_sat_tipo_persona_id', 'cat_sat_uso_cfdi_id','com_tipo_cliente_id', 'numero_exterior','rfc',
            'dp_municipio_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys = array('cat_sat_forma_pago_id','cat_sat_metodo_pago_id','cat_sat_moneda_id','cat_sat_regimen_fiscal_id',
            'cat_sat_tipo_persona_id', 'cat_sat_uso_cfdi_id','com_tipo_cliente_id','dp_municipio_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        return true;
    }

    /**
     * TOTAL
     * Valida la existencia de campos en un registro
     * @param array|stdClass $registro_entrada Registro de comprador
     * @return array|bool
     * @url https://github.com/gamboamartin/inmuebles/wiki/orm._com_cliente.valida_existencia_keys_com.4.34.2
     */
    private function valida_existencia_keys_com(array|stdClass $registro_entrada): bool|array
    {
        $keys = array('rfc','numero_exterior','lada_com','numero_com', 'cat_sat_regimen_fiscal_id','cat_sat_moneda_id',
            'cat_sat_forma_pago_id','cat_sat_metodo_pago_id', 'cat_sat_uso_cfdi_id','com_tipo_cliente_id',
            'cat_sat_tipo_persona_id','cp');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        return $valida;
    }

    /**
     * TOTAL
     * Valida los ids de tipo comercial que deben existir en inm_comprador
     * @param array|stdClass $registro_entrada registro en proceso
     * @return array
     * @url https://github.com/gamboamartin/inmuebles/wiki/orm._com_cliente.valida_ids_com.4.34.0
     *
     */
    private function valida_ids_com(array|stdClass $registro_entrada): array
    {
        $keys = array('cat_sat_forma_pago_id','cat_sat_metodo_pago_id','cat_sat_moneda_id','cat_sat_regimen_fiscal_id',
            'cat_sat_tipo_persona_id', 'cat_sat_uso_cfdi_id','com_tipo_cliente_id', 'lada_com', 'numero_com');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        return $valida;
    }
}
