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
use gamboamartin\calculo\calculo;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_prospecto_html;
use gamboamartin\inmuebles\models\_base_paquete;
use gamboamartin\inmuebles\models\_inm_prospecto;
use gamboamartin\inmuebles\models\inm_beneficiario;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_referencia_prospecto;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use html\doc_tipo_documento_html;
use PDO;
use stdClass;

class controlador_inm_prospecto extends _ctl_formato {

    public stdClass $header_frontend;
    public inm_prospecto_html $html_entidad;

    public string $link_inm_doc_prospecto_alta_bd = '';

    public array $inm_conf_docs_prospecto = array();

    public array $beneficiarios = array();
    public array $referencias = array();



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
        $this->lista_get_data = true;


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

    /**
     * Convierte un prospecto en cliente
     * @param bool $header Muestra resultado en web
     * @param bool $ws Muestra resultado a nivel ws
     * @return array|string
     */
    public function convierte_cliente(bool $header, bool $ws = false): array|string
    {
        $this->link->beginTransaction();

        if($this->registro_id <=0){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error registro_id debe ser mayor a 0', data: $this->registro_id,
                header: true, ws: false);
        }

        $retorno = (new _base())->init_retorno();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al obtener datos de retorno', data: $retorno,
                header: true, ws: false);
        }

        $conversion = (new inm_prospecto(link: $this->link))->convierte_cliente(inm_prospecto_id:  $this->registro_id);

        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al convertir en cliente', data: $conversion,
                header: true, ws: false);
        }

        $this->link->commit();

        $out = (new _base())->out(controlador: $this,header:  $header, result:  $conversion,
            retorno:  $retorno, ws: $ws);
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al dar salida', data: $out,
                header: true, ws: false);
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

    public function generales(bool $header, bool $ws = false): array|stdClass
    {

        $inm_prospecto = (new _generales())->inm_prospecto(inm_prospecto_id: $this->registro_id,link: $this->link);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al ajusta prospecto',data:  $inm_prospecto, header: $header,ws:  $ws);
        }


        $this->registro = new stdClass();
        $this->registro->inm_prospecto = $inm_prospecto;


        $inm_conyuge = (new _generales())->inm_conyuge_init();
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al al inicializar inm_conyuge',data:  $inm_conyuge, header: $header,ws:  $ws);
        }

        $existe_conyuge = (new inm_prospecto(link: $this->link))->existe_conyuge(inm_prospecto_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al validar si existe inm_conyuge',data:  $existe_conyuge, header: $header,ws:  $ws);
        }

        if($existe_conyuge) {
            $inm_conyuge = (new inm_prospecto(link: $this->link))->inm_conyuge(inm_prospecto_id: $this->registro_id);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al obtener inm_conyuge', data: $inm_conyuge, header: $header, ws: $ws);
            }
            $edad = (new calculo())->edad_hoy(fecha_nacimiento: $inm_conyuge->inm_conyuge_fecha_nacimiento);
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al obtener edad',data:  $edad, header: $header,ws:  $ws);
            }
            $inm_conyuge->inm_conyuge_edad = $edad;
            $inm_conyuge->inm_conyuge_edad.= ' AÑOS';

            $inm_conyuge->inm_conyuge_estado_civil= $inm_prospecto->inm_estado_civil_descripcion;
        }


        $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_conyuge',row:  $inm_conyuge);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener nombre_completo',data:  $nombre_completo, header: $header,ws:  $ws);
        }

        $inm_conyuge->inm_conyuge_nombre_completo = $nombre_completo;


        $lugar_fecha_nac = (new _generales())->data_nacimiento(entidad_edo: 'dp_estado', entidad_mun: 'dp_municipio', entidad_name: 'inm_conyuge', row: $inm_conyuge);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener lugar_fecha_nac',data:  $lugar_fecha_nac, header: $header,ws:  $ws);
        }

        $inm_conyuge->inm_conyuge_lugar_fecha_nac = $lugar_fecha_nac;


        $inm_tipo_beneficiarios = (new inm_tipo_beneficiario(link: $this->link))->registros_activos(retorno_obj: true);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_tipo_beneficiarios',data:  $inm_tipo_beneficiarios,
                header: $header,ws:  $ws);
        }

        $inm_beneficiarios = (new inm_prospecto(link: $this->link))->inm_beneficiarios(inm_prospecto_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_beneficiarios',data:  $inm_beneficiarios,
                header: $header,ws:  $ws);
        }

        foreach ($inm_tipo_beneficiarios as $indice=>$inm_tipo_beneficiario){
            if(!isset($inm_tipo_beneficiario->inm_beneficiarios)){
                $inm_tipo_beneficiario->inm_beneficiarios = array();
            }
            foreach ($inm_beneficiarios as $inm_beneficiario){

                $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_beneficiario',row:  $inm_beneficiario);
                if(errores::$error){
                    return $this->retorno_error(mensaje: 'Error al obtener nombre_completo',data:  $nombre_completo, header: $header,ws:  $ws);
                }
                $inm_beneficiario->inm_beneficiario_nombre_completo = $nombre_completo;

                if((int)$inm_beneficiario->inm_tipo_beneficiario_id === (int)$inm_tipo_beneficiario->inm_tipo_beneficiario_id){
                    $inm_tipo_beneficiario->inm_beneficiarios[] = $inm_beneficiario;
                }
                $inm_tipo_beneficiarios[$indice] = $inm_tipo_beneficiario;
            }
        }
        $this->registro->inm_conyuge = $inm_conyuge;
        $this->registro->inm_tipo_beneficiarios = $inm_tipo_beneficiarios;


        $inm_referencias = (new inm_prospecto(link: $this->link))->inm_referencias(inm_prospecto_id: $this->registro_id);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inm_referencias',data:  $inm_referencias,
                header: $header,ws:  $ws);
        }

        foreach ($inm_referencias as $indice => $inm_referencia){
            $nombre_completo = (new _generales())->nombre_completo(name_entidad: 'inm_referencia_prospecto',row:  $inm_referencia);
            if(errores::$error){
                return $this->retorno_error(mensaje: 'Error al obtener nombre_completo',data:  $nombre_completo, header: $header,ws:  $ws);
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
        $columns["inm_prospecto_id"]["titulo"] = "Id";
        $columns["inm_prospecto_razon_social"]["titulo"] = "Nombre";
        $columns["inm_prospecto_nss"]["titulo"] = "NSS";
        $columns["inm_prospecto_rfc"]["titulo"] = "RFC";
        $columns["inm_prospecto_curp"]["titulo"] = "CURP";
        $columns["inm_prospecto_fecha_alta"]["titulo"] = "Fecha Alta";
        $columns["com_agente_id"]["titulo"] = "Agente Id";
        $columns["com_agente_descripcion"]["titulo"] = "Agente";


        $filtro = array("inm_prospecto.id","inm_prospecto.razon_social",'inm_prospecto.nss','inm_prospecto.rfc',
            'inm_prospecto.curp','inm_prospecto.fecha_alta','com_agente.id','com_agente.descripcion');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    public function integra_relacion(bool $header, bool $ws = false): array|stdClass{
        $r_modifica = $this->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al generar salida de template',data:  $r_modifica,header: $header,ws: $ws);
        }
        $data = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_base(controlador: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar datos para front',data:  $data,
                header: $header,ws:  $ws);
        }
        $base = $this->base_upd(keys_selects: $data->keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }
        return $r_modifica;
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

        $data = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_base(controlador: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar datos para front',data:  $data,
                header: $header,ws:  $ws);
        }

        $base = $this->base_upd(keys_selects: $data->keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al integrar base',data:  $base, header: $header,ws:  $ws);
        }

        $headers = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->headers_front(controlador: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar headers',data:  $headers, header: $header,ws:  $ws);
        }

        $inputs = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->inputs_nacimiento(controlador: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al generar inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        $conyuge = (new _conyuge())->inputs_conyuge(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener conyuge',data:  $conyuge,
                header: $header,ws:  $ws);
        }

        $this->inputs->conyuge = $conyuge;

        $beneficiario = (new _beneficiario())->inputs_beneficiario(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener beneficiario',data:  $beneficiario,
                header: $header,ws:  $ws);
        }

        $this->inputs->beneficiario = $beneficiario;

        $filtro['inm_prospecto.id'] = $this->registro_id;

        $r_inm_beneficiario = (new inm_beneficiario(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios',data:  $r_inm_beneficiario,
                header: $header,ws:  $ws);
        }


        $params = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->params_btn(accion_retorno: __FUNCTION__,
            registro_id:  $this->registro_id,seccion_retorno:  $this->tabla);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener params',data:  $params,
                header: $header,ws:  $ws);
        }

        $beneficiarios = $r_inm_beneficiario->registros;

        $beneficiarios = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->rows(controlador: $this,
            datas: $beneficiarios,params:  $params, seccion_exe: 'inm_beneficiario');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios link del',data:  $beneficiarios,
                header: $header,ws:  $ws);
        }

        $this->beneficiarios = $beneficiarios;

        $referencia = (new _referencia())->inputs_referencia(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener referencias',data:  $referencia,
                header: $header,ws:  $ws);
        }
        $this->inputs->referencia = $referencia;

        $r_inm_referencia_prospecto = (new inm_referencia_prospecto(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener referencia_prospectos',data:  $r_inm_referencia_prospecto,
                header: $header,ws:  $ws);
        }

        $params = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->params_btn(accion_retorno: __FUNCTION__,
            registro_id:  $this->registro_id,seccion_retorno:  $this->tabla);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener params',data:  $params,
                header: $header,ws:  $ws);
        }

        $referencia_prospectos = $r_inm_referencia_prospecto->registros;

        $referencia_prospectos = (new \gamboamartin\inmuebles\controllers\_inm_prospecto())->rows(controlador: $this,
            datas: $referencia_prospectos,params:  $params, seccion_exe: 'inm_referencia_prospecto');
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener beneficiarios link del',data:  $referencia_prospectos,
                header: $header,ws:  $ws);
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

    public function regenera_nombre_completo_valida(bool $header, bool $ws = false): array|string{

        $this->link->beginTransaction();
        $regenera = (new inm_prospecto(link: $this->link))->regenera_nombre_completo_valida();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al regenerar',data:  $regenera,
                header: $header,ws:  $ws);
        }
        $this->link->commit();
        print_r($regenera);

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

    public function regenera_rel_com_agente(bool $header, bool $ws = false): array|string{

        $this->link->beginTransaction();
        $regenera = (new inm_prospecto(link: $this->link))->regenera_agentes_iniciales();
        if(errores::$error){
            $this->link->rollBack();
            return $this->retorno_error(mensaje: 'Error al regenerar',data:  $regenera,
                header: $header,ws:  $ws);
        }
        $this->link->commit();
        print_r($regenera);

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
