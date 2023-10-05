<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\system\html_controler;
use PDO;

class inm_comprador_html extends html_controler {


    /**
     * Genera un select de tipo comprador
     * @param int $cols Columnas css
     * @param bool $con_registros Si con registros anexa options
     * @param int $id_selected Option selected id
     * @param PDO $link Conexion a la base de datos
     * @param array $columns_ds Columnas a mostrar en el option
     * @param bool $disabled Si disabled deja vacio disabled el select
     * @param array $filtro Filtro de datos para options
     * @return array|string
     * @version 2.148.0
     *
     */
    final public function select_inm_comprador_id(int $cols, bool $con_registros, int $id_selected, PDO $link,
                                                  array $columns_ds=array(), bool $disabled = false,
                                                  array $filtro = array()): array|string
    {
        $modelo = new inm_comprador(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, filtro: $filtro,
            label: 'Comprador de vivienda', required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
