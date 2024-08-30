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
use gamboamartin\administrador\models\adm_campo;
use gamboamartin\calculo\calculo;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_direccion;
use gamboamartin\comercial\models\com_direccion_prospecto;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\comercial\models\com_rel_agente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\html\inm_prospecto_ubicacion_html;
use gamboamartin\inmuebles\models\_base_paquete;
use gamboamartin\inmuebles\models\_email;
use gamboamartin\inmuebles\models\_inm_prospecto;
use gamboamartin\inmuebles\models\_upd_prospecto;
use gamboamartin\inmuebles\models\inm_beneficiario;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto;
use gamboamartin\inmuebles\models\_inm_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_conf_institucion_campo;
use gamboamartin\inmuebles\models\inm_doc_prospecto;
use gamboamartin\inmuebles\models\inm_doc_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_referencia_prospecto;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use gamboamartin\proceso\html\pr_etapa_proceso_html;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\doc_tipo_documento_html;
use PDO;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PdfReader;
use setasign\Fpdi\PdfReader\PdfReaderException;
use stdClass;
use Throwable;
use Tomsgu\PdfMerger\Exception\FileNotFoundException;
use Tomsgu\PdfMerger\Exception\InvalidArgumentException;
use Tomsgu\PdfMerger\PdfCollection;
use Tomsgu\PdfMerger\PdfFile;
use Tomsgu\PdfMerger\PdfMerger;

class controlador_inm_prospecto_ubicacion extends _ctl_formato
{

    public stdClass $header_frontend;
    public inm_prospecto_ubicacion_html $html_entidad;

    public string $link_alta_etapa = '';
    public array $etapas = array();
    public string $link_inm_doc_prospecto_alta_bd = '';
    public string $link_modifica_direccion = '';
    public string $link_agrupa_documentos = '';
    public string $link_verifica_documentos = '';
    public string $link_envia_documentos = '';

    public string $link_fotografia_bd = '';

    public string $link_alta_integra_relacion_bd = '';

    public array $inm_conf_docs_prospecto = array();

    public array $direcciones = array();
    public array $beneficiarios = array();
    public array $referencias = array();
    public array $relaciones = array();
    public array $acciones_headers = array();
    public array $fotos = array();

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_prospecto_ubicacion(link: $link);
        $html_ = new inm_prospecto_ubicacion_html(html: $html);
        $obj_link = new links_menu(link: $link, registro_id: $this->registro_id);

