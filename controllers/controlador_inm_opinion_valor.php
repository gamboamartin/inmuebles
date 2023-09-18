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
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_opinion_valor_html;
use gamboamartin\inmuebles\models\inm_opinion_valor;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_opinion_valor extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_opinion_valor(link: $link);
        $html_ = new inm_opinion_valor_html(html: $html);
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

        $this->row_upd = new stdClass();
        $this->row_upd->fecha = date('Y-m-d');

        $keys_selects = array();

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Ubicacion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_valuador_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Valuador');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }


        $fecha = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            value: $this->row_upd->fecha);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener input fecha',data:  $fecha, header: $header,ws:  $ws);
        }

        $this->inputs->fecha = $fecha;

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('monto_resultado','fecha','costo');
        $keys->selects = array();

        $init_data = array();
        $init_data['inm_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['inm_valuador'] = "gamboamartin\\inmuebles";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'monto_resultado',
            keys_selects:$keys_selects, place_holder: 'Resultado');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'costo',
            keys_selects:$keys_selects, place_holder: 'Costo de Opinion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        return $keys_selects;
    }

    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_opinion_valor_id"]["titulo"] = "Id";
        $columns["inm_valuador_descripcion"]["titulo"] = "Valuador";
        $columns["dp_municipio_descripcion"]["titulo"] = "Mun";
        $columns["dp_cp_descripcion"]["titulo"] = "CP";
        $columns["dp_colonia_descripcion"]["titulo"] = "Col";
        $columns["dp_calle_descripcion"]["titulo"] = "Calle";
        $columns["inm_ubicacion_numero_exterior"]["titulo"] = "Ext";
        $columns["inm_opinion_valor_fecha"]["titulo"] = "Fecha";
        $columns["inm_opinion_valor_monto_resultado"]["titulo"] = "Resultado";

        $filtro = array("inm_opinion_valor.id","inm_valuador.descripcion",'dp_municipio.descripcion',
            'dp_cp.descripcion','dp_colonia.descripcion','dp_calle.descripcion','inm_ubicacion.numero_exterior',
            'inm_opinion.valor_fecha','inm_opinion_monto.resultado');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
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
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_ubicacion_id, label: 'Ubicacion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_valuador_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_valuador_id, label: 'Valuador');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }
        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $fecha = $this->html->input_fecha(cols: 6,row_upd:  $this->row_upd,value_vacio:  false,
            value: $this->row_upd->fecha);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener input fecha',data:  $fecha, header: $header,ws:  $ws);
        }

        $this->inputs->fecha = $fecha;

        return $r_modifica;
    }




}
