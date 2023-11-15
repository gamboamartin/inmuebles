<?php

namespace gamboamartin\inmuebles\controllers;

use base\orm\modelo;
use gamboamartin\comercial\models\com_cliente;
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
     * @param modelo $modelo Modelo de cliente
     * @param stdClass $row_upd Registro en proceso
     * @return stdClass|array
     * @version 1.46.1
     */
    private function dps_init_ids(modelo $modelo, stdClass $row_upd): stdClass|array
    {

        $entidades_pref[] = 'dp_pais';
        $entidades_pref[] = 'dp_estado';
        $entidades_pref[] = 'dp_municipio';
        $entidades_pref[] = 'dp_cp';
        $entidades_pref[] = 'dp_colonia_postal';
        $entidades_pref[] = 'dp_calle_pertenece';

        foreach ($entidades_pref as $entidad){
            $entidad_id = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id pref',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            $row_upd->$key_entidad_id = $entidad_id;
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
     * @version 1.53.1
     */
    private function key_con_descripcion(_ctl_base $controler, string $entidad, array $keys_selects, string $label,
                                         stdClass $row_upd, array $filtro = array()): array
    {
        $entidad = trim($entidad);
        if($entidad === ''){
            return $this->error->error(mensaje: 'Error entidad vacia',data:  $entidad);
        }
        $key_ds = $entidad.'_descripcion';
        $key_id = $entidad.'_id';
        $columns_ds = array($key_ds);

        if(!isset($row_upd->$key_id)){
            $row_upd->$key_id = -1;
        }

        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro, key: $key_id,
            keys_selects: $keys_selects, id_selected: $row_upd->$key_id, label: $label, columns_ds : $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Integra los keys para direcciones postales
     * @param _ctl_base $controler Controlador en ejecucion
     * @param array $keys_selects Key previos cargados
     * @param stdClass $row_upd Registro en proceso
     * @return array
     * @version 1.55.1
     */
    final public function ks_dp(_ctl_base $controler, array $keys_selects, stdClass $row_upd): array
    {

        $modelo_cliente = new com_cliente(link: $controler->link);

        $row_upd = $this->dps_init_ids(modelo: $modelo_cliente, row_upd: $row_upd);
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
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro,
            key: 'dp_colonia_postal_id', keys_selects: $keys_selects, id_selected: $row_upd->dp_colonia_postal_id,
            label: 'Colonia', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $keys_selects = $controler->key_select(cols:6, con_registros: true,filtro:  $filtro,
            key: 'dp_calle_pertenece_id', keys_selects: $keys_selects, id_selected: $row_upd->dp_calle_pertenece_id,
            label: 'Calle', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }
}