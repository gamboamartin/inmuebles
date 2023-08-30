<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use stdClass;

class _inm_comprador{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }


    final public function button(string $accion, controlador_inm_comprador $controler, string $etiqueta, int $indice,
                                 int $inm_doc_comprador_id, array $inm_conf_docs_comprador, array $params = array(),
                                 string $style = 'success', string $target = ''): array
    {
        $button = $controler->html->button_href(accion: $accion, etiqueta: $etiqueta, registro_id: $inm_doc_comprador_id,
            seccion: 'inm_doc_comprador', style: $style, params: $params, target: $target);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }
        $inm_conf_docs_comprador[$indice][$accion] = $button;
        return $inm_conf_docs_comprador;
    }
    final public function keys_selects(controlador_inm_comprador $controler){
        $row_upd = $this->row_upd_base(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }

        $keys_selects = (new _keys_selects())->init(controler: $controler,row_upd: $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
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

    private function row_upd_base(controlador_inm_comprador $controler){
        $row_upd = $this->row_upd_montos(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }

        $row_upd = $this->row_upd_ids(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }
        return $controler->row_upd;
    }

    private function row_upd_ids(controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->inm_producto_infonavit_id = 1;
        $controler->row_upd->inm_attr_tipo_credito_id = 6;
        $controler->row_upd->inm_destino_credito_id = 1;
        return $controler->row_upd;
    }
    private function row_upd_montos(controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->descuento_pension_alimenticia_dh = 0;
        $controler->row_upd->monto_credito_solicitado_dh = 0;
        $controler->row_upd->descuento_pension_alimenticia_fc = 0;
        $controler->row_upd->monto_ahorro_voluntario = 0;
        return $controler->row_upd;
    }



}
