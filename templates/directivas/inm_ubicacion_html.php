<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\_ubicacion;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
use gamboamartin\inmuebles\models\inm_costo;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\system\datatables;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use NumberFormatter;
use PDO;
use stdClass;

class inm_ubicacion_html extends html_controler {


    /**
     * Ajusta los registros con permisos
     * @param array $acciones_grupo permisos de ejecucion
     * @param array $arreglo_costos Resultado de costos
     * @param string $key Key a integrar
     * @param array $params_get Parametros adicionales para link de accion
     * @param array $registros Registros de ubicacion
     * @param array $row Registro en proceso
     * @return array
     * @version 2.166.1
     */
    private function ajusta_registros(array $acciones_grupo, array $arreglo_costos, string $key, array $params_get,
                                      array $registros, array $row): array
    {

        $valida = $this->valida_data_link(arreglo_costos: $arreglo_costos,key:  $key,row:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $links = $this->links(acciones_grupo: $acciones_grupo, arreglo_costos: $arreglo_costos, key: $key,
            params_get: $params_get, row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar link', data: $links);
        }

        $botones['acciones'] = $links;
        $registros[$key] = array_merge($row,$botones);
        return $registros;
    }

    /**
     * Integra una vista base de costos con registros de costeo
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param string $funcion Funcion de ejecucion
     * @param array $params_get Parametros adicionales para links
     * @return array|stdClass
     * @version 2.170.0
     */
    final public function base_costos(controlador_inm_ubicacion $controler, string $funcion,
                                      array $params_get): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $controler->registro_id);
        }
        $funcion = trim($funcion);
        if($funcion === ''){
            return $this->error->error(mensaje: 'Error funcion esta vacio',data:  $funcion);
        }

        $base = $this->base_inm_ubicacion_upd(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar base',data:  $base);
        }

        $data = $this->data_form(controler: $controler,funcion: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener formulario',data:  $data);
        }

        $costos = $this->init_costos(controler: $controler, params_get: $params_get);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener costos',data:  $costos);
        }

        $data->base = $base;
        $data->costos = $costos;
        return $data;
    }

    /**
     * Integra los parametros e inputs de una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @return array|stdClass
     * @version 2.155.1
     */
    private function base_inm_ubicacion_upd(controlador_inm_ubicacion $controler): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $controler->registro_id);
        }
        $data_front = (new _ubicacion())->base_upd(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data_front);
        }

        $keys_selects = $this->key_select_ubicacion(controler: $controler, registro: $data_front->data_row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar keys_selects',data:  $keys_selects);
        }

        $base = $controler->base_upd(keys_selects: $keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar base',data:  $base);
        }
        $data = new stdClass();
        $data->r_modifica = $data_front->r_modifica;
        $data->registro = $data_front->data_row;
        $data->keys_selects = $keys_selects;
        $data->base = $base;
        return $data;
    }

    /**
     * Obtiene los parametros de tipo key select para la integracion de selectores
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @param array $keys_selects Keys previos cargados
     * @param stdClass $registro Registro en proceso
     * @return array
     * @version 2.153.0
     */
    private function columnas_dp(controlador_inm_ubicacion $controler, array $keys_selects, stdClass $registro): array
    {

        $keys = array('dp_pais_id','dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id',
            'dp_calle_pertenece_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $columns_ds = array('dp_pais_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects: $keys_selects, id_selected: $registro->dp_pais_id, label: 'Pais', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_pais.id'] = $registro->dp_pais_id;

        $columns_ds = array('dp_estado_descripcion');

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_estado_id',
            keys_selects: $keys_selects, id_selected: $registro->dp_estado_id, label: 'Estado',
            columns_ds: $columns_ds,disabled: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $filtro = array();
        $filtro['dp_estado.id'] = $registro->dp_estado_id;

        $columns_ds = array('dp_municipio_descripcion');

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_municipio_id',
            keys_selects: $keys_selects, id_selected: $registro->dp_municipio_id, label: 'Municipio',
            columns_ds: $columns_ds, disabled: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_cp_descripcion');

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_cp_id',
            keys_selects: $keys_selects, id_selected: $registro->dp_cp_id, label: 'CP', columns_ds: $columns_ds,
            disabled: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_colonia_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro,
            key: 'dp_colonia_postal_id', keys_selects: $keys_selects, id_selected: $registro->dp_colonia_postal_id,
            label: 'Colonia', columns_ds: $columns_ds, disabled: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  $filtro,
            key: 'dp_calle_pertenece_id', keys_selects: $keys_selects, id_selected: $registro->dp_calle_pertenece_id,
            label: 'Calle', columns_ds: $columns_ds, disabled: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Integra los datos de compradores en una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @return array|controlador_inm_ubicacion
     * @version 2.148.0
     */
    final public function data_comprador(controlador_inm_ubicacion $controler): controlador_inm_ubicacion|array
    {
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error inputs no esta inicializado',data:  $controler->inputs);
        }

        $columns_ds = array('inm_comprador_curp','inm_comprador_nombre','inm_comprador_apellido_paterno',
            'inm_comprador_nss');
        $inm_comprador_id = (new inm_comprador_html(html: $controler->html_base))->select_inm_comprador_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $controler->link, columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_comprador_id select',data:  $inm_comprador_id);
        }

        $controler->inputs->inm_comprador_id = $inm_comprador_id;

        $link_rel_ubi_comp_alta_bd = $controler->obj_link->link_alta_bd(link: $controler->link,
            seccion: 'inm_rel_ubi_comp');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar link',data:  $link_rel_ubi_comp_alta_bd);
        }

        $controler->link_rel_ubi_comp_alta_bd = $link_rel_ubi_comp_alta_bd;


        $filtro = array();
        $filtro['inm_ubicacion.id'] = $controler->registro_id;
        $r_inm_rel_ubi_comp = (new inm_rel_ubi_comp(link: $controler->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener compradores',data:  $r_inm_rel_ubi_comp);
        }

        $controler->imp_compradores = $r_inm_rel_ubi_comp->registros;

        return $controler;
    }

    /**
     * Genera el formulario de una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @param string $funcion Funcion de retorno
     * @return array|stdClass
     * @version 2.156.1
     */
    private function data_form(controlador_inm_ubicacion $controler, string $funcion): array|stdClass
    {
        $funcion = trim($funcion);
        if($funcion === ''){
            return $this->error->error(mensaje: 'Error funcion esta vacio',data:  $funcion);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $inputs = $this->inputs_base_ubicacion(controler: $controler,funcion: $funcion);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }

        $keys = array('dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id','dp_calle_pertenece_id',
            'numero_exterior','numero_interior','manzana','lote','inm_ubicacion_id','seccion_retorno',
            'btn_action_next','id_retorno');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $controler->inputs,
            valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inputs', data: $valida);
        }

        $form_ubicacion = $this->form_ubicacion(controlador: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar form',data:  $form_ubicacion);
        }

        $controler->forms_inputs_modifica = $form_ubicacion;

        $data = new stdClass();
        $data->inputs = $inputs;
        $data->forms_inputs_modifica = $controler->forms_inputs_modifica;
        return $data;
    }

    /**
     * Integra los inputs en un string de tipo html
     * @param controlador_inm_ubicacion $controlador Controlador en ejecucion
     * @return string|array
     * @version 2.155.0
     */
    private function form_ubicacion(controlador_inm_ubicacion $controlador): string|array
    {
        $keys = array('dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id','dp_calle_pertenece_id',
            'numero_exterior','numero_interior','manzana','lote','inm_ubicacion_id','seccion_retorno',
            'btn_action_next','id_retorno');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys, registro: $controlador->inputs,
            valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inputs', data: $valida);
        }
        $inputs = '';
        foreach ($keys as $input){
            $inputs .= $controlador->inputs->$input;
        }

        return $inputs;
    }

    /**
     * Inicializa los registros de tipo costo
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @param array $params_get Parametros adicionales para integrar en link como var get
     * @return array|controlador_inm_ubicacion
     * @version 2.169.0
     */
    private function init_costos(controlador_inm_ubicacion $controler,
                                 array $params_get): controlador_inm_ubicacion|array
    {
        if($controler->registro_id <= 0){
            return $this->error->error(mensaje: 'Error $controler->registro_id debe ser mayor a 0',data:  $controler);
        }
        $r_inm_costos = (new inm_costo(link: $controler->link))->filtro_and(
            filtro: array('inm_ubicacion.id'=>$controler->registro_id));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_inm_costos',data:  $r_inm_costos);
        }

        $acciones_grupo = (new datatables())->acciones_permitidas(link: $controler->link,seccion: 'inm_costo',
            not_actions: array('modifica','status'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener acciones', data: $acciones_grupo);
        }

        $registros = $this->registros(acciones_grupo: $acciones_grupo, params_get: $params_get,
            r_inm_costos: $r_inm_costos);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar montos moneda',data:  $registros);
        }

        $controler->inm_costos = $registros;

        $costo = (new inm_ubicacion(link: $controler->link))->get_costo(inm_ubicacion_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener costo',data:  $costo);
        }

        $costo = $this->format_moneda_mx(monto: $costo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error formatear monto',data:  $costo);
        }

        $controler->costo = $costo;

        return $controler;
    }

    /**
     * Integra un valor de formato moneda a un campo de un registro
     * @param array $registros Registros a integrar
     * @param string $campo_integrar Campo a integrar por row
     * @return array
     * @version 2.168.0
     */
    private function format_moneda_mx_arreglo(array $registros, string $campo_integrar): array
    {
        $registros_format = array();
        foreach ($registros as $campo){
            if(!isset($campo[$campo_integrar])){
                return $this->error->error(mensaje: 'Error no existe indice de arreglo',data:  $campo);
            }

            $monto = trim($campo[$campo_integrar]);

            $valor_moneda = $this->format_moneda_mx(monto: $monto);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error formatear monto',data:  $valor_moneda);
            }
            $campo[$campo_integrar] = $valor_moneda;

            $registros_format[] = $campo;
        }

        return $registros_format;
    }

    /**
     * Da formato de moneda mexicana a un flotante
     * @param string|float|int $monto Monto a ajustar
     * @param string $locale Formato de moneda dependiendo pais var GLOBAL php LOCALES O LOCATIONS
     * @return array|string
     * @version 2.159.1
     */
    private function format_moneda_mx(string|float|int $monto, string $locale = 'es_MX'): array|string
    {
        $monto = trim($monto);
        if($monto === ''){
            return $this->error->error(mensaje: 'Error monto no puede ser vacio',data:  $monto);
        }

        $monto = str_replace(',', '', $monto);
        $monto = str_replace("'", '', $monto);
        $monto = str_replace("$", '', $monto);

        if($monto === ''){
            return $this->error->error(mensaje: 'Error monto no puede ser vacio',data:  $monto);
        }

        if(!is_numeric($monto)){
            return $this->error->error(mensaje: 'Error monto debe ser un numero',data:  $monto);
        }

        $amount = new NumberFormatter( $locale, NumberFormatter::CURRENCY);

        $monto_ajustado = $amount->format((float)$monto);
        if(is_bool($monto_ajustado) && !$monto_ajustado){
            return $this->error->error(mensaje: 'Error al dar formato de moneda',data:  $monto);
        }

        return $monto_ajustado;
    }

    /**
     * Obtiene los inputs de una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @param string $funcion Funcion de parametros de retorno para GET
     * @return array|stdClass
     * @version 2.146.1
     */
    final public function inputs_base_ubicacion(controlador_inm_ubicacion $controler,
                                                string $funcion): array|stdClass
    {
        $funcion = trim($funcion);
        if($funcion === ''){
            return $this->error->error(mensaje: 'Error funcion esta vacio',data:  $funcion);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $inm_ubicacion_id = $this->hidden(name:'inm_ubicacion_id',value: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al in_registro_id',data:  $inm_ubicacion_id);
        }

        $hiddens = (new _keys_selects())->hiddens(controler: $controler,funcion: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs',data:  $hiddens);
        }

        $inputs = (new _keys_selects())->inputs_form_base(btn_action_next: $hiddens->btn_action_next,
            controler: $controler, id_retorno: $hiddens->id_retorno, in_registro_id: $hiddens->in_registro_id,
            inm_comprador_id: '', inm_ubicacion_id: $inm_ubicacion_id, precio_operacion: $hiddens->precio_operacion,
            seccion_retorno: $hiddens->seccion_retorno);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
        }

        return $inputs;
    }

    /**
     * Integra un link con la accion de usuario
     * @param array $adm_accion_grupo permiso
     * @param array $arreglo_costos Datos de costos
     * @param string $key Key a integrar
     * @param array $links Links previos
     * @param array $params_get Parametros de link de row
     * @param array $row Registro en proceso
     * @return array
     * @version 2.164.1
     */
    private function integra_link(array $adm_accion_grupo, array $arreglo_costos, string $key, array $links,
                                  array $params_get, array $row): array
    {

        $valida = $this->valida_data_link(arreglo_costos: $arreglo_costos,key:  $key,row:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $registro_id = $row['inm_costo_id'];


        $data_link = (new datatables())->data_link(adm_accion_grupo: $adm_accion_grupo, data_result: $arreglo_costos,
            html_base: $this->html_base, key: $key,registro_id:  $registro_id, params_get: $params_get);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data para link', data: $data_link);
        }

        $links[$data_link->accion] = $data_link->link_con_id;

        return $links;
    }

    /**
     * Asigna disableds a keys ubicacion
     * @param array $keys_selects Keys a integrar disableds
     * @return array
     * @version 2.143.0
     */
    final public function keys_select_dom(array $keys_selects): array
    {
        $keys_selects['numero_exterior'] = new stdClass();
        $keys_selects['numero_exterior']->disabled = true;

        $keys_selects['numero_interior'] = new stdClass();
        $keys_selects['numero_interior']->disabled = true;

        $keys_selects['manzana'] = new stdClass();
        $keys_selects['manzana']->disabled = true;

        $keys_selects['lote'] = new stdClass();
        $keys_selects['lote']->disabled = true;
        return $keys_selects;
    }

    /**
     * Obtiene los keys selects base de una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador el ejecucion
     * @param stdClass $registro Registro en proceso
     * @return array
     * @version 2.153.0
     */
    private function key_select_ubicacion(controlador_inm_ubicacion $controler, stdClass $registro): array
    {
        $keys = array('dp_pais_id','dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id',
            'dp_calle_pertenece_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys_selects = $this->columnas_dp(controler: $controler,keys_selects:  array(),registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_select_dom(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar keys_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Integra los links de un registro para ser ejecutado en una lista
     * @param array $acciones_grupo Acciones o permisos de un grupo de usuarios
     * @param array $arreglo_costos Registros con los costos de ubicacion
     * @param string $key Key de costo
     * @param array $params_get Parametros de link de acciones
     * @param array $row Registro en ejecucion
     * @return array
     * @version 2.165.1
     */
    private function links(array $acciones_grupo, array $arreglo_costos, string $key, array $params_get,
                           array $row): array
    {
        $valida = $this->valida_data_link(arreglo_costos: $arreglo_costos,key:  $key,row:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar datos', data: $valida);
        }

        $links = array();
        foreach ($acciones_grupo as $adm_accion_grupo){

            if(!is_array($adm_accion_grupo)){
                return $this->error->error(mensaje: 'Error adm_accion_grupo debe ser una array',
                    data: $adm_accion_grupo);
            }


            $links = $this->integra_link(adm_accion_grupo: $adm_accion_grupo, arreglo_costos: $arreglo_costos,
                key: $key, links: $links, params_get: $params_get, row: $row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar link', data: $links);
            }
        }
        return $links;
    }

    /**
     * Integra los parametros para links
     * @param string $accion_retorno Accion de retorno
     * @param int $id_retorno Id de retorno
     * @param string $seccion_retorno Seccion de retorno
     * @return array
     * @version 2.180.1
     */
    final public function params_get_data(string $accion_retorno, int $id_retorno, string $seccion_retorno): array
    {
        $accion_retorno = trim($accion_retorno);
        if($accion_retorno === ''){
            return $this->error->error(mensaje: 'Error accion_retorno esta vacia',data:  $accion_retorno);
        }
        if($id_retorno <= 0 ){
            return $this->error->error(mensaje: 'Error id_retorno es menor a 0',data:  $id_retorno);
        }
        $seccion_retorno = trim($seccion_retorno);
        if($seccion_retorno === ''){
            return $this->error->error(mensaje: 'Error seccion_retorno esta vacia',data:  $seccion_retorno);
        }
        $params_get['seccion_retorno'] = $seccion_retorno;
        $params_get['accion_retorno'] = $accion_retorno;
        $params_get['id_retorno'] = $id_retorno;
        return $params_get;
    }

    /**
     * Inicializa los registros para ser mostrados en front
     * @param array $acciones_grupo Permisos
     * @param array $params_get Parametros adicionales para links
     * @param stdClass $r_inm_costos Resultado de costos
     * @return array
     * @version 2.168.1
     */
    private function registros(array $acciones_grupo, array $params_get, stdClass $r_inm_costos): array
    {
        if(!isset($r_inm_costos->registros)){
            return $this->error->error(mensaje: 'Error r_inm_costos->registros no existe',data:  $r_inm_costos);
        }
        if(!is_array($r_inm_costos->registros)){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros] debe ser una array',
                data:  $r_inm_costos);
        }

        $registros = $this->registros_con_link(acciones_grupo: $acciones_grupo, params_get: $params_get,
            r_inm_costos: $r_inm_costos);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al ajustar registros link', data: $registros);
        }

        $registros = $this->format_moneda_mx_arreglo(registros: $registros, campo_integrar: 'inm_costo_monto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar montos moneda',data:  $registros);
        }

        return $registros;
    }

    /**
     * Integra los links a un registro de tipo costo
     * @param array $acciones_grupo Permisos
     * @param array $params_get Parametros adicionales para links de acciones
     * @param stdClass $r_inm_costos Resultado de sql de costos
     * @return array
     * @version 2.167.1
     */
    private function registros_con_link(array $acciones_grupo, array $params_get, stdClass $r_inm_costos): array
    {
        if(!isset($r_inm_costos->registros)){
            return $this->error->error(mensaje: 'Error r_inm_costos->registros no existe',data:  $r_inm_costos);
        }
        if(!is_array($r_inm_costos->registros)){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros] debe ser una array',
                data:  $r_inm_costos);
        }

        $registros = $r_inm_costos->registros;
        $arreglo_costos = (array)$r_inm_costos;

        foreach ($arreglo_costos['registros'] as $key => $row){
            $registros = $this->ajusta_registros(acciones_grupo: $acciones_grupo, arreglo_costos: $arreglo_costos,
                key: $key, params_get: $params_get, registros: $registros, row: $row);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al ajustar registros link', data: $registros);
            }
        }
        return $registros;
    }

    /**
     * Genera un input select de ubicaciones
     * @param int $cols Columnas css
     * @param bool $con_registros si no con registros da u input vacio
     * @param int $id_selected Seleccion default
     * @param PDO $link Conexion a la base de datos
     * @param array $columns_ds Columnas a mostrar en select
     * @param bool $disabled Si disabled integra el atributo disabled al input
     * @param array $extra_params_keys
     * @param array $filtro Si se integra filtro el resultado de los options se ajusta al filtro
     * @param array $registros Registros para options
     * @return array|string
     * @version 1.103.1
     */
    final public function select_inm_ubicacion_id(
        int $cols, bool $con_registros, int $id_selected, PDO $link, array $columns_ds = array(),
        bool $disabled = false, array $extra_params_keys = array(), array $filtro = array(),
        array $registros = array()): array|string
    {
        $valida = (new directivas(html:$this->html_base))->valida_cols(cols:$cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }

        $modelo = new inm_ubicacion(link: $link);


        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, extra_params_keys: $extra_params_keys,
            filtro: $filtro, label: 'Ubicacion', registros: $registros, required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }

    /**
     * Valida las entradas de datos para integracion de links
     * @param array $arreglo_costos Arreglo de resultado de costos
     * @param string $key Key a integrar valor
     * @param array $row Registro en proceso
     * @return array|true
     * @version 2.164.1
     */
    private function valida_data_link(array $arreglo_costos, string $key, array $row): bool|array
    {
        $keys = array('inm_costo_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $row', data: $valida);
        }
        if(!isset($arreglo_costos['registros'])){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros] no existe',data:  $arreglo_costos);
        }
        if(!is_array($arreglo_costos['registros'])){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros] debe ser una array',
                data:  $arreglo_costos);
        }
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $key);
        }

        if(!isset($arreglo_costos['registros'][$key])){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros][key] no existe',
                data:  $arreglo_costos);
        }
        if(!is_array($arreglo_costos['registros'][$key])){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros][key] debe ser un array',
                data:  $arreglo_costos);
        }
        if(count($arreglo_costos['registros'][$key]) === 0){
            return $this->error->error(mensaje: 'Error arreglo_costos[registros][key] esta vacio',
                data:  $arreglo_costos);
        }
        return true;
    }


}
