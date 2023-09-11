<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_sub_proceso;
use PDO;
use stdClass;


class inm_comprador extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_comprador';
        $columnas = array($tabla=>false,'inm_producto_infonavit'=>$tabla,'inm_attr_tipo_credito'=>$tabla,
            'inm_tipo_credito'=>'inm_attr_tipo_credito','inm_destino_credito'=>$tabla,'inm_plazo_credito_sc'=>$tabla,
            'inm_tipo_discapacidad'=>$tabla,'inm_persona_discapacidad'=>$tabla,'inm_estado_civil'=>$tabla,
            'bn_cuenta'=>$tabla,'org_sucursal'=>'bn_cuenta','org_empresa'=>'org_sucursal');

        $campos_obligatorios = array('apellido_paterno','bn_cuenta_id','cel_com','correo_com','curp',
            'descuento_pension_alimenticia_dh', 'descuento_pension_alimenticia_fc', 'es_segundo_credito',
            'inm_attr_tipo_credito_id', 'inm_destino_credito_id','inm_estado_civil_id','inm_persona_discapacidad_id',
            'inm_producto_infonavit_id', 'inm_plazo_credito_sc_id', 'inm_tipo_discapacidad_id','lada_com','lada_nep',
            'monto_ahorro_voluntario', 'monto_credito_solicitado_dh','nombre','nombre_empresa_patron', 'nrp_nep',
            'numero_com','numero_nep');

        $columnas_extra= array();
        $renombres['dp_calle_pertenece_empresa']['nombre_original']= 'dp_calle_pertenece';
        $renombres['dp_calle_pertenece_empresa']['enlace']= 'org_empresa';
        $renombres['dp_calle_pertenece_empresa']['key']= 'id';
        $renombres['dp_calle_pertenece_empresa']['key_enlace']= 'dp_calle_pertenece_id';

        $renombres['dp_colonia_postal_empresa']['nombre_original']= 'dp_colonia_postal';
        $renombres['dp_colonia_postal_empresa']['enlace']= 'dp_calle_pertenece_empresa';
        $renombres['dp_colonia_postal_empresa']['key']= 'id';
        $renombres['dp_colonia_postal_empresa']['key_enlace']= 'dp_colonia_postal_id';

        $renombres['dp_cp_empresa']['nombre_original']= 'dp_cp';
        $renombres['dp_cp_empresa']['enlace']= 'dp_colonia_postal_empresa';
        $renombres['dp_cp_empresa']['key']= 'id';
        $renombres['dp_cp_empresa']['key_enlace']= 'dp_cp_id';

        $renombres['dp_municipio_empresa']['nombre_original']= 'dp_municipio';
        $renombres['dp_municipio_empresa']['enlace']= 'dp_cp_empresa';
        $renombres['dp_municipio_empresa']['key']= 'id';
        $renombres['dp_municipio_empresa']['key_enlace']= 'dp_municipio_id';

        $renombres['dp_estado_empresa']['nombre_original']= 'dp_estado';
        $renombres['dp_estado_empresa']['enlace']= 'dp_municipio_empresa';
        $renombres['dp_estado_empresa']['key']= 'id';
        $renombres['dp_estado_empresa']['key_enlace']= 'dp_estado_id';

        $atributos_criticos = array('apellido_materno','apellido_paterno','bn_cuenta_id','cel_com','curp','correo_com',
            'descuento_pension_alimenticia_dh', 'descuento_pension_alimenticia_fc','es_segundo_credito',
            'extension_nep','genero', 'inm_attr_tipo_credito_id', 'inm_destino_credito_id','inm_estado_civil_id',
            'inm_persona_discapacidad_id', 'inm_plazo_credito_sc_id', 'inm_producto_infonavit_id',
            'inm_tipo_discapacidad_id','lada_com','lada_nep', 'monto_ahorro_voluntario', 'monto_credito_solicitado_dh',
            'nombre', 'nombre_empresa_patron', 'nrp_nep','numero_com','numero_nep');


        $tipo_campos['lada_com'] = 'lada';
        $tipo_campos['lada_nep'] = 'lada';
        $tipo_campos['numero_nep'] = 'tel_sin_lada';
        $tipo_campos['numero_com'] = 'tel_sin_lada';
        $tipo_campos['curp'] = 'curp';
        $tipo_campos['nss'] = 'nss';
        $tipo_campos['cel_com'] = 'telefono_mx';
        $tipo_campos['correo_com'] = 'correo';


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Comprador de Vivienda';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro_entrada = $this->registro;

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->descripcion(registro: $this->registro );
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }

            $this->registro['descripcion'] = $descripcion;
        }
        if(!isset($this->registro['inm_plazo_credito_sc_id'])){
            $this->registro['inm_plazo_credito_sc_id'] = 7;
        }
        if(!isset($this->registro['inm_tipo_discapacidad_id'])){
            $this->registro['inm_tipo_discapacidad_id'] = 5;
        }
        if(!isset($this->registro['inm_persona_discapacidad_id'])){
            $this->registro['inm_persona_discapacidad_id'] = 6;
        }

        $keys = array('lada_nep','numero_nep','lada_com','numero_com');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $numero_completo_nep = $this->registro['lada_nep'].$this->registro['numero_nep'];

        $numero_completo_nep = trim($numero_completo_nep);
        if($numero_completo_nep === ''){
            return $this->error->error(mensaje: 'Error numero_completo_nep esta vacio',data:  $numero_completo_nep);
        }

        if(strlen($numero_completo_nep)!==10){
            return $this->error->error(mensaje: 'Error numero_completo_nep no es de 10 digitos',data:  $numero_completo_nep);
        }

        $numero_completo_com = $this->registro['lada_com'].$this->registro['numero_com'];

        $numero_completo_com = trim($numero_completo_com);
        if($numero_completo_com === ''){
            return $this->error->error(mensaje: 'Error numero_completo_com esta vacio',data:  $numero_completo_com);
        }

        if(strlen($numero_completo_com)!==10){
            return $this->error->error(mensaje: 'Error numero_completo_com no es de 10 digitos',data:  $numero_completo_com);
        }

        $valida = $this->validacion->valida_rfc(key: 'rfc',registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar rfc',data:  $valida);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }

        $integra_relacion_com_cliente = $this->integra_relacion_com_cliente(inm_comprador_id: $r_alta_bd->registro_id,
            registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $integra_relacion_com_cliente);
        }

        $filtro['adm_seccion.descripcion'] = $this->tabla;
        $filtro['pr_sub_proceso.descripcion'] = 'ALTA';
        $filtro['pr_proceso.descripcion'] = 'INMOBILIARIA CLIENTES';
        $existe = (new pr_sub_proceso(link: $this->link))->existe(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si existe sub proceso', data: $existe);
        }
        if(!$existe){
            return $this->error->error(mensaje: 'Error no existe sub proceso definido', data: $filtro);
        }
        $r_pr_sub_proceso = (new pr_sub_proceso(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener sub proceso', data: $r_pr_sub_proceso);
        }
        if($r_pr_sub_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad', data: $r_pr_sub_proceso);
        }
        if($r_pr_sub_proceso->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe sub proceso', data: $r_pr_sub_proceso);
        }

        $pr_sub_proceso = $r_pr_sub_proceso->registros[0];



        $inm_comprador_proceso_ins['inm_comprador_id'] = $r_alta_bd->registro_id;
        $inm_comprador_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso['pr_sub_proceso_id'];
        $inm_comprador_proceso_ins['fecha'] = date('Y-m-d');
        $r_alta_sp = (new inm_comprador_proceso(link: $this->link))->alta_registro(registro: $inm_comprador_proceso_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar sub proceso en comprador', data: $r_alta_sp);
        }


        return $r_alta_bd;

    }

    final public function asigna_nuevo_co_acreditado_bd(int $inm_comprador_id, array $inm_co_acreditado){

        $alta_inm_co_acreditado = (new inm_co_acreditado(link: $this->link))->alta_registro(registro: $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar alta_inm_co_acreditado',data:  $alta_inm_co_acreditado);
        }
        $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $alta_inm_co_acreditado->registro_id;
        $inm_rel_co_acred_ins['inm_comprador_id'] = $inm_comprador_id;

        $alta_inm_rel_co_acred = (new inm_rel_co_acred(link: $this->link))->alta_registro(registro: $inm_rel_co_acred_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar alta_inm_rel_co_acred',data:  $alta_inm_rel_co_acred);
        }

        $data = new stdClass();
        $data->inm_co_acreditado = $alta_inm_co_acreditado;
        $data->inm_rel_co_acred = $alta_inm_rel_co_acred;

        return $data;

    }

    /**
     * Obtiene el cliente fiscal asignado al comprador de vivienda
     * @param int $com_cliente_id Identificador de cliente
     * @param bool $retorno_obj Retorna un objeto en caso de ser verdadero
     * @return array|object
     * @version 1.64.1
     */
    private function com_cliente(int $com_cliente_id, bool $retorno_obj = false): object|array
    {

        if($com_cliente_id<=0){
            return $this->error->error(mensaje: 'Error com_cliente_id es menor a 0',data:  $com_cliente_id);
        }

        $filtro['com_cliente.id'] = $com_cliente_id;

        $r_com_cliente = (new com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
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

    private function com_cliente_ins(string $numero_interior, string $razon_social, array $registro_entrada): array
    {
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $razon_social = trim($razon_social);
        if($razon_social === ''){
            return $this->error->error(mensaje: 'Error razon_social vacia',data:  $razon_social);
        }

        $com_cliente_ins['razon_social'] = trim($razon_social);
        $com_cliente_ins['rfc'] = $registro_entrada['rfc'];
        $com_cliente_ins['dp_calle_pertenece_id'] = $registro_entrada['dp_calle_pertenece_id'];
        $com_cliente_ins['numero_exterior'] = $registro_entrada['numero_exterior'];
        $com_cliente_ins['numero_interior'] = $numero_interior;
        $com_cliente_ins['telefono'] = $registro_entrada['lada_com'].$registro_entrada['numero_com'];
        $com_cliente_ins['cat_sat_regimen_fiscal_id'] = $registro_entrada['cat_sat_regimen_fiscal_id'];
        $com_cliente_ins['cat_sat_moneda_id'] = $registro_entrada['cat_sat_moneda_id'];
        $com_cliente_ins['cat_sat_forma_pago_id'] = $registro_entrada['cat_sat_forma_pago_id'];
        $com_cliente_ins['cat_sat_metodo_pago_id'] = $registro_entrada['cat_sat_metodo_pago_id'];
        $com_cliente_ins['cat_sat_uso_cfdi_id'] = $registro_entrada['cat_sat_uso_cfdi_id'];
        $com_cliente_ins['cat_sat_tipo_de_comprobante_id'] = 1;
        $com_cliente_ins['codigo'] = $registro_entrada['rfc'];
        $com_cliente_ins['com_tipo_cliente_id'] = $registro_entrada['com_tipo_cliente_id'];
        $com_cliente_ins['cat_sat_tipo_persona_id'] = $registro_entrada['cat_sat_tipo_persona_id'];
        return $com_cliente_ins;
    }

    /**
     * Obtiene los datos par ala generacion de la solicitud de infonavit
     * @param int $inm_comprador_id Comprador en proceso
     * @return array|stdClass
     * @version 1.115.1
     */
    final public function data_pdf(int $inm_comprador_id): array|stdClass
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error al inm_comprador_id debe ser mayor a 0',
                data: $inm_comprador_id);
        }
        $inm_comprador = $this->registro(registro_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener comprador', data: $inm_comprador);
        }

        $imp_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))
            ->imp_rel_comprador_com_cliente(inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener imp_rel_comprador_com_cliente',
                data: $imp_rel_comprador_com_cliente);
        }

        $com_cliente = (new com_cliente(link: $this->link))->registro(
            registro_id: $imp_rel_comprador_com_cliente['com_cliente_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener com_cliente', data: $com_cliente);
        }

        $imp_rel_ubi_comp = (new inm_rel_ubi_comp(link: $this->link))->imp_rel_ubi_comp(
            inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener imp_rel_ubi_comp', data: $imp_rel_ubi_comp);
        }

        $inm_conf_empresa = (new inm_conf_empresa(link: $this->link))->inm_conf_empresa(
            org_empresa_id: $inm_comprador['org_empresa_id']);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener r_inm_conf_empresa', data: $inm_conf_empresa);
        }

        $inm_rel_co_acreditados = (new inm_co_acreditado(link: $this->link))->inm_co_acreditados(
            inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener inm_rel_co_acreditados', data: $inm_rel_co_acreditados);
        }

        $inm_referencias = (new inm_referencia(link: $this->link))->inm_referencias(inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener inm_referencias', data: $inm_referencias);
        }

        $data = new stdClass();
        $data->inm_comprador = $inm_comprador;
        $data->imp_rel_comprador_com_cliente = $imp_rel_comprador_com_cliente;
        $data->com_cliente = $com_cliente;
        $data->imp_rel_ubi_comp = $imp_rel_ubi_comp;
        $data->inm_conf_empresa = $inm_conf_empresa;
        $data->inm_rel_co_acreditados = $inm_rel_co_acreditados;
        $data->inm_referencias = $inm_referencias;

        return $data;

    }

    /**
     * Genera la descripcion de un comprador basado en datos del registro a insertar
     * @param array $registro Registro en proceso
     * @return string|array
     */
    private function descripcion(array $registro): string|array
    {
        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro', data: $valida);
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
        return $descripcion;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        $filtro['inm_comprador.id'] = $id;
        $del = (new inm_rel_comprador_com_cliente(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_comprador_com_cliente',
                data:  $del);
        }
        $del = (new inm_comprador_etapa(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_comprador_etapa',
                data:  $del);
        }
        $del = (new inm_referencia(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_referencia',
                data:  $del);
        }
        $del = (new inm_rel_co_acred(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_co_acred',
                data:  $del);
        }
        $del = (new inm_rel_ubi_comp(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_ubi_comp',
                data:  $del);
        }
        $del = (new inm_comprador_proceso(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_comprador_proceso',
                data:  $del);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar registro de comprador',data:  $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    /**
     * Obtiene los datos del cliente de fc basados en el comprador
     * @param int $inm_comprador_id Comprador id
     * @param bool $retorno_obj Retorna un objeto en caso de ser true
     * @return array|object
     * @version 1.66.1
     */
    final public function get_com_cliente(int $inm_comprador_id, bool $retorno_obj = false): object|array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $imp_rel_comprador_com_cliente = $this->inm_rel_comprador_cliente(inm_comprador_id: $inm_comprador_id);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $imp_rel_comprador_com_cliente);
        }


        $com_cliente = $this->com_cliente(com_cliente_id: $imp_rel_comprador_com_cliente['com_cliente_id'],
            retorno_obj: $retorno_obj);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }
        return $com_cliente;
    }

    private function inm_rel_com_cliente_ins(int $com_cliente_id, int $inm_comprador_id): array
    {
        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = $com_cliente_id;
        return $inm_rel_comprador_com_cliente_ins;
    }

    /**
     * Obtiene la relacion entre un cliente y un comprador
     * @param int $inm_comprador_id Comprador identificador
     * @return array
     * @version 1.63.1
     */
    private function inm_rel_comprador_cliente(int $inm_comprador_id): array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_imp_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(
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

    private function inserta_com_cliente(array $registro_entrada){

        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }


        $com_cliente_ins = $this->row_com_cliente_ins(registro_entrada: $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }

        $r_com_cliente = (new com_cliente(link: $this->link))->alta_registro(registro: $com_cliente_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }

    private function inserta_inm_rel_comprador_com_cliente(int $com_cliente_id, int $inm_comprador_id){
        $inm_rel_comprador_com_cliente_ins = $this->inm_rel_com_cliente_ins(com_cliente_id: $com_cliente_id,
            inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar row rel com cliente', data: $inm_rel_comprador_com_cliente_ins);
        }

        $r_inm_rel_comprador_com_cliente_ins = (new inm_rel_comprador_com_cliente(link: $this->link))->alta_registro(
            registro: $inm_rel_comprador_com_cliente_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion',
                data:  $r_inm_rel_comprador_com_cliente_ins);
        }
        return $r_inm_rel_comprador_com_cliente_ins;
    }

    private function integra_relacion_com_cliente(int $inm_comprador_id, array $registro_entrada){
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $r_com_cliente = $this->transacciona_com_cliente(registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
        }
        $r_inm_rel_comprador_com_cliente = $this->inserta_inm_rel_comprador_com_cliente(
            com_cliente_id: $r_com_cliente->registro_id,inm_comprador_id:  $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar r_inm_rel_comprador_com_cliente',
                data: $r_inm_rel_comprador_com_cliente);
        }
        return $r_inm_rel_comprador_com_cliente;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $r_modifica = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica);
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

        if($aplica_upd_posterior){
            $r_modifica_post = parent::modifica_bd(registro: $row_upd_post,id:  $id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_post);
            }
        }

        $com_cliente_upd = array();
        $com_cliente_upd['razon_social'] = $r_modifica->registro_actualizado->inm_comprador_nombre;
        $com_cliente_upd['razon_social'] .= ' '.$r_modifica->registro_actualizado->inm_comprador_apellido_paterno;
        $com_cliente_upd['razon_social'] .= ' '.$r_modifica->registro_actualizado->inm_comprador_apellido_materno;
        $keys_com_cliente = array('com_tipo_cliente_id','rfc','dp_calle_pertenece_id','numero_exterior',
            'numero_interior','telefono','cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id',
            'cat_sat_metodo_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_persona_id');

        foreach ($keys_com_cliente as $key_com_cliente){
           if(isset($registro[$key_com_cliente])){
               $com_cliente_upd[$key_com_cliente] = $registro[$key_com_cliente];
           }
        }
        if(count($com_cliente_upd) > 0){
            $filtro['inm_comprador.id'] = $id;
            $r_im_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener relacion',data:  $r_im_rel_comprador_com_cliente);
            }
            if($r_im_rel_comprador_com_cliente->n_registros === 0){
                return $this->error->error(mensaje: 'Error inm_rel_comprador_com_cliente no existe',data:  $r_im_rel_comprador_com_cliente);
            }
            if($r_im_rel_comprador_com_cliente->n_registros > 1){
                return $this->error->error(mensaje: 'Error de integridad inm_rel_comprador_com_cliente tiene mas de un registro',data:  $r_im_rel_comprador_com_cliente);
            }
            $im_rel_comprador_com_cliente = $r_im_rel_comprador_com_cliente->registros[0];

            $r_com_cliente = (new com_cliente(link: $this->link))->modifica_bd(registro: $registro,id:  $im_rel_comprador_com_cliente['com_cliente_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
            }
        }

        $keys_co_acreditado = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $inm_co_acreditado_ins = array();
        foreach ($keys_co_acreditado as $campo_co_acreditado){
            $key_co_acreditado = 'inm_co_acreditado_'.$campo_co_acreditado;
            if(isset($registro[$key_co_acreditado])) {
                $value = trim($registro[$key_co_acreditado]);
                if($value !=='') {
                    $inm_co_acreditado_ins[$campo_co_acreditado] = $registro[$key_co_acreditado];
                }
            }
        }

        $aplica_alta_co_acreditado = false;
        if(count($inm_co_acreditado_ins)>0){
            $aplica_alta_co_acreditado = true;
            if(count($inm_co_acreditado_ins) === 1){
                if(isset($inm_co_acreditado_ins['genero'])){
                    $aplica_alta_co_acreditado = false;
                }
            }
        }

        if($aplica_alta_co_acreditado) {
            $alta_inm_co_acreditado = (new inm_co_acreditado(link: $this->link))->alta_registro(
                registro: $inm_co_acreditado_ins);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar co_acreditado', data: $alta_inm_co_acreditado);
            }

            $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $alta_inm_co_acreditado->registro_id;
            $inm_rel_co_acred_ins['inm_comprador_id'] = $this->registro_id;

            $alta_inm_rel_co_acred = (new inm_rel_co_acred(link: $this->link))->alta_registro(registro:$inm_rel_co_acred_ins);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar alta_inm_rel_co_acred', data: $alta_inm_rel_co_acred);
            }

        }




        return $r_modifica;
    }



    private function numero_interior(array $registro_entrada){
        $numero_interior = '';
        if(isset($registro_entrada['numero_interior'])){
            $numero_interior = $registro_entrada['numero_interior'];
        }
        return $numero_interior;
    }

    private function r_com_cliente(array $filtro, array $registro_entrada){
        $r_com_cliente_f = (new com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente_f);
        }
        if($r_com_cliente_f->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe cliente', data: $r_com_cliente_f);
        }
        if($r_com_cliente_f->n_registros > 1){
            return $this->error->error(mensaje: 'Error existe mas de un cliente', data: $r_com_cliente_f);
        }

        $r_com_cliente = new stdClass();
        $r_com_cliente->registro_id = $r_com_cliente_f->registros[0]['com_cliente_id'];

        $row_upd['rfc'] = $registro_entrada['rfc'];

        $razon_social = trim($registro_entrada['nombre']);
        $razon_social .= ' '.trim($registro_entrada['apellido_paterno']);
        $razon_social .= ' '.trim($registro_entrada['apellido_materno']);
        $razon_social = trim($razon_social);

        $row_upd['razon_social'] = $razon_social;
        $row_upd['dp_calle_pertenece_id'] = $registro_entrada['dp_calle_pertenece_id'];
        $row_upd['numero_exterior'] = $registro_entrada['numero_exterior'];

        $telefono = trim($registro_entrada['lada_com']).trim($registro_entrada['numero_com']);

        $row_upd['telefono'] = trim($telefono);
        $row_upd['cat_sat_regimen_fiscal_id'] = $registro_entrada['cat_sat_regimen_fiscal_id'];
        $row_upd['cat_sat_moneda_id'] = $registro_entrada['cat_sat_moneda_id'];
        $row_upd['cat_sat_forma_pago_id'] = $registro_entrada['cat_sat_forma_pago_id'];
        $row_upd['cat_sat_metodo_pago_id'] = $registro_entrada['cat_sat_metodo_pago_id'];
        $row_upd['cat_sat_uso_cfdi_id'] = $registro_entrada['cat_sat_uso_cfdi_id'];
        $row_upd['cat_sat_tipo_persona_id'] = $registro_entrada['cat_sat_tipo_persona_id'];
        $row_upd['com_tipo_cliente_id'] = $registro_entrada['com_tipo_cliente_id'];



        $r_com_cliente_upd = (new com_cliente(link: $this->link))->modifica_bd(registro: $row_upd,
            id:  $r_com_cliente->registro_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al actualizar cliente', data: $r_com_cliente_upd);
        }



        return $r_com_cliente;

    }

    private function razon_social(array $registro_entrada): string
    {
        $razon_social = $registro_entrada['nombre'];
        $razon_social .= $registro_entrada['apellido_paterno'];
        $razon_social .= $registro_entrada['apellido_materno'];
        return $razon_social;
    }

    private function result_com_cliente(bool $existe_cliente, array $filtro, array $registro_entrada){
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        if(!$existe_cliente) {
            $r_com_cliente = $this->inserta_com_cliente(registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
            }
        }
        else{
            $r_com_cliente = $this->r_com_cliente(filtro: $filtro, registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
            }
        }
        return $r_com_cliente;
    }

    private function row_com_cliente_ins(array $registro_entrada){

        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }



        $razon_social = $this->razon_social(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar razon social',data:  $razon_social);
        }

        $numero_interior = $this->numero_interior(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar numero_interior',data:  $numero_interior);
        }

        $com_cliente_ins = $this->com_cliente_ins(numero_interior: $numero_interior,
            razon_social:  $razon_social,registro_entrada:  $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }
        return $com_cliente_ins;
    }

    private function transacciona_com_cliente(array $registro_entrada){
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $filtro['com_cliente.rfc'] = $registro_entrada['rfc'];
        $existe_cliente = (new com_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe_cliente);
        }

        $r_com_cliente = $this->result_com_cliente(existe_cliente: $existe_cliente,filtro:  $filtro,registro_entrada:  $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }


}