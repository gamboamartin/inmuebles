<?php
namespace gamboamartin\inmuebles\html;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto_ubicacion;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\system\html_controler;
use html\dp_calle_pertenece_html;
use html\dp_colonia_postal_html;
use html\dp_cp_html;
use html\dp_estado_html;
use html\dp_municipio_html;
use html\dp_pais_html;
use PDO;
use stdClass;

class _base extends html_controler{

    final protected function apellido_materno(int $cols,  string $entidad, bool $disabled = false,
                                              string $name = 'apellido_materno', string $place_holder= 'Apellido Materno',
                                              bool $required = true, stdClass $row_upd = new stdClass(),
                                              bool $value_vacio = false): array|string
    {

        $class_css = array($entidad.'_apellido_materno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, required: $required);

    }
    final protected function apellido_paterno(int $cols, string $entidad, bool $disabled = false,
                                              string $name = 'apellido_paterno',
                                              string $place_holder= 'Apellido Paterno', bool $required =true,
                                              stdClass $row_upd = new stdClass(),
                                              bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_apellido_paterno');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css,required: $required);

    }

    private function base_ref(int $indice,stdClass $inm_referencia, array $inm_referencia_data, PDO $link){


        $row_upd = new stdClass();
        foreach ($inm_referencia_data as $campo=>$value){
            $key_con_indice = $campo.'_'.$indice;
            $row_upd->$key_con_indice = $value;
        }

        $apellido_paterno = $this->apellido_paterno(cols: 6, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_apellido_paterno_'.$indice, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener apellido_paterno',data:  $apellido_paterno);
        }
        $inm_referencia->apellido_paterno = $apellido_paterno;


        $apellido_materno = $this->apellido_materno(cols: 6, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_apellido_materno_'.$indice, required: false, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener apellido_materno',data:  $apellido_materno);
        }
        $inm_referencia->apellido_materno = $apellido_materno;

