<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_upd_prospecto;
use gamboamartin\inmuebles\models\_upd_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_nacionalidad;
use gamboamartin\inmuebles\models\inm_ocupacion;
use gamboamartin\inmuebles\models\inm_prospecto;
use stdClass;

class _conyuge{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    final public function inputs_conyuge(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controler,
                                         string $class_upd){

        $conyuge = new stdClass();

        $existe_conyuge = false;
        if($controler->registro_id > 0) {

            $existe_conyuge = $controler->modelo->existe_conyuge(
                inm_prospecto_id: $controler->registro_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar si existe conyuge', data: $existe_conyuge);
            }
        }



        $row_upd = new stdClass();
        $row_upd->nombre = '';
        $row_upd->apellido_paterno = '';
        $row_upd->apellido_materno = '';
        $row_upd->fecha_nacimiento = '';
        $row_upd->curp = '';
        $row_upd->rfc = '';
        $row_upd->telefono_casa = '';
        $row_upd->telefono_celular = '';
        $row_upd->dp_estado_id = -1;
        $row_upd->dp_municipio_id = -1;
        $row_upd->inm_nacionalidad_id = -1;
        $row_upd->inm_ocupacion_id = -1;
        if($existe_conyuge){
            $rename = "gamboamartin\\inmuebles\\models\\".$class_upd;
            $class = new $rename();

            $row_upd = $class->inm_conyuge(columnas_en_bruto: true,
                inm_prospecto_id: $controler->registro_id, link: $controler->link, retorno_obj: true);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener datos de conyuge',data:  $row_upd);
            }
            $dp_municipio_data = (new dp_municipio(link: $controler->link))->registro(
                registro_id: $row_upd->dp_municipio_id, columnas_en_bruto: true, retorno_obj: true);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener datos  dp_municipio_data',data:  $dp_municipio_data);
            }
            $row_upd->dp_estado_id = $dp_municipio_data->dp_estado_id;

        }

        $nombre = $controler->html->input_text(cols: 12, disabled: false, name: 'conyuge[nombre]', place_holder: 'Nombre',
            row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_nombre'), required: false, value: $row_upd->nombre);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $nombre);
        }

        $conyuge->nombre = $nombre;

        $apellido_paterno = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[apellido_paterno]',
            place_holder: 'Apellido Pat', row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_apellido_paterno'),
            required: false, value: $row_upd->apellido_paterno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_paterno);
        }

        $conyuge->apellido_paterno = $apellido_paterno;

        $apellido_materno = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[apellido_materno]',
            place_holder: 'Apellido Mat', row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_apellido_materno'),
            required: false, value: $row_upd->apellido_materno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_materno);
        }

        $conyuge->apellido_materno = $apellido_materno;

        $curp = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[curp]', place_holder: 'CURP',
            row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_curp'), required: false, value: $row_upd->curp);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $curp);
        }

        $conyuge->curp = $curp;

        $rfc = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[rfc]', place_holder: 'RFC',
            row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_rfc'), required: false, value: $row_upd->rfc);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $rfc);
        }

        $conyuge->rfc = $rfc;

        $telefono_casa = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[telefono_casa]',
            place_holder: 'Tel Casa', row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_telefono_casa'), required: false, value: $row_upd->telefono_casa);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $telefono_casa);
        }

        $conyuge->telefono_casa = $telefono_casa;

        $telefono_celular = $controler->html->input_text(cols: 6, disabled: false, name: 'conyuge[telefono_celular]',
            place_holder: 'Cel', row_upd: $row_upd, value_vacio: false, class_css: array('conyuge_telefono_celular'), required: false, value: $row_upd->telefono_celular);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $telefono_celular);
        }

        $conyuge->telefono_celular = $telefono_celular;

        $modelo = new dp_estado(link: $controler->link);
        $dp_estado_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->dp_estado_id, modelo: $modelo, id_css: 'conyuge_dp_estado_id',
            label: 'Estado Nac', name: 'conyuge[dp_estado_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_estado_id);
        }

        $conyuge->dp_estado_id = $dp_estado_id;

        //print_r($dp_estado_id);exit;
        $modelo = new dp_municipio(link: $controler->link);
        $dp_municipio_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->dp_municipio_id, modelo: $modelo, filtro: array('dp_estado.id'=>$row_upd->dp_estado_id),
            id_css: 'conyuge_dp_municipio_id', label: 'Municipio Nac', name: 'conyuge[dp_municipio_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_municipio_id);
        }

        $conyuge->dp_municipio_id = $dp_municipio_id;

        $modelo = new inm_nacionalidad(link: $controler->link);
        $inm_nacionalidad_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->inm_nacionalidad_id, modelo: $modelo, label: 'Nacionalidad',
            name: 'conyuge[inm_nacionalidad_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_nacionalidad_id);
        }

        $conyuge->inm_nacionalidad_id = $inm_nacionalidad_id;

        $modelo = new inm_ocupacion(link: $controler->link);
        $inm_ocupacion_id = $controler->html->select_catalogo(cols: 12, con_registros: true,
            id_selected: $row_upd->inm_ocupacion_id, modelo: $modelo, label: 'Ocupacion',
            name: 'conyuge[inm_ocupacion_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_ocupacion_id);
        }

        $conyuge->inm_ocupacion_id = $inm_ocupacion_id;

        $fecha_nacimiento = $controler->html->input_fecha(cols: 6, row_upd: $row_upd,
            value_vacio: false, name: 'conyuge[fecha_nacimiento]', place_holder: 'Fecha Nac', required: false,
            value: $row_upd->fecha_nacimiento);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener fecha_nacimiento',data:  $fecha_nacimiento);
        }

        $conyuge->fecha_nacimiento = $fecha_nacimiento;

        return $conyuge;
    }
    
}
