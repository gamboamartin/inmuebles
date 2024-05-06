<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_parentesco;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use stdClass;

class _beneficiario{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    final public function inputs_beneficiario(controlador_inm_prospecto $controler){

        $beneficiario = new stdClass();
        $row_upd = new stdClass();
        $row_upd->nombre = '';
        $row_upd->apellido_paterno = '';
        $row_upd->apellido_materno = '';
        $row_upd->inm_parentesco_id = -1;

        $nombre = $controler->html->input_text(cols: 12, disabled: false, name: 'beneficiario[nombre]',
            place_holder: 'Nombre', row_upd: $row_upd, value_vacio: false, class_css: array('beneficiario_nombre'),
            required: false, value: $row_upd->nombre);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $nombre);
        }

        $beneficiario->nombre = $nombre;

        $apellido_paterno = $controler->html->input_text(cols: 6, disabled: false,
            name: 'beneficiario[apellido_paterno]', place_holder: 'Apellido Pat', row_upd: $row_upd, value_vacio: false,
            class_css: array('beneficiario_apellido_paterno'), required: false, value: $row_upd->apellido_paterno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_paterno);
        }

        $beneficiario->apellido_paterno = $apellido_paterno;

        $apellido_materno = $controler->html->input_text(cols: 6, disabled: false,
            name: 'beneficiario[apellido_materno]', place_holder: 'Apellido Mat', row_upd: $row_upd, value_vacio: false,
            class_css: array('beneficiario_apellido_materno'), required: false, value: $row_upd->apellido_materno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_materno);
        }

        $beneficiario->apellido_materno = $apellido_materno;


        $modelo = new inm_parentesco(link: $controler->link);
        $inm_parentesco_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->inm_parentesco_id, modelo: $modelo,
            class_css: array('beneficiario_inm_parentesco_id'), label: 'Parentesco',
            name: 'beneficiario[inm_parentesco_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_parentesco_id);
        }

        $beneficiario->inm_parentesco_id = $inm_parentesco_id;

        $modelo = new inm_tipo_beneficiario(link: $controler->link);
        $inm_tipo_beneficiario_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->inm_parentesco_id, modelo: $modelo,
            class_css: array('beneficiario_inm_tipo_beneficiario_id'), label: 'Tipo de Beneficiario',
            name: 'beneficiario[inm_tipo_beneficiario_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_tipo_beneficiario_id);
        }

        $beneficiario->inm_tipo_beneficiario_id = $inm_tipo_beneficiario_id;


        return $beneficiario;
    }
    
}
