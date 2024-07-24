<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\html\inm_concepto_html;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_ubicacion;
use stdClass;

class _ubicacion{

    private errores  $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Ejecuta los elementos base de una actualizacion view front
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 2.155.1
     */
    final public function base_upd(controlador_inm_ubicacion $controler): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $controler->registro_id);
        }

        $r_modifica = $controler->init_modifica();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar salida de template',data:  $r_modifica);
        }

        $data_row = $controler->modelo->registro(registro_id: $controler->registro_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro',data:  $data_row);
        }

        $data = new stdClass();
        $data->r_modifica = $r_modifica;
        $data->data_row = $data_row;
        return $data;
    }

    /**
     * Obtiene los datos de una vista para acciones de ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param array $disableds Agrega el attr disabled al campo seleccionado
     * @return array|stdClass
     * @version 2.145.0
     */
    private function base_view_accion(controlador_inm_ubicacion $controler, array $disableds): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $controler->registro_id);
        }

        $data_front = $this->base_upd(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $data_front);
        }

        $keys_selects = $this->keys_selects_view(controler: $controler,data_row:  $data_front->data_row,
            disableds: $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }

        $datas = new stdClass();
        $datas->r_modifica = $data_front->r_modifica;
        $datas->data_row = $data_front->data_row;
        $datas->keys_selects = $keys_selects;
        return $datas;
    }

    /**
     * Genera los datos necesarios para una vista de ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en proceso
     * @param array $disableds Anexa attr disabled
     * @param string $funcion Funcion de retorno
     * @return array|stdClass
     */
    final public function base_view_accion_data(controlador_inm_ubicacion $controler, array $disableds,
                                                string $funcion): array|stdClass
    {
        if($controler->registro_id<=0){
            return $this->error->error(mensaje: 'Error registro_id debe ser mayor a 0', data: $controler->registro_id);
        }
        $funcion = trim($funcion);
        if($funcion === ''){
            return $this->error->error(mensaje: 'Error funcion esta vacio',data:  $funcion);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $base_html = $this->base_view_accion(controler: $controler,disableds: $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener base_html', data:  $base_html);
        }

        $base = $controler->base_upd(keys_selects: $base_html->keys_selects, params: array(),params_ajustados: array());
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar base',data:  $base);
        }

        $html = new inm_ubicacion_html(html: $controler->html_base);

        $inputs = $html->inputs_base_ubicacion(controler: $controler,funcion: $funcion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar inputs',data:  $inputs);
        }


        $data = new stdClass();
        $data->base_html = $base_html;
        $data->base = $base;
        $data->inputs = $inputs;

        return $data;
    }

    /**
     * Obtiene los identificadores preferidos de una ubicacion
     * @param inm_ubicacion $modelo_preferido Modelo de tipo ubicacion
     * @return array|stdClass
     * @version 2.134.1
     *
     */
    private function ids_pref_dp(inm_ubicacion $modelo_preferido): array|stdClass
    {

        $entidades = $this->entidades_dp();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener entidades',data:  $entidades);
        }
        $data = $this->integra_ids_preferidos(data: new stdClass(),entidades:  $entidades,
            modelo_preferido:  $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $data);
        }

        return $data;
    }

    /**
     * Obtiene los datos para maquetacion de selects
     * @param inm_ubicacion $modelo_preferido Modelo para obtener defaults
     * @return array|stdClass
     * @version 2.134.0
     */
    PUBLIC function data_row_alta(inm_ubicacion $modelo_preferido): array|stdClass
    {
        $data_row = $this->ids_pref_dp(modelo_preferido: $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ids', data:  $data_row);
        }
        $inm_tipo_ubicacion_id = $modelo_preferido->id_preferido_detalle(entidad_preferida: 'inm_tipo_ubicacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_tipo_ubicacion_id', data:  $inm_tipo_ubicacion_id);
        }
        $data_row->inm_tipo_ubicacion_id = $inm_tipo_ubicacion_id;

        return $data_row;
    }

    /**
     * Obtiene las entidades de tipo direccion postal
     * @return string[]
     * @version 2.130.0
     */
    private function entidades_dp(): array
    {
        return array('dp_pais','dp_estado','dp_municipio','dp_cp','dp_colonia_postal','dp_calle_pertenece');
    }

    /**
     * Integra el id preferido a un data
     * @param stdClass $data Data previo cargado
     * @param string $entidad Entidad
     * @param inm_ubicacion|inm_prospecto|inm_prospecto_ubicacion $modelo_preferido modelo de ejecucion
     * @return array|stdClass
     * @version 2.127.0
     */
    private function get_id_preferido(
        stdClass $data, string $entidad, inm_ubicacion|inm_prospecto|inm_prospecto_ubicacion $modelo_preferido): array|stdClass
    {
        $entidad = trim($entidad);
        if($entidad === ''){
            return $this->error->error(mensaje: 'Error entidad esta vacia',data:  $entidad);
        }

        $key_id = $entidad.'_id';
        $id = $modelo_preferido->id_preferido_detalle(entidad_preferida: $entidad);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id',data:  $id);
        }
        $data->$key_id = $id;

        return $data;
    }

    /**
     * Inicializa los elementos para un alta
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param array $disableds Atributo disabled
     * @return array
     */
    final public function init_alta(controlador_inm_ubicacion $controler, array $disableds): array
    {

        $modelo_preferido = new inm_ubicacion(link: $controler->link);


        $data_row = $this->data_row_alta(modelo_preferido: $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ids', data:  $data_row);
        }

        $keys_selects = $this->keys_selects_base(controler: $controler,data_row:  $data_row, disableds: $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }

        $inm_ubicacion_id_ultimo = $controler->modelo->ultimo_registro_id();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_ubicacion_id_ultimo',
                data:  $inm_ubicacion_id_ultimo);
        }

        $codigo = $inm_ubicacion_id_ultimo+1;

        $controler->row_upd->costo_directo = 0;
        $controler->row_upd->codigo = $codigo;
        return $keys_selects;
    }

    /**
     * Integra los inputs de costeo
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 2.176.1
     */
    final public function inputs_costo(controlador_inm_ubicacion $controler): array|stdClass
    {
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error $controler->inputs no esta inicializado',
                data:  $controler->inputs);
        }
        $inm_concepto_id = (new inm_concepto_html(html: $controler->html_base))->select_inm_concepto_id(
            cols: 12,con_registros: true, id_selected: -1,link:  $controler->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar inm_concepto_id',data:  $inm_concepto_id);
        }

        $controler->inputs->inm_concepto_id = $inm_concepto_id;

        $referencia = (new inm_concepto_html(html: $controler->html_base))->input_text_required(cols: 12,disabled: false,
            name: 'referencia',place_holder: 'Referencia',row_upd: new stdClass(),value_vacio: false);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar referencia',data:  $referencia);
        }

        $controler->inputs->referencia = $referencia;

        $fecha = (new inm_concepto_html(html: $controler->html_base))->input_fecha(cols: 12,row_upd: new stdClass(),
            value_vacio: false, value: date('Y-m-d'));

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar fecha',data:  $fecha);
        }

        $controler->inputs->fecha = $fecha;


        $monto = (new inm_concepto_html(html: $controler->html_base))->input_monto(cols: 12,row_upd: new stdClass(),
            value_vacio: false);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar monto',data:  $monto);
        }

        $controler->inputs->monto = $monto;


        $inm_costo_descripcion = (new inm_concepto_html(html: $controler->html_base))->input_descripcion(
            cols: 12,row_upd: new stdClass(),value_vacio: false);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar inm_costo_descripcion',
                data:  $inm_costo_descripcion);
        }

        $controler->inputs->inm_costo_descripcion = $inm_costo_descripcion;

        return $controler->inputs;
    }

    /**
     * Integra los ids preferidos de una entidad
     * @param stdClass $data datos previos cargados
     * @param array $entidades Entidades preferidas a integrar
     * @param inm_ubicacion|inm_prospecto|inm_prospecto_ubicacion $modelo_preferido Modelo de ejecucion
     * @return array|stdClass
     * @version 2.133.1
     */
    final public function integra_ids_preferidos(stdClass $data, array $entidades,
                                            inm_ubicacion|inm_prospecto|inm_prospecto_ubicacion $modelo_preferido): array|stdClass
    {
        foreach ($entidades as $entidad){
            $entidad = trim($entidad);
            if($entidad === ''){
                return $this->error->error(mensaje: 'Error entidad esta vacia',data:  $entidad);
            }

            $data = $this->get_id_preferido(data: $data,entidad:  $entidad,modelo_preferido:  $modelo_preferido);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id',data:  $data);
            }
        }
        return $data;
    }

    /**
     * Integra el key select de tipo de ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param int $inm_tipo_ubicacion_id Tipo de ubicacion
     * @param array $keys_selects Keys previos cargados
     * @return array
     * @version 2.138.1
     */
    private function key_select_inm_tipo_ubicacion(controlador_inm_ubicacion $controler, int $inm_tipo_ubicacion_id,
                                                   array $keys_selects): array
    {

        $columns_ds = array('inm_tipo_ubicacion_descripcion');
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  array(),
            key: 'inm_tipo_ubicacion_id', keys_selects: $keys_selects, id_selected: $inm_tipo_ubicacion_id,
            label: 'Tipo de Ubicacion', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Integra los selectores con elementos precargados de ids
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param stdClass $data_row Datos previos cargados de registro en proceso
     * @param array $disableds Entidades disabled
     * @return array
     * @version 2.135.0
     */
    private function keys_selects(controlador_inm_ubicacion $controler, stdClass $data_row, array $disableds): array
    {


        $entidades_dp = $this->entidades_dp();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener entidades_dp',data:  $entidades_dp);
        }

        foreach ($entidades_dp as $entidad){
            $key_id = $entidad.'_id';
            if(!isset($data_row->$key_id)){
                $data_row->$key_id = -1;
            }
        }

        $columns_ds = array('dp_pais_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects: array(), id_selected: $data_row->dp_pais_id, label: 'Pais', columns_ds : $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_pais.id'] = $data_row->dp_pais_id;

        $columns_ds = array('dp_estado_descripcion');

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_estado_id',
            keys_selects: $keys_selects, id_selected: $data_row->dp_estado_id, label: 'Estado',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_estado.id'] = $data_row->dp_estado_id;

        $columns_ds = array('dp_municipio_descripcion');

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_municipio_id',
            keys_selects: $keys_selects, id_selected: $data_row->dp_municipio_id, label: 'Municipio',
            columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_cp_descripcion');
        $filtro = array();
        $filtro['dp_municipio.id'] = $data_row->dp_municipio_id;

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_cp_id',
            keys_selects: $keys_selects, id_selected:$data_row->dp_cp_id, label: 'CP', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_colonia_descripcion');
        $filtro = array();
        $filtro['dp_cp.id'] = $data_row->dp_cp_id;
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro,
            key: 'dp_colonia_postal_id', keys_selects: $keys_selects, id_selected: $data_row->dp_colonia_postal_id,
            label: 'Colonia', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $filtro = array();
        $filtro['dp_colonia_postal.id'] = $data_row->dp_colonia_postal_id;
        $keys_selects = $controler->key_select(cols:12, con_registros: true,filtro:  $filtro,
            key: 'dp_calle_pertenece_id', keys_selects: $keys_selects, id_selected: $data_row->dp_calle_pertenece_id,
            label: 'Calle', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        foreach ($disableds as $campo_id){
            $keys_selects[$campo_id]->disabled = true;
        }

        return $keys_selects;
    }

    /**
     * Obtiene los parametros de selectores
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param stdClass $data_row Datos previos cargados
     * @param array $disableds Selectores para integrar disabled
     * @return array
     * @version 2.139.1
     */
    final public function keys_selects_base(controlador_inm_ubicacion $controler, stdClass $data_row,
                                            array $disableds): array
    {
        $keys_selects = $this->keys_selects(controler: $controler,data_row:  $data_row, disableds: $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }
        if(!isset($data_row->inm_tipo_ubicacion_id)){
            $data_row->inm_tipo_ubicacion_id = -1;
        }

        $keys_selects = $this->key_select_inm_tipo_ubicacion(controler: $controler,
            inm_tipo_ubicacion_id:  $data_row->inm_tipo_ubicacion_id,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Integra los parametros para la generacion de selectores de una ubicacion
     * @param controlador_inm_ubicacion $controler Controlador en ejecucion
     * @param stdClass $data_row Datos de ubicacion
     * @param array $disableds
     * @return array
     * @version 2.144.0
     */
    private function keys_selects_view(controlador_inm_ubicacion $controler, stdClass $data_row,
                                       array $disableds): array
    {
        $keys_selects = $this->keys_selects_base(controler: $controler,data_row:  $data_row, disableds: $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }

        $html = new inm_ubicacion_html(html: $controler->html_base);

        $keys_selects = $html->keys_select_dom(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar keys_selects disabled',data:  $keys_selects);
        }

        return $keys_selects;
    }
}
