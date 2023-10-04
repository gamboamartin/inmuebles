<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_ubicacion;
use stdClass;

class _ubicacion{

    private errores  $error;

    public function __construct(){
        $this->error = new errores();
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
     * @param inm_ubicacion|inm_prospecto $modelo_preferido modelo de ejecucion
     * @return array|stdClass
     * @version 2.127.0
     */
    private function get_id_preferido(
        stdClass $data, string $entidad, inm_ubicacion|inm_prospecto $modelo_preferido): array|stdClass
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

    final public function init_alta(controlador_inm_ubicacion $controler){
        $modelo_preferido = new inm_ubicacion(link: $controler->link);


        $data_row = $this->data_row_alta(modelo_preferido: $modelo_preferido);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ids', data:  $data_row);
        }

        $keys_selects = $this->keys_selects_base(controler: $controler,data_row:  $data_row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }


        $controler->row_upd->costo_directo = 0;
        return $keys_selects;
    }

    /**
     * Integra los ids preferidos de una entidad
     * @param stdClass $data datos previos cargados
     * @param array $entidades Entidades preferidas a integrar
     * @param inm_ubicacion|inm_prospecto $modelo_preferido Modelo de ejecucion
     * @return array|stdClass
     * @version 2.133.1
     */
    final public function integra_ids_preferidos(stdClass $data, array $entidades,
                                            inm_ubicacion|inm_prospecto $modelo_preferido): array|stdClass
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
     * @param controlador_inm_ubicacion $controler
     * @param int $inm_tipo_ubicacion_id
     * @param array $keys_selects
     * @return array
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
     * @return array
     * @version 2.135.0
     */
    PUBLIC function keys_selects(controlador_inm_ubicacion $controler, stdClass $data_row): array
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
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro,
            key: 'dp_calle_pertenece_id', keys_selects: $keys_selects, id_selected: $data_row->dp_calle_pertenece_id,
            label: 'Calle', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    final public function keys_selects_base(controlador_inm_ubicacion $controler, stdClass $data_row){
        $keys_selects = $this->keys_selects(controler: $controler,data_row:  $data_row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }

        $keys_selects = $this->key_select_inm_tipo_ubicacion(controler: $controler,
            inm_tipo_ubicacion_id:  $data_row->inm_tipo_ubicacion_id,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_selects', data:  $keys_selects);
        }
        return $keys_selects;
    }
}
