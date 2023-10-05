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
use PDO;
use stdClass;

class inm_ubicacion_html extends html_controler {


    final public function base_costos(controlador_inm_ubicacion $controler, string $funcion){
        $base = $this->base_inm_ubicacion_upd(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar base',data:  $base);
        }

        $data = $this->data_form(controler: $controler,funcion: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener formulario',data:  $data);
        }

        $costos = $this->init_costos(controler: $controler);
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
            'inm_comprador_apellido_materno','inm_comprador_nss');
        $inm_comprador_id = (new inm_comprador_html(html: $controler->html_base))->select_inm_comprador_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $controler->link, columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inm_comprador_id',data:  $inm_comprador_id);
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
     */
    private function data_form(controlador_inm_ubicacion $controler, string $funcion): array|stdClass
    {
        $inputs = $this->inputs_base_ubicacion(controler: $controler,funcion: $funcion);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs_hidden',data:  $inputs);
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
     * @return string
     */
    private function form_ubicacion(controlador_inm_ubicacion $controlador): string
    {
        $keys = array('dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id','dp_calle_pertenece_id',
            'numero_exterior','numero_interior','manzana','lote','inm_ubicacion_id','seccion_retorno',
            'btn_action_next','id_retorno');


        $inputs = '';
        foreach ($keys as $input){
            $inputs .= $controlador->inputs->$input;
        }

        return $inputs;
    }

    private function init_costos(controlador_inm_ubicacion $controler){
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

        $registros = $r_inm_costos->registros;
        $arreglo_costos = (array)$r_inm_costos;
        foreach ($arreglo_costos['registros'] as $key => $row){

            $links = array();
            foreach ($acciones_grupo as $indice=>$adm_accion_grupo){
                $registro_id = $row['inm_costo_id'];

                $data_link = (new datatables())->data_link(adm_accion_grupo: $adm_accion_grupo,
                    data_result: $arreglo_costos, html_base: $this->html_base, key: $key,registro_id:  $registro_id);

                if(errores::$error){
                    return $this->error->error(mensaje: 'Error al obtener data para link', data: $data_link);
                }

                $links[$data_link->accion] = $data_link->link_con_id;
            }

            $botones['acciones'] = $links;
            $registros[$key] = array_merge($row,$botones);
        }

        $registros = $this->format_moneda_mx_arreglo(registros: $registros,
            campo_integrar: 'inm_costo_monto');
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

    public function format_moneda_mx_arreglo(array $registros, string $campo_integrar){
        $registros_format = array();
        foreach ($registros as $campo){
            if(!isset($campo[$campo_integrar])){
                return $this->error->error(mensaje: 'Error no existe indice de arreglo',data:  $campo);
            }

            $campo[$campo_integrar] = $this->format_moneda_mx(monto: $campo[$campo_integrar]);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error formatear monto',data:  $campo);
            }

            $registros_format[] = $campo;
        }

        return $registros_format;
    }

    public function format_moneda_mx(string $monto): bool|array|string
    {
        if($monto === ''){
            return $this->error->error(mensaje: 'Error monto no puede ser vacio',data:  $monto);
        }

        $amount = new \NumberFormatter( 'es_MX', \NumberFormatter::CURRENCY);

        return $amount->format((float)$monto);
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


}
