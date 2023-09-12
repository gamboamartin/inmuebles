<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_referencia;
use PDO;

class inm_referencia_html extends _base {

    public function select_inm_referencia_id(int $cols, bool $con_registros, int $id_selected, PDO $link, array $columns_ds=array(),
                                      bool $disabled = false, array $filtro = array()): array|string
    {
        $modelo = new inm_referencia(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, filtro: $filtro, label: 'Referencia',
            required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
