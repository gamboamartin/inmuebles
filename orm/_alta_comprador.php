<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\banco\models\bn_cuenta;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_etapa_proceso;
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
        if(!isset($registro['inm_nacionalidad_id'])){
            $registro['inm_nacionalidad_id'] = 1;
        }
        $registro = (new _base_paquete())->montos_0(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar montos',data:  $registro);
        }
        return $registro;
    }

    /**
     * Integra un filtro para la obtencion de una etapa
     * @param string $accion Accion de ejecucion
     * @param string $etapa Etapa a integrar
     * @param string $pr_proceso_descripcion Proceso que pertenece
     * @param string $tabla Tabla de la entidad
     * @return array
     * @version 2.42.0
     */
    private function filtro_etapa_proceso(string $accion, string $etapa, string $pr_proceso_descripcion,
                                          string $tabla): array
    {
        $accion = trim($accion);
        $etapa = trim($etapa);
        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $tabla = trim($tabla);

        $valida =$this->valida_data_etapa(accion: $accion,etapa:  $etapa,
            pr_proceso_descripcion:  $pr_proceso_descripcion,tabla:  $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos de etapa', data: $valida);
        }


        $filtro['adm_seccion.descripcion'] = $tabla;
        $filtro['adm_accion.descripcion'] = $accion;
        $filtro['pr_etapa.descripcion'] = $etapa;
        $filtro['pr_proceso.descripcion'] = $pr_proceso_descripcion;
        return $filtro;
    }

    /**
     * Inicializa un registro para su alta
     * @param inm_comprador $modelo Modelo de comprador
     * @param array $registro Registro en proceso
     * @return array
     */
    final public function init_row_alta(inm_comprador $modelo, array $registro): array
    {
        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
        }

        $registro = $this->integra_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $registro = $this->default_infonavit(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error integrar data default',data:  $registro);
        }

        if(!isset($registro['inm_sindicato_id'])){
            $inm_sindicato_id = $modelo->id_preferido_detalle(entidad_preferida: 'inm_sindicato');
            if(errores::$error){
                return $this->error->error(mensaje: 'Error integrar inm_sindicato_id default',
                    data:  $inm_sindicato_id);
            }
            if($inm_sindicato_id === -1){
                $inm_sindicato_id = 1;
            }

            $registro['inm_sindicato_id'] = $inm_sindicato_id;

        }

        if(!isset($registro['inm_ocupacion_id'])){
            $inm_ocupacion_id = $modelo->id_preferido_detalle(entidad_preferida: 'inm_ocupacion');
            if(errores::$error){
                return $this->error->error(mensaje: 'Error integrar inm_ocupacion_id default',
                    data:  $inm_ocupacion_id);
            }
            if($inm_ocupacion_id === -1){
                $inm_ocupacion_id = 1;
            }

            $registro['inm_ocupacion_id'] = $inm_ocupacion_id;

        }

        if(!isset($registro['dp_municipio_nacimiento_id'])){
            $registro['dp_municipio_nacimiento_id'] = 2469;
        }

        if(!isset($registro['bn_cuenta_id'])){
            $registro['bn_cuenta_id'] = 1;
        }
        if((int)$registro['bn_cuenta_id'] === -1){

            $bn_cuentas = (new bn_cuenta(link: $modelo->link))->registros_activos();
            if(errores::$error){
                return $this->error->error(mensaje: 'Error obtener cuentas', data:  $bn_cuentas);
            }
            if(count($bn_cuentas) === 0){
                return $this->error->error(mensaje: 'Error no existen cuentas cargadas', data:  $bn_cuentas);
            }
            $registro['bn_cuenta_id'] = $bn_cuentas[0]['bn_cuenta_id'];

        }

        $valida = $this->valida_base_comprador(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error validar registro',data:  $valida);
        }

        return $registro;
    }

    /**
     * Inserta una etapa proceso de un comprador
     * @param string $accion Accion de etapa
     * @param string $etapa Etapa
     * @param int $inm_comprador_id Id de comprador
     * @param PDO $link Conexion a la base de datos
     * @param string $pr_proceso_descripcion Descripcion de proceso
     * @param string $tabla Entidad de ejecucion
     * @return array|stdClass
     * @version 2.48.0
     */
    private function inm_comprador_etapa_alta(string $accion, string $etapa, int $inm_comprador_id, PDO $link,
                                              string $pr_proceso_descripcion, string $tabla): array|stdClass
    {

        $accion = trim($accion);
        $etapa = trim($etapa);
        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $tabla = trim($tabla);

        $valida =$this->valida_data_etapa(accion: $accion,etapa:  $etapa,
            pr_proceso_descripcion:  $pr_proceso_descripcion,tabla:  $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos de etapa', data: $valida);
        }

        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }

        $pr_etapa_proceso = $this->pr_etapa_proceso(accion: $accion, etapa: $etapa, link: $link,
            pr_proceso_descripcion: $pr_proceso_descripcion, tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener pr_etapa_proceso', data: $pr_etapa_proceso);
        }

        $inm_comprador_etapa_ins = $this->inm_comprador_etapa_ins(inm_comprador_id: $inm_comprador_id,
            pr_etapa_proceso:  $pr_etapa_proceso);;
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener inm_comprador_etapa_ins',
                data: $inm_comprador_etapa_ins);
        }

        $inm_comprador_etapa = (new inm_comprador_etapa(link: $link))->alta_registro(
            registro: $inm_comprador_etapa_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $inm_comprador_etapa);
        }
        return $inm_comprador_etapa;
    }

    /**
     * Genera un registro para insersion de comprador etapa
     * @param int $inm_comprador_id Id de comprador
     * @param array $pr_etapa_proceso datos de proceso
     * @return array
     * @version 2.47.0
     */
    private function inm_comprador_etapa_ins(int $inm_comprador_id, array $pr_etapa_proceso): array
    {
        $keys = array('pr_etapa_proceso_id');
        $valida = $this->validacion->valida_ids(keys: $keys, registro: $pr_etapa_proceso);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar pr_etapa_proceso', data: $valida);
        }
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }

        $inm_comprador_etapa_ins['pr_etapa_proceso_id'] = $pr_etapa_proceso['pr_etapa_proceso_id'];
        $inm_comprador_etapa_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_comprador_etapa_ins['fecha'] = date('Y-m-d');
        return $inm_comprador_etapa_ins;
    }

    /**
     * Inserta el subproceso del comprador
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @param int $pr_sub_proceso_id Identificador de subproceso
     * @return array|stdClass
     * @version 2.42.0
     */
    private function inserta_sub_proceso(int $inm_comprador_id, PDO $link, int $pr_sub_proceso_id): array|stdClass
    {
        $inm_comprador_proceso_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_comprador_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso_id;
        $inm_comprador_proceso_ins['fecha'] = date('Y-m-d');

        $valida = (new inm_comprador_proceso(link: $link))->valida_init(key_entidad_base_id: 'inm_comprador_id',
            key_entidad_id: 'pr_sub_proceso_id', registro: $inm_comprador_proceso_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }

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

    /**
     * Valida un numero telefonico sea correcto
     * @param string $key_lada Key de la lada
     * @param string $key_numero Key del numero
     * @param array $registro Registro en proceso
     * @return array|string
     * @version 1.189.1
     */
    private function numero_completo(string $key_lada, string $key_numero, array $registro): array|string
    {

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
     * @version 1.188.1
     */
    private function numero_completo_base(string $key_lada, string $key_numero, array $registro): bool|array
    {
        $key_lada = trim($key_lada);
        if($key_lada === ''){
            return $this->error->error(mensaje: 'Error key_lada esta vacio',data:  $key_lada);
        }
        $key_numero = trim($key_numero);
        if($key_numero === ''){
            return $this->error->error(mensaje: 'Error key_numero esta vacio',data:  $key_numero);
        }
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

    /**
     * Obtiene el numero completo con lada y numero
     * @param array $registro Registro en proceso
     * @return array|string
     * @version 2.1.0
     */
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

    /**
     * Ejecuta la insersion de un com_cliente, la relacion entre cliente y comprador y la etapa de un comprador
     * @param string $accion Accion de etapa o ejecucion
     * @param string $etapa Etapa de proceso
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @param string $pr_proceso_descripcion Descripcion de proceso
     * @param array $registro_entrada Registro de tipo comprador
     * @param string $tabla Tabla de ejecucion
     * @return array|stdClass
     */
    final public function posterior_alta(
        string $accion,string $etapa, int $inm_comprador_id, PDO $link, string $pr_proceso_descripcion,
        array $registro_entrada, string $tabla): array|stdClass
    {

        if(!isset($registro_entrada['cat_sat_forma_pago_id'])|| $registro_entrada['cat_sat_forma_pago_id'] === '' ||
            (int)$registro_entrada['cat_sat_forma_pago_id'] === -1){
            $registro_entrada['cat_sat_forma_pago_id'] = 99;
        }
        if(!isset($registro_entrada['cat_sat_metodo_pago_id'])|| $registro_entrada['cat_sat_metodo_pago_id'] === '' ||
            (int)$registro_entrada['cat_sat_metodo_pago_id'] === -1){
            $registro_entrada['cat_sat_metodo_pago_id'] = 2;
        }
        if(!isset($registro_entrada['cat_sat_moneda_id'])|| $registro_entrada['cat_sat_moneda_id'] === '' ||
            (int)$registro_entrada['cat_sat_moneda_id'] === -1){
            $registro_entrada['cat_sat_moneda_id'] = 161;
        }
        if(!isset($registro_entrada['cat_sat_regimen_fiscal_id'])|| $registro_entrada['cat_sat_regimen_fiscal_id'] === '' ||
            (int)$registro_entrada['cat_sat_regimen_fiscal_id'] === -1){
            $registro_entrada['cat_sat_regimen_fiscal_id'] = 605;
        }
        if(!isset($registro_entrada['cat_sat_tipo_persona_id'])|| $registro_entrada['cat_sat_tipo_persona_id'] === '' ||
            (int)$registro_entrada['cat_sat_tipo_persona_id'] === -1){
            $registro_entrada['cat_sat_tipo_persona_id'] = 5;
        }
        if(!isset($registro_entrada['cat_sat_uso_cfdi_id'])|| $registro_entrada['cat_sat_uso_cfdi_id'] === '' ||
            (int)$registro_entrada['cat_sat_uso_cfdi_id'] === -1){
            $registro_entrada['cat_sat_uso_cfdi_id'] = 3;
        }
        if(!isset($registro_entrada['com_tipo_cliente_id'])|| $registro_entrada['com_tipo_cliente_id'] === '' ||
            (int)$registro_entrada['com_tipo_cliente_id'] === -1){
            $registro_entrada['com_tipo_cliente_id'] = 7;
        }

        $valida = $this->valida_transacciones(inm_comprador_id: $inm_comprador_id,
            registro_entrada:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        $tabla = trim($tabla);

        if($tabla === ''){
            return $this->error->error(mensaje: 'Error tabla esta vacia', data: $tabla);
        }

        $accion = trim($accion);
        if($accion === ''){
            return $this->error->error(mensaje: 'Error accion esta vacia', data: $accion);
        }

        $etapa = trim($etapa);
        if($etapa === ''){
            return $this->error->error(mensaje: 'Error etapa esta vacia', data: $etapa);
        }

        $integra_relacion_com_cliente = (new _base_comprador())->integra_relacion_com_cliente(
            inm_comprador_id: $inm_comprador_id, link: $link, registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $integra_relacion_com_cliente);
        }

        $sub_proceso = $this->sub_proceso(inm_comprador_id: $inm_comprador_id,
            link: $link, pr_proceso_descripcion: 'INMOBILIARIA CLIENTES', pr_sub_proceso_descripcion: 'ALTA',
            tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sub proceso', data: $sub_proceso);
        }

        $r_inm_comprador_etapa = $this->inm_comprador_etapa_alta(accion: $accion, etapa: $etapa,
            inm_comprador_id: $inm_comprador_id, link: $link, pr_proceso_descripcion: $pr_proceso_descripcion,
            tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_inm_comprador_etapa);
        }

        $data = new stdClass();
        $data->integra_relacion_com_cliente = $integra_relacion_com_cliente;
        $data->sub_proceso = $sub_proceso;
        $data->r_inm_comprador_etapa = $r_inm_comprador_etapa;
        return $data;
    }

    /**
     * Obtiene el proceso de ejecucion del comprador
     * @param string $accion Accion a validar
     * @param string $etapa Etapa a validar
     * @param PDO $link Conexion a la base de datos
     * @param string $pr_proceso_descripcion Descripcion del proceso parent
     * @param string $tabla tabla de aplicacion
     * @return array
     * @version 2.47.0
     */
    private function pr_etapa_proceso(string $accion, string $etapa, PDO $link,
                                      string $pr_proceso_descripcion, string $tabla): array
    {

        $accion = trim($accion);
        $etapa = trim($etapa);
        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $tabla = trim($tabla);

        $valida =$this->valida_data_etapa(accion: $accion,etapa:  $etapa,
            pr_proceso_descripcion:  $pr_proceso_descripcion,tabla:  $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos de etapa', data: $valida);
        }


        $filtro = $this->filtro_etapa_proceso(accion: $accion, etapa: $etapa,
            pr_proceso_descripcion: $pr_proceso_descripcion, tabla: $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener filtro', data: $filtro);
        }
        $r_pr_etapa_proceso = (new pr_etapa_proceso(link: $link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener etapa proceso', data: $r_pr_etapa_proceso);
        }

        if($r_pr_etapa_proceso->n_registros === 0){
            return $this->error->error(mensaje: 'Error r_pr_etapa_proceso no existe', data: $r_pr_etapa_proceso);
        }

        if($r_pr_etapa_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad', data: $r_pr_etapa_proceso);
        }

        return $r_pr_etapa_proceso->registros[0];
    }

    /**
     * Obtiene el sub proceso definido para operar registro
     * @param PDO $link Conexion a la base de datos
     * @param string $pr_proceso_descripcion Descripcion de proceso
     * @param string $pr_sub_proceso_descripcion Descripcion de subproceso
     * @param string $tabla Entidad name
     * @return array
     * @version 2.39.0
     */
    private function pr_sub_proceso(PDO $link, string $pr_proceso_descripcion, string $pr_sub_proceso_descripcion,
                                    string $tabla): array
    {

        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $pr_sub_proceso_descripcion = trim($pr_sub_proceso_descripcion);
        $tabla = trim($tabla);


        $valida = $this->valida_sub_proceso(pr_proceso_descripcion: $pr_proceso_descripcion,
            pr_sub_proceso_descripcion:  $pr_sub_proceso_descripcion,tabla:  $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos de entrada', data: $valida);
        }

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

    /**
     * Transacciona el sub proceso a ajustar en la entidad de comprador
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @param string $pr_proceso_descripcion Descripcion del proceso
     * @param string $pr_sub_proceso_descripcion Descripcion del sub proceso
     * @param string $tabla Tabla de integracion
     * @return array
     * @version 2.42.0
     */
    private function sub_proceso(int $inm_comprador_id, PDO $link, string $pr_proceso_descripcion,
                                 string $pr_sub_proceso_descripcion, string $tabla): array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0', data: $inm_comprador_id);
        }

        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $pr_sub_proceso_descripcion = trim($pr_sub_proceso_descripcion);
        $tabla = trim($tabla);

        $valida = $this->valida_sub_proceso(pr_proceso_descripcion: $pr_proceso_descripcion,
            pr_sub_proceso_descripcion:  $pr_sub_proceso_descripcion,tabla:  $tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar datos de entrada', data: $valida);
        }

        $pr_sub_proceso = $this->pr_sub_proceso(link: $link, pr_proceso_descripcion: $pr_proceso_descripcion,
            pr_sub_proceso_descripcion: $pr_sub_proceso_descripcion, tabla: $tabla);
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

    /**
     * Valida elementos base de comprador
     * @param array $registro Registro en proceso
     * @return array|true
     * @version 2.5.0
     */
    private function valida_base_comprador(array $registro): bool|array
    {
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

    /**
     * Valida la entrada de datos de una etapa
     * @param string $accion Accion a validar
     * @param string $etapa Etapa a validar
     * @param string $pr_proceso_descripcion Proceso a validar
     * @param string $tabla Tabla de integracion
     * @return bool|array
     * @version 2.46.0
     */
    private function valida_data_etapa(string $accion, string $etapa, string $pr_proceso_descripcion,
                                       string $tabla): bool|array
    {
        $accion = trim($accion);
        $etapa = trim($etapa);
        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        $tabla = trim($tabla);

        if($accion == ''){
            return $this->error->error(mensaje: 'Error accion esta vacia', data: $accion);
        }
        if($etapa == ''){
            return $this->error->error(mensaje: 'Error etapa esta vacia', data: $etapa);
        }
        if($pr_proceso_descripcion == ''){
            return $this->error->error(mensaje: 'Error pr_proceso_descripcion esta vacia',
                data: $pr_proceso_descripcion);
        }
        if($tabla == ''){
            return $this->error->error(mensaje: 'Error tabla esta vacia', data: $tabla);
        }

        return true;
    }

    /**
     * Valida los datos base de entrada de proceso
     * @param string $pr_proceso_descripcion Descripcion de proceso
     * @param string $pr_sub_proceso_descripcion Descripcion de subproceso
     * @param string $tabla Tabla o entidad de integracion
     * @return bool|array
     * @version 2.42.0
     */
    private function valida_sub_proceso(string $pr_proceso_descripcion, string $pr_sub_proceso_descripcion,
                                        string $tabla): bool|array
    {
        $pr_proceso_descripcion = trim($pr_proceso_descripcion);
        if($pr_proceso_descripcion === ''){
            return $this->error->error(mensaje: 'Error pr_proceso_descripcion esta vacio',
                data: $pr_proceso_descripcion);
        }
        $pr_sub_proceso_descripcion = trim($pr_sub_proceso_descripcion);
        if($pr_sub_proceso_descripcion === ''){
            return $this->error->error(mensaje: 'Error pr_sub_proceso_descripcion esta vacio',
                data: $pr_sub_proceso_descripcion);
        }

        $tabla = trim($tabla);
        if($tabla === ''){
            return $this->error->error(mensaje: 'Error tabla esta vacio', data: $tabla);
        }

        return true;
    }

    /**
     * Valida las transacciones de insersion de un comprador
     * @param int $inm_comprador_id Identificador de comprador
     * @param array $registro_entrada registro de comprador
     * @return array|true
     */
    final public function valida_transacciones(int $inm_comprador_id, array $registro_entrada): bool|array
    {
        //print_r($registro_entrada);exit;
        $valida = (new _com_cliente())->valida_base_com(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }
        $valida = (new _com_cliente())->valida_data_result_cliente(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        if($inm_comprador_id <=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        return true;
    }
}
