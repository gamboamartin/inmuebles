<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\models\inm_ubicacion_etapa;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class inm_ubicacion_etapa_html extends html_controler {

    public function select_inm_ubicacion_etapa_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                            array $columns_ds = array(), bool $disabled = false,
                                            array $filtro = array()): array|string
    {
        $modelo = new inm_ubicacion_etapa(link: $link);


        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, filtro: $filtro, label: 'Ubicacion Etapa',
            required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
