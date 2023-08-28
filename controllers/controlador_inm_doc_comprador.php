<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\compresor\compresor;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_doc_comprador_html;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_doc_comprador;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;

class controlador_inm_doc_comprador extends _ctl_formato {

    public string $ruta_doc = '';
    public bool $es_imagen = false;
    public bool $es_pdf = false;

    public string $button_inm_doc_comprador_descarga = '';
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


        $inm_tipos_doc = (new _doctos())->documentos_de_comprador(link: $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al Obtener tipos de documento',data:  $inm_tipos_doc,
                header: $header,ws:  $ws);
        }

        $keys_selects['doc_tipo_documento_id']->registros = $inm_tipos_doc;

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

        $name_file = $this->name_file(registro: $registro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener name_file',data:  $name_file,header:  $header,
                ws:  $ws);
        }

        if($header) {
            ob_clean();
            // Define headers
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$name_file");
            header("Content-Type: application/$registro->doc_extension_descripcion");
            header("Content-Transfer-Encoding: binary");

            // Read the file
            readfile($ruta_doc);
            exit;
        }
        return $content;

    }

    public function descarga_zip(bool $header, bool $ws = false): array|string
    {

        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener documento',data:  $registro,header:  $header,
                ws:  $ws);
        }
        $ruta_doc = $this->path_base."$registro->doc_documento_ruta_relativa";



        $name = $this->name_doc(registro: $registro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener name',data:  $name,header:  $header,
                ws:  $ws);
        }
        $name_zip  = $name.'.zip';

        $name_file = $this->name_file(registro: $registro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener name_file',data:  $name_file,header:  $header,
                ws:  $ws);
        }

        $archivos[$ruta_doc] = $name_file;
        $comprime = compresor::descarga_zip_multiple(archivos: $archivos, name_zip: $name_zip);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al comprimir file',data:  $comprime,header:  $header,
                ws:  $ws);
        }


        return $comprime;

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

    private function name_doc(stdClass $registro): string
    {
        $name = $registro->inm_comprador_id.".".$registro->inm_comprador_nombre;
        $name .= ".".$registro->inm_comprador_apellido_paterno;
        $name .= ".".$registro->inm_comprador_apellido_materno.".".$registro->doc_tipo_documento_codigo;
        return $name;
    }

    private function name_file(stdClass $registro): array|string
    {
        $name = $this->name_doc(registro: $registro);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener name',data:  $name);
        }
        $name .= ".".$registro->doc_extension_descripcion;
        return $name;
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

    public function vista_previa(bool $header, bool $ws = false): array|string|stdClass
    {

        $registro = $this->modelo->registro(registro_id: $this->registro_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener documento',data:  $registro,header:  $header,
                ws:  $ws);
        }

        $com_cliente = (new inm_comprador(link: $this->link))->get_com_cliente(
            inm_comprador_id: $registro->inm_comprador_id, retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener cliente',data:  $com_cliente,header:  $header,
                ws:  $ws);
        }

        $ruta_doc = $this->url_base."$registro->doc_documento_ruta_relativa";


        $this->ruta_doc = $ruta_doc;
        if($registro->doc_extension_es_imagen === 'activo') {
            $this->es_imagen = true;
        }
        if($registro->doc_extension_descripcion === 'pdf'){
            $this->es_pdf = true;
        }

        $row_upd = new stdClass();
        $row_upd->nss = $registro->inm_comprador_nss;
        $row_upd->com_tipo_cliente_descripcion = $com_cliente->com_tipo_cliente_descripcion;
        $row_upd->curp = $registro->inm_comprador_curp;
        $row_upd->rfc = $com_cliente->com_cliente_rfc;
        $row_upd->apellido_paterno = $registro->inm_comprador_apellido_paterno;
        $row_upd->apellido_materno = $registro->inm_comprador_apellido_materno;
        $row_upd->nombre = $registro->inm_comprador_nombre;


        $com_tipo_cliente_descripcion = $this->html->input_text_required(cols: 12,disabled: true,
            name: 'com_tipo_cliente_descripcion', place_holder: 'Tipo de Cliente',row_upd: $row_upd,
            value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $com_tipo_cliente_descripcion,
                header:  $header, ws:  $ws);
        }
        $nss = $this->html->input_text_required(cols: 4,disabled: true,name: 'nss',place_holder: 'NSS',
            row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $nss,header:  $header,
                ws:  $ws);
        }
        $curp = $this->html->input_text_required(cols: 4,disabled: true,name: 'curp',place_holder: 'CURP',
            row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $curp,header:  $header,
                ws:  $ws);
        }
        $rfc = $this->html->input_text_required(cols: 4,disabled: true,name: 'rfc',place_holder: 'RFC',
            row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $rfc,header:  $header,
                ws:  $ws);
        }
        $apellido_paterno = $this->html->input_text_required(cols: 6,disabled: true,name: 'apellido_paterno',
            place_holder: 'AP',row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $apellido_paterno,header:  $header,
                ws:  $ws);
        }
        $apellido_materno = $this->html->input_text_required(cols: 6,disabled: true,name: 'apellido_materno',
            place_holder: 'AM',row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $apellido_materno,header:  $header,
                ws:  $ws);
        }
        $nombre = $this->html->input_text_required(cols: 12,disabled: true,name: 'nombre',place_holder: 'Nombre',
            row_upd:$row_upd,value_vacio: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $nombre,header:  $header,
                ws:  $ws);
        }

        $this->inputs = new stdClass();
        $this->inputs->nss = $nss;
        $this->inputs->com_tipo_cliente_descripcion = $com_tipo_cliente_descripcion;
        $this->inputs->curp = $curp;
        $this->inputs->rfc = $rfc;
        $this->inputs->apellido_paterno = $apellido_paterno;
        $this->inputs->apellido_materno = $apellido_materno;
        $this->inputs->nombre = $nombre;

        $button_inm_doc_comprador_descarga = $this->html->button_href(accion: 'descarga',etiqueta:  'Descarga',
            registro_id:  $this->registro_id,
            seccion:  $this->seccion,style:  'success');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar button',data:  $button_inm_doc_comprador_descarga,
                header: $header,ws:  $ws);
        }


        $this->button_inm_doc_comprador_descarga = $button_inm_doc_comprador_descarga;

        return $registro;


    }


}
