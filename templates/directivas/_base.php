<?php
namespace gamboamartin\inmuebles\html;

use gamboamartin\system\html_controler;
use stdClass;

class _base extends html_controler{

    final public function apellido_materno(int $cols,  string $entidad, bool $disabled = false,
                                           string $name = 'apellido_materno', string $place_holder= 'Apellido Materno',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $class_css = array($entidad.'_apellido_materno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }
    final public function apellido_paterno(
        int $cols, string $entidad, bool $disabled = false, string $name = 'apellido_paterno',
        string $place_holder= 'Apellido Paterno', stdClass $row_upd = new stdClass(),
        bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_apellido_paterno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    final public function lada(int $cols,  string $entidad, bool $disabled = false, string $name = 'lada', string $place_holder= 'Lada',
                          stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function nombre(int $cols,  string $entidad, bool $disabled = false, string $name = 'nombre', string $place_holder= 'Nombre',
                            stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_nombre');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }
}
