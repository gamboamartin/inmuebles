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
use gamboamartin\inmuebles\html\inm_co_acreditado_html;
use gamboamartin\inmuebles\html\inm_comprador_html;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\doc_tipo_documento_html;
use PDO;
use setasign\Fpdi\Fpdi;
use stdClass;
use Throwable;

class controlador_inm_comprador extends _ctl_base {

    public array $inm_ubicaciones = array();
    public array $inm_co_acreditados = array();
    public array $inm_conf_docs_comprador = array();

    public string $link_inm_doc_comprador_alta_bd = '';

    public string $link_rel_ubi_comp_alta_bd = '';
    public string $link_inm_rel_co_acred_alta_bd = '';
    public string $link_asigna_nuevo_co_acreditado_bd = '';

    private inm_comprador_html $html_entidad;

    public stdClass $header_frontend;

    public function __construct(PDO      $link, html $html = new \gamboamartin\template_1\html(),
                                stdClass $paths_conf = new stdClass())
    {
        $modelo = new inm_comprador(link: $link);
        $html_ = new inm_comprador_html(html: $html);
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
     * Integra formulario de alta
     * @param bool $header Si header retorna resultado en web
     * @param bool $ws Si ws muestra resultado en json
     * @return array|string
     * @version 1.102.1
     */
    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = (new _inm_comprador())->keys_selects(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar row_upd',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $radios = (new _inm_comprador())->radios(checked_default_cd: 1, checked_default_esc: 2, controler: $this);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al integrar radios',data:  $radios, header: $header,ws:  $ws);
        }


