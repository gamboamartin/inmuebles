<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use stdClass;

class _keys_selects{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    private function ajusta_row_data_cliente(controlador_inm_comprador $controler){
        $com_cliente = (new inm_comprador(link: $controler->link))->get_com_cliente(inm_comprador_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }

        $row_upd = $this->row_data_cliente(com_cliente: $com_cliente,controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener row_upd',data:  $row_upd);
        }
        return $row_upd;
    }

    private function base(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd){
        if(!isset($row_upd->com_tipo_cliente_id)){
            $row_upd->com_tipo_cliente_id = -1;
        }
        if(!isset($row_upd->inm_estado_civil_id)){
            $row_upd->inm_estado_civil_id = -1;
        }

        $columns_ds = array('com_tipo_cliente_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_tipo_cliente_id',
            keys_selects: $keys_selects, id_selected: $row_upd->com_tipo_cliente_id, label: 'Tipo de Cliente', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_estado_civil_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_estado_civil_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_estado_civil_id, label: 'Estado Civil',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function hiddens(controlador_inm_ubicacion|controlador_inm_comprador $controler, string $funcion){
        $in_registro_id = $controler->html->hidden(name:'registro_id',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $in_registro_id);
        }
        $id_retorno = $controler->html->hidden(name:'id_retorno',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al id_retorno',data:  $id_retorno);
        }

        $seccion_retorno = $controler->html->hidden(name:'seccion_retorno',value: $controler->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $seccion_retorno);
        }
        $btn_action_next = $controler->html->hidden(name:'btn_action_next',value: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $btn_action_next);
        }

