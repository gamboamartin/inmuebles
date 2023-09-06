<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class inm_co_acreditado_html extends html_controler {

    final public function apellido_materno(int $cols, bool $disabled = false, string $name = 'apellido_materno', string $place_holder= 'Apellido Materno',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }
    final public function apellido_paterno(int $cols, bool $disabled = false, string $name = 'apellido_paterno', string $place_holder= 'Apellido Paterno',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    final public function celular(int $cols, bool $disabled = false, string $name = 'celular', string $place_holder= 'Celular',
                                 stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['telefono_mx_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }
    final public function curp(int $cols, bool $disabled = false, string $name = 'curp', string $place_holder= 'CURP',
                              stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['curp_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function lada(int $cols, bool $disabled = false, string $name = 'lada', string $place_holder= 'Lada',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function nombre(int $cols, bool $disabled = false, string $name = 'nombre', string $place_holder= 'Nombre',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }
    final public function nss(int $cols, bool $disabled = false, string $name = 'nss', string $place_holder= 'NSS',
                              stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['nss_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function numero(int $cols, bool $disabled = false, string $name = 'numero', string $place_holder= 'Numero',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['tel_sin_lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function rfc(int $cols, bool $disabled = false, string $name = 'rfc', string $place_holder= 'RFC',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['rfc_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }
    final public function select_inm_co_acreditado_id(int $cols, bool $con_registros, int $id_selected, PDO $link, array $columns_ds=array(),
                                      bool $disabled = false, array $filtro = array()): array|string
    {
        $modelo = new inm_co_acreditado(link: $link);

        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, filtro: $filtro, label: 'Co Acreditado',
            required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
