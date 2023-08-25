<?php

namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;
use stdClass;

class _dps_init{

    private errores  $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * Inicializa los elementos de domicilio
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass
     * @version 1.46.1
     */
    private function dps_init_ids(stdClass $row_upd): stdClass
    {
        if(!isset($row_upd->dp_pais_id)){
            $row_upd->dp_pais_id = 151;
        }
        if(!isset($row_upd->dp_estado_id)){
            $row_upd->dp_estado_id = 14;
        }
        if(!isset($row_upd->dp_municipio_id)){
            $row_upd->dp_municipio_id = -1;
        }
        if(!isset($row_upd->dp_cp_id)){
            $row_upd->dp_cp_id = -1;
        }
        if(!isset($row_upd->dp_colonia_postal_id)){
            $row_upd->dp_colonia_postal_id = -1;
        }
        if(!isset($row_upd->dp_calle_pertenece_id)){
            $row_upd->dp_calle_pertenece_id = -1;
        }
        return $row_upd;
    }

    /**
     * Integra un key select basada en la descripcion para descripcion select
     * @param _ctl_base $controler Controlador en ejecucion
     * @param string $entidad Entidad para integracion de datos
     * @param array $keys_selects Parametros previamente cargados
     * @param string $label Etiqueta a mostrar en select
     * @param stdClass $row_upd Registro en proceso
     * @param array $filtro Filtro para registros a mostrar en options
     * @return array
     */
    private function key_con_descripcion(_ctl_base $controler, string $entidad, array $keys_selects, string $label,
                                         stdClass $row_upd, array $filtro = array()): array
    {
        $key_ds = $entidad.'_descripcion';
        $key_id = $entidad.'_id';
        $columns_ds = array($key_ds);

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: $key_id,
            keys_selects: $keys_selects, id_selected: $row_upd->$key_id, label: $label, columns_ds : $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function ks_dp(_ctl_base $controler, array $keys_selects, stdClass $row_upd){

        $row_upd = $this->dps_init_ids(row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar row_upd',data:  $row_upd);
        }

        $keys_selects = $this->key_con_descripcion(controler: $controler,entidad: 'dp_pais',
            keys_selects:  $keys_selects,label: 'Pais',row_upd:  $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $filtro = array();
        $filtro['dp_pais.id'] = $row_upd->dp_pais_id;

        $keys_selects = $this->key_con_descripcion(controler: $controler,entidad: 'dp_estado',
            keys_selects:  $keys_selects,label: 'Estado',row_upd:  $row_upd, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $filtro = array();
        $filtro['dp_estado.id'] = $row_upd->dp_estado_id;

        $keys_selects = $this->key_con_descripcion(controler: $controler,entidad: 'dp_municipio',
            keys_selects:  $keys_selects,label: 'Municipio',row_upd:  $row_upd, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $keys_selects = $this->key_con_descripcion(controler: $controler,entidad: 'dp_cp',
            keys_selects:  $keys_selects,label: 'CP',row_upd:  $row_upd, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_colonia_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_colonia_postal_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_colonia_postal_id, label: 'Colonia', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_calle_pertenece_id',
            keys_selects: $keys_selects, id_selected: $row_upd->dp_calle_pertenece_id, label: 'Calle', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }
}