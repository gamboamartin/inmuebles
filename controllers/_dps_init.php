<?php

namespace gamboamartin\inmuebles\controllers;

use base\orm\modelo;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\system\_ctl_base;
use stdClass;

class _dps_init{

    private errores  $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * POR DOCUMENTAR EN WIKI
     * Este es un método privado llamado dps_init_ids.
     *
     * @param modelo $modelo Recibe una instancia del objeto 'modelo' como parámetro.
     * @param stdClass $row_upd Recibe una instancia de la clase estándar de PHP, que generalmente representa un
     *  objeto genérico.
     * @return stdClass|array Devuelve una instancia de la clase estándar de PHP o un array en caso de error.
     *
     * Este método se ocupa de inicializar los ID de las entidades 'dp_pais', 'dp_estado' y 'dp_municipio'.
     * Para cada una de estas entidades, busca el ID preferido usando la función 'id_preferido_detalle' del objeto 'modelo'.
     * Si hay algún error durante esta búsqueda, el método devuelve un error con un mensaje específico.
     *
     * Luego, para cada entidad, si no se ha establecido el ID de la entidad en el objeto '$row_upd' o si el ID de la entidad es -1,
     * asigna el ID preferido de la entidad al objeto '$row_upd'.
     *
     * Finalmente, devuelve el objeto '$row_upd' actualizado.
     *
     * En conclusión, este método se utiliza para inicializar o actualizar los ID de ciertos elementos en el sistema,
     * a partir de un objeto de modelo dado.
     * @version 3.8.0
     */
    private function dps_init_ids(modelo $modelo, stdClass $row_upd): stdClass|array
    {

        //print_r($row_upd);exit;
        $entidades_pref[] = 'dp_pais';
        $entidades_pref[] = 'dp_estado';
        $entidades_pref[] = 'dp_municipio';

        foreach ($entidades_pref as $entidad){
            $entidad_id = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id pref',data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            if(!isset($row_upd->$key_entidad_id) || (int)$row_upd->$key_entidad_id === -1) {
                $row_upd->$key_entidad_id = $entidad_id;
            }
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
     * @param array $columns_ds
     * @param array $filtro Filtro para registros a mostrar en options
     * @return array
     */
    private function key_con_descripcion(_ctl_base $controler, string $entidad, array $keys_selects, string $label,
                                         stdClass $row_upd, array $columns_ds = array(), array $filtro = array()): array
    {
        $entidad = trim($entidad);
        if($entidad === ''){
            return $this->error->error(mensaje: 'Error entidad vacia',data:  $entidad);
        }
        $key_ds = $entidad.'_descripcion';
        $key_id = $entidad.'_id';
        if(count($columns_ds) === 0) {
            $columns_ds = array($key_ds);
        }

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
     */
    final public function ks_dp(_ctl_base $controler, array $keys_selects, stdClass $row_upd): array
    {

        $modelo_cliente = new com_cliente(link: $controler->link);

        $modelo_inm_comprador = new inm_comprador(link: $controler->link);

        $row_upd = $this->dps_init_ids(modelo: $modelo_inm_comprador, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar row_upd',data:  $row_upd);
        }

       // print_r($row_upd);

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
        $keys_selects = $this->key_con_descripcion(controler: $controler, entidad: 'dp_colonia_postal',
            keys_selects: $keys_selects, label: 'Colonia', row_upd: $row_upd, columns_ds: $columns_ds, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $columns_ds = array('dp_calle_descripcion');
        $keys_selects = $this->key_con_descripcion(controler: $controler, entidad: 'dp_calle_pertenece',
            keys_selects: $keys_selects, label: 'Calle', row_upd: $row_upd, columns_ds: $columns_ds, filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }



        return $keys_selects;
    }
}