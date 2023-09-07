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


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }
    private function apellido_paterno(int $cols, bool $disabled = false, string $name = 'apellido_paterno', string $place_holder= 'Apellido Paterno',
                               stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

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

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    private function extension_nep(int $cols, bool $disabled = false, string $name = 'extension_nep', string $place_holder= 'Extension',
                                     stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    final public function inputs(){
        $inputs = new stdClass();
        $inm_co_acreditado_nss = $this->nss(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_nss);
        }

        $inputs->nss = $inm_co_acreditado_nss;

        $inm_co_acreditado_curp = $this->curp(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_curp);
        }

        $inputs->curp = $inm_co_acreditado_curp;

        $inm_co_acreditado_rfc = $this->rfc(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_rfc);
        }

        $inputs->rfc = $inm_co_acreditado_rfc;

        $inm_co_acreditado_apellido_paterno = $this->apellido_paterno(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_apellido_paterno);
        }

        $inputs->apellido_paterno = $inm_co_acreditado_apellido_paterno;

        $inm_co_acreditado_apellido_materno = $this->apellido_materno(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_apellido_materno);
        }

        $inputs->apellido_materno = $inm_co_acreditado_apellido_materno;

        $inm_co_acreditado_nombre = $this->nombre(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_nombre);
        }

        $inputs->nombre = $inm_co_acreditado_nombre;

        $inm_co_acreditado_lada = $this->lada(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_lada);
        }

        $inputs->lada = $inm_co_acreditado_lada;

        $inm_co_acreditado_numero = $this->numero(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_numero);
        }

        $inputs->numero = $inm_co_acreditado_numero;

        $inm_co_acreditado_celular = $this->celular(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_celular);
        }

        $inputs->celular = $inm_co_acreditado_celular;

        $inm_co_acreditado_correo = $this->correo(cols: 6);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_correo);
        }

        $inputs->correo = $inm_co_acreditado_correo;

        $inm_co_acreditado_nombre_empresa_patron = $this->nombre_empresa_patron(cols: 12);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_nombre_empresa_patron);
        }

        $inputs->nombre_empresa_patron = $inm_co_acreditado_nombre_empresa_patron;

        $inm_co_acreditado_nrp = $this->nrp(cols: 12);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_nrp);
        }

        $inputs->nrp = $inm_co_acreditado_nrp;

        $inm_co_acreditado_lada_nep = $this->lada_nep(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_lada_nep);
        }

        $inputs->lada_nep = $inm_co_acreditado_lada_nep;

        $inm_co_acreditado_numero_nep = $this->numero_nep(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_numero_nep);
        }

        $inputs->numero_nep = $inm_co_acreditado_numero_nep;

        $inm_co_acreditado_extension_nep = $this->extension_nep(cols: 4);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $inm_co_acreditado_extension_nep);
        }

        $inputs->extension_nep = $inm_co_acreditado_extension_nep;
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


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    private function nombre_empresa_patron(int $cols, bool $disabled = false, string $name = 'nombre_empresa_patron',
                                                string $place_holder= 'Nombre Empresa Patron',
                                                stdClass $row_upd = new stdClass(),
                                                bool $value_vacio = false): array|string
    {


        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }

    private function nrp(int $cols, bool $disabled = false, string $name = 'nrp',
                                                string $place_holder= 'NRP',
                                                stdClass $row_upd = new stdClass(),
                                                bool $value_vacio = false): array|string
    {

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio);

    }
    private function nss(int $cols, bool $disabled = false, string $name = 'nss', string $place_holder= 'NSS',
                              stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['nss_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

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

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

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
