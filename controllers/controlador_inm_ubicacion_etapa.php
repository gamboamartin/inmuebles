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
use gamboamartin\inmuebles\html\inm_ubicacion_etapa_html;
use gamboamartin\inmuebles\models\inm_ubicacion_etapa;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_ubicacion_etapa extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_ubicacion_etapa(link: $link);
        $html_ = new inm_ubicacion_etapa_html(html: $html);
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

        $columns_ds = array('pr_proceso_descripcion','pr_etapa_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'pr_etapa_proceso_id',
            keys_selects: array(), id_selected: -1, label: 'Etapa Proceso', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_ubicacion_id','dp_municipio_descripcion','dp_colonia_descripcion',
            'dp_calle_descripcion','inm_ubicacion_numero_exterior');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Ubicacion', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $fecha = $this->html->input_fecha(cols: 12,row_upd: new stdClass(),value_vacio: false,value: date('Y-m-d'));
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $fecha, header: $header,ws:  $ws);
        }

        $this->inputs->fecha = $fecha;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }



    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $registro = $this->modelo->registro(registro_id: $this->registro_id,retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener registro',data:  $registro,header: $header,ws: $ws);
        }


        $columns_ds = array('pr_proceso_descripcion','pr_etapa_descripcion');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'pr_etapa_proceso_id',
            keys_selects: array(), id_selected: $registro->pr_etapa_proceso_id, label: 'Etapa Proceso', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_ubicacion_id','dp_municipio_descripcion','dp_colonia_descripcion',
            'dp_calle_descripcion','inm_ubicacion_numero_exterior');
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects: $keys_selects, id_selected: $registro->inm_ubicacion_id, label: 'Ubicacion', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $fecha = $this->html->input_fecha(cols: 12,row_upd: new stdClass(),value_vacio: false,value: $registro->inm_ubicacion_etapa_fecha);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $fecha, header: $header,ws:  $ws);
        }

        $this->inputs->fecha = $fecha;

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        return $r_modifica;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();


        $init_data = array();
        $init_data['inm_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['pr_etapa_proceso'] = "gamboamartin\\proceso";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }



    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_ubicacion_id"]["titulo"] = "Id";
        $columns["pr_etapa_descripcion"]["titulo"] = "Municipio";
        $columns["pr_proceso_descripcion"]["titulo"] = "CP";
        $columns["inm_ubicacion_etapa_fecha"]["titulo"] = "Colonia";


        $filtro = array("inm_ubicacion.id","pr_etapa.descripcion",'pr_proceso.descripcion','inm_ubicacion_etapa.fecha');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }


}
