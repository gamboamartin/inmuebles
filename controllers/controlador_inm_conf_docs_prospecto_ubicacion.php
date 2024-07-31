<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_conf_docs_comprador_html;
use gamboamartin\inmuebles\html\inm_conf_docs_prospecto_ubicacion_html;
use gamboamartin\inmuebles\models\inm_conf_docs_comprador;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto_ubicacion;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_conf_docs_prospecto_ubicacion extends _ctl_base {
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_conf_docs_prospecto_ubicacion(link: $link);
        $html_ = new inm_conf_docs_prospecto_ubicacion_html(html: $html);
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

        $columns_ds = array('doc_tipo_documento_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'doc_tipo_documento_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo de Documento', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_attr_tipo_credito_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo de Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_destino_credito_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Destino de Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_producto_infonavit_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_producto_infonavit_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Producto Infonavit', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('pr_proceso_descripcion','pr_sub_proceso_descripcion');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'pr_sub_proceso_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Sub Proceso', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }


        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    public function es_obligatorio(bool $header, bool $ws = false): array|stdClass{
        $en_transaccion = false;
        if($this->link->inTransaction()){
            $en_transaccion = true;
        }

        if(!$en_transaccion){
            $this->link->beginTransaction();
        }

        $upd = $this->row_upd(key: __FUNCTION__);
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener row upd',data:  $upd, header: $header,ws:  $ws);
        }
        $this->link->commit();

        $_SESSION[$upd->salida][]['mensaje'] = $upd->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $upd, header: $header,ws:  $ws);

        return $upd;
    }



    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['doc_tipo_documento'] = "gamboamartin\\documento";

        $init_data['inm_attr_tipo_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_destino_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";

        $init_data['pr_sub_proceso'] = "gamboamartin\\proceso";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }



    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $keys_selects = array();
        $columns_ds = array('doc_tipo_documento_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'doc_tipo_documento_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->doc_tipo_documento_id, label: 'Tipo de Documento',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_attr_tipo_credito_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_attr_tipo_credito_id, label: 'Tipo de Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_destino_credito_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_destino_credito_id, label: 'Destino de Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_producto_infonavit_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_producto_infonavit_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_producto_infonavit_id, label: 'Producto Infonavit', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('pr_proceso_descripcion','pr_sub_proceso_descripcion');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'pr_sub_proceso_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->pr_sub_proceso_id, label: 'Sub Proceso',
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
     */
    private function init_datatable(): stdClass
    {
        $columns["doc_tipo_documento_id"]["titulo"] = "Id";
        $columns["doc_tipo_documento_descripcion"]["titulo"] = "Tipo de Documento";
        $columns["inm_attr_tipo_credito_descripcion"]["titulo"] = "Tipo de Credito";
        $columns["pr_proceso_descripcion"]["titulo"] = "Proceso";
        $columns["pr_sub_proceso_descripcion"]["titulo"] = "SUB Proceso";

        $filtro = array("doc_tipo_documento.id",'doc_tipo_documento.descripcion');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }



}