        $nombre = $this->nombre(cols: 6, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_nombre_'.$indice, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener nombre',data:  $nombre);
        }
        $inm_referencia->nombre = $nombre;

        $lada = $this->lada(cols: 6, entidad: 'inm_referencia', disabled: true, name: 'inm_referencia_lada_'.$indice,
            row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener lada',data:  $lada);
        }
        $inm_referencia->lada = $lada;

        $numero = $this->numero(cols: 6, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_numero_'.$indice, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener numero',data:  $numero);
        }
        $inm_referencia->numero = $numero;

        $celular = $this->celular(cols: 6, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_celular_'.$indice, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener celular',data:  $celular);
        }
        $inm_referencia->celular = $celular;

        if(!isset($inm_referencia_data['dp_pais_id'])){
            $inm_referencia_data['dp_pais_id'] = 151;
        }
        $dp_pais_id = $this->dp_pais_id(cols: 6, disabled: true, id_selected: $inm_referencia_data['dp_pais_id'],
            indice: $indice, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_pais_id',data:  $dp_pais_id);
        }
        $inm_referencia->dp_pais_id = $dp_pais_id;

        if(!isset($inm_referencia_data['dp_estado_id'])){
            $inm_referencia_data['dp_estado_id'] = 14;
        }
        $filtro = array();
        $filtro['dp_pais.id'] = $inm_referencia_data['dp_pais_id'];
        $dp_estado_id = $this->dp_estado_id(cols: 6, disabled: true, filtro: $filtro,
            id_selected: $inm_referencia_data['dp_estado_id'], indice: $indice, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_estado_id',data:  $dp_estado_id);
        }

        $inm_referencia->dp_estado_id = $dp_estado_id;

        if(!isset($inm_referencia_data['dp_municipio_id'])){
            $inm_referencia_data['dp_municipio_id'] = -1;
        }
        $filtro = array();
        $filtro['dp_estado.id'] = $inm_referencia_data['dp_estado_id'];

        $dp_municipio_id = $this->dp_municipio_id(cols: 6, disabled: true,
            id_selected: $inm_referencia_data['dp_municipio_id'], filtro: $filtro, indice: $indice, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_municipio_id',data:  $dp_municipio_id);
        }
        $inm_referencia->dp_municipio_id = $dp_municipio_id;


        if(!isset($inm_referencia_data['dp_cp_id'])){
            $inm_referencia_data['dp_cp_id'] = -1;
        }
        $filtro = array();
        $filtro['dp_estado.id'] = $inm_referencia_data['dp_estado_id'];
        $dp_cp_id = $this->dp_cp_id(cols: 6, disabled: true, id_selected: $inm_referencia_data['dp_cp_id'],
            filtro: $filtro, indice: $indice, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_cp_id',data:  $dp_cp_id);
        }
        $inm_referencia->dp_cp_id = $dp_cp_id;

        if(!isset($inm_referencia_data['dp_colonia_postal_id'])){
            $inm_referencia_data['dp_colonia_postal_id'] = -1;
        }
        $filtro = array();
        $filtro['dp_estado.id'] = $inm_referencia_data['dp_estado_id'];

        $dp_colonia_postal_id = $this->dp_colonia_postal_id(cols: 6, disabled: true, filtro: $filtro,
            id_selected: $inm_referencia_data['dp_colonia_postal_id'], indice: $indice, link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_colonia_postal_id',data:  $dp_colonia_postal_id);
        }
        $inm_referencia->dp_colonia_postal_id = $dp_colonia_postal_id;

        if(!isset($inm_referencia_data['dp_calle_pertenece_id'])){
            $inm_referencia_data['dp_calle_pertenece_id'] = -1;
        }
        $filtro = array();
        $filtro['dp_estado.id'] = $inm_referencia_data['dp_estado_id'];
        $dp_calle_pertenece_id = $this->dp_calle_pertenece_id(cols: 6, disabled: true,
            filtro: $filtro, id_selected: $inm_referencia_data['dp_calle_pertenece_id'], indice: $indice, link: $link,
            required:true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece_id',data:  $dp_calle_pertenece_id);
        }
        $inm_referencia->dp_calle_pertenece_id = $dp_calle_pertenece_id;

        $numero_dom = $this->numero_dom(cols: 12, entidad: 'inm_referencia', disabled: true,
            name: 'inm_referencia_numero_dom_'.$indice, required: true, row_upd: $row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener numero_dom',data:  $numero_dom);
        }
        $inm_referencia->numero_dom = $numero_dom;

        return $inm_referencia;
    }

    final protected function celular(int $cols,  string $entidad, bool $disabled = false, string $name = 'celular',
                                     string $place_holder= 'Celular', bool $required = true,
                                     stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['telefono_mx_html'];
        $class_css = array($entidad.'_celular');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, regex: $regex, required: $required);

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

    private function dp_calle_pertenece_id(int $cols, bool $disabled, array $filtro, int $id_selected, int $indice,
                                           PDO $link, bool $required){
        $dp_calle_pertenece_id = (new dp_calle_pertenece_html(html: $this->html_base))->select_dp_calle_pertenece_id(
            cols: $cols, con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            filtro: $filtro, name: 'inm_referencia_dp_calle_pertenece_id_'.$indice, required: $required);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_calle_pertenece_id);
        }
        return $dp_calle_pertenece_id;
    }
    private function dp_colonia_postal_id(int $cols, bool $disabled, array $filtro, int $id_selected, int $indice,
                                          PDO $link){

        $dp_colonia_postal_id = (new dp_colonia_postal_html(html: $this->html_base))->select_dp_colonia_postal_id(
            cols: $cols, con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            filtro: $filtro, name: 'inm_referencia_dp_colonia_postal_id_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_colonia_postal_id);
        }
        return $dp_colonia_postal_id;
    }

    private function dp_cp_id(int $cols, bool $disabled, int $id_selected, array $filtro, int $indice, PDO $link){
        $dp_cp_id = (new dp_cp_html(html: $this->html_base))->select_dp_cp_id(cols: $cols,
            con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            filtro: $filtro, name: 'inm_referencia_dp_cp_id_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_cp_id);
        }
        return $dp_cp_id;
    }
    private function dp_estado_id(int $cols, bool $disabled, array $filtro, int $id_selected, int $indice, PDO $link){
        $dp_estado_id = (new dp_estado_html(html: $this->html_base))->select_dp_estado_id(cols: $cols,
            con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            filtro: $filtro, name: 'inm_referencia_dp_estado_id_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_estado_id);
        }
        return $dp_estado_id;
    }

    private function dp_municipio_id(int $cols, bool $disabled, int $id_selected, array $filtro, int $indice, PDO $link){
        $dp_municipio_id = (new dp_municipio_html(html: $this->html_base))->select_dp_municipio_id(cols: $cols,
            con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            filtro: $filtro, name: 'inm_referencia_dp_municipio_id_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_municipio_id);
        }
        return $dp_municipio_id;
    }
    private function dp_pais_id(int $cols, bool $disabled, int $id_selected, int $indice, PDO $link){
        $dp_pais_id = (new dp_pais_html(html: $this->html_base))->select_dp_pais_id(cols: $cols,
            con_registros: true, id_selected: $id_selected, link: $link, disabled: $disabled,
            name: 'inm_referencia_dp_pais_id_'.$indice);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar input',data:  $dp_pais_id);
        }
        return $dp_pais_id;
    }

    /**
     * Genera los encabezados para las vistas de prospeccion y clientes
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler
     * @param array $headers Conjunto de datos para ser mostrados en Frontend
     * @return array
     */
    final public function genera_headers(controlador_inm_comprador|controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controler,
                                         array $headers, array $acciones_headers = array()): array
    {
        $data = array();
        foreach ($headers as $n_apartado=>$tag_header){
            $header = $this->header_frontend(controler: $controler,n_apartado:  $n_apartado,tag_header:  $tag_header,
                acciones_headers: $acciones_headers);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar header',data:  $header);
            }

            $data[] = $header;
        }
        return $data;
    }

    /**
     * Genera un header para collapsibles
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler Controlador en ejecucion
     * @param int $n_apartado No de apartado en frontend
     * @param string $tag_header Tag a mostrar en div
     * @return array|stdClass
     * @version 2.313.2
     */
    private function header_frontend(controlador_inm_comprador|controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controler,
                                     int $n_apartado, string $tag_header, array $acciones_headers = array()): array|stdClass
    {
        $id_css_button = "collapse_a$n_apartado";
        $key_header = "apartado_$n_apartado";

        $header_apartado = $controler->html_entidad->header_collapsible(id_css_button: $id_css_button,
            style_button: 'primary', tag_button: 'Ver/Ocultar',tag_header:  $tag_header,
            acciones_headers: $acciones_headers, n_apartado: $n_apartado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar header',data:  $header_apartado);
        }

        $controler->header_frontend->$key_header = $header_apartado;
        return $controler->header_frontend;
    }

    /**
     * @return array
     */
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

        $data = $this->genera_headers(controler: $controler,headers:  $headers);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers',data:  $data);
        }


        return $data;
    }

    final public function inm_referencias(array $inm_referencias_data, PDO $link){

        if(!isset($inm_referencias_data[0])){
            $inm_referencias_data[0] = array();
        }
        if(!isset($inm_referencias_data[1])){
            $inm_referencias_data[1] = array();
        }

        $inm_referencias = array();

        $inm_referencia = new stdClass();

        $inm_referencia = $this->base_ref(indice: 1,inm_referencia:  $inm_referencia,
            inm_referencia_data: $inm_referencias_data[0],link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar referencia',data:  $inm_referencia);
        }

        $inm_referencias[0] = $inm_referencia;

        $inm_referencia = new stdClass();


        $inm_referencia = $this->base_ref(indice: 2, inm_referencia: $inm_referencia, inm_referencia_data: $inm_referencias_data[1], link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar referencia',data:  $inm_referencia);
        }

        $inm_referencias[1] = $inm_referencia;

        return $inm_referencias;
    }

    /**
     * Obtiene todos los datos de inputs alta
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|stdClass
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


        $sl_dp_estado_nacimiento_id = (new dp_estado_html(html: $controler->html_base))->select_dp_estado_id(
            cols: 6,con_registros:  true,id_selected:  101,link:  $controler->link, label: 'Estado Nac',
            name: 'dp_estado_nacimiento_id');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar sl_dp_estado_nacimiento_id',
                data:  $sl_dp_estado_nacimiento_id);
        }
        
        $filtro = array('dp_estado.id'=>101);
        $inputs->dp_estado_nacimiento_id = $sl_dp_estado_nacimiento_id;

        $sl_dp_municipio_nacimiento_id = (new dp_municipio_html(html: $controler->html_base))->select_dp_municipio_id(
            cols: 6, con_registros: true, id_selected: 2469, link: $controler->link, filtro: $filtro,
            label: 'Municipio Nac', name: 'dp_municipio_nacimiento_id');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar sl_dp_municipio_nacimiento_id',
                data:  $sl_dp_municipio_nacimiento_id);
        }

        $inputs->dp_municipio_nacimiento_id = $sl_dp_municipio_nacimiento_id;

        $fecha_nacimiento = $controler->html->input_fecha(cols: 12, row_upd: new stdClass(), value_vacio: false,
            name: 'fecha_nacimiento', place_holder: 'Fecha Nac', value: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al fecha_nacimiento input genera',
                data:  $fecha_nacimiento);
        }

        $inputs->fecha_nacimiento = $fecha_nacimiento;



        $data = new stdClass();
        $data->keys_selects = $keys_selects;
        $data->inputs = $inputs;
        $data->radios = $radios;

        return $data;

    }

    final protected function lada(int $cols,  string $entidad, bool $disabled = false, string $name = 'lada',
                                  string $place_holder= 'Lada',bool $required = true,
                                  stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['lada_html'];
        $class_css = array($entidad.'_lada');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, regex: $regex, required: $required);

    }

    final protected function nombre(int $cols,  string $entidad, bool $disabled = false, string $name = 'nombre',
                                    string $place_holder= 'Nombre', bool $required = true,
                                    stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {


        $class_css = array($entidad.'_nombre');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, required: $required);

    }

    final protected function numero(int $cols,  string $entidad, bool $disabled = false, string $name = 'numero',
                                    string $place_holder= 'Numero', bool $required = true,
                                    stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $regex = $this->validacion->patterns['tel_sin_lada_html'];
        $class_css = array($entidad.'_numero');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, regex: $regex, required: $required);

    }

    final protected function numero_dom(int $cols,  string $entidad, bool $disabled = false, string $name = 'numero_dom',
                                    string $place_holder= 'Numero Domicilio', bool $required = false,
                                    stdClass $row_upd = new stdClass(), bool $value_vacio = false): array|string
    {

        $class_css = array($entidad.'_numero_dom');

        return $this->input_text(cols: $cols, disabled: $disabled, name: $name, place_holder: $place_holder,
            row_upd: $row_upd, value_vacio: $value_vacio, class_css: $class_css, required: $required);

    }
}
