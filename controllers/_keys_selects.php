<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\js_base\valida;
use stdClass;

class _keys_selects{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Ajusta los elementos para front obtenidos del cliente
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 1.69.1
     */
    private function ajusta_row_data_cliente(controlador_inm_comprador $controler): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id es menor a 0',
                data:  $controler->registro_id);
        }
        $com_cliente = (new inm_comprador(link: $controler->link))->get_com_cliente(
            inm_comprador_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }

        $row_upd = $this->row_data_cliente(com_cliente: $com_cliente,controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener row_upd',data:  $row_upd);
        }
        return $row_upd;
    }

    /**
     * Integra los elementos base de sistema para frontend
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param array $keys_selects Parametros previos
     * @param stdClass $row_upd Registro en proceso
     * @return array
     * @version 1.60.1
     */
    private function base(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {
        if(!isset($row_upd->com_tipo_cliente_id)){
            $row_upd->com_tipo_cliente_id = -1;
        }
        if(!isset($row_upd->inm_estado_civil_id)){
            $row_upd->inm_estado_civil_id = -1;
        }

        $columns_ds = array('com_tipo_cliente_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'com_tipo_cliente_id', keys_selects: $keys_selects, id_selected: $row_upd->com_tipo_cliente_id,
            label: 'Tipo de Cliente', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_estado_civil_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_estado_civil_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_estado_civil_id,
            label: 'Estado Civil', columns_ds: $columns_ds);
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

    /**
     * Inicializa los parametros de los selectores para frontend
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param stdClass $row_upd Registro en proceso
     * @return array
     * @version 1.61.1
     */
    final public function init(controlador_inm_comprador $controler, stdClass $row_upd): array
    {
        $keys_selects = array();

        $keys_selects = (new _keys_selects())->ks_infonavit(controler: $controler, keys_selects: $keys_selects,
            row_upd: $row_upd);
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
     * @version 1.56.1
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

    /**
     * Integra el atributo disabled como true
     * @param string $key key a inicializar e integrar
     * @param array $keys_selects Parametros previos cargados
     * @return array
     * @version 1.82.1
     */
    private function integra_disabled(string $key, array $keys_selects): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data: $key);
        }
        if(is_numeric($key)){
            return $this->error->error(mensaje: 'Error key debe ser un texto',data: $key);
        }
        if(!isset($keys_selects[$key])){
            $keys_selects[$key] = new stdClass();
        }
        $keys_selects[$key]->disabled = true;
        return $keys_selects;
    }

    /**
     * Integra los keys en forma disabled para elementos de consulta
     * @param array $keys Keys a integrar coo disabled
     * @param array $keys_selects parametros previos cargados
     * @return array
     * @version 1.74.1
     */
    private function integra_disableds(array $keys, array $keys_selects): array
    {
        foreach ($keys as $key){
            if(!is_string($key)){
                return $this->error->error(mensaje: 'Error key no es un string',data: $key);
            }
            $key = trim($key);
            if($key === ''){
                return $this->error->error(mensaje: 'Error key esta vacio',data: $key);
            }
            if(is_numeric($key)){
                return $this->error->error(mensaje: 'Error key debe ser un texto',data: $key);
            }
            $keys_selects = $this->integra_disabled(key: $key,keys_selects:  $keys_selects);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integra disabled',data: $keys_selects);
            }
        }
        return $keys_selects;
    }

    /**
     * Ajusta los selects para forms upd
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array
     * @version 1.71.0
     */
    final public function key_selects_base(controlador_inm_comprador $controler): array
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id es menor a 0',
                data:  $controler->registro_id);
        }

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

    final public function keys_co_acreditado(): array
    {

        $keys_co_acreditado['inm_co_acreditado_nss']= array('x'=>16,'y'=>105);
        $keys_co_acreditado['inm_co_acreditado_curp']= array('x'=>64,'y'=>105);
        $keys_co_acreditado['inm_co_acreditado_rfc']= array('x'=>132,'y'=>105);
        $keys_co_acreditado['inm_co_acreditado_apellido_paterno']= array('x'=>16,'y'=>112);
        $keys_co_acreditado['inm_co_acreditado_apellido_materno']= array('x'=>107,'y'=>112);
        $keys_co_acreditado['inm_co_acreditado_nombre']= array('x'=>16,'y'=>119);
        $keys_co_acreditado['inm_co_acreditado_lada']= array('x'=>27,'y'=>129);
        $keys_co_acreditado['inm_co_acreditado_numero']= array('x'=>40,'y'=>129);
        $keys_co_acreditado['inm_co_acreditado_celular']= array('x'=>86,'y'=>129);
        $keys_co_acreditado['inm_co_acreditado_correo']= array('x'=>38,'y'=>138);
        $keys_co_acreditado['inm_co_acreditado_nombre_empresa_patron']= array('x'=>16,'y'=>152);
        $keys_co_acreditado['inm_co_acreditado_nrp']= array('x'=>140,'y'=>152);
        $keys_co_acreditado['inm_co_acreditado_lada_nep']= array('x'=>100,'y'=>158);
        $keys_co_acreditado['inm_co_acreditado_numero_nep']= array('x'=>113,'y'=>158);
        $keys_co_acreditado['inm_co_acreditado_extension_nep']= array('x'=>150,'y'=>158);
        return $keys_co_acreditado;
    }

    /**
     * @param array $keys_selects
     * @return array
     */
    final public function keys_disabled(array $keys_selects): array
    {
        $keys = array('com_tipo_cliente_id','nss','curp','rfc','apellido_paterno','apellido_materno','nombre');

        $keys_selects = $this->integra_disableds(keys: $keys,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integra disabled',data: $keys_selects);
        }

        return $keys_selects;
    }

    final public function keys_referencias(): array
    {

        $keys_referencias['inm_referencia_apellido_paterno']= array('x'=>16,'y'=>177);
        $keys_referencias['inm_referencia_apellido_materno']= array('x'=>16,'y'=>183.5);
        $keys_referencias['inm_referencia_nombre']= array('x'=>16,'y'=>191);
        $keys_referencias['inm_referencia_lada']= array('x'=>27,'y'=>199.5);
        $keys_referencias['inm_referencia_numero']= array('x'=>40,'y'=>199.5);
        $keys_referencias['inm_referencia_celular']= array('x'=>27,'y'=>206);
        $keys_referencias['dp_calle_descripcion']= array('x'=>16,'y'=>212);
        $keys_referencias['inm_referencia_numero_dom']= array('x'=>16,'y'=>217);
        $keys_referencias['dp_colonia_descripcion']= array('x'=>16,'y'=>226);
        $keys_referencias['dp_estado_descripcion']= array('x'=>16,'y'=>234);
        $keys_referencias['dp_municipio_descripcion']= array('x'=>16,'y'=>244);
        $keys_referencias['dp_cp_descripcion']= array('x'=>82,'y'=>244);
        return $keys_referencias;
    }

    final public function keys_referencias_2(): array
    {
        $keys_referencias['inm_referencia_apellido_paterno']= array('x'=>110,'y'=>177);
        $keys_referencias['inm_referencia_apellido_materno']= array('x'=>110,'y'=>183.5);
        $keys_referencias['inm_referencia_nombre']= array('x'=>110,'y'=>191);
        $keys_referencias['inm_referencia_lada']= array('x'=>121,'y'=>199.5);
        $keys_referencias['inm_referencia_numero']= array('x'=>121,'y'=>199.5);
        $keys_referencias['inm_referencia_celular']= array('x'=>134,'y'=>206);
        $keys_referencias['dp_calle_descripcion']= array('x'=>110,'y'=>212);
        $keys_referencias['inm_referencia_numero_dom']= array('x'=>110,'y'=>218);
        $keys_referencias['dp_colonia_descripcion']= array('x'=>110,'y'=>225);
        $keys_referencias['dp_estado_descripcion']= array('x'=>110,'y'=>237);
        $keys_referencias['dp_municipio_descripcion']= array('x'=>110,'y'=>245);
        $keys_referencias['dp_cp_descripcion']= array('x'=>178,'y'=>245);
        return $keys_referencias;
    }

    /**
     * Integra los elementos para la generacion de selects fiscales
     * @param controlador_inm_comprador $controler
     * @param array $keys_selects
     * @param stdClass $row_upd
     * @return array
     * @version 1.58.1
     */
    private function ks_fiscales(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $row_upd = $this->init_row_upd_fiscales(row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row_upd',data:  $row_upd);
        }

        $columns_ds = array('cat_sat_regimen_fiscal_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_regimen_fiscal_id', keys_selects: $keys_selects,
            id_selected: $row_upd->cat_sat_regimen_fiscal_id, label: 'Regimen Fiscal', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_moneda_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_moneda_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_moneda_id, label: 'Moneda',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_forma_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_forma_pago_id', keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_forma_pago_id,
            label: 'Forma de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_metodo_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_metodo_pago_id', keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_metodo_pago_id,
            label: 'Metodo de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_uso_cfdi_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_uso_cfdi_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_uso_cfdi_id, label: 'Uso de CFDI',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_tipo_persona_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_tipo_persona_id', keys_selects: $keys_selects,
            id_selected: $row_upd->cat_sat_tipo_persona_id, label: 'Tipo de Persona', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('bn_cuenta_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'bn_cuenta_id',
            keys_selects: $keys_selects, id_selected: $row_upd->bn_cuenta_id, label: 'Cuenta Deposito',
            columns_ds: $columns_ds);
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

    /**
     * Asigna elementos de cliente para modifica
     * @param array $com_cliente Registro de tipo cliente
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return stdClass|array
     * @version 1.67.1
     */
    private function row_data_cliente(array $com_cliente, controlador_inm_comprador $controler): stdClass|array
    {
        $keys = array('com_cliente_rfc','com_cliente_numero_exterior','com_cliente_telefono','dp_pais_id',
            'dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id','dp_calle_pertenece_id',
            'com_tipo_cliente_id');

        $valida = (new valida())->valida_existencia_keys(keys: $keys,registro:  $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar com_cliente',data:  $valida);
        }

        if(!isset($com_cliente['com_cliente_numero_interior'])){
            $com_cliente['com_cliente_numero_interior'] = '';
        }

        $keys = array('dp_pais_id', 'dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id',
            'dp_calle_pertenece_id', 'com_tipo_cliente_id');

        $valida = (new valida())->valida_ids(keys: $keys,registro:  $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar com_cliente',data:  $valida);
        }


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