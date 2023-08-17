<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_comprador_html;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use setasign\Fpdi\Tcpdf\Fpdi;
use stdClass;

class controlador_inm_comprador extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_comprador(link: $link);
        $html_ = new inm_comprador_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id:  $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }


        $keys_selects = array();

        $keys_selects = $this->ks_infonavit(keys_selects: $keys_selects, row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->ks_dp(keys_selects: $keys_selects, row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->ks_fiscales(keys_selects: $keys_selects, row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }


        $columns_ds = array('com_tipo_cliente_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_tipo_cliente_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo de Cliente', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $this->row_upd->descuento_pension_alimenticia_dh = 0;
        $this->row_upd->monto_credito_solicitado_dh = 0;
        $this->row_upd->descuento_pension_alimenticia_fc = 0;
        $this->row_upd->monto_ahorro_voluntario = 0;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion', 'es_segundo_credito', 'descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'rfc','apellido_paterno','apellido_materno','nombre','numero_exterior','numero_interior','telefono');
        $keys->selects = array();


        $init_data = array();
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";
        $init_data['inm_attr_tipo_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_destino_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_plazo_credito_sc'] = "gamboamartin\\inmuebles";

        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";

        $init_data['cat_sat_regimen_fiscal'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_moneda'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_forma_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_metodo_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_uso_cfdi'] = "gamboamartin\\cat_sat";
        $init_data['com_tipo_cliente'] = "gamboamartin\\comercial";
        $init_data['cat_sat_tipo_persona'] = "gamboamartin\\cat_sat";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'es_segundo_credito', keys_selects:$keys_selects,
            place_holder: 'Es segundo Credito');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_dh',
            keys_selects:$keys_selects, place_holder: 'Descuento Pension Alimenticia Derechohabiente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_fc',
            keys_selects:$keys_selects, place_holder: 'Descuento Pension Alimenticia Familiar/Corresidente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_credito_solicitado_dh',
            keys_selects:$keys_selects, place_holder: 'Monto Credito Solicitado Derechohabiente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_ahorro_voluntario',
            keys_selects:$keys_selects, place_holder: 'Monto Ahorro Voluntario');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nss',
            keys_selects:$keys_selects, place_holder: 'NSS');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'curp',
            keys_selects:$keys_selects, place_holder: 'CURP');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'rfc',
            keys_selects:$keys_selects, place_holder: 'RFC');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre(s)');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_exterior',
            keys_selects:$keys_selects, place_holder: 'Exterior');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_interior',
            keys_selects:$keys_selects, place_holder: 'Interior', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'telefono',
            keys_selects:$keys_selects, place_holder: 'Telefono');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }



        return $keys_selects;
    }

    private function ks_dp(array $keys_selects, stdClass $row_upd): array
    {
        if(!isset($row_upd->dp_pais_id)){
            $row_upd->dp_pais_id = 151;
        }
        if(!isset($row_upd->dp_estado_id)){
            $row_upd->dp_estado_id = 14;
        }
        if(!isset($row_upd->dp_municipio_id)){
            $row_upd->dp_municipio_id = -1;
        }
        if(!isset($row_upd->dp_cp_id)){
            $row_upd->dp_cp_id = -1;
        }
        if(!isset($row_upd->dp_colonia_postal_id)){
            $row_upd->dp_colonia_postal_id = -1;
        }
        if(!isset($row_upd->dp_calle_pertenece_id)){
            $row_upd->dp_calle_pertenece_id = -1;
        }

        $columns_ds = array('dp_pais_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_pais_id, label: 'Pais', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $filtro = array();
        $filtro['dp_pais.id'] = $row_upd->dp_pais_id;

        $columns_ds = array('dp_estado_descripcion');

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_estado_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_estado_id, label: 'Estado', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_estado.id'] = $row_upd->dp_estado_id;

        $columns_ds = array('dp_municipio_descripcion');

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_municipio_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_municipio_id, label: 'Municipio', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_cp_descripcion');

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_cp_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_cp_id, label: 'CP', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_colonia_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_colonia_postal_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_colonia_postal_id, label: 'Colonia', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_calle_pertenece_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_calle_pertenece_id, label: 'Calle', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    private function ks_fiscales(array $keys_selects, stdClass $row_upd): array
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

        $columns_ds = array('cat_sat_regimen_fiscal_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_regimen_fiscal_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_regimen_fiscal_id, label: 'Regimen Fiscal', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_moneda_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_moneda_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_moneda_id, label: 'Moneda', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_forma_pago_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_forma_pago_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_forma_pago_id, label: 'Forma de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_metodo_pago_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_metodo_pago_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_metodo_pago_id, label: 'Metodo de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_uso_cfdi_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_uso_cfdi_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_uso_cfdi_id, label: 'Uso de CFDI', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $columns_ds = array('cat_sat_tipo_persona_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_tipo_persona_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_tipo_persona_id, label: 'Tipo de Persona', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Integra los parametros para la generacion de inputs de tipo infonavit
     * @param array $keys_selects Configuraciones precargadas
     * @param stdClass $row_upd Registro en proceso
     * @return array
     */
    private function ks_infonavit(array $keys_selects, stdClass $row_upd): array
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

        $columns_ds[] = 'inm_producto_infonavit_descripcion';
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_producto_infonavit_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_producto_infonavit_id, label: 'Producto',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $columns_ds = array();
        $columns_ds[] = 'inm_tipo_credito_descripcion';
        $columns_ds[] = 'inm_attr_tipo_credito_descripcion';
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_attr_tipo_credito_id, label: 'Tipo de Credito',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_destino_credito_descripcion';

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_destino_credito_id, label: 'Destino del Credito',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_plazo_credito_sc_id',
            keys_selects: $keys_selects, id_selected: $row_upd->inm_plazo_credito_sc_id,
            label: 'Plazo Segundo Credito');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $filtro['inm_comprador.id'] = $this->registro_id;

        $r_imp_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,header: $header,ws: $ws);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros === 0){
            return $this->retorno_error(
                mensaje: 'Error no existe inm_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,
                header: $header,ws: $ws);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros > 1){
            return $this->retorno_error(
                mensaje: 'Error de integridad existe mas de un inm_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,
                header: $header,ws: $ws);
        }

        $imp_rel_comprador_com_cliente = $r_imp_rel_comprador_com_cliente->registros[0];

        $filtro = array();
        $filtro['com_cliente.id'] = $imp_rel_comprador_com_cliente['com_cliente_id'];

        $r_com_cliente = (new com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener com_cliente',data:  $r_com_cliente,header: $header,ws: $ws);
        }

        if($r_com_cliente->n_registros === 0){
            return $this->retorno_error(
                mensaje: 'Error no existe com_cliente',data:  $r_com_cliente,
                header: $header,ws: $ws);
        }

        if($r_com_cliente->n_registros > 1){
            return $this->retorno_error(
                mensaje: 'Error de integridad existe mas de un com_cliente',data:  $r_com_cliente,
                header: $header,ws: $ws);
        }

        $com_cliente = $r_com_cliente->registros[0];

        $this->row_upd->rfc = $com_cliente['com_cliente_rfc'];
        $this->row_upd->numero_exterior = $com_cliente['com_cliente_numero_exterior'];
        $this->row_upd->numero_interior = $com_cliente['com_cliente_numero_interior'];
        $this->row_upd->telefono = $com_cliente['com_cliente_telefono'];
        $this->row_upd->dp_pais_id = $com_cliente['dp_pais_id'];
        $this->row_upd->dp_estado_id = $com_cliente['dp_estado_id'];
        $this->row_upd->dp_municipio_id = $com_cliente['dp_municipio_id'];
        $this->row_upd->dp_cp_id = $com_cliente['dp_cp_id'];
        $this->row_upd->dp_colonia_postal_id = $com_cliente['dp_colonia_postal_id'];
        $this->row_upd->dp_calle_pertenece_id = $com_cliente['dp_calle_pertenece_id'];


        $keys_selects = array();

        $keys_selects = $this->ks_infonavit(keys_selects: $keys_selects, row_upd: $this->row_upd);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->ks_dp(keys_selects: $keys_selects, row_upd: $this->row_upd);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->ks_fiscales(keys_selects: $keys_selects, row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }


        $columns_ds = array('com_tipo_cliente_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_tipo_cliente_id',
            keys_selects: $keys_selects, id_selected: $com_cliente['com_tipo_cliente_id'], label: 'Tipo de Cliente',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        return $r_modifica;
    }

    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     * @version 1.3.0
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_comprador_id"]["titulo"] = "Id";


        $filtro = array("inm_comprador.id");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    public function solicitud_infonavit(bool $header, bool $ws = false){

        $inm_comprador = $this->modelo->registro(registro_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener comprador',data:  $inm_comprador,
                header: $header,ws:  $ws);
        }

        $filtro['inm_comprador.id'] = $this->registro_id;

        $r_imp_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener imp_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,header: $header,ws: $ws);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros === 0){
            return $this->retorno_error(
                mensaje: 'Error no existe inm_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,
                header: $header,ws: $ws);
        }

        if($r_imp_rel_comprador_com_cliente->n_registros > 1){
            return $this->retorno_error(
                mensaje: 'Error de integridad existe mas de un inm_rel_comprador_com_cliente',data:  $r_imp_rel_comprador_com_cliente,
                header: $header,ws: $ws);
        }

        $imp_rel_comprador_com_cliente = $r_imp_rel_comprador_com_cliente->registros[0];

        $filtro = array();
        $filtro['com_cliente.id'] = $imp_rel_comprador_com_cliente['com_cliente_id'];

        $r_com_cliente = (new com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener com_cliente',data:  $r_com_cliente,header: $header,ws: $ws);
        }

        if($r_com_cliente->n_registros === 0){
            return $this->retorno_error(
                mensaje: 'Error no existe com_cliente',data:  $r_com_cliente,
                header: $header,ws: $ws);
        }

        if($r_com_cliente->n_registros > 1){
            return $this->retorno_error(
                mensaje: 'Error de integridad existe mas de un com_cliente',data:  $r_com_cliente,
                header: $header,ws: $ws);
        }

        $com_cliente = $r_com_cliente->registros[0];

        //print_r($com_cliente);exit;


        //print_r($inm_comprador);exit;

        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile($this->path_base.'templates/solicitud_infonavit.pdf');
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx,null,null,null,null,true);

        $pdf->SetFont('Arial','B', 15);
        $pdf->SetTextColor(0,0,0);

        /**
         * 1. CRÉDITO SOLICITADO
         */
        $pdf->SetXY($inm_comprador['inm_producto_infonavit_x'], $inm_comprador['inm_producto_infonavit_y']);
        $pdf->Write(0, 'X');

        $pdf->SetXY($inm_comprador['inm_tipo_credito_x'], $inm_comprador['inm_tipo_credito_y']);
        $pdf->Write(0, 'X');

        $pdf->SetXY($inm_comprador['inm_attr_tipo_credito_x'], $inm_comprador['inm_attr_tipo_credito_y']);
        $pdf->Write(0, 'X');

        $pdf->SetXY($inm_comprador['inm_destino_credito_x'], $inm_comprador['inm_destino_credito_y']);
        $pdf->Write(0, 'X');

        $x_inm_comprador_es_segundo_credito = 46.5;
        $y_inm_comprador_es_segundo_credito = 91.5;
        if($inm_comprador['inm_comprador_es_segundo_credito'] === 'SI'){

            $x_inm_comprador_es_segundo_credito = 31.5;
        }

        $pdf->SetXY($x_inm_comprador_es_segundo_credito, $y_inm_comprador_es_segundo_credito);
        $pdf->Write(0, 'X');


        $pdf->SetXY($inm_comprador['inm_plazo_credito_sc_x'], $inm_comprador['inm_plazo_credito_sc_y']);
        $pdf->Write(0, 'X');


        /**
         * 2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO
         */

        if(round($inm_comprador['inm_comprador_descuento_pension_alimenticia_dh'],2)>0.0) {

            $x_inm_comprador_descuento_pension_alimenticia_dh = 77;
            $y_inm_comprador_descuento_pension_alimenticia_dh = 117;

            $pdf->SetXY($x_inm_comprador_descuento_pension_alimenticia_dh,
                $y_inm_comprador_descuento_pension_alimenticia_dh);
            $pdf->Write(0, $inm_comprador['inm_comprador_descuento_pension_alimenticia_dh']);
        }

        if(round($inm_comprador['inm_comprador_descuento_pension_alimenticia_fc'],2)>0.0) {
            $x_inm_comprador_descuento_pension_alimenticia_fc = 115;
            $y_inm_comprador_descuento_pension_alimenticia_fc = 117;

            $pdf->SetXY($x_inm_comprador_descuento_pension_alimenticia_fc,
                $y_inm_comprador_descuento_pension_alimenticia_fc);
            $pdf->Write(0, $inm_comprador['inm_comprador_descuento_pension_alimenticia_fc']);
        }

        if(round($inm_comprador['inm_comprador_monto_credito_solicitado_dh'],2)>0.0) {
            $x_inm_comprador_monto_credito_solicitado_dh = 79;
            $y_inm_comprador_monto_credito_solicitado_dh = 131;

            $pdf->SetXY($x_inm_comprador_monto_credito_solicitado_dh,
                $y_inm_comprador_monto_credito_solicitado_dh);
            $pdf->Write(0, $inm_comprador['inm_comprador_monto_credito_solicitado_dh']);
        }

        if(round($inm_comprador['inm_comprador_monto_ahorro_voluntario'],2)>0.0) {
            $x_inm_comprador_monto_ahorro_voluntario = 51.5;
            $y_inm_comprador_monto_ahorro_voluntario = 143;

            $pdf->SetXY($x_inm_comprador_monto_ahorro_voluntario,
                $y_inm_comprador_monto_ahorro_voluntario);
            $pdf->Write(0, $inm_comprador['inm_comprador_monto_ahorro_voluntario']);
        }


        /**
         * 3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO
         */

        $pdf->SetFont('Arial','B', 8);
        $pdf->SetTextColor(0,0,0);

        $x_dp_calle_descripcion = 15.5;
        $y_dp_calle_descripcion = 164;

        $pdf->SetXY($x_dp_calle_descripcion,
            $y_dp_calle_descripcion);
        $pdf->Write(0, $com_cliente['dp_calle_descripcion']);


        $x_com_cliente_numero_exterior = 15.5;
        $y_com_cliente_numero_exterior = 170;

        $pdf->SetXY($x_com_cliente_numero_exterior,
            $y_com_cliente_numero_exterior);
        $pdf->Write(0, $com_cliente['com_cliente_numero_exterior']);


        $pdf->AddPage();
        $tplIdx = $pdf->importPage(2);
        $pdf->useTemplate($tplIdx,null,null,null,null,true);

        $pdf->AddPage();
        $tplIdx = $pdf->importPage(3);
        $pdf->useTemplate($tplIdx,null,null,null,null,true);

        $pdf->Output('tu_pedorrote.pdf', 'I');

        exit;
    }


}
