<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_concepto;
use gamboamartin\system\html_controler;
use PDO;

class inm_concepto_html extends html_controler {

    /**
     * @param int $cols
     * @param bool $con_registros
     * @param int $id_selected
     * @param PDO $link
     * @param bool $disabled
     * @param array $filtro
     * @return array|string
     */
    public function select_inm_concepto_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                      bool $disabled = false, array $filtro = array()): array|string
    {
        $modelo = new inm_concepto(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, disabled: $disabled, filtro: $filtro, label: 'Concepto', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