        $btn_collapse_all = $this->html->button_para_java(id_css: 'collapse_all',style:  'primary',tag:  'Ver/Ocultar Todo');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al btn_collapse_all',data:  $btn_collapse_all, header: $header,ws:  $ws);
        }

        $this->buttons['btn_collapse_all'] = $btn_collapse_all;

        $headers['1'] = '1. CRÉDITO SOLICITADO';
        $headers['2'] = '2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO';
        $headers['3'] = '3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO';
        $headers['4'] = '4. DATOS DE LA EMPRESA O PATRÓN';
        $headers['5'] = '5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS';
        $headers['13'] = '13. DATOS FISCALES PARA FACTURACION';
        $headers['14'] = '14. CONTROL INTERNO';

        foreach ($headers as $n_apartado=>$tag_header){
            $id_css_button = "collapse_a$n_apartado";
            $key_header = "apartado_$n_apartado";

            $header_apartado = $this->html_entidad->header_collapsible(id_css_button: $id_css_button,
                style_button: 'primary', tag_button: 'Ver/Ocultar',tag_header:  $tag_header);

            if(errores::$error){
                return $this->retorno_error(
                    mensaje: 'Error al generar header',data:  $header_apartado, header: $header,ws:  $ws);
            }

            $this->header_frontend->$key_header = $header_apartado;
        }




        return $r_alta;
    }

    public function asigna_co_acreditado(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        $inputs = (new _keys_selects())->base_plantilla(controler: $this,function: __FUNCTION__);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs, header: $header,ws:  $ws);
        }


        $link_inm_rel_co_acred_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,seccion: 'inm_rel_co_acred');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar link',data:  $link_inm_rel_co_acred_alta_bd,
                header: $header,ws:  $ws);
        }

        $this->link_inm_rel_co_acred_alta_bd = $link_inm_rel_co_acred_alta_bd;

        $inm_co_acreditados = (new _inm_comprador())->inm_co_acreditados(inm_comprador_id: $this->registro_id,
            link:  $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener compradores',data:  $inm_co_acreditados,
                header: $header,ws:  $ws);
        }

        $this->inm_co_acreditados = $inm_co_acreditados;

        return $r_modifica;
    }

    public function asigna_nuevo_co_acreditado(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = (new _keys_selects())->base_co_acreditado(controler: $this,function: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        return $r_modifica;
    }

    final public function asigna_nuevo_co_acreditado_bd(bool $header, bool $ws = false): array|stdClass{

        $this->link->beginTransaction();

        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = $_POST['id_retorno'];
            unset($_POST['id_retorno']);
        }
        $siguiente_view = (new actions())->init_alta_bd();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view,
                header:  $header, ws: $ws);
        }

        $inm_comprador_id = $this->registro_id;
        $inm_co_acreditado_ins['nss'] = $_POST['nss'];
        $inm_co_acreditado_ins['curp'] = $_POST['curp'];
        $inm_co_acreditado_ins['rfc'] = $_POST['rfc'];
        $inm_co_acreditado_ins['apellido_paterno'] = $_POST['apellido_paterno'];
        $inm_co_acreditado_ins['apellido_materno'] = $_POST['apellido_materno'];
        $inm_co_acreditado_ins['nombre'] = $_POST['nombre'];
        $inm_co_acreditado_ins['lada'] = $_POST['lada'];
        $inm_co_acreditado_ins['numero'] = $_POST['numero'];
        $inm_co_acreditado_ins['celular'] = $_POST['celular'];
        $inm_co_acreditado_ins['genero'] = $_POST['genero'];
        $inm_co_acreditado_ins['correo'] = $_POST['correo'];
        $inm_co_acreditado_ins['nombre_empresa_patron'] = $_POST['nombre_empresa_patron'];
        $inm_co_acreditado_ins['nrp'] = $_POST['nrp'];
        $inm_co_acreditado_ins['lada_nep'] = $_POST['lada_nep'];
        $inm_co_acreditado_ins['numero_nep'] = $_POST['numero_nep'];
        $inm_co_acreditado_ins['extension_nep'] = $_POST['extension_nep'];

        $result = (new inm_comprador(link: $this->link))->asigna_nuevo_co_acreditado_bd(
            inm_comprador_id: $inm_comprador_id, inm_co_acreditado: $inm_co_acreditado_ins);

        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar datos',data:  $result,header:  $header,ws:  $ws);
        }
        $this->link->commit();

        if($header){
            if($id_retorno === -1) {
                $id_retorno = $this->registro_id;
            }
            $this->retorno_base(registro_id:$id_retorno, result: $result, siguiente_view: $siguiente_view,
                ws:  $ws,seccion_retorno: $this->seccion, valida_permiso: true);
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($result, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }
        $result->siguiente_view = $siguiente_view;


        return $result;
    }

    /**
     * Integra un formulario para la asignacion de una ubicacion
     * @param bool $header Si header retorna resultado en web
     * @param bool $ws Si ws muestra resultado en json
     * @return array|stdClass
     * @version 1.105.1
     */
    public function asigna_ubicacion(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $registro = $this->modelo->registro(registro_id: $this->registro_id,retorno_obj: true);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener registro',data:  $registro,header: $header,ws: $ws);
        }

        $keys_selects = (new _keys_selects())->key_selects_asigna_ubicacion(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }


        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $inm_comprador_id = $this->html->hidden(name:'inm_comprador_id',value: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al in_registro_id',data:  $inm_comprador_id,
                header: $header,ws:  $ws);
        }


        $hiddens = (new _keys_selects())->hiddens(controler: $this,funcion: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inputs',data:  $hiddens,
                header: $header,ws:  $ws);
        }

        $inputs = (new _keys_selects())->inputs_form_base(btn_action_next: $hiddens->btn_action_next,
            controler: $this, id_retorno: $hiddens->id_retorno, in_registro_id: $hiddens->in_registro_id,
            inm_comprador_id: $inm_comprador_id, inm_ubicacion_id: '', precio_operacion: $hiddens->precio_operacion,
            seccion_retorno: $hiddens->seccion_retorno);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs, header: $header,ws:  $ws);
        }

        $inm_ubicacion_id = (new _inm_comprador())->inm_ubicacion_id_input(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al inm_ubicacion_id',data:  $inm_ubicacion_id,
                header: $header,ws:  $ws);
        }

        $this->inputs->inm_ubicacion_id = $inm_ubicacion_id;

        $link_rel_ubi_comp_alta_bd = $this->obj_link->link_alta_bd(link: $this->link,seccion: 'inm_rel_ubi_comp');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar link',data:  $link_rel_ubi_comp_alta_bd,
                header: $header,ws:  $ws);
        }

        $this->link_rel_ubi_comp_alta_bd = $link_rel_ubi_comp_alta_bd;

        $inm_ubicaciones = (new _inm_comprador())->inm_ubicaciones(inm_comprador_id: $this->registro_id,
            link:  $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener compradores',data:  $inm_ubicaciones,
                header: $header,ws:  $ws);
        }

        $this->inm_ubicaciones = $inm_ubicaciones;

        return $r_modifica;
    }

    /**
     * Integra los campos para las vistas de frontend base
     * @return array
     * @version 1.106.1
     */
    protected function campos_view(): array
    {
        $keys = new stdClass();
        $keys->inputs = array('descripcion', 'es_segundo_credito', 'descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'rfc','apellido_paterno','apellido_materno','nombre','numero_exterior','numero_interior','telefono',
            'nombre_empresa_patron','nrp_nep','lada_nep','numero_nep','extension_nep','lada_com','numero_com',
            'cel_com','genero','correo_com');
        $keys->selects = array();


        $init_data = array();
        $init_data['inm_producto_infonavit'] = "gamboamartin\\inmuebles";
        $init_data['inm_attr_tipo_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_destino_credito'] = "gamboamartin\\inmuebles";
        $init_data['inm_plazo_credito_sc'] = "gamboamartin\\inmuebles";
        $init_data['inm_tipo_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_persona_discapacidad'] = "gamboamartin\\inmuebles";
        $init_data['inm_estado_civil'] = "gamboamartin\\inmuebles";

        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";

        $init_data['cat_sat_regimen_fiscal'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_moneda'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_forma_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_metodo_pago'] = "gamboamartin\\cat_sat";
        $init_data['cat_sat_uso_cfdi'] = "gamboamartin\\cat_sat";
        $init_data['com_tipo_cliente'] = "gamboamartin\\comercial";
        $init_data['cat_sat_tipo_persona'] = "gamboamartin\\cat_sat";

        $init_data['bn_cuenta'] = "gamboamartin\\banco";

        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function co_acreditados(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = (new _keys_selects())->base_co_acreditado(controler: $this,function: __FUNCTION__);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }


        return $r_modifica;
    }

    final public function documentos(bool $header, bool $ws = false): array
    {

        $template = $this->modifica(header: false);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $template, header: $header,ws:  $ws);
        }

        $inm_conf_docs_comprador = (new _inm_comprador())->integra_inm_documentos(controler: $this);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar buttons',data:  $inm_conf_docs_comprador, header: $header,ws:  $ws);
        }

        $this->inm_conf_docs_comprador = $inm_conf_docs_comprador;


        return $inm_conf_docs_comprador;

    }

    /**
     * Inicializa los elementos mostrables para datatables
     * @return stdClass
     * @version 1.40.0
     */
    private function init_datatable(): stdClass
    {
        $columns["inm_comprador_id"]["titulo"] = "Id";
        $columns["inm_comprador_nombre"]["titulo"] = "Nombre";
        $columns["inm_comprador_apellido_paterno"]["titulo"] = "AP";
        $columns["inm_comprador_apellido_materno"]["titulo"] = "AM";
        $columns["inm_comprador_nss"]["titulo"] = "NSS";
        $columns["inm_comprador_curp"]["titulo"] = "CURP";
        $columns["inm_comprador_etapa"]["titulo"] = "Etapa";
        $columns["inm_comprador_proceso"]["titulo"] = "Proceso Actual";


        $filtro = array("inm_comprador.id",'inm_comprador.nombre','inm_comprador.apellido_paterno',
            'inm_comprador.apellido_materno','inm_comprador.nss','inm_comprador.curp','inm_comprador.proceso');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }
    protected function key_selects_txt(array $keys_selects): array
    {


        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_dh',
            keys_selects:$keys_selects, place_holder: 'Descuento Pension Alimenticia Derechohabiente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'descuento_pension_alimenticia_fc',
            keys_selects:$keys_selects, place_holder: 'Descuento Pension Alimenticia Familiar/Corresidente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_credito_solicitado_dh',
            keys_selects:$keys_selects, place_holder: 'Monto Credito Solicitado Derechohabiente');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'monto_ahorro_voluntario',
            keys_selects:$keys_selects, place_holder: 'Monto Ahorro Voluntario');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new _keys_selects())->keys_base_cliente(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_exterior',
            keys_selects:$keys_selects, place_holder: 'Exterior');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_interior',
            keys_selects:$keys_selects, place_holder: 'Interior', required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'telefono',
            keys_selects:$keys_selects, place_holder: 'Telefono');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada_com', keys_selects:$keys_selects,
            place_holder: 'Lada');
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
            keys_selects:$keys_selects, place_holder: 'Celular');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['cel_com']->regex = $this->validacion->patterns['telefono_mx_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'genero',
            keys_selects:$keys_selects, place_holder: 'Genero');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'correo_com',
            keys_selects:$keys_selects, place_holder: 'Correo');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['correo_com']->regex = $this->validacion->patterns['correo_html_base'];

        return $keys_selects;
    }

    /**
     * Genera la vista de modifica
     * @param bool $header Si header retorna resultado en web
     * @param bool $ws Si ws muestra resultado en json
     * @return array|stdClass
     * @version 1.109.1
     */
    public function modifica(bool $header, bool $ws = false): array|stdClass
    {

        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }

        $keys_selects = (new _keys_selects())->key_selects_base(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }
        $base = $this->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $radios = (new _inm_comprador())->radios_chk(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar radios',data:  $radios, header: $header,ws:  $ws);
        }

        $btn_collapse_all = $this->html->button_para_java(id_css: 'collapse_all',style:  'primary',tag:  'Ver/Ocultar Todo');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al btn_collapse_all',data:  $btn_collapse_all, header: $header,ws:  $ws);
        }

        $this->buttons['btn_collapse_all'] = $btn_collapse_all;

        $headers['1'] = '1. CRÉDITO SOLICITADO';
        $headers['2'] = '2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO';
        $headers['3'] = '3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO';
        $headers['4'] = '4. DATOS DE LA EMPRESA O PATRÓN';
        $headers['5'] = '5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS';
        $headers['13'] = '13. DATOS FISCALES PARA FACTURACION';
        $headers['14'] = '14. CONTROL INTERNO';

        $headers['6'] = '6. DATOS DE IDENTIFICACIÓN QUE SERÁN VALIDADOS (OBLIGATORIOS EN CRÉDITO CONYUGAL, FAMILIAR O CORRESIDENCIAL)';

        foreach ($headers as $n_apartado=>$tag_header){
            $id_css_button = "collapse_a$n_apartado";
            $key_header = "apartado_$n_apartado";

            $header_apartado = $this->html_entidad->header_collapsible(id_css_button: $id_css_button,
                style_button: 'primary', tag_button: 'Ver/Ocultar',tag_header:  $tag_header);

            if(errores::$error){
                return $this->retorno_error(
                    mensaje: 'Error al generar header',data:  $header_apartado, header: $header,ws:  $ws);
            }

            $this->header_frontend->$key_header = $header_apartado;

            $cols_css['correo'] = 12;

            $inm_co_acreditado_inputs = (new inm_co_acreditado_html(html: $this->html_base))->inputs(
                integra_prefijo: true, cols_css: $cols_css);
            if(errores::$error){
                return $this->retorno_error(
                    mensaje: 'Error al generar inputs',data:  $inm_co_acreditado_inputs, header: $header,ws:  $ws);
            }

            $this->inputs->inm_co_acreditado = $inm_co_acreditado_inputs;

        }

        $button_upd = $this->html->boton_submit(class_button: 'modifica', class_control: 'btn-modifica',
            style: 'success', tag: 'Modifica', id_button: 'btn_modifica');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar button_upd',data:  $button_upd, header: $header,ws:  $ws);
        }

        $this->btn = $button_upd;


        return $r_modifica;
    }

    public function solicitud_infonavit(bool $header, bool $ws = false)
    {

        $pdf = new Fpdi();
        $_pdf = new _pdf(pdf: $pdf);

        $pdf_exe = $_pdf->solicitud_infonavit(inm_comprador_id: $this->registro_id,path_base:  $this->path_base,
            modelo:  $this->modelo);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf_exe, header: $header, ws: $ws);
        }

        exit;
    }

    final public function subir_documento(bool $header, bool $ws = false){

        $this->inputs = new stdClass();

        $filtro['inm_comprador.id'] = $this->registro_id;
        $inm_comprador_id = (new inm_comprador_html(html: $this->html_base))->select_inm_comprador_id(
            cols: 12,con_registros:  true,id_selected:  $this->registro_id,link:  $this->link,filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_comprador_id, header: $header, ws: $ws);
        }
        $this->inputs->inm_comprador_id = $inm_comprador_id;

        $doc_tipos_documentos = (new _doctos())->documentos_de_comprador(inm_comprador_id: $this->registro_id,
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
            return $this->retorno_error(mensaje: 'Error al generar input', data: $inm_comprador_id, header: $header, ws: $ws);
        }
        $this->inputs->doc_tipo_documento_id = $doc_tipo_documento_id;

        $documento = $this->html->input_file(cols: 12,name:  'documento',row_upd:  new stdClass(),value_vacio:  false);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $documento, header: $header,ws:  $ws);
        }

        $this->inputs->documento = $documento;

        $link_alta_doc = $this->obj_link->link_alta_bd(link:  $this->link,seccion:  'inm_doc_comprador');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar link',data:  $link_alta_doc, header: $header,ws:  $ws);
        }

        $this->link_inm_doc_comprador_alta_bd = $link_alta_doc;

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