        $datatables = $this->init_datatable();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar datatable', data: $datatables);
            print_r($error);
            die('Error');
        }

        parent::__construct(html: $html_, link: $link, modelo: $modelo, obj_link: $obj_link, datatables: $datatables,
            paths_conf: $paths_conf);

        $this->html_entidad = $html_;

        $this->header_frontend = new stdClass();
        $this->lista_get_data = true;

        $this->acciones_headers = array();

        $init_links = $this->init_links();
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al inicializar links', data: $init_links);
            print_r($error);
            die('Error');
        }
    }

    /**
     * Genera un formulario de alta
     * @param bool $header Muestra resultado en web
     * @param bool $ws Muestra resultado a nivel ws
     * @return array|string
     */
    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta', data: $r_alta, header: $header, ws: $ws);
        }

        $keys_selects = array();

        $id_selected = $this->id_selected_agente(link: $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $id_selected, label: 'Agente');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }

        $com_tipo_prospecto_id = (new com_prospecto(link: $this->link))->id_preferido_detalle(
            entidad_preferida: 'com_tipo_prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'com_tipo_prospecto_id', keys_selects:$keys_selects, id_selected: $com_tipo_prospecto_id,
            label: 'Tipo de prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'liga_red_social',
            keys_selects: $keys_selects, place_holder: 'Liga Red Social', required: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }
        $keys_selects['liga_red_social']->disabled = true;

        $keys_selects = $this->key_select(cols: 12, con_registros: true, filtro: array(),
            key: 'com_medio_prospeccion_id', keys_selects: $keys_selects, id_selected: -1,
            label: 'Medio de Prospeccion');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }
        $extra_params_keys[] = 'com_medio_prospeccion_id';
        $extra_params_keys[] = 'com_medio_prospeccion_es_red_social';
        $keys_selects['com_medio_prospeccion_id']->extra_params_keys = $extra_params_keys;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $inputs, header: $header, ws: $ws);
        }

        return $r_alta;
    }

    private function id_selected_agente(PDO $link): int|array
    {
        $com_agentes = (new com_agente(link: $link))->com_agentes_session();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $com_agentes, header: false, ws: false);
        }
        $id_selected = -1;
        if(count($com_agentes) > 0){
            $id_selected = (int)$com_agentes[0]['com_agente_id'];
        }
        return $id_selected;
    }


    /**
     * Inicializa los campos view para frontend
     * @return array
     * @version 2.250.2
     */
    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->textareas = array('direccion_empresa');
        $keys->inputs = array('nombre', 'apellido_paterno', 'apellido_materno', 'telefono', 'correo_com', 'razon_social',
            'lada_com', 'numero_com', 'cel_com', 'descuento_pension_alimenticia_dh', 'descuento_pension_alimenticia_fc',
            'monto_credito_solicitado_dh', 'monto_ahorro_voluntario', 'nombre_empresa_patron', 'nrp_nep', 'lada_nep',
            'numero_nep', 'extension_nep', 'nss', 'curp', 'rfc', 'numero_exterior', 'numero_interior', 'observaciones',
            'fecha_nacimiento', 'sub_cuenta', 'monto_final', 'descuento', 'puntos', 'telefono_casa', 'correo_empresa',
            'correo_mi_cuenta_infonavit', 'password_mi_cuenta_infonavit', 'nss_extra', 'liga_red_social', 'area_empresa',
            'texto_exterior', 'texto_interior', 'documentos', 'receptor', 'asunto', 'mensaje','manzana','lote',
            'cuenta_predial', 'adeudo_hipoteca','adeudo_predial', 'cuenta_agua', 'adeudo_agua',
            'adeudo_luz','monto_devolucion', 'nivel','recamaras','metros_terreno', 'metros_construccion',
            'fecha_otorgamiento_credito','cp','colonia','calle');

        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";
        $init_data['com_medio_prospeccion'] = "gamboamartin\\comercial";
        $init_data['com_prospecto'] = "gamboamartin\\comercial";

        $init_data['inm_institucion_hipotecaria'] = "gamboamartin\\inmuebles";
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";
        $init_data['com_tipo_direccion'] = "gamboamartin\\comercial";
        $init_data['inm_prototipo'] = "gamboamartin\\inmuebles";
        $init_data['inm_complemento'] = "gamboamartin\\inmuebles";
        $init_data['inm_estado_vivienda'] = "gamboamartin\\inmuebles";
        $init_data['inm_prospecto_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";

        $init_data = (new _base_paquete())->init_data_domicilio(init_data: $init_data);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $init_data);
        }

        $init_data['inm_nacionalidad'] = "gamboamartin\\inmuebles";
        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);

        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }


        return $campos_view;
    }

    /**
     * Convierte un prospecto en cliente
     * @param bool $header Muestra resultado en web
     * @param bool $ws Muestra resultado a nivel ws
     * @return array|string
     */
    public function convierte_cliente(bool $header, bool $ws = false): array|string
    {
        $this->link->beginTransaction();

        if ($this->registro_id <= 0) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error registro_id debe ser mayor a 0', data: $this->registro_id,
                header: true, ws: false);
        }

        $retorno = (new _base())->init_retorno();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener datos de retorno', data: $retorno,
                header: true, ws: false, class: __CLASS__, file: __FILE__, function: __FILE__, line: __LINE__);
        }

        $conversion = (new inm_prospecto(link: $this->link))->convierte_cliente(inm_prospecto_id: $this->registro_id);

        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al convertir en cliente', data: $conversion,
                header: true, ws: false, class: __CLASS__, file: __FILE__, function: __FILE__, line: __LINE__);
        }

        $this->link->commit();

        $out = (new _base())->out(controlador: $this, header: $header, result: $conversion,
            retorno: $retorno, ws: $ws);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar salida', data: $out,
                header: true, ws: false, class: __CLASS__, file: __FILE__, function: __FILE__, line: __LINE__);
        }

        $conversion->r_alta_rel->siguiente_view = $retorno->siguiente_view;

        return $conversion->r_alta_rel;


    }

    final public function documentos(bool $header, bool $ws = false): array
    {
        $template = $this->modifica(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $template, header: $header, ws: $ws);
        }

        $inm_conf_docs_prospecto = (new _inm_prospecto_ubicacion())->integra_inm_documentos(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar buttons', data: $inm_conf_docs_prospecto, header: $header, ws: $ws);
        }

        $keys_selects = $this->init_selects_inputs();
        if (errores::$error) {return $this->errores->error(mensaje: 'Error al inicializar selects', data: $keys_selects);
        }

        $keys_selects['com_tipo_prospecto_id']->id_selected = $this->registro['com_tipo_prospecto_id'];

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $this->row_upd->asunto = "TU MENSAJE";
        $this->row_upd->mensaje = "TU MENSAJE";
        $this->inm_conf_docs_prospecto = $inm_conf_docs_prospecto;

        //print_r($this->row_upd);

        return $inm_conf_docs_prospecto;
    }

    final public function fotografias(bool $header, bool $ws = false): array|stdClass
    {
        $template = $this->modifica(header: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $template, header: $header, ws: $ws);
        }

        $filtro['inm_conf_docs_prospecto_ubicacion.es_foto'] = 'activo';
        $inm_conf_docs_prospecto_ubicacion = (new inm_conf_docs_prospecto_ubicacion(link: $this->link))->filtro_and(
            columnas: ['doc_tipo_documento_id','doc_tipo_documento_descripcion'], filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_conf_docs_prospecto',
                data:  $inm_conf_docs_prospecto_ubicacion,header: $header, ws: $ws);
        }

        $inputs_fotos = array();
        foreach ($inm_conf_docs_prospecto_ubicacion->registros as $registro){
            $filtro_foto['inm_doc_prospecto_ubicacion.es_foto'] = 'activo';
            $filtro_foto['doc_tipo_documento.id'] = $registro['doc_tipo_documento_id'];
            $filtro_foto['inm_prospecto_ubicacion.id'] = $this->registro_id;
            $inm_doc_prospecto_ubicacion = (new inm_doc_prospecto_ubicacion(link: $this->link))->filtro_and(
                filtro: $filtro_foto);
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al obtener inm_conf_docs_prospecto',
                    data:  $inm_doc_prospecto_ubicacion,header: $header, ws: $ws);
            }

            $fotos = array();
            foreach ($inm_doc_prospecto_ubicacion->registros as $reg){
                $foto = $this->img_btn_modal(src: $reg['doc_documento_ruta_relativa'],
                    css_id: $registro['doc_tipo_documento_id'],class_css: ['imagen']);
                if(errores::$error){
                    return $this->retorno_error(mensaje: 'Error al obtener inm_conf_docs_prospecto',
                        data:  $foto,header: $header, ws: $ws);
                }

                $link_elimina_foto_bd = $this->obj_link->link_con_id(
                    accion: 'elimina_bd', link: $this->link, registro_id: $reg['inm_doc_prospecto_ubicacion_id'],
                    seccion: 'inm_doc_prospecto_ubicacion');
                if (errores::$error) {
                    $this->retorno_error(mensaje: 'Error al generar link', data: $link_elimina_foto_bd, header: $header, ws: $ws);
                }

                $contenedor = array();
                $contenedor['doc_documento_id'] = $reg['doc_documento_id'];
                $contenedor['input'] = $foto;
                $contenedor['inm_doc_prospecto_ubicacion_id'] = $reg['inm_doc_prospecto_ubicacion_id'];
                $fotos[$registro['doc_tipo_documento_id']][] = $contenedor;
            }

            $documento = $this->html->input_file(cols: 12, name: "fotos[$registro[doc_tipo_documento_id]][]",
                row_upd: new stdClass(), value_vacio: false, place_holder: $registro['doc_tipo_documento_descripcion'],
                required: false, multiple: true);
            if (errores::$error) {
                return $this->retorno_error(
                    mensaje: 'Error al obtener inputs', data: $documento, header: $header, ws: $ws);
            }

            $inputs_fotos[$registro['doc_tipo_documento_id']]['doc_tipo_documento_id'] = $registro['doc_tipo_documento_id'];
            $inputs_fotos[$registro['doc_tipo_documento_id']]['input'] = $documento;
            $inputs_fotos[$registro['doc_tipo_documento_id']]['fotos'] = $fotos;
        }

        $this->fotos = $inputs_fotos;

        $link_fotografia_bd = $this->obj_link->link_con_id(
            accion: 'fotografias_bd', link: $this->link, registro_id: $this->registro_id, seccion: 'inm_prospecto_ubicacion');
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $link_fotografia_bd, header: $header, ws: $ws);
        }

        $this->link_fotografia_bd = $link_fotografia_bd;

        return $template;
    }

    public function fotografias_bd(bool $header, bool $ws = false): array|stdClass{
        $this->link->beginTransaction();

        $inm_doc_prospecto_ubicacion =  new inm_doc_prospecto_ubicacion(link: $this->link);

        $names = array();
        foreach ($_FILES['fotos']['name'] as $key => $foto){
            $names[$key]['name'] = $foto;
        }

        foreach ($_FILES['fotos']['tmp_name'] as $key => $foto){
            $names[$key]['tmp_name'] = $foto;
        }

        $result = array();
        foreach ($names as $key => $name){
            $valor = array();
            foreach ($name['name'] as $item => $value){

                $valor['name'] = $name['name'][$item];
                $valor['tmp_name'] = $name['tmp_name'][$item];

                if($name['name'][$item] !== '' && $name['tmp_name'][$item] !== '') {
                    $registro['doc_tipo_documento_id'] = $key;
                    $registro['inm_prospecto_ubicacion_id'] = $this->registro_id;
                    $registro['es_foto'] = 'activo';
                    $_FILES['documento'] = $valor;
                    $result = $inm_doc_prospecto_ubicacion->alta_registro(registro: $registro);
                    if (errores::$error) {
                        $this->link->rollBack();
                        return $this->retorno_error(mensaje: 'Error al insertar datos', data: $result, header: $header, ws: $ws);
                    }
                }
            }
        }

        $this->link->commit();

        $link_fotografia_bd = $this->obj_link->link_con_id(
            accion: 'fotografias', link: $this->link, registro_id: $this->registro_id, seccion: 'inm_prospecto_ubicacion');
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $link_fotografia_bd, header: $header, ws: $ws);
        }

        if($header) {
            header('Location:' . $link_fotografia_bd);
            exit;
        }

        return $result;
    }

    public function elimina_foto_bd(){

    }

    public function img_btn_modal(string $src, int $css_id, array $class_css = array()): string|array
    {
        if($css_id<=0){
            return $this->errores->error('Error $css_id debe ser mayor a 0',$css_id);
        }

        $class_html = '';
        foreach ($class_css as $class){
            $class_html.=' '.$class;
        }

        $img = '<img class="img-thumbnail '.$class_html.'" src="'.$src.'" ';
        $img.= ' role="button" data-toggle="modal" data-target="#img_'.$css_id.'">';
        return $img;
    }

    public function etapa(bool $header, bool $ws = false): array|stdClass
    {
        $template = parent::modifica(header: false); // TODO: Change the autogenerated stub
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar template', data: $template, header: $header, ws: $ws);
        }

        $filtro = array();
        $filtro['pr_proceso.descripcion'] = 'PROSPECTO UBICACION';
        $columns_ds[] = 'pr_etapa_descripcion';

        $pr_etapa_proceso_id = (new pr_etapa_proceso_html(html: $this->html_base))->select_pr_etapa_proceso_id(
            cols: 12, con_registros: true, id_selected: -1, link: $this->link, columns_ds: $columns_ds,
            filtro: $filtro, label: 'Etapa');
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al obtener selector de etapa', data: $pr_etapa_proceso_id, header: $header, ws: $ws);
        }

        $this->inputs->pr_etapa_proceso_id = $pr_etapa_proceso_id;
        $hoy = date('Y-m-d');
        $fecha = $this->html->input_fecha(cols: 12, row_upd: new stdClass(), value_vacio: false, value: $hoy);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar input fecha', data: $fecha, header: $header, ws: $ws);
        }

        $this->inputs->fecha = $fecha;

        $observaciones = $this->html->input_text(cols: 12, disabled: false, name: 'observaciones', place_holder: 'Observaciones',
            row_upd: new stdClass(), value_vacio: false, required: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $observaciones,  header: $header, ws: $ws);
        }

        $this->inputs->observaciones = $observaciones;

        $registro = (new inm_prospecto_ubicacion(link: $this->link))->registro($this->registro_id);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $registro, header: $header, ws: $ws);
        }

        $link_alta_etapa = $this->obj_link->link_con_id(
            accion: 'etapa_bd', link: $this->link, registro_id: $registro['com_prospecto_id'], seccion: 'com_prospecto');
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $link_alta_etapa, header: $header, ws: $ws);
        }

        $this->link_alta_etapa = $link_alta_etapa;

        $etapas = (new com_prospecto(link: $this->link))->etapas(com_prospecto_id: $registro['com_prospecto_id']);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al obtener etapas', data: $etapas, header: $header, ws: $ws);
        }

        $this->etapas = $etapas;


        return $template;
    }

    public function integra_relacion(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = array();
        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_tipo_prospecto_id", label: "Tipo Prospecto",disabled: true);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "com_agente_id", label: "Agente",cols: 12);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $base = $this->base_upd(keys_selects: $keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        $inm_prospecto_ubicacion = (new inm_prospecto_ubicacion($this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar datos', data: $inm_prospecto_ubicacion, header: $header, ws: $ws);
        }

        $filtro['com_prospecto.id'] = $inm_prospecto_ubicacion['com_prospecto_id'];
        $relaciones = (new com_rel_agente(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al obtener etapas', data: $relaciones, header: $header, ws: $ws);
        }

        $this->relaciones = $relaciones->registros;

        return $r_modifica;
    }

    public function integra_relacion_bd(bool $header, bool $ws = false): array|stdClass{

        $this->link->beginTransaction();

        $inm_prospecto_ubicacion = (new inm_prospecto_ubicacion($this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar datos', data: $inm_prospecto_ubicacion, header: $header, ws: $ws);
        }

        $con_rel_agente = new com_rel_agente($this->link);
        $registro['com_agente_id'] = $_POST['com_agente_id'];
        $registro['com_prospecto_id'] = $inm_prospecto_ubicacion['com_prospecto_id'];
        $result = $con_rel_agente->alta_registro(registro: $registro);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar datos', data: $result, header: $header, ws: $ws);
        }

        $this->link->commit();

        $link_integra_relacion_bd = $this->obj_link->link_con_id(
            accion: 'integra_relacion', link: $this->link, registro_id: $this->registro_id, seccion: 'inm_prospecto_ubicacion');
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar link', data: $link_integra_relacion_bd, header: $header, ws: $ws);
        }

        if($header) {
            header('Location:' . $link_integra_relacion_bd);
            exit;
        }
    }

    private function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array(), $disabled = false): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label,disabled: $disabled);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
    }

    public function init_selects_inputs(): array{

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_prospecto_id", label: "Tipo de Prospecto");
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        $keys_selects = $this->init_selects(keys_selects: $keys_selects, key: "inm_prospecto_ubicacion_id ", label: "Prospecto Ubicacion");
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        return $keys_selects;
    }


    /**
     * @throws FileNotFoundException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     */
    final public function agrupa_documentos(bool $header, bool $ws = false): array|string
    {
        $documentos = explode(',', $_POST['documentos']);
        $pdfCollection = new PdfCollection();

        foreach ($documentos as $documento) {
            $registro = (new inm_doc_prospecto($this->link))->registro(registro_id: $documento, retorno_obj: true);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro, header: $header, ws: $ws);
            }
            $ruta_doc = $this->path_base . "$registro->doc_documento_ruta_relativa";
            $pdfCollection->addPdf($ruta_doc);
        }

        $fpdi = new Fpdi();
        $pdfMerger = new PdfMerger($fpdi);

        $outputPath = 'documentos.pdf';
        $pdfMerger->merge($pdfCollection, $outputPath, PdfMerger::MODE_DOWNLOAD);

        return $documentos;
    }

    public function valida_campos(array $campos): array
    {
        $campos_validos = array('documentos', 'receptor', 'asunto', 'mensaje');
        $campos_faltantes = array_diff($campos_validos, array_keys($campos));
        if (!empty($campos_faltantes)) {
            $mensaje_error = 'Faltan los siguientes campos: ' . implode(', ', $campos_faltantes);
            return $this->errores->error(mensaje: $mensaje_error, data: $campos_faltantes);
        }

        return $campos;
    }

    final public function envia_documentos(bool $header, bool $ws = false): array|string
    {
        $campos_necesarios = $this->valida_campos($_POST);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar campos', data: $campos_necesarios,
                header: $header, ws: $ws);
        }

        $validacion = (new _email($this->link))->validar_correo(correo: $campos_necesarios['receptor']);
        if (!$validacion) {
            $mensaje_error = sprintf(_email::ERROR_CORREO_NO_VALIDO, $campos_necesarios['receptor']);
            return $this->retorno_error(mensaje: $mensaje_error, data: $campos_necesarios,
                header: $header, ws: $ws);
        }

        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        $receptor = (new _email($this->link))->receptor(correo: $campos_necesarios['receptor']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener receptor', data: $receptor,
                header: $header, ws: $ws);
        }

        $emisor = (new _email($this->link))->emisor(correo: 'test@ivitec.mx');
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener emisor', data: $emisor,
                header: $header, ws: $ws);
        }

        $mensaje = (new _email($this->link))->mensaje(asunto: $campos_necesarios['asunto'],
            mensaje: $campos_necesarios['mensaje'], emisor: $emisor['not_emisor_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener mensaje', data: $mensaje,
                header: $header, ws: $ws);
        }

        $mensaje_receptor = (new _email($this->link))->mensaje_receptor(mensaje: $mensaje['not_mensaje_id'],
            receptor: $receptor['not_receptor_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener mensaje receptor', data: $mensaje_receptor,
                header: $header, ws: $ws);
        }

        $documentos = explode(',', $campos_necesarios);
        $r_alta_doc_etapa = new stdClass();

        $mensaje_adjuntos = (new _email($this->link))->adjuntos(mensaje: $mensaje['not_mensaje_id'],
            documentos: $documentos);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener adjuntos', data: $mensaje_adjuntos,
                header: $header, ws: $ws);
        }

        print_r($mensaje_adjuntos);
        exit();

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $r_alta_doc_etapa,
                siguiente_view: "documentos", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_alta_doc_etapa, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_alta_doc_etapa->siguiente_view = "documentos";

        return $r_alta_doc_etapa;
    }

    final public function verifica_documentos(bool $header, bool $ws = false): array|string
    {
        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        $documentos = explode(',', $_POST['documentos']);
        $r_alta_doc_etapa = new stdClass();

        foreach ($documentos as $documento) {
            $prospecto = (new inm_prospecto($this->link))->registro(registro_id: $this->registro_id);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener prospecto', data: $prospecto, header: $header, ws: $ws);
            }

            if ($prospecto['inm_prospecto_etapa'] != "VERIFICADO") {
                $registro = (new inm_doc_prospecto($this->link))->registro(registro_id: $documento, retorno_obj: true);
                if (errores::$error) {
                    return $this->retorno_error(mensaje: 'Error al obtener documento', data: $registro, header: $header, ws: $ws);
                }

                $r_alta_doc_etapa = (new inm_doc_prospecto($this->link))->
                genera_documento_etapa(doc_documento_id: $registro->doc_documento_id, etapa: "VERIFICADO");
                if (errores::$error) {
                    return $this->retorno_error(mensaje: 'Error al generar documento etapa',
                        data: $r_alta_doc_etapa, header: $header, ws: $ws);
                }

                $accion = (new inm_prospecto($this->link))->actualiza_etapa(com_prospecto_id: $this->registro_id,
                    etapa: "VERIFICADO");
                if (errores::$error) {
                    return $this->retorno_error(mensaje: 'Error al actualizar etapa', data: $accion, header: $header, ws: $ws);
                }
            }

        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $r_alta_doc_etapa,
                siguiente_view: "documentos", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_alta_doc_etapa, JSON_THROW_ON_ERROR);
            exit;
        }
        $r_alta_doc_etapa->siguiente_view = "documentos";

        return $r_alta_doc_etapa;
    }


    public function tipos_documentos(bool $header, bool $ws = false): array
    {
        $inm_conf_docs_prospecto = (new _inm_prospecto())->integra_inm_documentos_ubicacion(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar buttons', data: $inm_conf_docs_prospecto, header: $header, ws: $ws);
        }

        $salida['draw'] = count($inm_conf_docs_prospecto);
        $salida['recordsTotal'] = count($inm_conf_docs_prospecto);
        $salida['recordsFiltered'] = count($inm_conf_docs_prospecto);
        $salida['data'] = $inm_conf_docs_prospecto;

        header('Content-Type: application/json');
        echo json_encode($salida);
        exit;
    }

    public function load_html(bool $header, bool $ws = false): array
    {
        $r_modifica = $this->init_modifica();
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $data = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_base(controlador: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar datos para front', data: $data,
                header: $header, ws: $ws);
        }

        $base = $this->base_upd(keys_selects: $data->keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }

        echo $this->inputs->inm_producto_infonavit_id;
        echo $this->inputs->inm_attr_tipo_credito_id;
        echo $this->inputs->inm_destino_credito_id;
        echo $this->inputs->es_segundo_credito;
        echo $this->inputs->inm_plazo_credito_sc_id;

        exit;
    }

    public function generales(bool $header, bool $ws = false): array|stdClass
    {

        $inm_prospecto = (new _generales())->inm_prospecto(inm_prospecto_id: $this->registro_id, link: $this->link);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al ajusta prospecto', data: $inm_prospecto, header: $header, ws: $ws);
        }


        $this->registro = new stdClass();
        $this->registro->inm_prospecto = $inm_prospecto;


        $inm_conyuge = (new _generales())->inm_conyuge_init();
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al al inicializar inm_conyuge', data: $inm_conyuge, header: $header, ws: $ws);
        }

        $existe_conyuge = (new inm_prospecto(link: $this->link))->existe_conyuge(inm_prospecto_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al validar si existe inm_conyuge', data: $existe_conyuge, header: $header, ws: $ws);
        }

        if ($existe_conyuge) {
            $inm_conyuge = (new inm_prospecto(link: $this->link))->inm_conyuge(inm_prospecto_id: $this->registro_id);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener inm_conyuge', data: $inm_conyuge, header: $header, ws: $ws);
            }
            $edad = (new calculo())->edad_hoy(fecha_nacimiento: $inm_conyuge->inm_conyuge_fecha_nacimiento);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener edad', data: $edad, header: $header, ws: $ws);
            }
            $inm_conyuge->inm_conyuge_edad = $edad;
            $inm_conyuge->inm_conyuge_edad .= ' AÑOS';

            $inm_conyuge->inm_conyuge_estado_civil = $inm_prospecto->inm_estado_civil_descripcion;
        }


        $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_conyuge', row: $inm_conyuge);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener nombre_completo', data: $nombre_completo, header: $header, ws: $ws);
        }

        $inm_conyuge->inm_conyuge_nombre_completo = $nombre_completo;


        $lugar_fecha_nac = (new _generales())->data_nacimiento(entidad_edo: 'dp_estado', entidad_mun: 'dp_municipio', entidad_name: 'inm_conyuge', row: $inm_conyuge);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener lugar_fecha_nac', data: $lugar_fecha_nac, header: $header, ws: $ws);
        }

        $inm_conyuge->inm_conyuge_lugar_fecha_nac = $lugar_fecha_nac;


        $inm_tipo_beneficiarios = (new inm_tipo_beneficiario(link: $this->link))->registros_activos(retorno_obj: true);

        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_tipo_beneficiarios', data: $inm_tipo_beneficiarios,
                header: $header, ws: $ws);
        }

        $inm_beneficiarios = (new inm_prospecto(link: $this->link))->inm_beneficiarios(inm_prospecto_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_beneficiarios', data: $inm_beneficiarios,
                header: $header, ws: $ws);
        }

        foreach ($inm_tipo_beneficiarios as $indice => $inm_tipo_beneficiario) {
            if (!isset($inm_tipo_beneficiario->inm_beneficiarios)) {
                $inm_tipo_beneficiario->inm_beneficiarios = array();
            }
            foreach ($inm_beneficiarios as $inm_beneficiario) {

                $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_beneficiario', row: $inm_beneficiario);
                if (errores::$error) {
                    return $this->retorno_error(mensaje: 'Error al obtener nombre_completo', data: $nombre_completo, header: $header, ws: $ws);
                }
                $inm_beneficiario->inm_beneficiario_nombre_completo = $nombre_completo;

                if ((int)$inm_beneficiario->inm_tipo_beneficiario_id === (int)$inm_tipo_beneficiario->inm_tipo_beneficiario_id) {
                    $inm_tipo_beneficiario->inm_beneficiarios[] = $inm_beneficiario;
                }
                $inm_tipo_beneficiarios[$indice] = $inm_tipo_beneficiario;
            }
        }
        $this->registro->inm_conyuge = $inm_conyuge;
        $this->registro->inm_tipo_beneficiarios = $inm_tipo_beneficiarios;


        $inm_referencias = (new inm_prospecto(link: $this->link))->inm_referencias(inm_prospecto_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_referencias', data: $inm_referencias,
                header: $header, ws: $ws);
        }

        foreach ($inm_referencias as $indice => $inm_referencia) {
            $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_referencia_prospecto', row: $inm_referencia);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener nombre_completo', data: $nombre_completo, header: $header, ws: $ws);
            }
            $inm_referencia->inm_referencia_prospecto_nombre_completo = $nombre_completo;
            $inm_referencia->inm_referencia_prospecto_telefono = $inm_referencia->inm_referencia_prospecto_lada;
            $inm_referencia->inm_referencia_prospecto_telefono .= $inm_referencia->inm_referencia_prospecto_numero;

            $inm_referencias[$indice] = $inm_referencia;
        }

        $this->registro->inm_referencias = $inm_referencias;

        return new stdClass();
    }


    /**
     * POR DOCUMENTAR EN WIKI
     * Inicializa el objeto de datos 'datatables' con cinco columnas - Id, Nombre, NSS, RFC y CURP.
     * También establece el filtro en estas columnas para buscar y filtrar en la tabla de datos.
     *
     * @return stdClass Un objeto con dos propiedades 'columns' y 'filtro', que definen las columnas que se mostrarán
     *  en la tabla de datos y los campos a filtrar respectivamente.
     * @version 2.347.3
     *
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_prospecto_ubicacion_id"]["titulo"] = "Id";
        $columns["com_prospecto_razon_social"]["titulo"] = "Nombre";
        $columns["inm_prospecto_ubicacion_nss"]["titulo"] = "NSS";
        $columns["com_prospecto_rfc"]["titulo"] = "RFC";
        $columns["inm_prospecto_ubicacion_curp"]["titulo"] = "CURP";
        $columns["inm_prospecto_ubicacion_fecha_alta"]["titulo"] = "Fecha Alta";
        $columns["com_agente_id"]["titulo"] = "Agente Id";
        $columns["com_agente_descripcion"]["titulo"] = "Agente";
        $columns["pr_etapa_descripcion"]["titulo"] = "Etapa Actual";


        $filtro = array("inm_prospecto_ubicacion.id", "com_prospecto.razon_social",
            'inm_prospecto_ubicacion.nss', 'com_prospecto.rfc',
            'inm_prospecto_ubicacion.curp', 'inm_prospecto_ubicacion.fecha_alta', 'com_agente.id', 'com_agente.descripcion');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function init_links(): array|string
    {
        $links = $this->obj_link->genera_links(controler: $this);
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al generar links', data: $links);
            print_r($error);
            exit;
        }

        $link = $this->obj_link->get_link(seccion: "inm_prospecto", accion: "modifica_direccion");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link modifica_direccion', data: $link);
            print_r($error);
            exit;
        }
        $this->link_modifica_direccion = $link;

        $link = $this->obj_link->get_link(seccion: "inm_prospecto", accion: "agrupa_documentos");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link agrupa_documentos', data: $link);
            print_r($error);
            exit;
        }
        $this->link_agrupa_documentos = $link;

        $link = $this->obj_link->get_link(seccion: "inm_prospecto", accion: "verifica_documentos");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link verifica_documentos', data: $link);
            print_r($error);
            exit;
        }
        $this->link_verifica_documentos = $link;

        $link = $this->obj_link->get_link(seccion: "inm_prospecto", accion: "envia_documentos");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link envia_documentos', data: $link);
            print_r($error);
            exit;
        }
        $this->link_envia_documentos = $link;

        $link = $this->obj_link->get_link(seccion: "inm_prospecto_ubicacion", accion: "integra_relacion_bd");
        if (errores::$error) {
            $error = $this->errores->error(mensaje: 'Error al recuperar link envia_documentos', data: $link);
            print_r($error);
            exit;
        }
        $this->link_alta_integra_relacion_bd = $link;

        return $link;
    }

    public function inserta_domicilio(bool $header, bool $ws): array|stdClass
    {
        $domicilio = (new inm_prospecto_ubicacion(link: $this->link))->inserta_domicilio(domicilio: $_POST,
            inm_prospecto_ubicacion_id: $this->registro_id, link: $this->link);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al insertar domicilio', data: $domicilio,
                header: $header, ws: $ws);
        }

        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($domicilio, JSON_THROW_ON_ERROR);
            exit;
        }

        return $domicilio;
    }

    protected function key_selects_txt(array $keys_selects, int $cols_descripcion = 12): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'nombre',
            keys_selects: $keys_selects, place_holder: 'Nombre');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_paterno',
            keys_selects: $keys_selects, place_holder: 'Apellido Paterno');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'apellido_materno',
            keys_selects: $keys_selects, place_holder: 'Apellido Materno', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'lada_com',
            keys_selects: $keys_selects, place_holder: 'Lada', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'documentos',
            keys_selects: $keys_selects, place_holder: 'Documentos');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'receptor',
            keys_selects: $keys_selects, place_holder: 'Receptor');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'asunto',
            keys_selects: $keys_selects, place_holder: 'Asunto');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'mensaje',
            keys_selects: $keys_selects, place_holder: 'Mensaje');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['lada_com']->regex = $this->validacion->patterns['lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'numero_com',
            keys_selects: $keys_selects, place_holder: 'Numero', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['numero_com']->regex = $this->validacion->patterns['tel_sin_lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'cel_com',
            keys_selects: $keys_selects, place_holder: 'Cel', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['cel_com']->regex = $this->validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'correo_com',
            keys_selects: $keys_selects, place_holder: 'Correo', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['correo_com']->regex = $this->validacion->patterns['correo_html5'];

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'razon_social',
            keys_selects: $keys_selects, place_holder: 'Razon Social');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'nombre_empresa_patron',
            keys_selects: $keys_selects, place_holder: 'Nombre Empresa Patron', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'nrp_nep',
            keys_selects: $keys_selects, place_holder: 'Numero de Registro Patronal', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'lada_nep',
            keys_selects: $keys_selects, place_holder: 'Lada Tel Empresa', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'numero_nep',
            keys_selects: $keys_selects, place_holder: 'Numero Tel Empresa', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'extension_nep',
            keys_selects: $keys_selects, place_holder: 'Extension Empresa', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'cp',
            keys_selects: $keys_selects, place_holder: 'CP', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'colonia',
            keys_selects: $keys_selects, place_holder: 'Colonia', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'calle',
            keys_selects: $keys_selects, place_holder: 'Calle', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'numero_exterior',
            keys_selects: $keys_selects, place_holder: 'Numero Ext', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'numero_interior',
            keys_selects: $keys_selects, place_holder: 'Numero Int', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'texto_exterior',
            keys_selects: $keys_selects, place_holder: 'Numero Ext', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }


        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'texto_interior',
            keys_selects: $keys_selects, place_holder: 'Numero Int', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'nss',
            keys_selects: $keys_selects, place_holder: 'NSS', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'curp',
            keys_selects: $keys_selects, place_holder: 'CURP', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'descuento_pension_alimenticia_dh',
            keys_selects: $keys_selects, place_holder: 'Desc Pension Alimenticia Derecho Habiente', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'descuento_pension_alimenticia_fc',
            keys_selects: $keys_selects, place_holder: 'Desc Pension Alimenticia Co Acreditado', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'monto_credito_solicitado_dh',
            keys_selects: $keys_selects, place_holder: 'Monto Precalificacion ', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'monto_ahorro_voluntario',
            keys_selects: $keys_selects, place_holder: 'Monto Ahorro Voluntario ', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'observaciones',
            keys_selects: $keys_selects, place_holder: 'Observaciones');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'sub_cuenta',
            keys_selects: $keys_selects, place_holder: 'Sub Cuenta', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'monto_final',
            keys_selects: $keys_selects, place_holder: 'Monto Final', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'descuento',
            keys_selects: $keys_selects, place_holder: 'Descuento', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'puntos',
            keys_selects: $keys_selects, place_holder: 'Puntos', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'adeudo_hipoteca',
            keys_selects: $keys_selects, place_holder: 'Adeudo Hipoteca', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'adeudo_predial',
            keys_selects: $keys_selects, place_holder: 'Adeudo Predial', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'cuenta_agua',
            keys_selects: $keys_selects, place_holder: 'Cuenta Agua', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'adeudo_agua',
            keys_selects: $keys_selects, place_holder: 'Adeudo Agua', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'adeudo_luz',
            keys_selects: $keys_selects, place_holder: 'Adeudo Luz', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'monto_devolucion',
            keys_selects: $keys_selects, place_holder: 'Monto Devolucion', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'nivel',
            keys_selects: $keys_selects, place_holder: 'Nivel', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'recamaras',
            keys_selects: $keys_selects, place_holder: 'Recamaras', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'metros_terreno',
            keys_selects: $keys_selects, place_holder: 'Metros Terreno', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'metros_construccion',
            keys_selects: $keys_selects, place_holder: 'Metros Construccion', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'telefono_casa',
            keys_selects: $keys_selects, place_holder: 'Telefono de Casa', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }
        $keys_selects['telefono_casa']->regex = $this->validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'correo_empresa',
            keys_selects: $keys_selects, place_holder: 'Correo Empresa', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects['correo_empresa']->regex = $this->validacion->patterns['correo_html5'];

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $keys_selects = array();
        $keys_selects = (new \gamboamartin\inmuebles\controllers\_inm_prospecto_ubicacion())->integra_keys_selects_comercial(
            controlador: $this,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener registro prospecto', data: $keys_selects,
                header: $header, ws: $ws);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'liga_red_social',
            keys_selects: $keys_selects, place_holder: 'Liga Red Social', required: false);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects', data: $keys_selects,
                header: $header, ws: $ws);
        }
        $keys_selects['liga_red_social']->disabled = true;

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'rfc',
            keys_selects: $keys_selects, place_holder: 'RFC', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'manzana',
            keys_selects: $keys_selects, place_holder: 'Manzana', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'lote',
            keys_selects: $keys_selects, place_holder: 'Lote', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'cuenta_predial',
            keys_selects: $keys_selects, place_holder: 'Cuenta Predial', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $registro_prospecto = (new inm_prospecto_ubicacion(link: $this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener registro prospecto', data: $registro_prospecto,
                header: $header, ws: $ws);
        }

        $keys_selects = $this->key_selects_txt(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $inputs = $this->genera_inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $headers = (new \gamboamartin\inmuebles\controllers\_inm_prospecto_ubicacion())->headers_front(controlador: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar headers', data: $headers, header: $header, ws: $ws);
        }

        $fecha_otorgamiento_credito = $this->html->input_fecha(cols: 12, row_upd: $this->row_upd, value_vacio: false,
            name: 'fecha_otorgamiento_credito', place_holder: 'Fecha Otorgamiento Credito',
            value: $this->row_upd->fecha_otorgamiento_credito);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $fecha_otorgamiento_credito,header: $header,
                ws: $ws);
        }

        $this->inputs->fecha_otorgamiento_credito = $fecha_otorgamiento_credito;

        $class_upd = '_upd_prospecto_ubicacion';
        $conyuge = (new _conyuge())->inputs_conyuge(controler: $this,class_upd: $class_upd);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener conyuge', data: $conyuge,
                header: $header, ws: $ws);
        }

        $this->inputs->conyuge = $conyuge;


        $direccion = (new _direccion())->inputs_direccion(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener direccion', data: $direccion,
                header: $header, ws: $ws);
        }

        $this->inputs->direccion = $direccion;

        return $r_modifica;
    }

    public function modifica_bd(bool $header, bool $ws): array|stdClass
    {
        //print_r($_POST);exit;
        $this->link->beginTransaction();

        $result_transacciones = (new inm_prospecto_ubicacion(link: $this->link))->transacciones_upd(
            inm_prospecto_ubicacion_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al ejecutar result_transacciones', data: $result_transacciones,
                header: $header, ws: $ws);
        }

        $r_modifica = parent::modifica_bd(header: false, ws: $ws); // TODO: Change the autogenerated stub
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al modificar inm_prospecto_ubicacion', data: $r_modifica,
                header: $header, ws: $ws);
        }
        $this->link->commit();

        $_SESSION[$r_modifica->salida][]['mensaje'] = $r_modifica->mensaje . ' del id ' . $this->registro_id;
        $this->header_out(result: $r_modifica, header: $header, ws: $ws);

        return $r_modifica;


    }

    public function modifica_direccion(bool $header, bool $ws = false): array|stdClass
    {
        if (!isset($_POST['dp_calle_pertenece_id'])) {
            return $this->retorno_error(mensaje: 'Error no existe dp_calle_pertenece_id', data: $_POST, header: $header,
                ws: $ws);
        }

        $this->link->beginTransaction();

        $siguiente_view = (new actions())->init_alta_bd();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header: $header, ws: $ws);
        }

        if (isset($_POST['btn_action_next'])) {
            unset($_POST['btn_action_next']);
        }

        $modifica = array();
        $modifica['dp_calle_pertenece_id'] = $_POST['dp_calle_pertenece_id'];
        $modifica['texto_exterior'] = $_POST['texto_exterior'];
        $modifica['texto_interior'] = $_POST['texto_interior'];
        $modifica = (new com_direccion(link: $this->link))->modifica_bd(registro: $modifica, id: $_POST['com_direccion_id']);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al modificar com_direccion', data: $modifica,
                header: $header, ws: $ws);
        }

        $this->link->commit();

        if ($header) {
            $this->retorno_base(registro_id: $this->registro_id, result: $modifica,
                siguiente_view: "modifica", ws: $ws);
        }
        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode(array(), JSON_THROW_ON_ERROR);
            exit;
        }

        return $modifica;
    }


    public function regenera_curp(bool $header, bool $ws = false): array|string
    {
        $columnas[] = 'inm_prospecto_ubicacion_id';
        $columnas[] = 'inm_prospecto_ubicacion_curp';
        $registros = (new inm_prospecto_ubicacion(link: $this->link))->registros(columnas: $columnas);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener prospectos', data: $registros,
                header: $header, ws: $ws);
        }

        foreach ($registros as $inm_prospecto_ubicacion) {
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto_ubicacion['inm_prospecto_ubicacion_curp']);
            if ($nss === '') {
                $inm_prospecto_ubicacion_upd['curp'] = 'XEXX010101HNEXXXA4';
                $upd = (new inm_prospecto_ubicacion(link: $this->link))->modifica_bd(registro: $inm_prospecto_ubicacion_upd,
                    id: $inm_prospecto_ubicacion['inm_prospecto_ubicacion_id']);
                if (errores::$error) {
                    $this->link->rollBack();
                    return $this->retorno_error(mensaje: 'Error al upd prospecto', data: $upd,
                        header: $header, ws: $ws);
                }
                print_r($upd);

            }
            $this->link->commit();
        }
        exit;
    }

    public function regenera_nombre_completo_valida(bool $header, bool $ws = false): array|string
    {

        $this->link->beginTransaction();
        $regenera = (new inm_prospecto_ubicacion(link: $this->link))->regenera_nombre_completo_valida();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al regenerar', data: $regenera,
                header: $header, ws: $ws);
        }
        $this->link->commit();
        print_r($regenera);

        exit;
    }

    public function regenera_nss(bool $header, bool $ws = false): array|string
    {
        $columnas[] = 'inm_prospecto_ubicacion_id';
        $columnas[] = 'inm_prospecto_ubicacion_nss';
        $registros = (new inm_prospecto_ubicacion(link: $this->link))->registros(columnas: $columnas);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener prospectos', data: $registros,
                header: $header, ws: $ws);
        }

        foreach ($registros as $inm_prospecto_ubicacion) {
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto_ubicacion['inm_prospecto_ubicacion_nss']);
            if ($nss === '') {
                $inm_prospecto_ubicacion_upd['nss'] = '99999999999';
                $upd = (new inm_prospecto_ubicacion(link: $this->link))->modifica_bd(registro: $inm_prospecto_ubicacion_upd,
                    id: $inm_prospecto_ubicacion['inm_prospecto_ubicacion_id']);
                if (errores::$error) {
                    $this->link->rollBack();
                    return $this->retorno_error(mensaje: 'Error al upd prospecto', data: $upd,
                        header: $header, ws: $ws);
                }
                print_r($upd);

            }
            $this->link->commit();
        }
        exit;
    }

    public function regenera_rel_com_agente(bool $header, bool $ws = false): array|string
    {

        $this->link->beginTransaction();
        $regenera = (new inm_prospecto_ubicacion(link: $this->link))->regenera_agentes_iniciales();
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al regenerar', data: $regenera,
                header: $header, ws: $ws);
        }
        $this->link->commit();
        print_r($regenera);

        exit;
    }

    final public function subir_documento(bool $header, bool $ws = false)
    {
        $inm_prospecto = (new inm_prospecto_ubicacion(link: $this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_prospecto', data: $inm_prospecto,
                header: $header, ws: $ws);
        }

        $inm_conf_docs_prospecto = (new inm_conf_docs_prospecto_ubicacion(link: $this->link))->filtro_and(
            columnas: ['doc_tipo_documento_id'],
            filtro: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_conf_docs_prospecto', data: $inm_conf_docs_prospecto,
                header: $header, ws: $ws);
        }

        $this->inputs = new stdClass();

        $filtro['inm_prospecto_ubicacion.id'] = $this->registro_id;
        $inm_prospecto_id = (new inm_prospecto_ubicacion_html(html: $this->html_base))->select_inm_prospecto_ubicacion_id(
            cols: 12, con_registros: true, id_selected: $this->registro_id, link: $this->link, filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_id, header: $header, ws: $ws);
        }
        $this->inputs->inm_prospecto_ubicacion_id  = $inm_prospecto_id;

        $doc_ids = array_map(function ($registro) {
            return $registro['doc_tipo_documento_id'];
        }, $inm_conf_docs_prospecto->registros);

        $doc_tipos_documentos = array();

        if (count($doc_ids) > 0) {
            $doc_tipos_documentos = (new _doctos())->documentos_de_prospecto_ubicacion(inm_prospecto_ubicacion_id: $this->registro_id,
                link: $this->link, todos: false, tipos_documentos: $doc_ids);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener tipos de documento', data: $doc_tipos_documentos,
                    header: $header, ws: $ws);
            }
        }

        $_doc_tipo_documento_id = -1;
        $filtro = array();
        if (isset($_GET['doc_tipo_documento_id'])) {
            $_doc_tipo_documento_id = $_GET['doc_tipo_documento_id'];
            $filtro['doc_tipo_documento.id'] = $_GET['doc_tipo_documento_id'];
        }

        $doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols: 12, con_registros: true, id_selected: $_doc_tipo_documento_id, link: $this->link, filtro: $filtro,
            registros: $doc_tipos_documentos);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_id, header: $header, ws: $ws);
        }
        $this->inputs->doc_tipo_documento_id = $doc_tipo_documento_id;

        $documento = $this->html->input_file(cols: 12, name: 'documento', row_upd: new stdClass(), value_vacio: false);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs', data: $documento, header: $header, ws: $ws);
        }

        $this->inputs->documento = $documento;

        $link_alta_doc = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'inm_doc_prospecto_ubicacion');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar link', data: $link_alta_doc, header: $header, ws: $ws);
        }

        $this->link_inm_doc_prospecto_alta_bd = $link_alta_doc;

        $btn_action_next = $this->html->hidden('btn_action_next', value: 'documentos');
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $id_retorno = $this->html->hidden('id_retorno', value: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $seccion_retorno = $this->html->hidden('seccion_retorno', value: $this->seccion);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next', data: $btn_action_next, header: $header, ws: $ws);
        }

        $this->inputs->btn_action_next = $btn_action_next;
        $this->inputs->id_retorno = $id_retorno;
        $this->inputs->seccion_retorno = $seccion_retorno;


    }

    public function valida_prioridad(bool $header, bool $ws = false)
    {
        $inm_prospecto_ubicacion_id = (new inm_prospecto_ubicacion(link: $this->link))->valida_prioridad_campo($_GET);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_ubicacion_id, header: $header, ws: $ws);
        }

        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($inm_prospecto_ubicacion_id, JSON_THROW_ON_ERROR);
            exit;
        }

        return $inm_prospecto_ubicacion_id;
    }

}
