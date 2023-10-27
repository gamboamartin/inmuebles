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
use gamboamartin\inmuebles\html\inm_beneficiario_html;
use gamboamartin\inmuebles\models\inm_beneficiario;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_beneficiario extends _ctl_base {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_beneficiario(link: $link);
        $html_ = new inm_beneficiario_html(html: $html);
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
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_prospecto_id',
            keys_selects: array(), id_selected: -1, label: 'Prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_tipo_beneficiario_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo de Beneficiario');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_parentesco_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Parentesco');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
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
        $keys->inputs = array();
        $keys->selects = array();


        $init_data = array();
        $init_data['inm_tipo_beneficiario'] = "gamboamartin\\inmuebles";
        $init_data['inm_prospecto'] = "gamboamartin\\inmuebles";
        $init_data['inm_parentesco'] = "gamboamartin\\inmuebles";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_prospecto_id',
            keys_selects: array(), id_selected: $this->registro['inm_prospecto_id'],
            label: 'Prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_tipo_beneficiario_id',
            keys_selects: $keys_selects, id_selected: $this->registro['inm_tipo_beneficiario_id'],
            label: 'Tipo de Beneficiario');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_parentesco_id',
            keys_selects: $keys_selects, id_selected: $this->registro['inm_parentesco_id'],
            label: 'Parenteso');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
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
        $columns["inm_beneficiario_id"]["titulo"] = "Id";
        $columns["inm_prospecto_descripcion"]["titulo"] = "Prospecto";
        $columns["inm_tipo_beneficiario_descripcion"]["titulo"] = "Tipo de Beneficiario";
        $columns["inm_parentesco_descripcion"]["titulo"] = "Parentesco";

        $filtro = array("inm_beneficiario.id","inm_prospecto.descripcion",
            "inm_tipo_beneficiario.descripcion",'inm_parentesco.descripcion');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }


}