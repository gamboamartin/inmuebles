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
use gamboamartin\comercial\models\com_prospecto_etapa;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\models\_base_paquete;
use gamboamartin\inmuebles\models\_email;
use gamboamartin\inmuebles\models\_inm_prospecto;
use gamboamartin\inmuebles\models\_upd_prospecto;
use gamboamartin\inmuebles\models\inm_beneficiario;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto;
use gamboamartin\inmuebles\models\inm_conf_institucion_campo;
use gamboamartin\inmuebles\models\inm_doc_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto;
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

class controlador_inm_prospecto extends _ctl_formato
{

    public stdClass $header_frontend;
    public inm_prospecto_html $html_entidad;

    public string $link_alta_etapa = '';
    public array $etapas = array();
    public string $link_inm_doc_prospecto_alta_bd = '';
    public string $link_modifica_direccion = '';
    public string $link_agrupa_documentos = '';
    public string $link_verifica_documentos = '';
    public string $link_envia_documentos = '';

    public array $inm_conf_docs_prospecto = array();

    public array $direcciones = array();
    public array $beneficiarios = array();
    public array $referencias = array();
    public array $acciones_headers = array();


    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_prospecto(link: $link);
        $html_ = new inm_prospecto_html(html: $html);
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
        $keys_selects = (new _keys_selects())->keys_selects_prospecto(controler: $this, keys_selects: $keys_selects);
        if (errores::$error) {
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
            'texto_exterior', 'texto_interior', 'documentos', 'receptor', 'asunto', 'mensaje');

        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";
        $init_data['com_medio_prospeccion'] = "gamboamartin\\comercial";
        $init_data['com_prospecto'] = "gamboamartin\\comercial";

        $init_data['inm_institucion_hipotecaria'] = "gamboamartin\\inmuebles";
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";
        $init_data['inm_attr_tipo_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_destino_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_plazo_credito_sc'] = "gamboamartin\\inmuebles";
        $init_data['inm_tipo_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_persona_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_sindicato'] = "gamboamartin\\inmuebles";
        $init_data['inm_ocupacion'] = "gamboamartin\\inmuebles";
        $init_data['com_tipo_direccion'] = "gamboamartin\\comercial";

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

        $inm_conf_docs_prospecto = (new _inm_prospecto())->integra_inm_documentos(controler: $this);
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

    public function etapa(bool $header, bool $ws = false): array|stdClass
    {
        $template = parent::modifica(header: false); // TODO: Change the autogenerated stub
        if (errores::$error) {
            $this->retorno_error(mensaje: 'Error al generar template', data: $template, header: $header, ws: $ws);
        }

        $filtro = array();
        $filtro['pr_proceso.descripcion'] = 'PROSPECCION';
        $columns_ds[] = 'pr_etapa_descripcion';

        $pr_etapa_proceso_id = (new pr_etapa_proceso_html(html: $this->html_base))->select_pr_etapa_proceso_id(
            cols: 12, con_registros: true, id_selected: -1, link: $this->link, columns_ds: $columns_ds,
            filtro: $filtro, label: 'Etapa');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener selector de etapa', data: $pr_etapa_proceso_id, header: $header, ws: $ws);
        }

