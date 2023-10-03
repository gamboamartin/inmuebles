<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_prospecto extends _ctl_formato {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_prospecto(link: $link);
        $html_ = new inm_prospecto_html(html: $html);
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


        $com_agentes = (new com_agente(link: $this->link))->com_agentes_session();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener agentes',data:  $com_agentes, header: $header,
                ws:  $ws);
        }

        $id_selected = -1;
        if(count($com_agentes) > 0){
            $id_selected = $com_agentes[0]['com_agente_id'];
        }


        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $id_selected, label: 'Agente');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $com_tipo_prospecto_id = (new com_prospecto(link: $this->link))->id_preferido_detalle(entidad_preferida: 'com_tipo_prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener id',data:  $com_tipo_prospecto_id, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_tipo_prospecto_id',
            keys_selects:$keys_selects, id_selected: $com_tipo_prospecto_id, label: 'Tipo de prospecto');
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
        $keys->inputs = array('descripcion');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";
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
     * @version 1.2.0
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_prospecto_id"]["titulo"] = "Id";
        $columns["inm_prospecto_descripcion"]["titulo"] = "Descripcion";


        $filtro = array("inm_prospecto.id","inm_prospecto.descripcion");

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }


}