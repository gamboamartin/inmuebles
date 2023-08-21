<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_estado_civil;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class inm_estado_civil_html extends html_controler {

    public function select_inm_producto_infonavit_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                      bool $disabled = false, array $filtro = array()): array|string
    {
        $modelo = new inm_estado_civil(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, filtro: $filtro, label: 'Estado Civil', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
