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
use gamboamartin\inmuebles\html\inm_parentesco_html;
use gamboamartin\inmuebles\models\inm_parentesco;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_parentesco extends _ctl_base {

    public function __construct(PDO $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_parentesco(link: $link);
        $html_ = new inm_parentesco_html(html: $html);
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
        $keys->inputs = array('descripcion');
        $keys->selects = array();

        $init_data = array();
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
        $columns["inm_parentesco_id"]["titulo"] = "Id";
        $columns["inm_parentesco_descripcion"]["titulo"] = "Descripcion";

        $filtro = array("inm_parentesco.id","inm_parentesco.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descripcion',
            keys_selects:$keys_selects, place_holder: 'Descripcion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }


}
