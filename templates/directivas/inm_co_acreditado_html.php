<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\system\html_controler;
use PDO;
use stdClass;

class inm_co_acreditado_html extends html_controler {

    private function apellido_materno(int $cols, bool $disabled = false, string $name = 'apellido_materno', string $place_holder= 'Apellido Materno',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $class_css = array('inm_co_acreditado_apellido_materno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }
    private function apellido_paterno(int $cols, bool $disabled = false, string $name = 'apellido_paterno', string $place_holder= 'Apellido Paterno',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        $class_css = array('inm_co_acreditado_apellido_paterno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    private function celular(int $cols, bool $disabled = false, string $name = 'celular', string $place_holder= 'Celular',
                                 stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['telefono_mx_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function correo(int $cols, bool $disabled = false, string $name = 'correo', string $place_holder= 'Correo',
                                  stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['correo_html_base'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }
    private function curp(int $cols, bool $disabled = false, string $name = 'curp', string $place_holder= 'CURP',
                              stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['curp_html'];
        $class_css = array('inm_co_acreditado_curp');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, regex: $regex);

    }

    private function extension_nep(int $cols, bool $disabled = false, string $name = 'extension_nep', string $place_holder= 'Extension',
                                     stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    private function init_campo(string $campo, array $data): array
    {
        if(!isset($data[$campo])){
            $data[$campo] = $campo;
        }
        return $data;
    }

    private function init_campos(array $campos, array $datas){
        foreach ($campos as $campo) {
            $datas = $this->init_campo(campo: $campo, data: $datas);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al inicializar datas', data: $datas);
            }
        }
        return $datas;

    }

    final public function inputs(bool $integra_prefijo = false,array $cols_css = array(), array $disableds = array(),
                                 array $names = array()): array|stdClass
    {

        $campos = array('apellido_materno','apellido_paterno','celular','correo','curp','extension_nep','lada',
            'lada_nep','nombre', 'nombre_empresa_patron','nrp','nss', 'numero','numero_nep','rfc');


        if(!isset($cols_css['apellido_materno'])){
            $cols_css['apellido_materno'] = 6;
        }
        if(!isset($cols_css['apellido_paterno'])){
            $cols_css['apellido_paterno'] = 6;
        }
        if(!isset($cols_css['celular'])){
            $cols_css['celular'] = 4;
        }
        if(!isset($cols_css['correo'])){
            $cols_css['correo'] = 6;
        }
        if(!isset($cols_css['curp'])){
            $cols_css['curp'] = 6;
        }
        if(!isset($cols_css['extension_nep'])){
            $cols_css['extension_nep'] = 4;
        }
        if(!isset($cols_css['lada'])){
            $cols_css['lada'] = 4;
        }
        if(!isset($cols_css['lada_nep'])){
            $cols_css['lada_nep'] = 4;
        }
        if(!isset($cols_css['nombre'])){
            $cols_css['nombre'] = 6;
        }
        if(!isset($cols_css['nombre_empresa_patron'])){
            $cols_css['nombre_empresa_patron'] = 12;
        }
        if(!isset($cols_css['nrp'])){
            $cols_css['nrp'] = 12;
        }
        if(!isset($cols_css['nss'])){
            $cols_css['nss'] = 6;
        }
        if(!isset($cols_css['numero'])){
            $cols_css['numero'] = 4;
        }
        if(!isset($cols_css['numero_nep'])){
            $cols_css['numero_nep'] = 4;
        }
        if(!isset($cols_css['rfc'])){
            $cols_css['rfc'] = 6;
        }


        $names = $this->init_campos(campos: $campos,datas:  $names);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar names',data:  $names);
        }
        $disableds = $this->init_campos(campos: $campos,datas:  $disableds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar disableds',data:  $disableds);
        }


        if($integra_prefijo){
            foreach ($names as $campo=>$name){
                $names[$campo] = 'inm_co_acreditado_'.$campo;
            }
        }


        $inputs = new stdClass();

        foreach ($campos as $campo){
            $input = $this->$campo(cols: $cols_css[$campo], disabled: $disableds[$campo], name: $names[$campo]);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar input',data:  $input);
            }
            $inputs->$campo = $input;
        }


        return $inputs;
    }


    private function lada(int $cols, bool $disabled = false, string $name = 'lada', string $place_holder= 'Lada',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function lada_nep(int $cols, bool $disabled = false, string $name = 'lada_nep', string $place_holder= 'Lada',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function nombre(int $cols, bool $disabled = false, string $name = 'nombre', string $place_holder= 'Nombre',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        $class_css = array('inm_co_acreditado_nombre');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    private function nombre_empresa_patron(int $cols, bool $disabled = false, string $name = 'nombre_empresa_patron',
                                                string $place_holder= 'Nombre Empresa Patron',
                                                stdClass $row_upd = new stdClass(),
                                                bool $value_vacio = false): array|string
    {


        $class_css = array('inm_co_acreditado_nombre_empresa_patron');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    private function nrp(int $cols, bool $disabled = false, string $name = 'nrp',
                                                string $place_holder= 'NRP',
                                                stdClass $row_upd = new stdClass(),
                                                bool $value_vacio = false): array|string
    {

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    /**
     * Integra un input de tipo nss
     * @param int $cols Columnas css
     * @param bool $disabled atributo disabled input
     * @param string $name Name input
     * @param string $place_holder Marca de agua mostrable en input
     * @param bool $required Atributo required
     * @param stdClass $row_upd Registro en proceso
     * @param bool $value_vacio Si vacio deja el input vacio
     * @return array|string
     */
    private function nss(int $cols, bool $disabled = false, string $name = 'nss', string $place_holder= 'NSS',
                         bool $required = true, stdClass $row_upd = new stdClass(),
                         bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['nss_html'];

        return $this->input_text(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex,required: $required);

    }

    private function numero(int $cols, bool $disabled = false, string $name = 'numero', string $place_holder= 'Numero',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['tel_sin_lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function numero_nep(int $cols, bool $disabled = false, string $name = 'numero_nep', string $place_holder= 'Numero',
                                 stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['tel_sin_lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function rfc(int $cols, bool $disabled = false, string $name = 'rfc', string $place_holder= 'RFC',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['rfc_html'];

        $class_css = array('inm_co_acreditado_rfc');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, regex: $regex);

    }

    /**
     * Genera un selector de tipo co acreditado
     * @param int $cols No de columnas css
     * @param bool $con_registros Si con registros integra registros en options
     * @param int $id_selected Selected id
     * @param PDO $link Conexion a la base de datos
     * @param array $columns_ds Columnas a mostrar en opciones
     * @param bool $disabled Atributo disabled
     * @param array $filtro Filtro de datos
     * @return array|string
     * @version 1.130.1
     */
    final public function select_inm_co_acreditado_id(int $cols, bool $con_registros, int $id_selected,
                                                      PDO $link, array $columns_ds=array(), bool $disabled = false,
                                                      array $filtro = array()): array|string
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
