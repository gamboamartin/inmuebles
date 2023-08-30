<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use stdClass;

class _inm_comprador{
    final public function row_upd_montos(controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->descuento_pension_alimenticia_dh = 0;
        $controler->row_upd->monto_credito_solicitado_dh = 0;
        $controler->row_upd->descuento_pension_alimenticia_fc = 0;
        $controler->row_upd->monto_ahorro_voluntario = 0;
        return $controler->row_upd;
    }
}
