<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_doc_comprador_html;
use gamboamartin\inmuebles\models\inm_conf_docs_comprador;
use gamboamartin\inmuebles\models\inm_doc_comprador;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_doc_comprador extends _ctl_formato {

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_doc_comprador(link: $link);
        $html_ = new inm_doc_comprador_html(html: $html);
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

        $columns_ds = array('inm_comprador_id','inm_comprador_nss','inm_comprador_curp','inm_comprador_nombre',
            'inm_comprador_apellido_paterno','inm_comprador_apellido_materno');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_comprador_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Comprador', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $columns_ds = array('doc_tipo_documento_descripcion');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'doc_tipo_documento_id',
            keys_selects: $keys_selects, id_selected: -1, label: 'Tipo de Documento', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $confs = (new inm_conf_docs_comprador(link: $this->link))->registros_activos();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al Obtener configuraciones',data:  $confs,
                header: $header,ws:  $ws);
        }

        $values_in = array();

        foreach ($confs as $value){
            $values_in[] = $value['doc_tipo_documento_id'];
        }


        $in['llave'] = 'doc_tipo_documento.id';
        $in['values'] = $values_in;

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $this->link))->filtro_and(in: $in);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al Obtener tipos de documento',data:  $r_doc_tipo_documento,
                header: $header,ws:  $ws);
        }

        $keys_selects['doc_tipo_documento_id']->registros = $r_doc_tipo_documento->registros;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $documento = $this->html->input_file(cols: 12,name:  'documento',row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $documento, header: $header,ws:  $ws);
        }

        $this->inputs->documento = $documento;

        return $r_alta;
    }

    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['inm_comprador'] = "gamboamartin\\inmuebles";

        $init_data['doc_tipo_documento'] = "gamboamartin\\documento";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function descarga(bool $header, bool $ws = false): array|string
    {

        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener documento',data:  $registro,header:  $header,
                ws:  $ws);
        }
        $ruta_doc = $this->path_base."$registro->doc_documento_ruta_relativa";

        $content = file_get_contents($ruta_doc);
        $name = $registro->inm_comprador_id.".".$registro->inm_comprador_nombre;
        $name .= ".".$registro->inm_comprador_apellido_paterno;
        $name .= ".".$registro->inm_comprador_apellido_materno.".".$registro->doc_tipo_documento_codigo;
        $name .= ".".$registro->doc_extension_descripcion;

        if($header) {
            ob_clean();
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$name");
            header("Content-Type: application/$registro->doc_extension_descripcion");
            header("Content-Transfer-Encoding: binary");

            // Read the file
            readfile($ruta_doc);
            exit;
        }
        return $content;

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
        $columns["inm_doc_comprador_id"]["titulo"] = "Id";
        $columns["doc_documento_id"]["titulo"] = "Id Doc";
        $columns["doc_documento_ruta_relativa"]["titulo"] = "Ruta";
        $columns["doc_tipo_documento_descripcion"]["titulo"] = "Tipo de Documento";
        $columns["inm_comprador_nombre"]["titulo"] = "Nombre";
        $columns["inm_comprador_apellido_paterno"]["titulo"] = "AP";
        $columns["inm_comprador_apellido_materno"]["titulo"] = "AM";

        $filtro = array("inm_doc_comprador.id","doc_documento.id", "doc_documento.ruta_relativa",
            'doc_tipo_documento.descripcion','inm_comprador.nombre','inm_comprador.apellido_paterno',
            'inm_comprador_apellido_materno');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }


}
