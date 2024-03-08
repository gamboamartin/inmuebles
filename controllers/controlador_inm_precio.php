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
use DateTime;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_precio_html;
use gamboamartin\inmuebles\models\inm_precio;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_precio extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_precio(link: $link);
        $html_ = new inm_precio_html(html: $html);
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

        $this->row_upd->porcentaje_descuento_maximo = 0;
        $this->row_upd->monto_descuento_maximo = 0;
        $this->row_upd->porcentaje_comisiones_maximo = 0;
        $this->row_upd->monto_comisiones_maximo = 0;
        $this->row_upd->porcentaje_devolucion_maximo = 0;
        $this->row_upd->monto_devolucion_maximo = 0;
        $this->row_upd->fecha_inicial = date('Y-m-d');

        $fecha_final = new DateTime();
        $fecha_final->modify('last day of this month');
        $this->row_upd->fecha_final = $fecha_final->format('Y-m-d');


        $keys_selects = array();
        $columns_ds = array();
        $columns_ds[] = 'inm_ubicacion_descripcion';
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Ubicacion', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_institucion_hipotecaria_descripcion';
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_institucion_hipotecaria_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Institucion', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $fecha_inicial = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            name: 'fecha_inicial', value: $this->row_upd->fecha_inicial);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener fecha_inicial',data:  $fecha_inicial, header: $header,ws:  $ws);
        }

        $this->inputs->fecha_inicial = $fecha_inicial;

        $fecha_final = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            name: 'fecha_final', value: $this->row_upd->fecha_final);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener fecha_final',data:  $fecha_final, header: $header,ws:  $ws);
        }

        $this->inputs->fecha_final = $fecha_final;

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('precio_venta','porcentaje_descuento_maximo','porcentaje_comisiones_maximo',
            'monto_descuento_maximo','monto_comisiones_maximo',
            'porcentaje_devolucion_maximo','monto_devolucion_maximo');
        $keys->selects = array();

        $init_data = array();
        $init_data['inm_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['inm_institucion_hipotecaria'] = "gamboamartin\\inmuebles";
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

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_ubicacion_id, label: 'Ubicacion', columns_ds: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_institucion_hipotecaria_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_institucion_hipotecaria_id, label: 'Institucion', columns_ds: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $fecha_inicial = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            name: 'fecha_inicial', value: $this->row_upd->fecha_inicial);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener fecha_inicial',data:  $fecha_inicial, header: $header,ws:  $ws);
        }

        $this->inputs->fecha_inicial = $fecha_inicial;

        $fecha_final = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            name: 'fecha_final', value: $this->row_upd->fecha_final);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener fecha_final',data:  $fecha_final, header: $header,ws:  $ws);
        }

        $this->inputs->fecha_final = $fecha_final;

        return $r_modifica;
    }

    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_precio_id"]["titulo"] = "Id";
        $columns["inm_precio_precio_venta"]["titulo"] = "Precio";
        $columns["inm_precio_fecha_inicial"]["titulo"] = "Fecha Inicio";
        $columns["inm_precio_fecha_final"]["titulo"] = "Fecha Fin";
        $columns["dp_colonia_descripcion"]["titulo"] = "Colonia";
        $columns["dp_cp_descripcion"]["titulo"] = "CP";
        $columns["dp_municipio_descripcion"]["titulo"] = "Mun";
        $columns["dp_calle_descripcion"]["titulo"] = "Calle";
        $columns["inm_ubicacion_numero_exterior"]["titulo"] = "Ext";
        $columns["inm_ubicacion_numero_interior"]["titulo"] = "Int";
        $columns["inm_ubicacion_cuenta_predial"]["titulo"] = "Predial";
        $columns["inm_tipo_ubicacion_descripcion"]["titulo"] = "Predial";
        $columns["inm_institucion_hipotecaria_descripcion"]["titulo"] = "Institucion";

        $filtro = array("inm_precio.id","inm_precio.fecha_inicial",'inm_precio.fecha_final','dp_colonia.descripcion',
            'dp_cp.descripcion','dp_municipio.descripcion','dp_calle.descripcion','inm_ubicacion.cuenta_predial',
            'inm_tipo_ubicacion.descripcion','inm_institucion_hipotecaria.descripcion');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects): array
    {


        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'precio_venta',
            keys_selects:$keys_selects, place_holder: 'Precio de Venta');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'porcentaje_descuento_maximo',
            keys_selects:$keys_selects, place_holder: '% Descuento Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'porcentaje_comisiones_maximo',
            keys_selects:$keys_selects, place_holder: '% Com Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'monto_descuento_maximo',
            keys_selects:$keys_selects, place_holder: '$ Desc Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'monto_comisiones_maximo',
            keys_selects:$keys_selects, place_holder: '$ Com Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'fecha_inicial',
            keys_selects:$keys_selects, place_holder: 'Inicio');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'fecha_final',
            keys_selects:$keys_selects, place_holder: 'Fin');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'porcentaje_devolucion_maximo',
            keys_selects:$keys_selects, place_holder: '% Dev Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'monto_devolucion_maximo',
            keys_selects:$keys_selects, place_holder: '$ Dev Max');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        return $keys_selects;
    }


}
