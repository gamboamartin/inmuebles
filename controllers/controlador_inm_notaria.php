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
use gamboamartin\inmuebles\html\inm_notaria_html;
use gamboamartin\inmuebles\html\inm_referencia_html;
use gamboamartin\inmuebles\models\inm_notaria;
use gamboamartin\inmuebles\models\inm_referencia;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_notaria extends _ctl_base {

    public array $imp_ubicaciones = array();
    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_notaria(link: $link);
        $html_ = new inm_notaria_html(html: $html);
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

    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
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