        $com_agentes = (new com_agente(link: $this->link))->com_agentes_session();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener agentes',data:  $com_agentes, header: $header, ws: $ws);
        }

        $disabled = false;
        if(count($com_agentes) > 0){
            $disabled = true;
        }

        $this->inputs->pr_etapa_proceso_id = $pr_etapa_proceso_id;
        $hoy = date('Y-m-d\TH:i:s');
        $fecha = $this->html->input_fecha(cols: 12, row_upd: new stdClass(), value_vacio: false, disabled: $disabled,
            value: $hoy, value_hora: true);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input fecha', data: $fecha, header: $header, ws: $ws);
        }

        $this->inputs->fecha = $fecha;

        $observaciones = $this->html->input_text(cols: 12, disabled: false, name: 'observaciones', place_holder: 'Observaciones',
            row_upd: new stdClass(), value_vacio: false, required: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $observaciones,  header: $header, ws: $ws);
        }

        $this->inputs->observaciones = $observaciones;

        $registro = (new inm_prospecto(link: $this->link))->registro($this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar link', data: $registro, header: $header, ws: $ws);
        }

        $link_alta_etapa = $this->obj_link->link_con_id(
            accion: 'etapa_bd', link: $this->link, registro_id: $registro['com_prospecto_id'], seccion: 'com_prospecto');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar link', data: $link_alta_etapa, header: $header, ws: $ws);
        }

        $this->link_alta_etapa = $link_alta_etapa;

        $etapas = (new com_prospecto(link: $this->link))->etapas(com_prospecto_id: $registro['com_prospecto_id']);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener etapas', data: $etapas, header: $header, ws: $ws);
        }

        $this->etapas = $etapas;

        return $template;
    }

    private function init_selects(array $keys_selects, string $key, string $label, int|null $id_selected = -1, int $cols = 6,
                                  bool  $con_registros = true, array $filtro = array()): array
    {
        $keys_selects = $this->key_select(cols: $cols, con_registros: $con_registros, filtro: $filtro, key: $key,
            keys_selects: $keys_selects, id_selected: $id_selected, label: $label);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        return $keys_selects;
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
        $columns["inm_prospecto_id"]["titulo"] = "Id";
        $columns["inm_prospecto_razon_social"]["titulo"] = "Nombre";
        $columns["inm_prospecto_nss"]["titulo"] = "NSS";
        $columns["inm_prospecto_rfc"]["titulo"] = "RFC";
        $columns["inm_prospecto_curp"]["titulo"] = "CURP";
        $columns["inm_prospecto_fecha_alta"]["titulo"] = "Fecha Alta";
        $columns["com_agente_id"]["titulo"] = "Agente Id";
        $columns["com_agente_descripcion"]["titulo"] = "Agente";
        $columns["pr_etapa_descripcion"]["titulo"] = "Etapa";


        $filtro = array("inm_prospecto.id", "inm_prospecto.razon_social", 'inm_prospecto.nss', 'inm_prospecto.rfc',
            'inm_prospecto.curp', 'inm_prospecto.fecha_alta', 'com_agente.id', 'com_agente.descripcion');

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

        return $link;
    }

    public function inserta_beneficiario(bool $header, bool $ws): array|stdClass
    {
        $r_inm_beneficiario_bd = (new _upd_prospecto())->inserta_beneficiario(beneficiario: $_POST,
            inm_prospecto_id: $_GET['registro_id'], link: $this->link);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al insertar r_inm_beneficiario_bd', data: $r_inm_beneficiario_bd,
                header: $header, ws: $ws);
        }

        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_inm_beneficiario_bd, JSON_THROW_ON_ERROR);
            exit;
        }

        return $r_inm_beneficiario_bd;
    }

    public function inserta_domicilio(bool $header, bool $ws): array|stdClass
    {
        $domicilio = (new _upd_prospecto())->inserta_domicilio(domicilio: $_POST,
            inm_prospecto_id: $this->registro_id, link: $this->link);
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

    public function inserta_referencia(bool $header, bool $ws): array|stdClass
    {
        $r_inm_referencia_bd = (new _upd_prospecto())->inserta_referencia(referencia: $_POST,
            inm_prospecto_id: $_GET['registro_id'], link: $this->link);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al insertar r_inm_referencia_bd', data: $r_inm_referencia_bd,
                header: $header, ws: $ws);
        }

        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($r_inm_referencia_bd, JSON_THROW_ON_ERROR);
            exit;
        }

        return $r_inm_referencia_bd;
    }

    public function integra_relacion(bool $header, bool $ws = false): array|stdClass
    {
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
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
        return $r_modifica;
    }
    public function init_selects_inputs(): array{

        $keys_selects = $this->init_selects(keys_selects: array(), key: "com_tipo_prospecto_id", label: "Tipo de Prospecto");
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al integrar selector',data:  $keys_selects);
        }

        return $keys_selects;
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

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'numero_exterior',
            keys_selects: $keys_selects, place_holder: 'Numero Ext', required: false);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12, key: 'numero_interior',
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

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template', data: $r_modifica, header: $header, ws: $ws);
        }

        $data = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_base(controlador: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar datos para front', data: $data,
                header: $header, ws: $ws);
        }

        $registro_prospecto = (new inm_prospecto(link: $this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener registro prospecto', data: $registro_prospecto,
                header: $header, ws: $ws);
        }

        $keys_selects = $this->key_selects_txt(keys_selects: $data->keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $inputs = $this->genera_inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        /*foreach ($keys_selects as $key => $value) {
            echo '<br>';
            //print_r($key);
            if(isset($value->place_holder))
                print_r($value->place_holder);
            else
                print_r($value->label);
        }exit;*/
        if(isset($registro_prospecto['inm_institucion_hipotecaria_id'])){
            $filtro_campo['adm_seccion.descripcion'] = $this->seccion;
            $campos_totales = (new adm_campo(link: $this->link))->filtro_and(filtro: $filtro_campo);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener campos totales', data: $campos_totales,
                    header: $header, ws: $ws);
            }

            $filtro_conf['inm_institucion_hipotecaria.id'] = $registro_prospecto['inm_institucion_hipotecaria_id'];
            if($campos_totales->n_registros > 0) {
                $keys_ajustado = array();
                foreach ($campos_totales->registros as $campo) {
                    $filtro_conf['adm_campo.id'] = $campo['adm_campo_id'];
                    $r_conf_institucion = (new inm_conf_institucion_campo(link: $this->link))->filtro_and(filtro: $filtro_conf);
                    if (errores::$error) {
                        return $this->retorno_error(mensaje: 'Error al obtener registros campos', data: $r_conf_institucion,
                            header: $header, ws: $ws);
                    }

                    if($r_conf_institucion->n_registros <= 0){
                        $val = $campo['adm_campo_descripcion'];
                        $this->inputs->$val = '';
                    }/*else{
                        if(isset($keys_selects[$r_conf_institucion->registros[0]['adm_campo_descripcion']])) {
                            $keys_ajustado[$r_conf_institucion->registros[0]['adm_campo_descripcion']] =
                                $keys_selects[$r_conf_institucion->registros[0]['adm_campo_descripcion']];
                            if ($r_conf_institucion->registros[0]['adm_campo_es_foranea'] === 'activo') {
                                $keys_ajustado[$r_conf_institucion->registros[0]['adm_campo_descripcion']]->cols =
                                    $r_conf_institucion->registros[0]['inm_conf_institucion_campo_cols'];
                                $keys_ajustado[$r_conf_institucion->registros[0]['adm_campo_descripcion']]->label =
                                    $r_conf_institucion->registros[0]['inm_conf_institucion_campo_alias'];
                            } else {
                                $keys_ajustado[$r_conf_institucion->registros[0]['adm_campo_descripcion']]->cols =
                                    $r_conf_institucion->registros[0]['inm_conf_institucion_campo_cols'];
                                $keys_ajustado[$r_conf_institucion->registros[0]['adm_campo_descripcion']]->place_holder =
                                    $r_conf_institucion->registros[0]['inm_conf_institucion_campo_alias'];
                            }
                        }
                    }*/
                }
            }
        }
       /* $base = $this->base_upd(keys_selects: $data->keys_selects, params: array(), params_ajustados: array());
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al integrar base', data: $base, header: $header, ws: $ws);
        }*/

        /*$this->acciones_headers['3'] = new  stdClass();
        $this->acciones_headers['3']->id_css_button_acc = 'inserta_domicilio';
        $this->acciones_headers['3']->style_button_acc = 'success';
        $this->acciones_headers['3']->tag_button_acc = 'Guardar';

        $this->acciones_headers['9'] = new  stdClass();
        $this->acciones_headers['9']->id_css_button_acc = 'inserta_beneficiario';
        $this->acciones_headers['9']->style_button_acc = 'success';
        $this->acciones_headers['9']->tag_button_acc = 'Guardar';

        $this->acciones_headers['10'] = new  stdClass();
        $this->acciones_headers['10']->id_css_button_acc = 'inserta_referencia';
        $this->acciones_headers['10']->style_button_acc = 'success';
        $this->acciones_headers['10']->tag_button_acc = 'Guardar';*/

        $headers = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->headers_front(controlador: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar headers', data: $headers, header: $header, ws: $ws);
        }

        $inputs = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_nacimiento(controlador: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar inputs', data: $inputs, header: $header, ws: $ws);
        }

        $class_upd = '_upd_prospecto';
        $conyuge = (new _conyuge())->inputs_conyuge(controler: $this,class_upd: $class_upd);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener conyuge', data: $conyuge,
                header: $header, ws: $ws);
        }

        $this->inputs->conyuge = $conyuge;

        $beneficiario = (new _beneficiario())->inputs_beneficiario(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener beneficiario', data: $beneficiario,
                header: $header, ws: $ws);
        }

        $this->inputs->beneficiario = $beneficiario;

        $direccion = (new _direccion())->inputs_direccion(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener direccion', data: $direccion,
                header: $header, ws: $ws);
        }

        $this->inputs->direccion = $direccion;

        $filtro['inm_prospecto.id'] = $this->registro_id;

        $r_inm_beneficiario = (new inm_beneficiario(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios', data: $r_inm_beneficiario,
                header: $header, ws: $ws);
        }


        $params = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->params_btn(accion_retorno: __FUNCTION__,
            registro_id: $this->registro_id, seccion_retorno: $this->tabla);

        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener params', data: $params,
                header: $header, ws: $ws);
        }

        $beneficiarios = $r_inm_beneficiario->registros;

        $beneficiarios = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->rows(controlador: $this,
            datas: $beneficiarios, params: $params, seccion_exe: 'inm_beneficiario');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios link del', data: $beneficiarios,
                header: $header, ws: $ws);
        }

        $this->beneficiarios = $beneficiarios;

        $direcciones = (new com_direccion_prospecto(link: $this->link))->filtro_and(filtro: array('com_prospecto_id' =>
            $this->registro['com_prospecto_id']));
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener direcciones', data: $direcciones,
                header: $header, ws: $ws);
        }


        $direcciones_reg = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->rows_direccion(controlador: $this,
            datas: $direcciones->registros, params: $params, seccion_exe: 'com_direccion', seccion_sec: 'com_direccion_prospecto');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios link del', data: $direcciones,
                header: $header, ws: $ws);
        }

        $this->direcciones = $direcciones_reg;

        $referencia = (new _referencia())->inputs_referencia(controler: $this);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener referencias', data: $referencia,
                header: $header, ws: $ws);
        }
        $this->inputs->referencia = $referencia;

        $r_inm_referencia_prospecto = (new inm_referencia_prospecto(link: $this->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener referencia_prospectos', data: $r_inm_referencia_prospecto,
                header: $header, ws: $ws);
        }

        $params = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->params_btn(accion_retorno: __FUNCTION__,
            registro_id: $this->registro_id, seccion_retorno: $this->tabla);

        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener params', data: $params,
                header: $header, ws: $ws);
        }

        $referencia_prospectos = $r_inm_referencia_prospecto->registros;

        $referencia_prospectos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->rows(controlador: $this,
            datas: $referencia_prospectos, params: $params, seccion_exe: 'inm_referencia_prospecto');
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios link del', data: $referencia_prospectos,
                header: $header, ws: $ws);
        }

        $this->referencias = $referencia_prospectos;

        return $r_modifica;
    }

    public function modifica_bd(bool $header, bool $ws): array|stdClass
    {
        //print_r($_POST);exit;
        $this->link->beginTransaction();

        $result_transacciones = (new inm_prospecto(link: $this->link))->transacciones_upd(inm_prospecto_id: $this->registro_id);
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al ejecutar result_transacciones', data: $result_transacciones,
                header: $header, ws: $ws);
        }

        $r_modifica = parent::modifica_bd(header: false, ws: $ws); // TODO: Change the autogenerated stub
        if (errores::$error) {
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al modificar inm_prospecto', data: $r_modifica,
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
        $columnas[] = 'inm_prospecto_id';
        $columnas[] = 'inm_prospecto_curp';
        $registros = (new inm_prospecto(link: $this->link))->registros(columnas: $columnas);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener prospectos', data: $registros,
                header: $header, ws: $ws);
        }

        foreach ($registros as $inm_prospecto) {
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto['inm_prospecto_curp']);
            if ($nss === '') {
                $inm_prospecto_upd['curp'] = 'XEXX010101HNEXXXA4';
                $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,
                    id: $inm_prospecto['inm_prospecto_id']);
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
        $regenera = (new inm_prospecto(link: $this->link))->regenera_nombre_completo_valida();
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
        $columnas[] = 'inm_prospecto_id';
        $columnas[] = 'inm_prospecto_nss';
        $registros = (new inm_prospecto(link: $this->link))->registros(columnas: $columnas);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener prospectos', data: $registros,
                header: $header, ws: $ws);
        }

        foreach ($registros as $inm_prospecto) {
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto['inm_prospecto_nss']);
            if ($nss === '') {
                $inm_prospecto_upd['nss'] = '99999999999';
                $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,
                    id: $inm_prospecto['inm_prospecto_id']);
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
        $regenera = (new inm_prospecto(link: $this->link))->regenera_agentes_iniciales();
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
        $inm_prospecto = (new inm_prospecto(link: $this->link))->registro(registro_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_prospecto', data: $inm_prospecto,
                header: $header, ws: $ws);
        }

        $inm_conf_docs_prospecto = (new inm_conf_docs_prospecto(link: $this->link))->filtro_and(
            columnas: ['doc_tipo_documento_id'],
            filtro: array('inm_attr_tipo_credito_id' => $inm_prospecto['inm_attr_tipo_credito_id']));
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al obtener inm_conf_docs_prospecto', data: $inm_conf_docs_prospecto,
                header: $header, ws: $ws);
        }

        $this->inputs = new stdClass();

        $filtro['inm_prospecto.id'] = $this->registro_id;
        $inm_prospecto_id = (new inm_prospecto_html(html: $this->html_base))->select_inm_prospecto_id(
            cols: 12, con_registros: true, id_selected: $this->registro_id, link: $this->link, filtro: $filtro);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_id, header: $header, ws: $ws);
        }
        $this->inputs->inm_prospecto_id = $inm_prospecto_id;

        $doc_ids = array_map(function ($registro) {
            return $registro['doc_tipo_documento_id'];
        }, $inm_conf_docs_prospecto->registros);

        $doc_tipos_documentos = array();

        if (count($doc_ids) > 0) {
            $doc_tipos_documentos = (new _doctos())->documentos_de_prospecto(inm_prospecto_id: $this->registro_id,
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

        $link_alta_doc = $this->obj_link->link_alta_bd(link: $this->link, seccion: 'inm_doc_prospecto');
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

    public function tipos_documentos(bool $header, bool $ws = false): array
    {
        $inm_conf_docs_prospecto = (new _inm_prospecto())->integra_inm_documentos(controler: $this);
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

    public function valida_prioridad(bool $header, bool $ws = false)
    {
        $resultado = (new inm_prospecto(link: $this->link))->valida_prioridad_campo($_GET);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al generar input', data: $resultado, header: $header, ws: $ws);
        }

        if ($ws) {
            header('Content-Type: application/json');
            echo json_encode($resultado, JSON_THROW_ON_ERROR);
            exit;
        }

        return $resultado;
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
}
