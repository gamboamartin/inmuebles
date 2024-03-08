<?php

namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use base\orm\modelo;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_co_acreditado_html;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_nacionalidad;
use gamboamartin\inmuebles\models\inm_ocupacion;
use gamboamartin\inmuebles\models\inm_sindicato;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\js_base\valida;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _keys_selects{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    /**
     * Ajusta los elementos para front obtenidos del cliente
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|stdClass
     */
    private function ajusta_row_data_cliente(controlador_inm_comprador $controler): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id es menor a 0',
                data:  $controler->registro_id);
        }
        $com_cliente = (new inm_comprador(link: $controler->link))->get_com_cliente(
            inm_comprador_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente',data:  $com_cliente);
        }

        $row_upd = $this->row_data_cliente(com_cliente: $com_cliente,controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener row_upd',data:  $row_upd);
        }
        //print_r($row_upd);exit;
        return $row_upd;
    }

    /**
     * Integra los elementos base de sistema para frontend
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param array $keys_selects Parametros previos
     * @param stdClass $row_upd Registro en proceso
     * @return array
     * @version 1.60.1
     */
    private function base(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $entidades_pref[] = 'com_tipo_cliente';

        foreach ($entidades_pref as $entidad){
            $entidad_id = (new com_cliente(link: $controler->link))->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al entidad_id',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id)) {
                $row_upd->$key_entidad_id = $entidad_id;
            }
        }

        $entidades_pref = array();
        $entidades_pref[] = 'inm_estado_civil';
        $entidades_pref[] = 'inm_sindicato';
        $entidades_pref[] = 'inm_nacionalidad';
        $entidades_pref[] = 'inm_ocupacion';

        foreach ($entidades_pref as $entidad){
            $entidad_id = $controler->modelo->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al entidad_id',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id)) {
                $row_upd->$key_entidad_id = $entidad_id;
            }
        }


        $columns_ds = array('com_tipo_cliente_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'com_tipo_cliente_id', keys_selects: $keys_selects, id_selected: $row_upd->com_tipo_cliente_id,
            label: 'Tipo de Cliente', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_estado_civil_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_estado_civil_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_estado_civil_id,
            label: 'Estado Civil', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_sindicato_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_sindicato_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_sindicato_id,
            label: 'Sindicato', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_nacionalidad_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_nacionalidad_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_nacionalidad_id,
            label: 'Nacionalidad', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('inm_ocupacion_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_ocupacion_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_ocupacion_id,
            label: 'Ocupacion', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Integra los elementos base de comprador
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param string $function Funcion de retorno en transaccion
     * @return array|stdClass|string
     */
    final public function base_co_acreditado(
        controlador_inm_comprador $controler, string $function): array|string|stdClass
    {
        $r_modifica = $controler->init_modifica(); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar salida de template',data:  $r_modifica);
        }

        $inputs = $this->base_plantilla(controler: $controler,function: $function);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }

        $link_asigna_nuevo_co_acreditado_bd = $controler->obj_link->link_con_id(accion: 'asigna_nuevo_co_acreditado_bd',
            link: $controler->link, registro_id: $controler->registro_id, seccion: $controler->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar link',data:  $link_asigna_nuevo_co_acreditado_bd);
        }

        $controler->link_asigna_nuevo_co_acreditado_bd = $link_asigna_nuevo_co_acreditado_bd;

        $inm_co_acreditados = (new _inm_comprador())->inm_co_acreditados(inm_comprador_id: $controler->registro_id,
            link:  $controler->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener compradores',data:  $inm_co_acreditados);
        }

        $controler->inm_co_acreditados = $inm_co_acreditados;

        $controler->inputs->inm_co_acreditado = new stdClass();

        $inputs_co_acreditado = (new inm_co_acreditado_html(html: $controler->html_base))->inputs(
            entidad: 'inm_co_acreditado');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar inputs',data:  $inputs_co_acreditado);
        }

        $controler->inputs->inm_co_acreditado = $inputs_co_acreditado;


        $button = $controler->html->button_href(accion: 'modifica',etiqueta: 'Ver Datos',
            registro_id: $controler->registro_id,seccion: $controler->tabla,style: 'warning');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar button',data:  $button);
        }

        $controler->buttons['modifica'] = $button;
        return $r_modifica;
    }

    /**
     * Integra los key base para vistas generales
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param string $function Funcion de retorno
     * @return array|stdClass
     */
    final public function base_plantilla(controlador_inm_comprador $controler, string $function): array|stdClass
    {

        if($controler->registro_id <=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id debe ser mayor a 0',
                data:  $controler->registro_id);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data:  $controler->inputs);
        }
        if(count($controler->registro) === 0){
            return $this->error->error(mensaje: 'Error controler->registro esta vacio',data:  $controler->registro);
        }
        $function = trim($function);
        if($function === ''){
            return $this->error->error(mensaje: 'Error function esta vacio',data:  $function);
        }

        $registro = $controler->modelo->registro(registro_id: $controler->registro_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro',data:  $registro);
        }

        $keys_selects = $this->key_selects_asigna_ubicacion(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $base = $controler->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar base',data:  $base);
        }


        $inputs = $this->input_full(controler: $controler,function: $function);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }
        return $inputs;
    }

    /**
     * Integra los inputs de tipo hidden
     * @param controlador_inm_ubicacion|controlador_inm_comprador $controler Controlador en ejecucion
     * @param string $funcion Funcion de retorno
     * @return array|stdClass
     * @version 1.78.1
     */
    final public function hiddens(
        controlador_inm_ubicacion|controlador_inm_comprador $controler, string $funcion): array|stdClass
    {

        $funcion = trim($funcion);
        if($funcion === ''){
            return $this->error->error(mensaje: 'Error funcion esta vacio',data:  $funcion);
        }

        $in_registro_id = $controler->html->hidden(name:'registro_id',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $in_registro_id);
        }
        $id_retorno = $controler->html->hidden(name:'id_retorno',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al id_retorno',data:  $id_retorno);
        }

        $seccion_retorno = $controler->html->hidden(name:'seccion_retorno',value: $controler->tabla);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $seccion_retorno);
        }
        $btn_action_next = $controler->html->hidden(name:'btn_action_next',value: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $btn_action_next);
        }

        $precio_operacion = $controler->html->input_monto(cols: 12, row_upd: new stdClass(),value_vacio: false,
            name: 'precio_operacion',place_holder: 'Precio de operacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio operacion',data:  $precio_operacion);
        }

        $data = new stdClass();
        $data->id_retorno = $id_retorno;
        $data->seccion_retorno = $seccion_retorno;
        $data->btn_action_next = $btn_action_next;
        $data->precio_operacion = $precio_operacion;
        $data->in_registro_id = $in_registro_id;

        return $data;

    }

    /**
     * Obtiene el identificador de agente
     * @param PDO $link Conexion a la base de datos
     * @return array|int
     * @version 2.239.2
     */
    private function id_selected_agente(PDO $link): int|array
    {
        $com_agentes = (new com_agente(link: $link))->com_agentes_session();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener agentes',data:  $com_agentes);
        }
        $id_selected = -1;
        if(count($com_agentes) > 0){
            $id_selected = (int)$com_agentes[0]['com_agente_id'];
        }
        return $id_selected;
    }

    /**
     * Inicializa los parametros de los selectores para frontend
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param stdClass $row_upd Registro en proceso
     * @return array
     */
    final public function init(controlador_inm_comprador $controler, stdClass $row_upd): array
    {
        $keys_selects = array();

        $keys_selects = (new _keys_selects())->ks_infonavit(controler: $controler, keys_selects: $keys_selects,
            row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new _dps_init())->ks_dp(controler: $controler,keys_selects:  $keys_selects,row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->ks_fiscales(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->base(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Inicializa los key id de elementos fiscales
     * @param inm_ubicacion $modelo
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass
     * @version 1.56.1
     */
    private function init_row_upd_fiscales(modelo $modelo, stdClass $row_upd): stdClass
    {

        $entidades_pref = array('cat_sat_regimen_fiscal','cat_sat_moneda','cat_sat_forma_pago','cat_sat_metodo_pago',
            'cat_sat_uso_cfdi','cat_sat_tipo_persona');

        $com_cliente = new com_cliente(link: $modelo->link);
        foreach ($entidades_pref as $entidad){
            $entidad_id = $com_cliente->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id)){
                $row_upd->$key_entidad_id = $entidad_id;
            }
        }


        $entidades_pref = array('bn_cuenta');

        foreach ($entidades_pref as $entidad){
            $entidad_id = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id)){
                $row_upd->$key_entidad_id = $entidad_id;
            }
        }


        return $row_upd;
    }

    /**
     * Inicializa los elementos por default de datos de infonavit
     * @param inm_comprador $modelo Modelo de ejecucion
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass|array
     * @version 1.41.0
     */
    private function init_row_upd_infonavit(modelo $modelo, stdClass $row_upd): stdClass|array
    {

        $entidades_pref[] = 'inm_producto_infonavit';
        $entidades_pref[] = 'inm_attr_tipo_credito';
        $entidades_pref[] = 'inm_destino_credito';
        $entidades_pref[] = 'inm_plazo_credito_sc';
        $entidades_pref[] = 'inm_tipo_discapacidad';
        $entidades_pref[] = 'inm_persona_discapacidad';
        $entidades_pref[] = 'inm_institucion_hipotecaria';

        foreach ($entidades_pref as $entidad){
            $entidad_id = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener entidad', data: $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id)){
                $row_upd->$key_entidad_id = $entidad_id;
            }
        }


        return $row_upd;
    }

    /**
     * Genera los inputs base de las vistas de comprador
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param string $function Funcion de retorno
     * @param string $inm_comprador_id Input
     * @return array|stdClass
     * @version 1.144.1
     */
    private function inputs_base(controlador_inm_comprador $controler, string $function, string $inm_comprador_id): array|stdClass
    {

        $function = trim($function);
        if($function === ''){
            return $this->error->error(mensaje: 'Error function esta vacio',data:  $function);
        }
        if(is_array($controler->inputs)){return $this->error->error(
            mensaje: 'Error $controler->inputs no esta inicializado', data: $controler->inputs);
        }


        $hiddens = $this->hiddens(controler: $controler,funcion: $function);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs',data:  $hiddens);
        }

        $inputs = $this->inputs_form_base(btn_action_next: $hiddens->btn_action_next,
            controler: $controler, id_retorno: $hiddens->id_retorno, in_registro_id: $hiddens->in_registro_id,
            inm_comprador_id: $inm_comprador_id, inm_ubicacion_id: '', precio_operacion: $hiddens->precio_operacion,
            seccion_retorno: $hiddens->seccion_retorno);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }
        return $inputs;
    }

    /**
     * Se integran los inputs a un objeto legible para uso en frontend
     * @param string $btn_action_next Button siguiente accion
     * @param controlador_inm_comprador|controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param string $id_retorno Identificador de retorno como get registro_id
     * @param string $in_registro_id Input de retorno
     * @param string $inm_comprador_id Comprador identificador
     * @param string $inm_ubicacion_id Ubicacion Identificador
     * @param string $precio_operacion Precio de operacion de compra venta
     * @param string $seccion_retorno Seccion de retorno
     * @return array|stdClass
     * @version 1.81.1
     */
    final public function inputs_form_base(
        string $btn_action_next, controlador_inm_comprador|controlador_inm_ubicacion $controler,
        string $id_retorno, string $in_registro_id, string $inm_comprador_id, string $inm_ubicacion_id,
        string $precio_operacion, string $seccion_retorno): array|stdClass
    {
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $id_retorno = trim($id_retorno);
        if($id_retorno === ''){
            return $this->error->error(mensaje: 'Error id_retorno esta vacio', data: $id_retorno);
        }
        $btn_action_next = trim($btn_action_next);
        if($btn_action_next === ''){
            return $this->error->error(mensaje: 'Error btn_action_next esta vacio', data: $btn_action_next);
        }
        $seccion_retorno = trim($seccion_retorno);
        if($seccion_retorno === ''){
            return $this->error->error(mensaje: 'Error seccion_retorno esta vacio', data: $seccion_retorno);
        }
        $in_registro_id = trim($in_registro_id);
        if($in_registro_id === ''){
            return $this->error->error(mensaje: 'Error in_registro_id esta vacio', data: $in_registro_id);
        }
        $precio_operacion = trim($precio_operacion);
        if($precio_operacion === ''){
            return $this->error->error(mensaje: 'Error precio_operacion esta vacio', data: $precio_operacion);
        }

        $inm_comprador_id = trim($inm_comprador_id);
        $inm_ubicacion_id = trim($inm_ubicacion_id);

        if($inm_comprador_id === '' && $inm_ubicacion_id === ''){
            return $this->error->error(mensaje: 'Error inm_comprador_id o  inm_ubicacion_id debe tener info',
                data: $controler->inputs);
        }

        $controler->inputs->id_retorno = $id_retorno;
        $controler->inputs->btn_action_next = $btn_action_next;
        $controler->inputs->seccion_retorno = $seccion_retorno;
        $controler->inputs->registro_id = $in_registro_id;
        $controler->inputs->inm_comprador_id = $inm_comprador_id;
        $controler->inputs->precio_operacion = $precio_operacion;
        $controler->inputs->inm_ubicacion_id = $inm_ubicacion_id;

        return $controler->inputs;
    }


    /**
     * Obtiene todos los inputs para un controlador en ejecucion
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param string $function Funcion de retorno
     * @return array|stdClass
     * @version 1.146.1
     */
    private function input_full(controlador_inm_comprador $controler, string $function): array|stdClass
    {
        $function = trim($function);
        if($function === ''){
            return $this->error->error(mensaje: 'Error function esta vacio',data:  $function);
        }

        $inm_comprador_id = $controler->html->hidden(name:'inm_comprador_id',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $inm_comprador_id);
        }

        $inputs = $this->inputs_base(controler: $controler,function: $function,inm_comprador_id:  $inm_comprador_id);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }

        $inm_co_acreditado_id = (new _inm_comprador())->inm_co_acreditado_id_input(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inm_co_acreditado_id',data:  $inm_co_acreditado_id);
        }

        $controler->inputs->inm_co_acreditado_id = $inm_co_acreditado_id;

        return $controler->inputs;
    }

    /**
     * Integra el atributo disabled como true
     * @param string $key key a inicializar e integrar
     * @param array $keys_selects Parametros previos cargados
     * @return array
     * @version 1.82.1
     */
    private function integra_disabled(string $key, array $keys_selects): array
    {
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data: $key);
        }
        if(is_numeric($key)){
            return $this->error->error(mensaje: 'Error key debe ser un texto',data: $key);
        }
        if(!isset($keys_selects[$key])){
            $keys_selects[$key] = new stdClass();
        }
        $keys_selects[$key]->disabled = true;
        return $keys_selects;
    }

    /**
     * Integra los keys en forma disabled para elementos de consulta
     * @param array $keys Keys a integrar coo disabled
     * @param array $keys_selects parametros previos cargados
     * @return array
     * @version 1.74.1
     */
    private function integra_disableds(array $keys, array $keys_selects): array
    {
        foreach ($keys as $key){
            if(!is_string($key)){
                return $this->error->error(mensaje: 'Error key no es un string',data: $key);
            }
            $key = trim($key);
            if($key === ''){
                return $this->error->error(mensaje: 'Error key esta vacio',data: $key);
            }
            if(is_numeric($key)){
                return $this->error->error(mensaje: 'Error key debe ser un texto',data: $key);
            }
            $keys_selects = $this->integra_disabled(key: $key,keys_selects:  $keys_selects);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integra disabled',data: $keys_selects);
            }
        }
        return $keys_selects;
    }

    /**
     * @param controlador_inm_prospecto $controler
     * @param array $keys_selects
     * @return array
     * @version 2.240.3
     */
    private function key_select_agente(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $id_selected = $this->id_selected_agente(link: $controler->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_selected',data:  $id_selected);
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $id_selected, label: 'Agente');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera un selector de nacionalidad
     * @param controlador_inm_prospecto $controler controlador en ejecucion
     * @param array $keys_selects Params previos cargados
     * @return array
     * @version 2.245.2
     */
    private function key_select_nacionalidad(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $inm_nacionalidad_id = (new inm_nacionalidad(link: $controler->link))->id_preferido_detalle(
            entidad_preferida: 'inm_nacionalidad');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $inm_nacionalidad_id);
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_nacionalidad_id',
            keys_selects:$keys_selects, id_selected: $inm_nacionalidad_id, label: 'Nacionalidad');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera un selector de ocupacion
     * @param controlador_inm_prospecto $controler Controlador en ejecucion
     * @param array $keys_selects Parametros previos cargados
     * @return array
     * @version 2.245.2
     */
    private function key_select_ocupacion(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $inm_ocupacion_id = (new inm_ocupacion(link: $controler->link))->id_preferido_detalle(
            entidad_preferida: 'inm_ocupacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $inm_ocupacion_id);
        }

        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_ocupacion_id',
            keys_selects:$keys_selects, id_selected: $inm_ocupacion_id, label: 'Ocupacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera un selector de tipo sindicato
     * @param controlador_inm_prospecto $controler Controlador en ejecucion
     * @param array $keys_selects Key precargados
     * @return array
     * @version 2.243.2
     */
    private function key_select_sindicato(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $inm_sindicato_id = (new inm_sindicato(link: $controler->link))->id_preferido_detalle(
            entidad_preferida: 'inm_sindicato');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $inm_sindicato_id);
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_sindicato_id',
            keys_selects:$keys_selects, id_selected: $inm_sindicato_id, label: 'Sindicato');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera un select de tipo agente
     * @param controlador_inm_prospecto $controler Controlador en ejecucion
     * @param array $keys_selects parametros precargados
     * @return array
     * @version 2.242.2
     */
    private function key_select_tipo_agente(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $com_tipo_prospecto_id = (new com_prospecto(link: $controler->link))->id_preferido_detalle(
            entidad_preferida: 'com_tipo_prospecto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $com_tipo_prospecto_id);
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'com_tipo_prospecto_id', keys_selects:$keys_selects, id_selected: $com_tipo_prospecto_id,
            label: 'Tipo de prospecto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Obtiene los keys para view asigna ubicacion
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array
     */
    final public function key_selects_asigna_ubicacion(controlador_inm_comprador $controler): array
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id es menor a 0',
                data:  $controler->registro_id);
        }

        $keys_selects = $this->key_selects_base(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_disabled(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Ajusta los selects para forms upd
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array
     */
    final public function key_selects_base(controlador_inm_comprador $controler): array
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error $controler->registro_id es menor a 0',
                data:  $controler->registro_id);
        }

        $row_upd = $this->ajusta_row_data_cliente(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener row_upd',data:  $row_upd);
        }

        $keys_selects = $this->init(controler: $controler,row_upd: $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera los selectores base de un prospecto a nivel parametros
     * @param controlador_inm_prospecto $controler Controlador en ejecucion
     * @param array $keys_selects Parametros previos cargados
     * @return array
     * @version 2.246.2
     */
    final public function keys_selects_prospecto(controlador_inm_prospecto $controler, array $keys_selects): array
    {
        $keys_selects = $this->key_select_agente(controler: $controler,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $this->key_select_tipo_agente(controler: $controler,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $this->key_select_sindicato(controler: $controler,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $this->key_select_nacionalidad(controler: $controler,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $this->key_select_ocupacion(controler: $controler,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function keys_base_cliente(array $keys_selects){
        $keys_selects = $this->keys_identificadores(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_name(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_nrp(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function keys_co_acreditado(): array
    {

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
        return $keys_co_acreditado;
    }

    final public function keys_contacto(array $keys_selects){
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['lada']->regex = $this->validacion->patterns['lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['numero']->regex = $this->validacion->patterns['tel_sin_lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'celular',
            keys_selects:$keys_selects, place_holder: 'Celular');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['celular']->regex = $this->validacion->patterns['telefono_mx_html'];

        return $keys_selects;
    }

    /**
     * Genera los keys selects con disabled
     * @param array $keys_selects Parametros previos cargados
     * @return array
     * @version 1.76.1
     */
    private function keys_disabled(array $keys_selects): array
    {
        $keys = array('com_tipo_cliente_id','nss','curp','rfc','apellido_paterno','apellido_materno','nombre');

        $keys_selects = $this->integra_disableds(keys: $keys,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integra disabled',data: $keys_selects);
        }

        return $keys_selects;
    }

    private function keys_identificadores(array $keys_selects){
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nss', keys_selects:$keys_selects,
            place_holder: 'NSS');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['nss']->regex = $this->validacion->patterns['nss_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'curp',
            keys_selects:$keys_selects, place_holder: 'CURP');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['curp']->regex = $this->validacion->patterns['curp_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'rfc',
            keys_selects:$keys_selects, place_holder: 'RFC');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['rfc']->regex = $this->validacion->patterns['rfc_html'];

        return $keys_selects;
    }

    final public function keys_name(array $keys_selects){
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_paterno',
            keys_selects:$keys_selects, place_holder: 'Apellido Paterno');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'apellido_materno',
            keys_selects:$keys_selects, place_holder: 'Apellido Materno');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'nombre',
            keys_selects:$keys_selects, place_holder: 'Nombre(s)');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    private function keys_nrp(array $keys_selects){
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nombre_empresa_patron',
            keys_selects:$keys_selects, place_holder: 'Nombre de la Empresa/Patrón');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nrp',
            keys_selects:$keys_selects, place_holder: 'NÚMERO DE REGISTRO PATRONAL (NRP)');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nrp_nep',
            keys_selects:$keys_selects, place_holder: 'NÚMERO DE REGISTRO PATRONAL (NRP)');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'lada_nep',
            keys_selects:$keys_selects, place_holder: 'Lada');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['lada_nep']->regex = $this->validacion->patterns['lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'numero_nep',
            keys_selects:$keys_selects, place_holder: 'Numero');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['numero_nep']->regex = $this->validacion->patterns['tel_sin_lada_html'];

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'extension_nep',
            keys_selects:$keys_selects, place_holder: 'Extension',required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function keys_referencias(): array
    {

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
        return $keys_referencias;
    }

    final public function keys_referencias_2(): array
    {
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
        return $keys_referencias;
    }

    /**
     * Integra los elementos para la generacion de selects fiscales
     * @param controlador_inm_comprador $controler
     * @param array $keys_selects
     * @param stdClass $row_upd
     * @return array
     * @version 1.58.1
     */
    private function ks_fiscales(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $row_upd = $this->init_row_upd_fiscales(modelo: $controler->modelo, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row_upd',data:  $row_upd);
        }

        $columns_ds = array('cat_sat_regimen_fiscal_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_regimen_fiscal_id', keys_selects: $keys_selects,
            id_selected: $row_upd->cat_sat_regimen_fiscal_id, label: 'Regimen Fiscal', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_moneda_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_moneda_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_moneda_id, label: 'Moneda',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_forma_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_forma_pago_id', keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_forma_pago_id,
            label: 'Forma de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_metodo_pago_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_metodo_pago_id', keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_metodo_pago_id,
            label: 'Metodo de Pago', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_uso_cfdi_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'cat_sat_uso_cfdi_id',
            keys_selects: $keys_selects, id_selected: $row_upd->cat_sat_uso_cfdi_id, label: 'Uso de CFDI',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('cat_sat_tipo_persona_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'cat_sat_tipo_persona_id', keys_selects: $keys_selects,
            id_selected: $row_upd->cat_sat_tipo_persona_id, label: 'Tipo de Persona', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('bn_cuenta_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(), key: 'bn_cuenta_id',
            keys_selects: $keys_selects, id_selected: $row_upd->bn_cuenta_id, label: 'Cuenta Deposito',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Integra los parametros para la generacion de inputs de tipo infonavit
     * @param controlador_inm_comprador $controler  Controlador en ejecucion
     * @param array $keys_selects Configuraciones precargadas
     * @param stdClass $row_upd Registro en proceso
     * @return array
     */
    private function ks_infonavit(controlador_inm_comprador $controler, array $keys_selects, stdClass $row_upd): array
    {

        $row_upd = $this->init_row_upd_infonavit(modelo: $controler->modelo, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializa row_upd',data:  $row_upd);
        }

        $columns_ds[] = 'inm_producto_infonavit_descripcion';
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_producto_infonavit_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_producto_infonavit_id, label: 'Producto', columns_ds: $columns_ds);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_tipo_credito_descripcion';
        $columns_ds[] = 'inm_attr_tipo_credito_descripcion';
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_attr_tipo_credito_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_attr_tipo_credito_id, label: 'Tipo de Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_destino_credito_descripcion';

        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_destino_credito_id', keys_selects: $keys_selects, id_selected: $row_upd->inm_destino_credito_id,
            label: 'Destino del Credito', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $disabled = false;
        if((int)$row_upd->inm_plazo_credito_sc_id === 7){
            $disabled = true;
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_plazo_credito_sc_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_plazo_credito_sc_id, label: 'Plazo Segundo Credito', disabled: $disabled);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $columns_ds = array();
        $columns_ds[] = 'inm_tipo_discapacidad_descripcion';

        $disabled = false;
        if((int)$row_upd->inm_tipo_discapacidad_id === 5){
            $disabled = true;
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(),
            key: 'inm_tipo_discapacidad_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_tipo_discapacidad_id, label: 'Tipo de Discapacidad', columns_ds: $columns_ds,
            disabled: $disabled);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_persona_discapacidad_descripcion';

        $disabled = false;
        if((int)$row_upd->inm_persona_discapacidad_id === 6){
            $disabled = true;
        }

        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_persona_discapacidad_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_persona_discapacidad_id, label: 'Persona Discapacidad',
            columns_ds: $columns_ds, disabled: $disabled);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array();
        $columns_ds[] = 'inm_institucion_hipotecaria_descripcion';
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_institucion_hipotecaria_id', keys_selects: $keys_selects,
            id_selected: $row_upd->inm_institucion_hipotecaria_id, label: 'Institucion Hipotecaria',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        return $keys_selects;
    }

    /**
     * Asigna elementos de cliente para modifica
     * @param array $com_cliente Registro de tipo cliente
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return stdClass|array
     */
    private function row_data_cliente(array $com_cliente, controlador_inm_comprador $controler): stdClass|array
    {
        $keys = array('com_cliente_rfc','com_cliente_numero_exterior','com_cliente_telefono','dp_pais_id',
            'dp_estado_id','dp_municipio_id', 'com_tipo_cliente_id');

        $valida = (new valida())->valida_existencia_keys(keys: $keys,registro:  $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar com_cliente',data:  $valida);
        }

        if(!isset($com_cliente['com_cliente_numero_interior'])){
            $com_cliente['com_cliente_numero_interior'] = '';
        }

        $keys = array('dp_pais_id', 'dp_estado_id','dp_municipio_id', 'com_tipo_cliente_id');

        $valida = (new valida())->valida_ids(keys: $keys,registro:  $com_cliente);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar com_cliente',data:  $valida);
        }

        $keys = array('dp_pais_id', 'dp_estado_id','dp_municipio_id','dp_cp_id','dp_calle_id','dp_colonia_id',
            'dp_colonia_postal_id','dp_calle_pertenece_id');

        $valida = (new valida())->valida_ids(keys: $keys,registro:  $controler->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar com_cliente',data:  $valida);
        }

        //print_r($controler->registro);exit;


        $controler->row_upd->rfc = $com_cliente['com_cliente_rfc'];
        $controler->row_upd->numero_exterior = $com_cliente['com_cliente_numero_exterior'];
        $controler->row_upd->numero_interior = $com_cliente['com_cliente_numero_interior'];
        $controler->row_upd->telefono = $com_cliente['com_cliente_telefono'];
        $controler->row_upd->dp_pais_id = $controler->registro['dp_pais_id'];
        $controler->row_upd->dp_estado_id = $controler->registro['dp_estado_id'];
        $controler->row_upd->dp_municipio_id = $controler->registro['dp_municipio_id'];
        $controler->row_upd->com_tipo_cliente_id = $com_cliente['com_tipo_cliente_id'];
        $controler->row_upd->dp_cp_id = $controler->registro['dp_cp_id'];
        $controler->row_upd->dp_calle_id = $controler->registro['dp_calle_id'];
        $controler->row_upd->dp_colonia_id = $controler->registro['dp_colonia_id'];
        $controler->row_upd->dp_colonia_postal_id = $controler->registro['dp_colonia_postal_id'];
        $controler->row_upd->dp_calle_pertenece_id = $controler->registro['dp_calle_pertenece_id'];
        return $controler->row_upd;
    }
}