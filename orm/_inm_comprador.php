<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use stdClass;

class _inm_comprador{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function radios(controlador_inm_comprador $controler){
        $es_segundo_credito = $controler->html->directivas->input_radio_doble(campo: 'es_segundo_credito',
            checked_default: 2,tag: 'Es Segundo Credito', val_1: 'SI',val_2: 'NO');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener es_segundo_credito',data:  $es_segundo_credito);
        }
        $controler->inputs->es_segundo_credito = $es_segundo_credito;

        $con_discapacidad = $controler->html->directivas->input_radio_doble(campo: 'con_discapacidad',
            checked_default: 1,tag: 'Con Discapacidad', val_1: 'NO',val_2: 'SI');


        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener con_discapacidad',data:  $con_discapacidad);
        }

        $controler->inputs->con_discapacidad = $con_discapacidad;

        return $controler->inputs;
    }
    final public function row_upd_montos(controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->descuento_pension_alimenticia_dh = 0;
        $controler->row_upd->monto_credito_solicitado_dh = 0;
        $controler->row_upd->descuento_pension_alimenticia_fc = 0;
        $controler->row_upd->monto_ahorro_voluntario = 0;
        return $controler->row_upd;
    }

}
