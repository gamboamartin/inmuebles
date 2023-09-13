<?php
namespace gamboamartin\inmuebles\html;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\system\html_controler;
use stdClass;

class _base extends html_controler{

    final protected function apellido_materno(int $cols,  string $entidad, bool $disabled = false,
                                           string $name = 'apellido_materno', string $place_holder= 'Apellido Materno',
                                           stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $class_css = array($entidad.'_apellido_materno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }
    final protected function apellido_paterno(
        int $cols, string $entidad, bool $disabled = false, string $name = 'apellido_paterno',
        string $place_holder= 'Apellido Paterno', stdClass $row_upd = new stdClass(),
        bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_apellido_paterno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    final public function base_ref(int $indice,stdClass $inm_referencia){
        $apellido_paterno = $this->apellido_paterno(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_apellido_paterno_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener apellido_paterno',data:  $apellido_paterno);
        }
        $inm_referencia->apellido_paterno = $apellido_paterno;


        $apellido_materno = $this->apellido_materno(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_apellido_materno_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener apellido_materno',data:  $apellido_materno);
        }
        $inm_referencia->apellido_materno = $apellido_materno;

        $nombre = $this->nombre(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_nombre_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener nombre',data:  $nombre);
        }
        $inm_referencia->nombre = $nombre;

        $lada = $this->lada(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_lada_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener lada',data:  $lada);
        }
        $inm_referencia->lada = $lada;

        $numero = $this->numero(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_numero_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener numero',data:  $numero);
        }
        $inm_referencia->numero = $numero;

        $celular = $this->celular(cols: 6,entidad: 'inm_referencia',name: 'inm_referencia_celular_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener celular',data:  $celular);
        }
        $inm_referencia->celular = $celular;

        return $inm_referencia;
    }

    final protected function celular(int $cols,  string $entidad, bool $disabled = false, string $name = 'celular', string $place_holder= 'Celular',
                             stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['telefono_mx_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final public function data_front_alta(controlador_inm_comprador $controler){
        $inputs = $this->inputs_alta(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }


        $btn_collapse_all = $controler->html->button_para_java(id_css: 'collapse_all',style:  'primary',
            tag:  'Ver/Ocultar Todo');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al btn_collapse_all',data:  $btn_collapse_all);
        }

        $controler->buttons['btn_collapse_all'] = $btn_collapse_all;

        $headers = $this->headers_view(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers base',data:  $headers);
        }


        $data = new stdClass();
        $data->btn_collapse_all = $btn_collapse_all;
        $data->inputs = $inputs;
        $data->headers = $headers;
        return $data;
    }

    private function header_frontend(controlador_inm_comprador $controler,int $n_apartado, string $tag_header){
        $id_css_button = "collapse_a$n_apartado";
        $key_header = "apartado_$n_apartado";

        $header_apartado = $controler->html_entidad->header_collapsible(id_css_button: $id_css_button,
            style_button: 'primary', tag_button: 'Ver/Ocultar',tag_header:  $tag_header);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar header',data:  $header_apartado);
        }

        $controler->header_frontend->$key_header = $header_apartado;
        return $controler->header_frontend;
    }

    private function headers_base(): array
    {
        $headers['1'] = '1. CRÉDITO SOLICITADO';
        $headers['2'] = '2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO';
        $headers['3'] = '3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO';
        $headers['4'] = '4. DATOS DE LA EMPRESA O PATRÓN';
        $headers['5'] = '5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS';
        $headers['13'] = '13. DATOS FISCALES PARA FACTURACION';
        $headers['14'] = '14. CONTROL INTERNO';
        return $headers;
    }

    private function headers_view(controlador_inm_comprador $controler){
        $headers = $this->headers_base();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers base',data:  $headers);
        }

        $data = array();
        foreach ($headers as $n_apartado=>$tag_header){


            $header = $this->header_frontend(controler: $controler,n_apartado:  $n_apartado,tag_header:  $tag_header);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar header',data:  $header);
            }

            $data[] = $header;

        }
        return $data;
    }

    /**
     * Obtiene todos los datos de inputs alta
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 1.170.1
     */
    private function inputs_alta(controlador_inm_comprador $controler): array|stdClass
    {
        if(!is_object($controler->inputs)){
            return $this->error->error(mensaje: 'Error controlador->inputs debe se run objeto',
                data: $controler->inputs);
        }
        $keys_selects = (new _inm_comprador())->keys_selects(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $keys_selects);
        }

        $inputs = $controler->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inputs',data:  $inputs);
        }

        $radios = (new _inm_comprador())->radios(checked_default_cd: 1, checked_default_esc: 2, controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar radios',data:  $radios);
        }
        $data = new stdClass();
        $data->keys_selects = $keys_selects;
        $data->inputs = $inputs;
        $data->radios = $radios;

        return $data;

    }

    final protected function lada(int $cols,  string $entidad, bool $disabled = false, string $name = 'lada', string $place_holder= 'Lada',
                          stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }

    final protected function nombre(int $cols,  string $entidad, bool $disabled = false, string $name = 'nombre', string $place_holder= 'Nombre',
                            stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_nombre');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css);

    }

    final protected function numero(int $cols,  string $entidad, bool $disabled = false, string $name = 'numero', string $place_holder= 'Numero',
                            stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['tel_sin_lada_html'];

        return $this->input_text_required(cols: $cols,disabled:  $disabled,name:  $name,
            place_holder:  $place_holder,row_upd:  $row_upd,value_vacio:  $value_vacio,regex: $regex);

    }
}
