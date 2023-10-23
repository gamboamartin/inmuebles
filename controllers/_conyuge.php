<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_nacionalidad;
use gamboamartin\inmuebles\models\inm_ocupacion;
use stdClass;

class _conyuge{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    final public function inputs_conyuge(controlador_inm_prospecto $controler){

        $conyuge = new stdClass();

        $nombre = $controler->html->input_text(cols: 12,disabled: false,name: 'conyuge[nombre]',place_holder: 'Nombre',
            row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $nombre);
        }

        $conyuge->nombre = $nombre;

        $apellido_paterno = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[apellido_paterno]',
            place_holder: 'Apellido Pat', row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_paterno);
        }

        $conyuge->apellido_paterno = $apellido_paterno;

        $apellido_materno = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[apellido_materno]',
            place_holder: 'Apellido Mat', row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_paterno);
        }

        $conyuge->apellido_materno = $apellido_materno;

        $curp = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[curp]',place_holder: 'CURP',
            row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $curp);
        }

        $conyuge->curp = $curp;

        $rfc = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[rfc]',place_holder: 'RFC',
            row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $rfc);
        }

        $conyuge->rfc = $rfc;

        $telefono_casa = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[telefono_casa]',
            place_holder: 'Tel Casa', row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $telefono_casa);
        }

        $conyuge->telefono_casa = $telefono_casa;

        $telefono_celular = $controler->html->input_text(cols: 6,disabled: false,name: 'conyuge[telefono_celular]',
            place_holder: 'Cel', row_upd: new stdClass(),value_vacio: false, required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $telefono_celular);
        }

        $conyuge->telefono_celular = $telefono_celular;

        $modelo = new dp_estado(link: $controler->link);
        $dp_estado_id = $controler->html->select_catalogo(cols: 6,con_registros:  true,id_selected:  -1,
            modelo:  $modelo, name: 'conyuge[dp_estado_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_estado_id);
        }

        $conyuge->dp_estado_id = $dp_estado_id;

        $modelo = new dp_municipio(link: $controler->link);
        $dp_municipio_id = $controler->html->select_catalogo(cols: 6,con_registros:  true,id_selected:  -1,
            modelo:  $modelo, name: 'conyuge[dp_municipio_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_municipio_id);
        }

        $conyuge->dp_municipio_id = $dp_municipio_id;

        $modelo = new inm_nacionalidad(link: $controler->link);
        $inm_nacionalidad_id = $controler->html->select_catalogo(cols: 6,con_registros:  true,id_selected:  -1,
            modelo:  $modelo, name: 'conyuge[inm_nacionalidad_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_nacionalidad_id);
        }

        $conyuge->inm_nacionalidad_id = $inm_nacionalidad_id;

        $modelo = new inm_ocupacion(link: $controler->link);
        $inm_ocupacion_id = $controler->html->select_catalogo(cols: 12,con_registros:  true,id_selected:  -1,
            modelo:  $modelo, name: 'conyuge[inm_ocupacion_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_ocupacion_id);
        }

        $conyuge->inm_ocupacion_id = $inm_ocupacion_id;

        $fecha_nacimiento = $controler->html->input_fecha(cols: 6,row_upd:  new stdClass(),
            value_vacio:  false, name: 'conyuge[fecha_nacimiento]', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener fecha_nacimiento',data:  $fecha_nacimiento);
        }

        $conyuge->fecha_nacimiento = $fecha_nacimiento;

        return $conyuge;
    }
    
}
