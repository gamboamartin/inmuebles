<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_comprador extends _modelo_parent{
    public bool $desde_prospecto = false;
    public function __construct(PDO $link, bool $valida_atributos_criticos = true)
    {
        $tabla = 'inm_comprador';
        $columnas = array($tabla=>false,'inm_producto_infonavit'=>$tabla,'inm_attr_tipo_credito'=>$tabla,
            'inm_tipo_credito'=>'inm_attr_tipo_credito','inm_destino_credito'=>$tabla,'inm_plazo_credito_sc'=>$tabla,
            'inm_tipo_discapacidad'=>$tabla,'inm_persona_discapacidad'=>$tabla,'inm_estado_civil'=>$tabla,
            'bn_cuenta'=>$tabla,'org_sucursal'=>'bn_cuenta','org_empresa'=>'org_sucursal',
            'inm_institucion_hipotecaria'=>$tabla,'inm_sindicato'=>$tabla,'inm_nacionalidad'=>$tabla,
            'inm_ocupacion'=>$tabla,'dp_calle_pertenece'=>$tabla,'dp_calle'=>'dp_calle_pertenece',
            'dp_colonia_postal'=>'dp_calle_pertenece','dp_colonia'=>'dp_colonia_postal','dp_cp'=>'dp_colonia_postal',
            'dp_municipio'=>'dp_cp','dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado');

        $campos_obligatorios = array('apellido_paterno','bn_cuenta_id','cel_com','correo_com','curp',
            'descuento_pension_alimenticia_dh', 'descuento_pension_alimenticia_fc', 'es_segundo_credito',
            'inm_attr_tipo_credito_id', 'inm_destino_credito_id','inm_estado_civil_id','inm_persona_discapacidad_id',
            'inm_producto_infonavit_id', 'inm_plazo_credito_sc_id', 'inm_tipo_discapacidad_id','lada_com','lada_nep',
            'monto_ahorro_voluntario', 'monto_credito_solicitado_dh','nombre','nombre_empresa_patron', 'nrp_nep',
            'numero_com','numero_nep','inm_institucion_hipotecaria_id','inm_sindicato_id','dp_municipio_nacimiento_id',
            'fecha_nacimiento','monto_final','sub_cuenta','descuento','puntos','inm_nacionalidad_id',
            'inm_ocupacion_id','telefono_casa','correo_empresa');

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

        $renombres = (new _base_paquete())->rename_data_nac(enlace: $tabla, renombres: $renombres);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al integrar rename', data: $renombres);
            print_r($error);
            exit;
        }

        $atributos_criticos = array('apellido_materno','apellido_paterno','bn_cuenta_id','cel_com','curp','correo_com',
            'descuento_pension_alimenticia_dh', 'descuento_pension_alimenticia_fc','es_segundo_credito',
            'extension_nep','genero', 'inm_attr_tipo_credito_id', 'inm_destino_credito_id','inm_estado_civil_id',
            'inm_persona_discapacidad_id', 'inm_plazo_credito_sc_id', 'inm_producto_infonavit_id',
            'inm_tipo_discapacidad_id','lada_com','lada_nep', 'monto_ahorro_voluntario', 'monto_credito_solicitado_dh',
            'nombre', 'nombre_empresa_patron', 'nrp_nep','numero_com','numero_nep','inm_institucion_hipotecaria_id',
            'inm_sindicato_id','dp_municipio_nacimiento_id','fecha_nacimiento','monto_final','sub_cuenta','descuento',
            'puntos','inm_nacionalidad_id','inm_ocupacion_id','telefono_casa','correo_empresa');


        $tipo_campos['lada_com'] = 'lada';
        $tipo_campos['lada_nep'] = 'lada';
        $tipo_campos['numero_nep'] = 'tel_sin_lada';
        $tipo_campos['numero_com'] = 'tel_sin_lada';
        $tipo_campos['curp'] = 'curp';
        $tipo_campos['nss'] = 'nss';
        $tipo_campos['cel_com'] = 'telefono_mx';
        $tipo_campos['telefono_casa'] = 'telefono_mx';
        $tipo_campos['correo_com'] = 'correo';
        $tipo_campos['correo_empresa'] = 'correo';


        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos,
            valida_atributos_criticos: $valida_atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Comprador de Vivienda';
    }

    /**
     * Inserta un comprador, un cliente, una relacion entre comprador y cliente proceso comprador y etapa comprador
     * @param array $keys_integra_ds Keys para descripcion select
     * @return array|stdClass
     */
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro_entrada = $this->registro;


        $registro = (new _alta_comprador())->init_row_alta(modelo: $this, registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar registro',data:  $registro);
        }


        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }

        if(!$this->desde_prospecto) {
            $tiene_prospecto = (new inm_comprador(link: $this->link))->tiene_prospecto(
                inm_comprador_id: $r_alta_bd->registro_id);
            if (errores::$error) {
                $this->link->rollBack();
                return $this->error->error(mensaje: 'Error al validar inm_prospecto', data: $tiene_prospecto);
            }

            if (!$tiene_prospecto) {
                $r_alta_prospecto = (new _conversion())->inserta_inm_prospecto(inm_comprador_id: $r_alta_bd->registro_id,
                    modelo: $this);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_prospecto);
                }

                $r_alta_rel = (new _conversion())->inserta_rel_prospecto_cliente(
                    inm_comprador_id: $r_alta_bd->registro_id, inm_prospecto_id: $r_alta_prospecto->registro_id,
                    link: $this->link);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
                }
            }
        }

        if(!isset($registro_entrada['cp'])){

            $inm_prospecto = (new inm_comprador(link: $this->link))->inm_prospecto(inm_comprador_id: $r_alta_bd->registro_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto);
            }
            $registro_entrada['cp'] = $inm_prospecto->dp_cp_codigo;
            $registro_entrada['dp_municipio_id'] = $inm_prospecto->dp_municipio_id;

            //print_r($inm_prospecto);exit;
        }



        $transacciones = (new _alta_comprador())->posterior_alta(
            accion: __FUNCTION__, etapa: 'ALTA', inm_comprador_id: $r_alta_bd->registro_id, link: $this->link,
            pr_proceso_descripcion: 'INMOBILIARIA CLIENTES', registro_entrada: $registro_entrada, tabla: $this->tabla);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar transacciones', data: $transacciones);
        }

        return $r_alta_bd;

    }


    /**
     * Asigna un co acreditado a un comprador
     * @param int $inm_comprador_id Identificador
     * @param array $inm_co_acreditado Registro de co acreditado
     * @return array|stdClass
     */
    final public function asigna_nuevo_co_acreditado_bd(
        int $inm_comprador_id, array $inm_co_acreditado): array|stdClass
    {

        $valida = (new inm_co_acreditado(link: $this->link))->valida_data_alta(inm_co_acreditado: $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inm_co_acreditado',data:  $valida);
        }
        $valida = (new inm_co_acreditado(link: $this->link))->valida_alta(inm_co_acreditado: $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $alta_inm_co_acreditado = (new inm_co_acreditado(link: $this->link))->alta_registro
        (registro: $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar alta_inm_co_acreditado',
                data:  $alta_inm_co_acreditado);
        }
        $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $alta_inm_co_acreditado->registro_id;
        $inm_rel_co_acred_ins['inm_comprador_id'] = $inm_comprador_id;

        $alta_inm_rel_co_acred = (new inm_rel_co_acred(link: $this->link))->alta_registro(
            registro: $inm_rel_co_acred_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar alta_inm_rel_co_acred',
                data:  $alta_inm_rel_co_acred);
        }

        $data = new stdClass();
        $data->inm_co_acreditado = $alta_inm_co_acreditado;
        $data->inm_rel_co_acred = $alta_inm_rel_co_acred;

        return $data;

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
     * Elimina todas las relaciones de comprador y con ella a si misma
     * @relaciones inm_rel_comprador_com_cliente, inm_comprador_etapa, inm_referencia, inm_rel_co_acred,
     *  inm_rel_ubi_comp, inm_comprador_proceso, inm_rel_prospecto_cliente
     * @param int $id Id de registro
     * @return array|stdClass
     * @version 2.51.0
     */
    public function elimina_bd(int $id): array|stdClass
    {
        if($id <= 0){
            return  $this->error->error(mensaje: 'El id no puede ser menor a 0 en '.$this->tabla, data: $id);
        }

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
        $del = (new inm_rel_prospecto_cliente(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_prospecto_cliente',
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
     * @param bool $columnas_en_bruto
     * @param bool $retorno_obj Retorna un objeto en caso de ser true
     * @return array|object
     */
    final public function get_com_cliente(int $inm_comprador_id, bool $columnas_en_bruto = false,
                                          bool $retorno_obj = false): object|array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $imp_rel_comprador_com_cliente = (new _base_comprador())->inm_rel_comprador_cliente(
            inm_comprador_id: $inm_comprador_id,link: $this->link);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $imp_rel_comprador_com_cliente);
        }

        $com_cliente = (new _base_comprador())->com_cliente(com_cliente_id: $imp_rel_comprador_com_cliente['com_cliente_id'],
            link: $this->link, columnas_en_bruto: $columnas_en_bruto, retorno_obj: $retorno_obj);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }
        return $com_cliente;
    }

    /**
     * Obtiene los co acreditados de un comprador
     * @param int $inm_comprador_id Identificador de comprador
     * @return array
     * @version 2.52.0
     */
    final public function get_co_acreditados(int $inm_comprador_id): array
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_rel_co_acredit = (new inm_rel_co_acred(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_rel_co_acredit',data:  $r_inm_rel_co_acredit);
        }
        $rels = $r_inm_rel_co_acredit->registros;
        $co_acreditados = array();
        foreach ($rels as $rel){
            $co_acreditado = (new inm_co_acreditado(link: $this->link))->registro(
                registro_id: $rel['inm_co_acreditado_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener co_acreditado',data:  $co_acreditado);
            }
            $co_acreditados[] = $co_acreditado;
        }
        return $co_acreditados;

    }

    /**
     * Obtiene las referencias de un comprador
     * @param int $inm_comprador_id Comprador id
     * @return array
     * @version 2.53.0
     */
    final public function get_referencias(int $inm_comprador_id): array
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }

        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_referencia = (new inm_referencia(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_inm_referencia',data:  $r_inm_referencia);
        }

        return $r_inm_referencia->registros;

    }

    final public function inm_prospecto(int $inm_comprador_id){

        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_rel_prospecto_cliente = (new inm_rel_prospecto_cliente(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_inm_rel_prospecto_cliente',
                data:  $r_inm_rel_prospecto_cliente);
        }
        if($r_inm_rel_prospecto_cliente->n_registros === 0){
            return $this->error->error(mensaje: 'Error al no existe prospecto relacionado',
                data:  $r_inm_rel_prospecto_cliente);
        }
        if($r_inm_rel_prospecto_cliente->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad prospecto relacionado',
                data:  $r_inm_rel_prospecto_cliente);
        }

        $inm_rel_prospecto_cliente = $r_inm_rel_prospecto_cliente->registros[0];

        $inm_prospecto = (new inm_prospecto(link: $this->link))->registro(
            registro_id: $inm_rel_prospecto_cliente['inm_prospecto_id'],retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_prospecto', data:  $inm_prospecto);
        }

        return $inm_prospecto;

    }

    /**
     * Modifica un registro de tipo comprador, ademas inserta si existe un co acreditado, la relacion y si existe
     * referencia inserta o modifica
     * @param array $registro Registro en proceso
     * @param int $id Id de comprador
     * @param bool $reactiva si reactiva se brinca validaciones de cancelacion
     * @param array $keys_integra_ds campos para generar una descripcion select
     * @return array|stdClass
     */
    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if($id<=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0',data: $registro);
        }

        $r_modifica = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica);
        }

        $transacciones = (new _base_comprador())->transacciones_posterior_upd(inm_comprador_upd: $registro,
            inm_comprador_id:  $id,modelo_inm_comprador:  $this,r_modifica:  $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar transacciones',data:  $transacciones);
        }

        return $r_modifica;
    }

    /**
     * Transacciona un elemento para su actualizacion posterior
     * @param stdClass $data_upd Datos de actualizacion
     * @param int $id Id de comprador
     * @return array|stdClass
     */
    private function r_modifica_post(stdClass $data_upd, int $id): array|stdClass
    {
        if($id <=0){
            return  $this->error->error(mensaje: 'Error al obtener registro id debe ser mayor a 0', data: $id);
        }

        $registro_previo = $this->registro(registro_id: $id,columnas_en_bruto: true,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro_previo',data:  $registro_previo);
        }
        if(!isset($data_upd->row_upd_post['descripcion'])){
            $data_upd->row_upd_post['descripcion'] = $registro_previo->descripcion;
        }

        $r_modifica_post = parent::modifica_bd(registro: $data_upd->row_upd_post,id:  $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_post);
        }
        return $r_modifica_post;
    }

    /**
     * Integra el resultado post upd si es que aplica cambio
     * @param stdClass $data_upd Datos de upd
     * @param int $id Id de comprador
     * @return array|stdClass
     */
    private function result_upd_post(stdClass $data_upd, int $id): array|stdClass
    {
        if($id <= 0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0',data:  $id);
        }
        if(!isset($data_upd->aplica_upd_posterior)){
            return $this->error->error(mensaje: 'Error $data_upd->aplica_upd_posterior no existe',data:  $data_upd);
        }
        $r_modifica_post = new stdClass();
        if($data_upd->aplica_upd_posterior){

            $r_modifica_post = $this->r_modifica_post(data_upd: $data_upd,id:  $id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_post);
            }
        }
        return $r_modifica_post;
    }

    final public function tiene_cliente(int $inm_comprador_id):bool
    {
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $existe = (new inm_rel_comprador_com_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe cliente',data:  $existe);
        }
        return $existe;

    }

    final public function tiene_prospecto(int $inm_comprador_id){
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $existe = (new inm_rel_prospecto_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe prospecto',data:  $existe);
        }

        return $existe;
    }

    /**
     * Ejecuta transacciones posteriores a la actualizacion de un comprador
     * @param int $id Id de comprador
     * @param stdClass $r_modifica resultado de modificacion
     * @return array|stdClass
     */
    final public function upd_post(int $id, stdClass $r_modifica): array|stdClass
    {
        $valida = (new _base_comprador())->valida_r_modifica(r_modifica: $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar r_modifica', data: $valida);
        }
        if($id<=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0',data:  $id);
        }

        $data_upd = (new _base_comprador())->data_upd_post(r_modifica: $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data_upd);
        }

        $r_modifica_post = $this->result_upd_post(data_upd: $data_upd, id: $id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_post);
        }

        $r_modifica_post->data_upd = $data_upd;
        return $r_modifica_post;
    }

}