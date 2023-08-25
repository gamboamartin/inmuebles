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
use gamboamartin\inmuebles\html\inm_referencia_html;
use gamboamartin\inmuebles\models\inm_referencia;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_referencia extends _ctl_base {

    public array $imp_ubicaciones = array();
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_referencia(link: $link);
        $html_ = new inm_referencia_html(html: $html);
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

        $keys_selects = (new _dps_init())->ks_dp(controler: $this,keys_selects:  $keys_selects,row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_comprador_nombre','inm_comprador_apellido_paterno','inm_comprador_apellido_materno');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_comprador_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Comprador', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


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
        $keys->inputs = array('apellido_paterno', 'apellido_materno', 'nombre',
            'lada','numero','celular','numero_dom');
        $keys->selects = array();


        $init_data = array();
        $init_data['inm_comprador'] = "gamboamartin\\inmuebles";

        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";


        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno', keys_selects:$keys_selects,
            place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'celular',
            keys_selects:$keys_selects, place_holder: 'Celular');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'numero_dom',
            keys_selects:$keys_selects, place_holder: 'Numero');
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

       // print_r($this->registro);exit;
        $this->row_upd->dp_pais_id = $this->registro['dp_pais_id'];
        $this->row_upd->dp_estado_id = $this->registro['dp_estado_id'];
        $this->row_upd->dp_municipio_id = $this->registro['dp_municipio_id'];
        $this->row_upd->dp_cp_id = $this->registro['dp_cp_id'];
        $this->row_upd->dp_colonia_id = $this->registro['dp_colonia_id'];

        $keys_selects = array();

        $keys_selects = (new _dps_init())->ks_dp(controler: $this,keys_selects:  $keys_selects,row_upd: $this->row_upd);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }



        $columns_ds = array('inm_comprador_nombre','inm_comprador_apellido_paterno','inm_comprador_apellido_materno');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_comprador_id',
            keys_selects: $keys_selects, id_selected: $this->row_upd->inm_comprador_id, label: 'Comprador',
            columns_ds : $columns_ds);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
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
        $columns["inm_referencia_id"]["titulo"] = "Id";
        $columns["inm_referencia_nombre"]["titulo"] = "Nombre";
        $columns["inm_referencia_apellido_paterno"]["titulo"] = "AP";
        $columns["inm_referencia_apellido_materno"]["titulo"] = "AM";


        $filtro = array("inm_referencia.id",'inm_referencia.nombre','inm_referencia.apellido_paterno',
            'inm_referencia.apellido_materno');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }



}