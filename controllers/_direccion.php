<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_pais;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_parentesco;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use stdClass;

class _direccion{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    final public function inputs_direccion(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controler){

        $direccion = new stdClass();
        $row_upd = new stdClass();
        $row_upd->cp = '';
        $row_upd->colonia = '';
        $row_upd->calle = '';
        $row_upd->texto_exterior = '';
        $row_upd->texto_interior = '';
        $row_upd->dp_pais_id = -1;
        $row_upd->dp_estado_id = -1;
        $row_upd->dp_municipio_id = -1;

        $cp = $controler->html->input_text(cols: 6, disabled: false, name: 'direccion[cp]',
            place_holder: 'CP', row_upd: $row_upd, value_vacio: false, class_css: array('direccion_cp'),
            required: false, value: $row_upd->cp);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $cp);
        }

        $direccion->cp = $cp;

        $colonia = $controler->html->input_text(cols: 12, disabled: false,
            name: 'direccion[colonia]', place_holder: 'Colonia', row_upd: $row_upd, value_vacio: false,
            class_css: array('direccion_colonia'), required: false, value: $row_upd->colonia);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $colonia);
        }

        $direccion->colonia = $colonia;

        $calle = $controler->html->input_text(cols: 12, disabled: false,
            name: 'direccion[calle]', place_holder: 'Calle', row_upd: $row_upd, value_vacio: false,
            class_css: array('direccion_calle'), required: false, value: $row_upd->calle);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $calle);
        }

        $direccion->calle = $calle;

        $texto_exterior = $controler->html->input_text(cols: 6, disabled: false,
            name: 'direccion[texto_exterior]', place_holder: 'Num. Exterior', row_upd: $row_upd, value_vacio: false,
            class_css: array('direccion_texto_exterior'), required: false, value: $row_upd->texto_exterior);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $texto_exterior);
        }

        $direccion->texto_exterior = $texto_exterior;

        $texto_interior = $controler->html->input_text(cols: 6, disabled: false,
            name: 'direccion[texto_interior]', place_holder: 'Num. Interior', row_upd: $row_upd, value_vacio: false,
            class_css: array('direccion_texto_interior'), required: false, value: $row_upd->texto_interior);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $texto_interior);
        }

        $direccion->texto_interior = $texto_interior;

        $modelo = new dp_pais(link: $controler->link);
        $dp_pais_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->dp_pais_id, modelo: $modelo, columns_ds: array('dp_pais_descripcion'),
            id_css: 'direccion_dp_pais_id', label: 'Pais', name: 'direccion[dp_pais_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_pais_id);
        }

        $direccion->dp_pais_id = $dp_pais_id;

        $modelo = new dp_estado(link: $controler->link);
        $dp_estado_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->dp_estado_id, modelo: $modelo, columns_ds: array('dp_estado_descripcion'),
            id_css: 'direccion_dp_estado_id', label: 'Estado', name: 'direccion[dp_estado_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_estado_id);
        }

        $direccion->dp_estado_id = $dp_estado_id;

        $modelo = new dp_municipio(link: $controler->link);
        $dp_municipio_id = $controler->html->select_catalogo(cols: 6, con_registros: false,
            id_selected: $row_upd->dp_municipio_id, modelo: $modelo, columns_ds: array('dp_municipio_descripcion'),
            id_css: 'direccion_dp_municipio_id', label: 'Municipio', name: 'direccion[dp_municipio_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_municipio_id);
        }

        $direccion->dp_municipio_id = $dp_municipio_id;

        return $direccion;
    }
    
}
