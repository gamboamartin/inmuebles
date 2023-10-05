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
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\_base;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_rel_prospecto_cliente;
use gamboamartin\system\actions;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use stdClass;
use Throwable;

class controlador_inm_prospecto extends _ctl_formato {

    public stdClass $header_frontend;
    public inm_prospecto_html $html_entidad;
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


        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $id_selected, label: 'Agente');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects, header: $header,ws:  $ws);
        }

        $com_tipo_prospecto_id = (new com_prospecto(link: $this->link))->id_preferido_detalle(entidad_preferida: 'com_tipo_prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener id',data:  $com_tipo_prospecto_id, header: $header,ws:  $ws);
        }

        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_tipo_prospecto_id',
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
        $keys->inputs = array('nombre','apellido_paterno','apellido_materno','telefono','correo_com','razon_social',
            'lada_com','numero_com','cel_com','descuento_pension_alimenticia_dh','descuento_pension_alimenticia_fc',
            'monto_credito_solicitado_dh','monto_ahorro_voluntario','nombre_empresa_patron','nrp_nep','lada_nep',
            'numero_nep','extension_nep','nss','curp','rfc','numero_exterior','numero_interior');
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

        $init_data['dp_pais'] = "gamboamartin\\direccion_postal";
        $init_data['dp_estado'] = "gamboamartin\\direccion_postal";
        $init_data['dp_municipio'] = "gamboamartin\\direccion_postal";
        $init_data['dp_cp'] = "gamboamartin\\direccion_postal";
        $init_data['dp_colonia_postal'] = "gamboamartin\\direccion_postal";
        $init_data['dp_calle_pertenece'] = "gamboamartin\\direccion_postal";
        $campos_view = $this->campos_view_base(init_data: $init_data,keys:  $keys);

        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al inicializar campo view',data:  $campos_view);
        }


        return $campos_view;
    }

    public function convierte_cliente(bool $header, bool $ws = false): array|string
    {
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

        $inm_prospecto = (new inm_prospecto(link: $this->link))->registro(registro_id: $this->registro_id,
            columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto, header: $header, ws: $ws);
        }

        $inm_prospecto_completo = (new inm_prospecto(link: $this->link))->registro(registro_id: $this->registro_id,
            retorno_obj: true);
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto, header: false, ws: false);
        }

        $keys = array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'nombre','apellido_paterno','apellido_materno','con_discapacidad','nombre_empresa_patron','nrp_nep',
            'lada_nep','numero_nep','extension_nep','lada_com','numero_com','cel_com','genero','correo_com',
            'inm_tipo_discapacidad_id','inm_persona_discapacidad_id','inm_estado_civil_id',
            'inm_institucion_hipotecaria_id');

        foreach ($keys as $key){
            $inm_comprador_ins[$key] = $inm_prospecto->$key;
        }

        if($inm_comprador_ins['nss'] === ''){
            $inm_comprador_ins['nss'] = '99999999999';
        }
        if($inm_comprador_ins['curp'] === ''){
            $inm_comprador_ins['curp'] = 'XEXX010101MNEXXXA8';
        }
        if($inm_comprador_ins['lada_nep'] === ''){
            $inm_comprador_ins['lada_nep'] = '33';
        }
        if($inm_comprador_ins['numero_nep'] === ''){
            $inm_comprador_ins['numero_nep'] = '33333333';
        }
        if($inm_comprador_ins['nombre_empresa_patron'] === ''){
            $inm_comprador_ins['nombre_empresa_patron'] = 'POR DEFINIR';
        }
        if($inm_comprador_ins['nrp_nep'] === ''){
            $inm_comprador_ins['nrp_nep'] = 'POR DEFINIR';
        }

        $bn_cuenta_id = (new inm_comprador(link: $this->link))->id_preferido_detalle(entidad_preferida: 'bn_cuenta');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener bn_cuenta_id', data: $bn_cuenta_id, header: true, ws: false);
        }

        $inm_comprador_ins['bn_cuenta_id'] = $bn_cuenta_id;


        $dp_calle_pertenece_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'dp_calle_pertenece');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener dp_calle_pertenece_id', data: $dp_calle_pertenece_id, header: true, ws: false);
        }

        $inm_comprador_ins['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;

        $cat_sat_regimen_fiscal_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_regimen_fiscal');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_regimen_fiscal_id', data: $cat_sat_regimen_fiscal_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_regimen_fiscal_id'] = $cat_sat_regimen_fiscal_id;

        $cat_sat_moneda_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_moneda');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_moneda_id', data: $cat_sat_moneda_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_moneda_id'] = $cat_sat_moneda_id;

        $cat_sat_forma_pago_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_forma_pago');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_forma_pago_id', data: $cat_sat_forma_pago_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_forma_pago_id'] = $cat_sat_forma_pago_id;

        $cat_sat_metodo_pago_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_metodo_pago');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_metodo_pago_id', data: $cat_sat_metodo_pago_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_metodo_pago_id'] = $cat_sat_metodo_pago_id;

        $cat_sat_uso_cfdi_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_uso_cfdi');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_uso_cfdi_id', data: $cat_sat_uso_cfdi_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_uso_cfdi_id'] = $cat_sat_uso_cfdi_id;

        $com_tipo_cliente_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'com_tipo_cliente');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener com_tipo_cliente_id', data: $com_tipo_cliente_id, header: true, ws: false);
        }

        $inm_comprador_ins['com_tipo_cliente_id'] = $com_tipo_cliente_id;

        $cat_sat_tipo_persona_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'cat_sat_tipo_persona');
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener cat_sat_tipo_persona_id', data: $cat_sat_tipo_persona_id, header: true, ws: false);
        }

        $inm_comprador_ins['cat_sat_tipo_persona_id'] = $cat_sat_tipo_persona_id;

        $inm_comprador_ins['rfc'] = $inm_prospecto_completo->com_prospecto_rfc;
        $inm_comprador_ins['numero_exterior'] = 'POR ASIGNAR';

        $r_alta_comprador = (new inm_comprador(link: $this->link))->alta_registro(registro: $inm_comprador_ins);

        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar cliente', data: $r_alta_comprador, header: true, ws: false);
        }


        $inm_rel_prospecto_cliente_ins['inm_prospecto_id'] = $this->registro_id;
        $inm_rel_prospecto_cliente_ins['inm_comprador_id'] = $r_alta_comprador->registro_id;

        $r_alta_rel = (new inm_rel_prospecto_cliente(link: $this->link))->alta_registro(registro: $inm_rel_prospecto_cliente_ins);

        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel, header: true, ws: false);
        }


        $this->link->commit();

        if($header){
            if($id_retorno === -1) {
                $id_retorno = $this->registro_id;
            }
            $this->retorno_base(registro_id:$id_retorno, result: $r_alta_rel, siguiente_view: $siguiente_view,
                ws:  $ws,seccion_retorno: $this->seccion, valida_permiso: true);
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($r_alta_rel, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }
        $r_alta_rel->siguiente_view = $siguiente_view;


        return $r_alta_rel;


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
            keys_selects:$keys_selects, place_holder: 'Correo');
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

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'rfc',
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

        $headers = (new _base(html: $this->html_base))->genera_headers(controler: $this,headers:  $headers);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar headers',data:  $headers, header: $header,ws:  $ws);
        }


        return $r_modifica;
    }


}
