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


        $registro = $this->integra_descripcion(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $registro = $this->default_infonavit(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error integrar data default',data:  $registro);
        }

        $this->registro = $registro;

        $keys = array('lada_nep','numero_nep','lada_com','numero_com');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $numero_completo_nep = $this->numero_completo_nep(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero_completo_nep',data:  $numero_completo_nep);
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

        $integra_relacion_com_cliente = (new _base_comprador())->integra_relacion_com_cliente(inm_comprador_id: $r_alta_bd->registro_id,
            link: $this->link, registro_entrada: $registro_entrada);
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
        $imp_rel_comprador_com_cliente = (new _base_comprador())->inm_rel_comprador_cliente(
            inm_comprador_id: $inm_comprador_id,link: $this->link);
        if(errores::$error){
            return $this->error->error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $imp_rel_comprador_com_cliente);
        }

        $com_cliente = (new _base_comprador())->com_cliente(com_cliente_id: $imp_rel_comprador_com_cliente['com_cliente_id'],
            link: $this->link, retorno_obj: $retorno_obj);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }
        return $com_cliente;
    }

    final public function get_co_acreditados(int $inm_comprador_id){
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_rel_co_acredit = (new inm_rel_co_acred(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_rel_co_acredit',data:  $r_inm_rel_co_acredit);
        }
        $rels = $r_inm_rel_co_acredit->registros;
        $co_acreditados = array();
        foreach ($rels as $rel){
            $co_acreditado = (new inm_co_acreditado(link: $this->link))->registro(registro_id: $rel['inm_co_acreditado_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener co_acreditado',data:  $co_acreditado);
            }
            $co_acreditados[] = $co_acreditado;
        }
        return $co_acreditados;

    }

    final public function get_referencias(int $inm_comprador_id){
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_referencia = (new inm_referencia(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_inm_referencia',data:  $r_inm_referencia);
        }

        return $r_inm_referencia->registros;

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

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

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
     * @param array $registro
     * @return array|string
     */
    private function numero_completo_nep(array $registro): array|string
    {
        $numero_completo_nep = $registro['lada_nep'].$registro['numero_nep'];

        $numero_completo_nep = trim($numero_completo_nep);
        if($numero_completo_nep === ''){
            return $this->error->error(mensaje: 'Error numero_completo_nep esta vacio',data:  $numero_completo_nep);
        }

        if(strlen($numero_completo_nep)!==10){
            return $this->error->error(mensaje: 'Error numero_completo_nep no es de 10 digitos',data:  $numero_completo_nep);
        }
        return $numero_completo_nep;
    }

    final public function upd_post(int $id, stdClass $r_modifica){
        $data_upd = (new _base_comprador())->data_upd_post(r_modifica: $r_modifica);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data_upd);
        }

        $r_modifica_post = new stdClass();
        if($data_upd->aplica_upd_posterior){
            $r_modifica_post = parent::modifica_bd(registro: $data_upd->row_upd_post,id:  $id);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_post);
            }
        }
        $r_modifica_post->data_upd = $data_upd;
        return $r_modifica_post;
    }


}