        $precio_operacion = $controler->html->input_monto(cols: 12, row_upd: new stdClass(),value_vacio: false,
            name: 'precio_operacion',place_holder: 'Precio de operacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio operacion',data:  $precio_operacion);
        }

        $data = new stdClass();
        $data->id_retorno = $id_retorno;
        $data->seccion_retorno = $seccion_retorno;
        $data->btn_action_next = $btn_action_next;
        $data->precio_operacion = $precio_operacion;
        $data->in_registro_id = $in_registro_id;

        return $data;

    }

    final public function init(controlador_inm_comprador $controler, stdClass $row_upd){
        $keys_selects = array();

        $keys_selects = (new _keys_selects())->ks_infonavit(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new _dps_init())->ks_dp(controler: $controler,keys_selects:  $keys_selects,row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->ks_fiscales(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->base(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Inicializa los key id de elementos fiscales
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass
     */
    private function init_row_upd_fiscales(stdClass $row_upd): stdClass
    {
        if(!isset($row_upd->cat_sat_regimen_fiscal_id)){
            $row_upd->cat_sat_regimen_fiscal_id = 605;
        }
        if(!isset($row_upd->cat_sat_moneda_id)){
            $row_upd->cat_sat_moneda_id = 161;
        }
        if(!isset($row_upd->cat_sat_forma_pago_id)){
            $row_upd->cat_sat_forma_pago_id = 99;
        }
        if(!isset($row_upd->cat_sat_metodo_pago_id)){
            $row_upd->cat_sat_metodo_pago_id = 2;
        }
        if(!isset($row_upd->cat_sat_uso_cfdi_id)){
            $row_upd->cat_sat_uso_cfdi_id = 22;
        }
        if(!isset($row_upd->cat_sat_tipo_persona_id)){
            $row_upd->cat_sat_tipo_persona_id = 5;
        }
        if(!isset($row_upd->bn_cuenta_id)){
            $row_upd->bn_cuenta_id = -1;
        }
        return $row_upd;
    }

    /**
     * Inicializa los elementos por default de datos de infonavit
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass
     * @version 1.41.0
     */
    private function init_row_upd_infonavit(stdClass $row_upd): stdClass
    {
        if(!isset($row_upd->inm_producto_infonavit_id)){
            $row_upd->inm_producto_infonavit_id = -1;
        }
        if(!isset($row_upd->inm_attr_tipo_credito_id)){
            $row_upd->inm_attr_tipo_credito_id = -1;
        }
        if(!isset($row_upd->inm_destino_credito_id)){
            $row_upd->inm_destino_credito_id = -1;
        }
        if(!isset($row_upd->inm_plazo_credito_sc_id)){
            $row_upd->inm_plazo_credito_sc_id = 7;
        }
        if(!isset($row_upd->inm_tipo_discapacidad_id)){
            $row_upd->inm_tipo_discapacidad_id = 5;
        }
        if(!isset($row_upd->inm_persona_discapacidad_id)){
            $row_upd->inm_persona_discapacidad_id = 6;
        }
        return $row_upd;
    }

    final public function inputs_form_base(
        string $btn_action_next, controlador_inm_comprador|controlador_inm_ubicacion $controler,
        string $id_retorno, string $in_registro_id, string $inm_comprador_id, string $inm_ubicacion_id,
        string $precio_operacion, string $seccion_retorno): array|stdClass
    {
        $controler->inputs->id_retorno = $id_retorno;
        $controler->inputs->btn_action_next = $btn_action_next;
        $controler->inputs->seccion_retorno = $seccion_retorno;
        $controler->inputs->registro_id = $in_registro_id;
        $controler->inputs->inm_comprador_id = $inm_comprador_id;
        $controler->inputs->precio_operacion = $precio_operacion;
        $controler->inputs->inm_ubicacion_id = $inm_ubicacion_id;

        return $controler->inputs;
    }

    final public function key_selects_base(controlador_inm_comprador $controler){
        $row_upd = $this->ajusta_row_data_cliente(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener row_upd',data:  $row_upd);
        }


        $keys_selects = $this->init(controler: $controler,row_upd: $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function keys_disabled(array $keys_selects): array
    {
        $keys_selects['com_tipo_cliente_id']->disabled = true;

        $keys_selects['nss'] = new stdClass();
        $keys_selects['nss']->disabled = true;

        $keys_selects['curp'] = new stdClass();
        $keys_selects['curp']->disabled = true;

        $keys_selects['rfc'] = new stdClass();
        $keys_selects['rfc']->disabled = true;

        $keys_selects['apellido_paterno'] = new stdClass();
        $keys_selects['apellido_paterno']->disabled = true;

        $keys_selects['apellido_materno'] = new stdClass();
        $keys_selects['apellido_materno']->disabled = true;

        $keys_selects['nombre'] = new stdClass();
        $keys_selects['nombre']->disabled = true;
        return $keys_selects;
    }

    private function ks_fiscales(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $row_upd = $this->init_row_upd_fiscales(row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row_upd',data:  $row_upd);
        }

        $columns_ds = array('cat_sat_regimen_fiscal_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_regimen_fiscal_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_regimen_fiscal_id, label: 'Regimen Fiscal', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_moneda_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_moneda_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_moneda_id, label: 'Moneda', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_forma_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_forma_pago_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_forma_pago_id, label: 'Forma de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_metodo_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_metodo_pago_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_metodo_pago_id, label: 'Metodo de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_uso_cfdi_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_uso_cfdi_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_uso_cfdi_id, label: 'Uso de CFDI', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_tipo_persona_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_tipo_persona_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_tipo_persona_id, label: 'Tipo de Persona', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('bn_cuenta_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'bn_cuenta_id',
            keys_selects: $keys_selects, id_selected: $row_upd->bn_cuenta_id, label: 'Cuenta Deposito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Integra los parametros para la generacion de inputs de tipo infonavit
     * @param controlador_inm_comprador $controler  Controlador en ejecucion
     * @param array $keys_selects Configuraciones precargadas
     * @param stdClass $row_upd Registro en proceso
     * @return array
     * @version 1.42.0
     */
    private function ks_infonavit(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $row_upd = $this->init_row_upd_infonavit(row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializa row_upd',data:  $row_upd);
        }

        $columns_ds[] = 'inm_producto_infonavit_descripcion';
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_producto_infonavit_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_producto_infonavit_id, label: 'Producto', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_tipo_credito_descripcion';
        $columns_ds[] = 'inm_attr_tipo_credito_descripcion';
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_attr_tipo_credito_id, label: 'Tipo de Credito',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_destino_credito_descripcion';

        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_destino_credito_id, label: 'Destino del Credito',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_plazo_credito_sc_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_plazo_credito_sc_id,
            label: 'Plazo Segundo Credito');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_tipo_discapacidad_descripcion';
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_tipo_discapacidad_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_tipo_discapacidad_id, label: 'Tipo de Discapacidad',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_persona_discapacidad_descripcion';
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_persona_discapacidad_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_persona_discapacidad_id, label: 'Persona Discapacidad',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    private function row_data_cliente(array $com_cliente, controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->rfc = $com_cliente['com_cliente_rfc'];
        $controler->row_upd->numero_exterior = $com_cliente['com_cliente_numero_exterior'];
        $controler->row_upd->numero_interior = $com_cliente['com_cliente_numero_interior'];
        $controler->row_upd->telefono = $com_cliente['com_cliente_telefono'];
        $controler->row_upd->dp_pais_id = $com_cliente['dp_pais_id'];
        $controler->row_upd->dp_estado_id = $com_cliente['dp_estado_id'];
        $controler->row_upd->dp_municipio_id = $com_cliente['dp_municipio_id'];
        $controler->row_upd->dp_cp_id = $com_cliente['dp_cp_id'];
        $controler->row_upd->dp_colonia_postal_id = $com_cliente['dp_colonia_postal_id'];
        $controler->row_upd->dp_calle_pertenece_id = $com_cliente['dp_calle_pertenece_id'];
        $controler->row_upd->com_tipo_cliente_id = $com_cliente['com_tipo_cliente_id'];
        return $controler->row_upd;
    }
}