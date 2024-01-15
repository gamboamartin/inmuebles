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
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\inmuebles\html\inm_valuador_html;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_ubicacion extends _ctl_base {

    public string $link_rel_ubi_comp_alta_bd = '';
    public string $link_opinion_valor_alta_bd = '';
    public string $link_costo_alta_bd = '';
    public array $imp_compradores = array();

    public array $inm_opiniones_valor = array();
    public int $n_opiniones_valor = 0;
    public float $monto_opinion_promedio = 0.0;

    public array $inm_costos = array();

    public string $costo = '0.0';
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_ubicacion(link: $link);
        $html_ = new inm_ubicacion_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id:  $this->registro_id);

        $datatables = $this->init_datatable();
        if(errores::$error){
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable',data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html:$html_, link: $link,modelo:  $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->lista_get_data = true;
    }

    /**
     * Genera un formulario de alta de una ubicacion
     * @param bool $header If header muestra result en web
     * @param bool $ws If ws muestra result json
     * @return array|string
     */
    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = (new _ubicacion())->init_alta(controler: $this,disableds: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }


    /**
     * Integra una vista para la asignacion de un comprador a una vivienda
     * @param bool $header Retorna datos via WEB
     * @param bool $ws Retorna datos vis JSON
     * @return array|stdClass
     * @version 2.150.0
     */
    final public function asigna_comprador(bool $header, bool $ws = false): array|stdClass
    {
        $this->inputs = new stdClass();
        $disableds = array('dp_pais_id','dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id',
            'dp_calle_pertenece_id');
        $base_data = (new _ubicacion())->base_view_accion_data(controler: $this, disableds: $disableds,
            funcion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener base_data',data:  $base_data,
                header: $header,ws:  $ws);
        }

        $data_compra = (new inm_ubicacion_html(html: $this->html_base))->data_comprador(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener data_compra',data:  $data_compra,
                header: $header,ws:  $ws);
        }

        return $base_data->base_html->r_modifica;
    }

    /**
     * Vista para asignacion de costo
     * @param bool $header Retorna datos via WEB
     * @param bool $ws Retorna datos vis JSON
     * @return array|stdClass
     */
    public function asigna_costo(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->detalle_costo(header: false,funcion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar modificacion',data:  $r_modifica,
                header: $header,ws:  $ws);
        }


        $inputs = (new _ubicacion())->inputs_costo(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $link_costo_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,seccion: 'inm_costo');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener link_costo_alta_bd', data:  $link_costo_alta_bd,
                header: $header,ws:  $ws);
        }
        $this->link_costo_alta_bd = $link_costo_alta_bd;


        return $r_modifica;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion', 'manzana', 'lote','costo_directo','numero_exterior','numero_interior',
            'cuenta_predial','codigo');
        $keys->selects = array();


        $init_data = array();
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";

        $init_data['inm_tipo_ubicacion'] = "gamboamartin\\inmuebles";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    /**
     * Obtiene una vista de detalle de costo por ubicacion
     * @param bool $header retorna datos en html
     * @param bool $ws Retorna datos en ws
     * @param string $funcion Funcion para retorno de links
     * @return array|stdClass
     * @version 2.181.0
     *
     */
    public function detalle_costo(bool $header, bool $ws = false, string $funcion='detalle_costo'): array|stdClass
    {

        $params_get = (new inm_ubicacion_html(html: $this->html_base))->params_get_data(accion_retorno: $funcion,
            id_retorno: $this->registro_id,seccion_retorno: $this->tabla);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar params_get',data:  $params_get,
                header: $header,ws:  $ws);
        }

        $base = (new inm_ubicacion_html(html: $this->html_base))->base_costos(controler: $this,funcion: $funcion,
            params_get: $params_get);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }


        return $base->base->r_modifica;
    }

    /**
     * Inicializa el objeto Datatables con las columnas y filtros necesarios para visualizar la ubicación de los inmuebles.
     * La funcion consigue los datos para un tabla que debe mostrar la información sobre el id, código,
     * tipo de ubicación, municipio, CP, colonia, calle, número exterior, número interior, manzana, lote, etapa,
     * cuenta predial, número de opiniones de valor, valor estimado y costo de una ubicación de inmueble.
     * @return stdClass Objeto con la configuración de Datatables.
     */
    private function init_datatable(): stdClass
    {
        // Definir los títulos de las columnas para el datatable
        $columns["inm_ubicacion_id"]["titulo"] = "Id";
        $columns["inm_ubicacion_codigo"]["titulo"] = "Cod";
        $columns["inm_tipo_ubicacion_descripcion"]["titulo"] = "Tipo de Ubicacion";
        $columns["dp_municipio_descripcion"]["titulo"] = "Municipio";
        $columns["dp_cp_descripcion"]["titulo"] = "CP";
        $columns["dp_colonia_descripcion"]["titulo"] = "Colonia";
        $columns["dp_calle_descripcion"]["titulo"] = "Calle";
        $columns["inm_ubicacion_numero_exterior"]["titulo"] = "Ext";
        $columns["inm_ubicacion_numero_interior"]["titulo"] = "Int";
        $columns["inm_ubicacion_manzana"]["titulo"] = "Manzana";
        $columns["inm_ubicacion_lote"]["titulo"] = "Lote";
        $columns["inm_ubicacion_etapa"]["titulo"] = "Etapa";
        $columns["inm_ubicacion_cuenta_predial"]["titulo"] = "Predial";
        $columns["inm_ubicacion_n_opiniones_valor"]["titulo"] = "Op Valor";
        $columns["inm_ubicacion_monto_opinion_promedio"]["titulo"] = "Valor Est";
        $columns["inm_ubicacion_costo"]["titulo"] = "Costo";

        // Definir los filtros para el datatable
        $filtro = array("inm_ubicacion.id","dp_municipio.descripcion",'dp_cp.descripcion','dp_colonia.descripcion',
            'dp_calle.descripcion','inm_ubicacion.numero_exterior','inm_ubicacion.numero_interior',
            'inm_ubicacion.manzana','inm_ubicacion.lote','inm_ubicacion.cuenta_predial',
            'inm_tipo_ubicacion.descripcion');

        // Crear el objeto Datatables y asignarle las columnas y filtros definidos
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

        $data_row = $this->modelo->registro(registro_id: $this->registro_id,retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener registro',data:  $data_row,header: $header,ws: $ws);
        }


        $keys_selects = (new _ubicacion())->keys_selects_base(controler: $this,data_row:  $data_row, disableds: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects, header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        return $r_modifica;
    }



    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects,
            place_holder: 'Descripcion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'manzana', keys_selects:$keys_selects,
            place_holder: 'Manzana',required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lote', keys_selects:$keys_selects,
            place_holder: 'Lote', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'costo_directo', keys_selects:$keys_selects,
            place_holder: 'Costo Directo', value: 0);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'cuenta_predial', keys_selects:$keys_selects,
            place_holder: 'Cuenta Predial');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_exterior', keys_selects:$keys_selects,
            place_holder: 'Exterior');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_interior', keys_selects: $keys_selects,
            place_holder: 'Interior',required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'codigo', keys_selects: $keys_selects,
            place_holder: 'Codigo',required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    public function opinion_valor_alta(bool $header, bool $ws = false): array|stdClass
    {


        $base_data = (new _ubicacion())->base_view_accion_data(controler: $this, disableds: array(),
            funcion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener base_data',data:  $base_data, header: $header,ws:  $ws);
        }


        $inm_valuador_id = (new inm_valuador_html(html: $this->html_base))->select_inm_valuador_id(cols: 12,
            con_registros:  true,id_selected:  -1,link:  $this->link);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_valuador_id',data:  $inm_valuador_id, header: $header,ws:  $ws);
        }

        $this->inputs->inm_valuador_id = $inm_valuador_id;

        $monto_resultado = $this->html->input_monto(cols: 12,row_upd:  new stdClass(),value_vacio:  false,
            name: 'monto_resultado',place_holder: 'Monto Resultado');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener monto_resultado',data:  $monto_resultado, header: $header,ws:  $ws);
        }

        $this->inputs->monto_resultado = $monto_resultado;

        $fecha = $this->html->input_fecha(cols: 12,row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener fecha',data:  $fecha, header: $header,ws:  $ws);
        }

        $this->inputs->fecha = $fecha;

        $costo = $this->html->input_monto(cols: 12,row_upd:  new stdClass(),value_vacio:  false,name: 'costo',
            place_holder: 'Costo de opinion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener monto_resultado',data:  $monto_resultado, header: $header,ws:  $ws);
        }

        $this->inputs->costo = $costo;


        $link_opinion_valor_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,seccion: 'inm_opinion_valor');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener link_opinion_valor_lata_bd',
                data:  $link_opinion_valor_alta_bd, header: $header,ws:  $ws);
        }
        $this->link_opinion_valor_alta_bd = $link_opinion_valor_alta_bd;

        $inm_opiniones_valor = (new inm_ubicacion(link: $this->link))->opiniones_valor(inm_ubicacion_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_opiniones_valor', data:  $inm_opiniones_valor,
                header: $header,ws:  $ws);
        }
        $this->inm_opiniones_valor = $inm_opiniones_valor;

        $this->n_opiniones_valor = count($this->inm_opiniones_valor);

        $monto_opinion_promedio = (new inm_ubicacion(link: $this->link))->monto_opinion_promedio(inm_ubicacion_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener promedio', data:  $monto_opinion_promedio,
                header: $header,ws:  $ws);
        }

        $this->monto_opinion_promedio = $monto_opinion_promedio;


        return $base_data->base_html->r_modifica;
    }




}
