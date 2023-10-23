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
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\_base;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\models\_base_paquete;
use gamboamartin\inmuebles\models\_inm_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_rel_conyuge_prospecto;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\doc_tipo_documento_html;
use html\dp_estado_html;
use html\dp_municipio_html;
use PDO;
use stdClass;
use Throwable;

class controlador_inm_prospecto extends _ctl_formato {

    public stdClass $header_frontend;
    public inm_prospecto_html $html_entidad;

    public string $link_inm_doc_prospecto_alta_bd = '';

    public array $inm_conf_docs_prospecto = array();


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

        $this->html_entidad = $html_;

        $this->header_frontend = new stdClass();


    }

    /**
     * Genera un formulario de alta
     * @param bool $header Muestra resultado en web
     * @param bool $ws Muestra resultado a nivel ws
     * @return array|string
     * @version 2.247.2
     */
    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = array();
        $keys_selects = (new _keys_selects())->keys_selects_prospecto(controler: $this,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
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
     * Inicializa los campos view para frontend
     * @return array
     * @version 2.250.2
     */
    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('nombre','apellido_paterno','apellido_materno','telefono','correo_com','razon_social',
            'lada_com','numero_com','cel_com','descuento_pension_alimenticia_dh','descuento_pension_alimenticia_fc',
            'monto_credito_solicitado_dh','monto_ahorro_voluntario','nombre_empresa_patron','nrp_nep','lada_nep',
            'numero_nep','extension_nep','nss','curp','rfc','numero_exterior','numero_interior','observaciones',
            'fecha_nacimiento','sub_cuenta','monto_final','descuento','puntos','telefono_casa','correo_empresa');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['com_tipo_prospecto'] = "gamboamartin\\comercial";

        $init_data['inm_institucion_hipotecaria'] = "gamboamartin\\inmuebles";
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";
        $init_data['inm_attr_tipo_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_destino_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_plazo_credito_sc'] = "gamboamartin\\inmuebles";
        $init_data['inm_tipo_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_persona_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_sindicato'] = "gamboamartin\\inmuebles";
        $init_data['inm_ocupacion'] = "gamboamartin\\inmuebles";


        $init_data = (new _base_paquete())->init_data_domicilio(init_data: $init_data);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $init_data);
        }

        $init_data['inm_nacionalidad'] = "gamboamartin\\inmuebles";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function convierte_cliente(bool $header, bool $ws = false): array|string
    {
        $this->link->beginTransaction();

        $retorno = (new \gamboamartin\inmuebles\controllers\_base())->init_retorno();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener datos de retorno', data: $retorno, header: true, ws: false);
        }

        $conversion = (new inm_prospecto(link: $this->link))->convierte_cliente(inm_prospecto_id:  $this->registro_id);

        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al convertir en cliente', data: $conversion, header: true, ws: false);
        }


        $this->link->commit();

        if($header){
            if($retorno->id_retorno === -1) {
                $retorno->id_retorno = $this->registro_id;
            }
            $this->retorno_base(registro_id:$retorno->id_retorno, result: $conversion, siguiente_view: $retorno->siguiente_view,
                ws:  $ws,seccion_retorno: $this->seccion, valida_permiso: true);
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($conversion, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }
        $conversion->r_alta_rel->siguiente_view = $retorno->siguiente_view;


        return $conversion->r_alta_rel;


    }

    final public function documentos(bool $header, bool $ws = false): array
    {

        $template = $this->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $template, header: $header,ws:  $ws);
        }

        $inm_conf_docs_prospecto = (new _inm_prospecto())->integra_inm_documentos(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar buttons',data:  $inm_conf_docs_prospecto, header: $header,ws:  $ws);
        }

        $this->inm_conf_docs_prospecto = $inm_conf_docs_prospecto;


        return $inm_conf_docs_prospecto;

    }


    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     * @version 1.2.0
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_prospecto_id"]["titulo"] = "Id";
        $columns["inm_prospecto_razon_social"]["titulo"] = "Nombre";
        $columns["inm_prospecto_nss"]["titulo"] = "NSS";
        $columns["inm_prospecto_rfc"]["titulo"] = "RFC";
        $columns["inm_prospecto_curp"]["titulo"] = "CURP";


        $filtro = array("inm_prospecto.id","inm_prospecto.razon_social",'inm_prospecto.nss','inm_prospecto.rfc',
            'inm_prospecto.curp');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    protected function key_selects_txt(array $keys_selects, int $cols_descripcion = 12): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada_com',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['lada_com']->regex = $this->validacion->patterns['lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_com',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['numero_com']->regex = $this->validacion->patterns['tel_sin_lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'cel_com',
            keys_selects:$keys_selects, place_holder: 'Cel');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['cel_com']->regex = $this->validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'correo_com',
            keys_selects:$keys_selects, place_holder: 'Correo', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['correo_com']->regex = $this->validacion->patterns['correo_html5'];

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'razon_social',
            keys_selects:$keys_selects, place_holder: 'Razon Social');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre_empresa_patron',
            keys_selects:$keys_selects, place_holder: 'Nombre Empresa Patron', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nrp_nep',
            keys_selects:$keys_selects, place_holder: 'Numero de Registro Patronal', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'lada_nep',
            keys_selects:$keys_selects, place_holder: 'Lada Tel Empresa', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'numero_nep',
            keys_selects:$keys_selects, place_holder: 'Numero Tel Empresa', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'extension_nep',
            keys_selects:$keys_selects, place_holder: 'Extension Empresa', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'rfc',
            keys_selects:$keys_selects, place_holder: 'RFC', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'numero_exterior',
            keys_selects:$keys_selects, place_holder: 'Numero Ext', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'numero_interior',
            keys_selects:$keys_selects, place_holder: 'Numero Int', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nss',
            keys_selects:$keys_selects, place_holder: 'NSS', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'curp',
            keys_selects:$keys_selects, place_holder: 'CURP', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_dh',
            keys_selects:$keys_selects, place_holder: 'Desc Pension Alimenticia Derecho Habiente', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_fc',
            keys_selects:$keys_selects, place_holder: 'Desc Pension Alimenticia Co Acreditado', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_credito_solicitado_dh',
            keys_selects:$keys_selects, place_holder: 'Monto Precalificacion ', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_ahorro_voluntario',
            keys_selects:$keys_selects, place_holder: 'Monto Ahorro Voluntario ', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'observaciones',
            keys_selects:$keys_selects, place_holder: 'Observaciones', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'sub_cuenta',
            keys_selects:$keys_selects, place_holder: 'Sub Cuenta', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'monto_final',
            keys_selects:$keys_selects, place_holder: 'Monto Final', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'descuento',
            keys_selects:$keys_selects, place_holder: 'Descuento', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'puntos',
            keys_selects:$keys_selects, place_holder: 'Puntos', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'telefono_casa',
            keys_selects:$keys_selects, place_holder: 'Telefono de Casa', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['telefono_casa']->regex = $this->validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'correo_empresa',
            keys_selects:$keys_selects, place_holder: 'Correo Empresa', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['correo_empresa']->regex = $this->validacion->patterns['correo_html5'];

        return $keys_selects;
    }

    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $adm_usuario = (new adm_usuario(link: $this->link))->registro(registro_id: $_SESSION['usuario_id'],
            columnas: array('adm_grupo_root'));
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al obtener adm_usuario ',data:  $adm_usuario);
            print_r($error);
            exit;
        }


        $filtro = array();
        if($adm_usuario['adm_grupo_root'] === 'inactivo'){
            $filtro['adm_usuario.id'] = $_SESSION['usuario_id'];
        }


        $keys_selects = array();

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  $filtro, key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $this->registro['com_agente_id'], label: 'Agente');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_tipo_prospecto_id',
            keys_selects:$keys_selects, id_selected: $this->registro['com_tipo_prospecto_id'], label: 'Tipo de prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_institucion_hipotecaria_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_institucion_hipotecaria_id'], label: 'Institucion Hipotecaria');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_producto_infonavit_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_producto_infonavit_id'], label: 'Producto Infonavit');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_attr_tipo_credito_id'], label: 'Tipo de Credito');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_destino_credito_id'], label: 'Destino de Credito');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $disabled = true;
        if($this->registro['inm_prospecto_es_segundo_credito'] === 'SI'){
            $disabled = false;
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_plazo_credito_sc_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_plazo_credito_sc_id'], label: 'Plazo de Segundo Credito',disabled: $disabled);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_tipo_discapacidad_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_tipo_discapacidad_id'], label: 'Tipo de Discapacidad');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_persona_discapacidad_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_persona_discapacidad_id'], label: 'Persona de Discapacidad');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }


        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_pais_id'], label: 'Pais');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $filtro = array();
        $filtro['dp_pais.id'] = $this->registro['dp_pais_id'];

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro: $filtro, key: 'dp_estado_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_estado_id'], label: 'Estado');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $filtro = array();
        $filtro['dp_estado.id'] = $this->registro['dp_estado_id'];

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_municipio_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_municipio_id'], label: 'Municipio');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $filtro = array();
        $filtro['dp_municipio.id'] = $this->registro['dp_municipio_id'];
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_cp_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_cp_id'], label: 'CP');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $filtro = array();
        $filtro['dp_cp.id'] = $this->registro['dp_cp_id'];
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_colonia_postal_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_colonia_postal_id'], label: 'Colonia');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $filtro = array();
        $filtro['dp_colonia_postal.id'] = $this->registro['dp_colonia_postal_id'];
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_calle_pertenece_id',
            keys_selects:$keys_selects, id_selected: $this->registro['dp_calle_pertenece_id'], label: 'Calle');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }


        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_sindicato_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_sindicato_id'], label: 'Sindicato');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_nacionalidad_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_nacionalidad_id'], label: 'Nacionalidad');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_ocupacion_id',
            keys_selects:$keys_selects, id_selected: $this->registro['inm_ocupacion_id'], label: 'Ocupacion');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }



        if($this->registro['inm_prospecto_nss'] === ''){
            $this->row_upd->nss = '99999999999';
        }
        if($this->registro['inm_prospecto_curp'] === ''){
            $this->row_upd->curp = 'XEXX010101HNEXXXA4';
        }
        if($this->registro['inm_prospecto_rfc'] === ''){
            $this->row_upd->rfc = 'XAXX010101000';
        }


        $radios = (new \gamboamartin\inmuebles\models\_inm_comprador())->radios_chk(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar radios',data:  $radios, header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $headers = array();
        $headers['1'] = '1. DATOS PERSONALES';
        $headers['2'] = '2. DATOS DE CONTACTO';
        $headers['3'] = '3. DOMICILIO';
        $headers['4'] = '4. CREDITO';
        $headers['5'] = '5. MONTO CREDITO';
        $headers['6'] = '6. DISCAPACIDAD';
        $headers['7'] = '7. DATOS EMPRESA TRABAJADOR';
        $headers['8'] = '8. DATOS DE CONYUGE';

        $headers = (new _base(html: $this->html_base))->genera_headers(controler: $this,headers:  $headers);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar headers',data:  $headers, header: $header,ws:  $ws);
        }


        $dp_estado_nacimiento_id = (new dp_estado_html(html: $this->html_base))->select_dp_estado_id(cols: 6,
            con_registros: true, id_selected: $this->registro['dp_estado_nacimiento_id'], link: $this->link,
            label: 'Edo Nac', name: 'dp_estado_nacimiento_id');

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $dp_estado_nacimiento_id,
                header: $header,ws:  $ws);
        }

        $this->inputs->dp_estado_nacimiento_id = $dp_estado_nacimiento_id;

        $filtro = array('dp_estado.id'=>$this->registro['dp_estado_nacimiento_id']);
        $dp_municipio_nacimiento_id = (new dp_municipio_html(html: $this->html_base))->select_dp_municipio_id(cols: 6,
            con_registros: true, id_selected: $this->registro['dp_municipio_nacimiento_id'], link: $this->link,
            filtro: $filtro, label: 'Mun Nac', name: 'dp_municipio_nacimiento_id');

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $dp_municipio_nacimiento_id,
                header: $header,ws:  $ws);
        }

        $this->inputs->dp_municipio_nacimiento_id = $dp_municipio_nacimiento_id;


        $fecha_nacimiento = $this->html->input_fecha(cols: 6, row_upd: $this->row_upd, value_vacio: false,
            name: 'fecha_nacimiento', place_holder: 'Fecha Nac', value: $this->row_upd->fecha_nacimiento);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener input',data:  $fecha_nacimiento,
                header: $header,ws:  $ws);
        }

        $this->inputs->fecha_nacimiento = $fecha_nacimiento;


        $conyuge = (new _conyuge())->inputs_conyuge(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener conyuge',data:  $conyuge,
                header: $header,ws:  $ws);
        }

        $this->inputs->conyuge = $conyuge;


        return $r_modifica;
    }

    public function modifica_bd(bool $header, bool $ws): array|stdClass
    {
        $this->link->beginTransaction();
        $conyuge = array();
        if(isset($_POST['conyuge'])){
            $conyuge = $_POST['conyuge'];
            unset($_POST['conyuge']);
        }

        $tiene_dato_conyuge = false;

        foreach ($conyuge as $campo=>$value){
            if($value === null){
                $value = '';
            }
            $value = trim($value);
            if($value!==''){
                $tiene_dato_conyuge = true;
                break;
            }
        }

        if($tiene_dato_conyuge){
            $alta_rel_conyuge_prospecto = (new inm_rel_conyuge_prospecto(link: $this->link))->alta_registro(
                registro: $conyuge);
            if(errores::$error){
                $this->link->rollBack();
                return $this->retorno_error(mensaje: 'Error al insertar conyuge',data:  $alta_rel_conyuge_prospecto,
                    header: $header,ws:  $ws);
            }
        }
        else{


        }

        $r_modifica = parent::modifica_bd(header: false,ws:  $ws); // TODO: Change the autogenerated stub
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al modificar inm_prospecto',data:  $r_modifica,
                header: $header,ws:  $ws);
        }
        $this->link->commit();

        $_SESSION[$r_modifica->salida][]['mensaje'] = $r_modifica->mensaje.' del id '.$this->registro_id;
        $this->header_out(result: $r_modifica, header: $header,ws:  $ws);

        return $r_modifica;


    }

    public function regenera_curp(bool $header, bool $ws = false): array|string{
        $columnas[]  ='inm_prospecto_id';
        $columnas[]  ='inm_prospecto_curp';
        $registros = (new inm_prospecto(link: $this->link))->registros(columnas: $columnas);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener prospectos',data:  $registros,
                header: $header,ws:  $ws);
        }

        foreach ($registros as $inm_prospecto){
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto['inm_prospecto_curp']);
            if($nss === ''){
                $inm_prospecto_upd['curp'] = 'XEXX010101HNEXXXA4';
                $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,
                    id:  $inm_prospecto['inm_prospecto_id']);
                if(errores::$error){
                    $this->link->rollBack();
                    return $this->retorno_error(mensaje: 'Error al upd prospecto',data:  $upd,
                        header: $header,ws:  $ws);
                }
                print_r($upd);

            }
            $this->link->commit();
        }
        exit;
    }

    public function regenera_nss(bool $header, bool $ws = false): array|string{
        $columnas[]  ='inm_prospecto_id';
        $columnas[]  ='inm_prospecto_nss';
        $registros = (new inm_prospecto(link: $this->link))->registros(columnas: $columnas);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener prospectos',data:  $registros,
                header: $header,ws:  $ws);
        }

        foreach ($registros as $inm_prospecto){
            $this->link->beginTransaction();
            $nss = trim($inm_prospecto['inm_prospecto_nss']);
            if($nss === ''){
                $inm_prospecto_upd['nss'] = '99999999999';
                $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,
                    id:  $inm_prospecto['inm_prospecto_id']);
                if(errores::$error){
                    $this->link->rollBack();
                    return $this->retorno_error(mensaje: 'Error al upd prospecto',data:  $upd,
                        header: $header,ws:  $ws);
                }
                print_r($upd);

            }
            $this->link->commit();
        }
        exit;
    }

    final public function subir_documento(bool $header, bool $ws = false){

        $this->inputs = new stdClass();

        $filtro['inm_prospecto.id'] = $this->registro_id;
        $inm_prospecto_id = (new inm_prospecto_html(html: $this->html_base))->select_inm_prospecto_id(
            cols: 12,con_registros:  true,id_selected:  $this->registro_id,link:  $this->link,filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_id, header: $header, ws: $ws);
        }
        $this->inputs->inm_prospecto_id = $inm_prospecto_id;

        $doc_tipos_documentos = (new _doctos())->documentos_de_prospecto(inm_prospecto_id: $this->registro_id,
            link: $this->link, todos: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener tipos de documento', data: $doc_tipos_documentos,
                header: $header, ws: $ws);
        }

        $_doc_tipo_documento_id = -1;
        $filtro = array();
        if(isset($_GET['doc_tipo_documento_id'])){
            $_doc_tipo_documento_id = $_GET['doc_tipo_documento_id'];
            $filtro['doc_tipo_documento.id'] = $_GET['doc_tipo_documento_id'];
        }

        $doc_tipo_documento_id = (new doc_tipo_documento_html(html: $this->html_base))->select_doc_tipo_documento_id(
            cols: 12, con_registros: true, id_selected: $_doc_tipo_documento_id, link: $this->link, filtro: $filtro,
            registros: $doc_tipos_documentos);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_prospecto_id, header: $header, ws: $ws);
        }
        $this->inputs->doc_tipo_documento_id = $doc_tipo_documento_id;

        $documento = $this->html->input_file(cols: 12,name:  'documento',row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $documento, header: $header,ws:  $ws);
        }

        $this->inputs->documento = $documento;

        $link_alta_doc = $this->obj_link->link_alta_bd(link:  $this->link,seccion:  'inm_doc_prospecto');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar link',data:  $link_alta_doc, header: $header,ws:  $ws);
        }

        $this->link_inm_doc_prospecto_alta_bd = $link_alta_doc;

        $btn_action_next = $this->html->hidden('btn_action_next',value: 'documentos');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next',data:  $btn_action_next, header: $header,ws:  $ws);
        }

        $id_retorno = $this->html->hidden('id_retorno',value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next',data:  $btn_action_next, header: $header,ws:  $ws);
        }

        $seccion_retorno = $this->html->hidden('seccion_retorno',value: $this->seccion);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar btn_action_next',data:  $btn_action_next, header: $header,ws:  $ws);
        }

        $this->inputs->btn_action_next = $btn_action_next;
        $this->inputs->id_retorno = $id_retorno;
        $this->inputs->seccion_retorno = $seccion_retorno;


    }


}
