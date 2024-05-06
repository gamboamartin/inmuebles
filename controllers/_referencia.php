<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\direccion_postal\models\dp_colonia_postal;
use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_parentesco;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use stdClass;

class _referencia{
    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    final public function inputs_referencia(controlador_inm_prospecto $controler){

        $referencia = new stdClass();
        $row_upd = new stdClass();
        $row_upd->nombre = '';
        $row_upd->apellido_paterno = '';
        $row_upd->apellido_materno = '';
        $row_upd->inm_parentesco_id = -1;
        $row_upd->dp_estado_id = -1;
        $row_upd->dp_municipio_id = -1;
        $row_upd->dp_colonia_postal_id = -1;
        $row_upd->dp_cp_id = -1;
        $row_upd->lada = '';
        $row_upd->numero = '';
        $row_upd->celular = '';
        $row_upd->numero_dom = '';

        $nombre = $controler->html->input_text(cols: 12, disabled: false, name: 'referencia[nombre]', place_holder: 'Nombre',
            row_upd: $row_upd, value_vacio: false, class_css: array('referencia_nombre'), required: false, value: $row_upd->nombre);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $nombre);
        }

        $referencia->nombre = $nombre;

        $apellido_paterno = $controler->html->input_text(cols: 6, disabled: false, name: 'referencia[apellido_paterno]',
            place_holder: 'Apellido Pat', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_apellido_paterno'),
            required: false, value: $row_upd->apellido_paterno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_paterno);
        }

        $referencia->apellido_paterno = $apellido_paterno;

        $apellido_materno = $controler->html->input_text(cols: 6, disabled: false, name: 'referencia[apellido_materno]',
            place_holder: 'Apellido Mat', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_apellido_materno'),
            required: false, value: $row_upd->apellido_materno);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $apellido_materno);
        }

        $referencia->apellido_materno = $apellido_materno;

        $lada = $controler->html->input_text(cols: 6, disabled: false, name: 'referencia[lada]',
            place_holder: 'Lada', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_lada'),
            required: false, value: $row_upd->lada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $lada);
        }

        $referencia->lada = $lada;

        $numero = $controler->html->input_text(cols: 6, disabled: false, name: 'referencia[numero]',
            place_holder: 'Numero', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_numero'),
            required: false, value: $row_upd->numero);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $numero);
        }

        $referencia->numero = $numero;

        $celular = $controler->html->input_text(cols: 12, disabled: false, name: 'referencia[celular]',
            place_holder: 'Celular', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_celular'),
            required: false, value: $row_upd->celular);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $celular);
        }

        $referencia->celular = $celular;

        $numero_dom = $controler->html->input_text(cols: 12, disabled: false, name: 'referencia[numero_dom]',
            place_holder: 'Dom', row_upd: $row_upd, value_vacio: false, class_css: array('referencia_numero_dom'),
            required: false, value: $row_upd->numero_dom);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $numero_dom);
        }

        $referencia->numero_dom = $numero_dom;


        $modelo = new inm_parentesco(link: $controler->link);
        $inm_parentesco_id = $controler->html->select_catalogo(cols: 12, con_registros: true,
            id_selected: $row_upd->inm_parentesco_id, modelo: $modelo,class_css: array('referencia_inm_parentesco_id'),
            label: 'Parentesco', name: 'referencia[inm_parentesco_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $inm_parentesco_id);
        }

        $referencia->inm_parentesco_id = $inm_parentesco_id;


        $modelo = new dp_estado(link: $controler->link);
        $dp_estado_id = $controler->html->select_catalogo(cols: 6, con_registros: true,
            id_selected: $row_upd->dp_estado_id, modelo: $modelo, columns_ds: array('dp_estado_descripcion'),
            id_css: 'referencia_dp_estado_id', label: 'Estado', name: 'referencia[dp_estado_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_estado_id);
        }

        $referencia->dp_estado_id = $dp_estado_id;

        $modelo = new dp_municipio(link: $controler->link);
        $dp_municipio_id = $controler->html->select_catalogo(cols: 6, con_registros: false,
            id_selected: $row_upd->dp_municipio_id, modelo: $modelo, columns_ds: array('dp_municipio_descripcion'),
            id_css: 'referencia_dp_municipio_id', label: 'Municipio', name: 'referencia[dp_municipio_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_municipio_id);
        }

        $referencia->dp_municipio_id = $dp_municipio_id;

        $modelo = new dp_cp(link: $controler->link);
        $dp_cp_id = $controler->html->select_catalogo(cols: 6, con_registros: false,
            id_selected: $row_upd->dp_cp_id, modelo: $modelo, columns_ds: array('dp_cp_descripcion'),
            id_css: 'referencia_dp_cp_id', label: 'CP', name: 'referencia[dp_cp_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_cp_id);
        }

        $referencia->dp_cp_id = $dp_cp_id;

        $modelo = new dp_colonia_postal(link: $controler->link);
        $dp_colonia_postal_id = $controler->html->select_catalogo(cols: 6, con_registros: false,
            id_selected: $row_upd->dp_colonia_postal_id, modelo: $modelo, columns_ds: array('dp_colonia_descripcion'),
            id_css: 'referencia_dp_colonia_postal_id', label: 'Colonia', name: 'referencia[dp_colonia_postal_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_colonia_postal_id);
        }

        $referencia->dp_colonia_postal_id = $dp_colonia_postal_id;


        $modelo = new dp_calle_pertenece(link: $controler->link);
        $dp_calle_pertenece_id = $controler->html->select_catalogo(cols: 12, con_registros: false,
            id_selected: $row_upd->inm_parentesco_id, modelo: $modelo,class_css: array('referencia_dp_calle_pertenece_id'),
            columns_ds: array('dp_calle_descripcion'), id_css: 'referencia_dp_calle_pertenece_id', label: 'Calle',
            name: 'referencia[dp_calle_pertenece_id]');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_calle_pertenece_id);
        }

        $referencia->dp_calle_pertenece_id = $dp_calle_pertenece_id;


        return $referencia;
    }
    
}
