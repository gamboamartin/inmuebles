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
use gamboamartin\inmuebles\html\inm_comprador_html;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\system\_ctl_base;
use gamboamartin\system\links_menu;
use gamboamartin\template\html;
use PDO;
use setasign\Fpdi\Fpdi;
use stdClass;
use Throwable;

class controlador_inm_comprador extends _ctl_base {

    public array $imp_ubicaciones = array();
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
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = (new _keys_selects())->init(controler: $this,row_upd: new stdClass());
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $this->row_upd->descuento_pension_alimenticia_dh = 0;
        $this->row_upd->monto_credito_solicitado_dh = 0;
        $this->row_upd->descuento_pension_alimenticia_fc = 0;
        $this->row_upd->monto_ahorro_voluntario = 0;

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

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


        $keys_selects = (new _keys_selects())->key_selects_base(controler: $this);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects,
                header: $header,ws:  $ws);
        }

        $keys_selects = (new _keys_selects())->keys_disabled(keys_selects: $keys_selects);
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

        $inputs = (new _keys_selects())->inputs_form_base(btn_action_next: $hiddens->btn_action_next, controler: $this,
            id_retorno: $hiddens->id_retorno, in_registro_id: $hiddens->in_registro_id, inm_comprador_id: $inm_comprador_id,
            inm_ubicacion_id: '', precio_operacion: $hiddens->precio_operacion, seccion_retorno: $hiddens->seccion_retorno);

        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs, header: $header,ws:  $ws);
        }


        $columns_ds = array('inm_ubicacion_id','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_cp_descripcion','dp_colonia_descripcion','dp_calle_descripcion','inm_ubicacion_numero_exterior');

        $inm_ubicacion_id = (new inm_ubicacion_html(html: $this->html_base))->select_inm_ubicacion_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $this->link, columns_ds: $columns_ds);
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


        $filtro = array();
        $filtro['inm_comprador.id'] = $this->registro_id;
        $r_inm_rel_ubi_comp = (new inm_rel_ubi_comp(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al obtener compradores',data:  $r_inm_rel_ubi_comp,
                header: $header,ws:  $ws);
        }

        $this->imp_ubicaciones = $r_inm_rel_ubi_comp->registros;



        return $r_modifica;
    }

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


    protected function key_selects_txt(array $keys_selects): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'es_segundo_credito', keys_selects:$keys_selects,
            place_holder: 'Es segundo Credito');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
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
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nss',
            keys_selects:$keys_selects, place_holder: 'NSS');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'curp',
            keys_selects:$keys_selects, place_holder: 'CURP');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'rfc',
            keys_selects:$keys_selects, place_holder: 'RFC');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre(s)');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre_empresa_patron',
            keys_selects:$keys_selects, place_holder: 'Nombre Empresa o Patron');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada_nep',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_nep',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'extension_nep',
            keys_selects:$keys_selects, place_holder: 'Extension',required: false);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nrp_nep',
            keys_selects:$keys_selects, place_holder: 'Registro Patronal');
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





        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada_com',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_com',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'cel_com',
            keys_selects:$keys_selects, place_holder: 'Celular');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

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

        return $keys_selects;
    }





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

        return $r_modifica;
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


        $filtro = array("inm_comprador.id",'inm_comprador.nombre','inm_comprador.apellido_paterno',
            'inm_comprador.apellido_materno','inm_comprador.nss','inm_comprador.curp');

        $datatables = new stdClass();
        $datatables->columns = $columns;
        $datatables->filtro = $filtro;

        return $datatables;
    }

    public function solicitud_infonavit(bool $header, bool $ws = false)
    {


        $data = (new inm_comprador(link: $this->link))->data_pdf(inm_comprador_id: $this->registro_id);
        if (errores::$error) {
            return $this->retorno_error(
                mensaje: 'Error al obtener datos', data: $data, header: $header, ws: $ws);
        }

        $pdf = new Fpdi();

        $_pdf = new _pdf(pdf: $pdf);

        $pdf->AddPage();
        try {
            $pdf->setSourceFile($this->path_base . 'templates/solicitud_infonavit.pdf');
            $tplIdx = $pdf->importPage(1);
        } catch (Throwable $e) {
            return $this->retorno_error(mensaje: 'Error al obtener plantilla', data: $e, header: $header, ws: $ws);
        }
        $pdf->useTemplate($tplIdx, null, null, null, null, true);


        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(0, 0, 0);

        /**
         * 1. CRÉDITO SOLICITADO
         */


        $pdf = $_pdf->credito_solicitado(data: $data);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
        }



        /**
         * 2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO
         */

        $pdf->SetFont('Arial', 'B', 10);

        if (round($data->inm_comprador['inm_comprador_descuento_pension_alimenticia_dh'], 2) > 0.0) {

            $pdf = $_pdf->write( valor: $data->inm_comprador['inm_comprador_descuento_pension_alimenticia_dh'], x: 77, y: 117);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
            }


        }

        if (round($data->inm_comprador['inm_comprador_descuento_pension_alimenticia_fc'], 2) > 0.0) {

            $pdf = $_pdf->write( valor: $data->inm_comprador['inm_comprador_descuento_pension_alimenticia_fc'], x: 115, y: 117);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
            }

        }

        if (round($data->inm_comprador['inm_comprador_monto_credito_solicitado_dh'], 2) > 0.0) {

            $pdf = $_pdf->write(valor: $data->inm_comprador['inm_comprador_monto_credito_solicitado_dh'], x: 79, y: 131);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
            }

        }

        if (round($data->inm_comprador['inm_comprador_monto_ahorro_voluntario'], 2) > 0.0) {


            $pdf = $_pdf->write( valor: $data->inm_comprador['inm_comprador_monto_ahorro_voluntario'], x: 51.5, y: 143);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
            }

        }


        /**
         * 3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO
         */

        $keys_ubicacion['dp_calle_ubicacion_descripcion']= array('x'=>15.5,'y'=>164);
        $keys_ubicacion['inm_ubicacion_numero_exterior']= array('x'=>15.5,'y'=>170);
        $keys_ubicacion['inm_ubicacion_numero_interior']= array('x'=>31,'y'=>170);
        $keys_ubicacion['inm_ubicacion_lote']= array('x'=>46,'y'=>170);
        $keys_ubicacion['inm_ubicacion_manzana']= array('x'=>61,'y'=>170);
        $keys_ubicacion['dp_colonia_ubicacion_descripcion']= array('x'=>81,'y'=>170);
        $keys_ubicacion['dp_estado_ubicacion_descripcion']= array('x'=>15.5,'y'=>176);
        $keys_ubicacion['dp_municipio_ubicacion_descripcion']= array('x'=>100,'y'=>176);
        $keys_ubicacion['dp_cp_ubicacion_descripcion']= array('x'=>173,'y'=>176);



        $write = $_pdf->write_data(keys: $keys_ubicacion,row:  $data->imp_rel_ubi_comp);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
        }


        $x = 94.5;
        $y = 190;

        if($data->inm_comprador['inm_comprador_con_discapacidad'] === 'SI'){

            $x = 84;
        }

        $pdf = $_pdf->write( valor: 'X', x: $x, y: $y);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
        }


        $x = 21.5;
        $y = 224.5;


        if((int)$data->inm_comprador['inm_destino_credito_id'] === 3 ){
            $x = 67;

        }
        if((int)$data->inm_comprador['inm_destino_credito_id'] === 4 ){
            $x = 114;

        }
        if((int)$data->inm_comprador['inm_destino_credito_id'] === 5 ){
            $x = 163;

        }

        $pdf = $_pdf->write( valor: $data->imp_rel_ubi_comp['inm_rel_ubi_comp_precio_operacion'], x: $x, y: $y);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
        }


        /**
         * 4. DATOS DE LA EMPRESA O PATRÓN
         */


        $keys_comprador['inm_comprador_nombre_empresa_patron']= array('x'=>16,'y'=>249);
        $keys_comprador['inm_comprador_nrp_nep']= array('x'=>140,'y'=>249);
        $keys_comprador['inm_comprador_lada_nep']= array('x'=>57,'y'=>256);
        $keys_comprador['inm_comprador_numero_nep']= array('x'=>70,'y'=>256);
        $keys_comprador['inm_comprador_extension_nep']= array('x'=>116,'y'=>256);


        $write = $_pdf->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
        }


        $pdf->AddPage();
        try {
            $tplIdx = $pdf->importPage(2);
        }
        catch (Throwable $e){
            return $this->retorno_error(mensaje: 'Error al obtener plantilla',data:  $e,header: $header,ws: $ws);
        }
        $pdf->useTemplate($tplIdx,null,null,null,null,true);

        /**
         * 5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS
         */

        $keys_comprador = array();
        $keys_comprador['inm_comprador_nss']= array('x'=>16,'y'=>30);
        $keys_comprador['inm_comprador_curp']= array('x'=>67,'y'=>30);
        $keys_comprador['inm_comprador_apellido_paterno']= array('x'=>16,'y'=>37);
        $keys_comprador['inm_comprador_apellido_materno']= array('x'=>106,'y'=>37);
        $keys_comprador['inm_comprador_nombre']= array('x'=>16,'y'=>44);
        $keys_comprador['inm_comprador_lada_com']= array('x'=>27,'y'=>76);
        $keys_comprador['inm_comprador_numero_com']= array('x'=>40,'y'=>76);
        $keys_comprador['inm_comprador_cel_com']= array('x'=>88,'y'=>76);
        $keys_comprador['inm_comprador_correo_com']= array('x'=>37.5,'y'=>85.5);


        $write = $_pdf->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
        }



        $pdf = $_pdf->write(valor: $data->com_cliente['com_cliente_rfc'], x: 132, y: 30);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
        }



        $domicilio = $data->com_cliente['dp_calle_descripcion'].' '.$data->com_cliente['com_cliente_numero_exterior'];
        $domicilio .= $data->com_cliente['com_cliente_numero_interior'];

        $x = 16;
        $y = 54;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, strtoupper($domicilio));


        $keys_cliente = array();
        $keys_cliente['dp_colonia_descripcion']= array('x'=>16,'y'=>61);
        $keys_cliente['dp_estado_descripcion']= array('x'=>105,'y'=>61);
        $keys_cliente['dp_municipio_descripcion']= array('x'=>16,'y'=>68);
        $keys_cliente['dp_cp_descripcion']= array('x'=>82,'y'=>68);



        $write = $_pdf->write_data(keys: $keys_cliente,row:  $data->com_cliente);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
        }



        $x = 144.5;
        $y = 77;

        if($data->inm_comprador['inm_comprador_genero'] === 'F'){

            $x = 150.5;
        }

        $pdf = $_pdf->write( valor: 'X', x: $x, y: $y);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $pdf, header: $header, ws: $ws);
        }



        $pdf->SetXY($data->inm_comprador['inm_estado_civil_x'], $data->inm_comprador['inm_estado_civil_y']);
        $pdf->Write(0, 'X');

        if((int)$data->inm_comprador['inm_estado_civil_id'] !==1){
            $pdf->SetXY(58.5, 90);
            $pdf->Write(0, 'X');
        }


        foreach ($data->inm_rel_co_acreditados as $imp_rel_co_acred){


            $inm_co_acreditado = (new inm_co_acreditado(link: $this->link))->registro(registro_id: $imp_rel_co_acred['inm_co_acreditado_id']);
            if(errores::$error){
                return $this->retorno_error(
                    mensaje: 'Error al obtener inm_co_acreditado',data:  $inm_co_acreditado,header: $header,ws: $ws);
            }


            $keys_co_acreditado = array();
            $keys_co_acreditado['inm_co_acreditado_nss']= array('x'=>16,'y'=>105);
            $keys_co_acreditado['inm_co_acreditado_curp']= array('x'=>64,'y'=>105);
            $keys_co_acreditado['inm_co_acreditado_rfc']= array('x'=>132,'y'=>105);
            $keys_co_acreditado['inm_co_acreditado_apellido_paterno']= array('x'=>16,'y'=>112);
            $keys_co_acreditado['inm_co_acreditado_apellido_materno']= array('x'=>107,'y'=>112);
            $keys_co_acreditado['inm_co_acreditado_nombre']= array('x'=>16,'y'=>119);
            $keys_co_acreditado['inm_co_acreditado_lada']= array('x'=>27,'y'=>129);
            $keys_co_acreditado['inm_co_acreditado_numero']= array('x'=>40,'y'=>129);
            $keys_co_acreditado['inm_co_acreditado_celular']= array('x'=>86,'y'=>129);
            $keys_co_acreditado['inm_co_acreditado_correo']= array('x'=>38,'y'=>138);
            $keys_co_acreditado['inm_co_acreditado_nombre_empresa_patron']= array('x'=>16,'y'=>152);
            $keys_co_acreditado['inm_co_acreditado_nrp']= array('x'=>140,'y'=>152);
            $keys_co_acreditado['inm_co_acreditado_lada_nep']= array('x'=>100,'y'=>158);
            $keys_co_acreditado['inm_co_acreditado_numero_nep']= array('x'=>113,'y'=>158);
            $keys_co_acreditado['inm_co_acreditado_extension_nep']= array('x'=>150,'y'=>158);



            $write = $_pdf->write_data(keys: $keys_co_acreditado,row:  $inm_co_acreditado);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
            }


            $x = 144;
            $y = 130;

            if($inm_co_acreditado['inm_co_acreditado_genero'] === 'F'){

                $x = 150.5;
            }

            $pdf->SetXY($x, $y);
            $pdf->Write(0, 'X');


        }



        if(count($data->inm_referencias) > 0) {
            $inm_referencia = $data->inm_referencias[0];

            $keys_referencias = array();
            $keys_referencias['inm_referencia_apellido_paterno']= array('x'=>16,'y'=>177);
            $keys_referencias['inm_referencia_apellido_materno']= array('x'=>16,'y'=>183.5);
            $keys_referencias['inm_referencia_nombre']= array('x'=>16,'y'=>191);
            $keys_referencias['inm_referencia_lada']= array('x'=>27,'y'=>199.5);
            $keys_referencias['inm_referencia_numero']= array('x'=>40,'y'=>199.5);
            $keys_referencias['inm_referencia_celular']= array('x'=>27,'y'=>206);
            $keys_referencias['dp_calle_descripcion']= array('x'=>16,'y'=>212);
            $keys_referencias['inm_referencia_numero_dom']= array('x'=>16,'y'=>217);
            $keys_referencias['dp_colonia_descripcion']= array('x'=>16,'y'=>226);
            $keys_referencias['dp_estado_descripcion']= array('x'=>16,'y'=>234);
            $keys_referencias['dp_municipio_descripcion']= array('x'=>16,'y'=>244);
            $keys_referencias['dp_cp_descripcion']= array('x'=>82,'y'=>244);



            $write = $_pdf->write_data(keys: $keys_referencias,row:  $inm_referencia);
            if (errores::$error) {
                return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
            }



            if(isset($inm_referencias[1])){
                $inm_referencia = $inm_referencias[1];

                $keys_referencias = array();
                $keys_referencias['inm_referencia_apellido_paterno']= array('x'=>110,'y'=>177);
                $keys_referencias['inm_referencia_apellido_materno']= array('x'=>110,'y'=>183.5);
                $keys_referencias['inm_referencia_nombre']= array('x'=>110,'y'=>191);
                $keys_referencias['inm_referencia_lada']= array('x'=>121,'y'=>199.5);
                $keys_referencias['inm_referencia_numero']= array('x'=>121,'y'=>199.5);
                $keys_referencias['inm_referencia_celular']= array('x'=>134,'y'=>206);
                $keys_referencias['dp_calle_descripcion']= array('x'=>110,'y'=>212);
                $keys_referencias['inm_referencia_numero_dom']= array('x'=>110,'y'=>218);
                $keys_referencias['dp_colonia_descripcion']= array('x'=>110,'y'=>225);
                $keys_referencias['dp_estado_descripcion']= array('x'=>110,'y'=>237);
                $keys_referencias['dp_municipio_descripcion']= array('x'=>110,'y'=>245);
                $keys_referencias['dp_cp_descripcion']= array('x'=>178,'y'=>245);

                $write = $_pdf->write_data(keys: $keys_referencias,row:  $inm_referencia);
                if (errores::$error) {
                    return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
                }


            }


        }

        $pdf->AddPage();

        try {
            $tplIdx = $pdf->importPage(3);
        }
        catch (Throwable $e){
            return $this->retorno_error(mensaje: 'Error al obtener plantilla',data:  $e,header: $header,ws: $ws);
        }

        $pdf->useTemplate($tplIdx,null,null,null,null,true);


        $pdf = $_pdf->write_x(name_entidad: 'inm_tipo_inmobiliaria',row:  $data->inm_conf_empresa);
        if(errores::$error){
            return $this->retorno_error(mensaje: 'Error al escribir en pdf',data:  $pdf,header: $header,ws: $ws);
        }



        $keys_comprador = array();
        $keys_comprador['org_empresa_razon_social']= array('x'=>16,'y'=>37);
        $keys_comprador['org_empresa_rfc']= array('x'=>22,'y'=>57);
        $keys_comprador['bn_cuenta_descripcion']= array('x'=>16,'y'=>85);

        $write = $_pdf->write_data(keys: $keys_comprador,row:  $data->inm_comprador);
        if (errores::$error) {
            return $this->retorno_error(mensaje: 'Error al escribir en pdf', data: $write, header: $header, ws: $ws);
        }





        $x = 16;
        $y = 62;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, strtoupper($data->inm_comprador['org_empresa_razon_social']));



        $ciudad = strtoupper($data->inm_comprador['dp_municipio_empresa_descripcion']);
        $ciudad .= ", ".strtoupper($data->inm_comprador['dp_estado_empresa_descripcion']);


        $x = 36;
        $y = 240;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, strtoupper($ciudad));

        $x = 119;
        $y = 240;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, ((int)date('d')));


        $mes_letra = $this->modelo->mes['espaniol'][date('m')]['nombre'];

        $x = 128;
        $y = 240;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $mes_letra);


        $year = $this->modelo->year['espaniol'][date('Y')]['abreviado'];

        $x = 178;
        $y = 240;
        $pdf->SetXY($x, $y);
        $pdf->Write(0, $year);


        $pdf->Output('tu_pedorrote.pdf', 'I');

        exit;
    }


}
