<?php

namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\errores\errores;
use gamboamartin\system\_ctl_base;

class _ctl_formato extends _ctl_base{
    /**
     * Obtiene los parametros para selectores de tipo texto
     * @param array $keys_selects Parametros previamente cargados
     * @param int $cols_descripcion Columnas para descripcion
     * @return array
     * @version 1.21.0
     */
     protected function key_selects_txt(array $keys_selects, int $cols_descripcion = 12): array
    {

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'descripcion', keys_selects:$keys_selects,
            place_holder: 'Descripcion');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'x', keys_selects:$keys_selects, place_holder: 'x');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'y', keys_selects:$keys_selects, place_holder: 'y');
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }
}
