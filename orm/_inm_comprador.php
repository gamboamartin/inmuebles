<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\banco\models\bn_cuenta;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_doctos;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\html\inm_co_acreditado_html;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _inm_comprador{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }


    private function button(string $accion, controlador_inm_comprador $controler, string $etiqueta, int $indice,
                                 int $inm_doc_comprador_id, array $inm_conf_docs_comprador, array $params = array(),
                                 string $style = 'success', string $target = ''): array
    {
        $button = $controler->html->button_href(accion: $accion, etiqueta: $etiqueta, registro_id: $inm_doc_comprador_id,
            seccion: 'inm_doc_comprador', style: $style, params: $params, target: $target);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }
        $inm_conf_docs_comprador[$indice][$accion] = $button;
        return $inm_conf_docs_comprador;
    }

    final public function button_del(controlador_inm_comprador $controler, int $indice, int $inm_comprador_id,
                                array $inm_conf_docs_comprador, array $inm_doc_comprador){
        $params = array('accion_retorno'=>'documentos','seccion_retorno'=>$controler->seccion,
            'id_retorno'=>$inm_comprador_id);

        $inm_conf_docs_comprador = (new _inm_comprador())->button(accion: 'elimina_bd', controler: $controler,
            etiqueta: 'Elimina', indice: $indice, inm_doc_comprador_id: $inm_doc_comprador['inm_doc_comprador_id'],
            inm_conf_docs_comprador: $inm_conf_docs_comprador, params: $params, style: 'danger');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }
        return $inm_conf_docs_comprador;
    }

    private function buttons(controlador_inm_comprador $controler, int $indice, array $inm_conf_docs_comprador,
                                  array $inm_doc_comprador){

        $inm_conf_docs_comprador = $this->button(accion: 'descarga', controler: $controler,
            etiqueta: 'Descarga', indice: $indice, inm_doc_comprador_id: $inm_doc_comprador['inm_doc_comprador_id'],
            inm_conf_docs_comprador: $inm_conf_docs_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }

        $inm_conf_docs_comprador = $this->button(accion: 'vista_previa', controler: $controler,
            etiqueta: 'Vista Previa', indice: $indice, inm_doc_comprador_id: $inm_doc_comprador['inm_doc_comprador_id'],
            inm_conf_docs_comprador: $inm_conf_docs_comprador, target: '_blank');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }

        $inm_conf_docs_comprador = $this->button(accion: 'descarga_zip', controler: $controler,
            etiqueta: 'ZIP', indice: $indice, inm_doc_comprador_id: $inm_doc_comprador['inm_doc_comprador_id'],
            inm_conf_docs_comprador: $inm_conf_docs_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }
        return $inm_conf_docs_comprador;
    }

    private function buttons_base(controlador_inm_comprador $controler, int $indice, int $inm_comprador_id,
                                  array $inm_conf_docs_comprador, array $inm_doc_comprador): array
    {
        $inm_conf_docs_comprador = $this->buttons(controler: $controler,indice:  $indice,
            inm_conf_docs_comprador:  $inm_conf_docs_comprador,inm_doc_comprador:  $inm_doc_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }

        $inm_conf_docs_comprador = $this->button_del(controler: $controler,indice:  $indice,
            inm_comprador_id:  $inm_comprador_id,inm_conf_docs_comprador:  $inm_conf_docs_comprador,
            inm_doc_comprador:  $inm_doc_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }

        return $inm_conf_docs_comprador;
    }

    /**
     * Integra los checkeds default para upd
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler Controlador en ejecucion
     * @return stdClass|array
     * @version 1.107.1
     */
    private function checkeds_default(controlador_inm_comprador|controlador_inm_prospecto $controler): stdClass|array
    {
        $keys = array('es_segundo_credito','con_discapacidad');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar row upd',data:  $valida);
        }
        $checked_default_esc = 1;
        if($controler->row_upd->es_segundo_credito === 'NO'){
            $checked_default_esc = 2;
        }

        $checked_default_cd = 2;
        if($controler->row_upd->con_discapacidad === 'NO'){
            $checked_default_cd = 1;
        }

        $data = new stdClass();
        $data->checked_default_esc = $checked_default_esc;
        $data->checked_default_cd = $checked_default_cd;

        return $data;

    }

    private function doc_existente(controlador_inm_comprador $controler, array $doc_tipo_documento, int $indice,
                                        array $inm_conf_docs_comprador, array $inm_doc_comprador){

        $existe = false;
        if($doc_tipo_documento['doc_tipo_documento_id'] === $inm_doc_comprador['doc_tipo_documento_id']){

            $existe = true;

            $inm_conf_docs_comprador = $this->buttons_base(
                controler: $controler,indice:  $indice,inm_comprador_id:  $controler->registro_id,
                inm_conf_docs_comprador:  $inm_conf_docs_comprador,inm_doc_comprador:  $inm_doc_comprador);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
            }
        }

        $data = new stdClass();
        $data->existe = $existe;
        $data->inm_conf_docs_comprador = $inm_conf_docs_comprador;
        return $data;
    }

    /**
     * Genera un input select para co acreditado
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|string
     * @version 1.132.1
     */
    final public function inm_co_acreditado_id_input(controlador_inm_comprador $controler): array|string
    {
        $columns_ds = array('inm_co_acreditado_nss','inm_co_acreditado_curp','inm_co_acreditado_nombre',
            'inm_co_acreditado_apellido_paterno','inm_co_acreditado_apellido_materno');

        $inm_co_acreditado_id = (new inm_co_acreditado_html(html: $controler->html_base))->select_inm_co_acreditado_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $controler->link, columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inm_co_acreditado_id',data:  $inm_co_acreditado_id);
        }
        return $inm_co_acreditado_id;
    }

    /**
     * Obtiene los co acreaditados de un cliente
     * @param int $inm_comprador_id Comprador id
     * @param PDO $link Conexion a la base de datos
     * @return array
     * @version 1.134.1
     */
    final public function inm_co_acreditados(int $inm_comprador_id, PDO $link): array
    {
        if($inm_comprador_id<= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $filtro = array();
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_rel_co_acred = (new inm_rel_co_acred(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener co acreditados',data:  $r_inm_rel_co_acred);
        }

        return $r_inm_rel_co_acred->registros;
    }

    private function inm_conf_docs_comprador(controlador_inm_comprador $controler, array $inm_docs_comprador){
        $inm_conf_docs_comprador = (new _doctos())->documentos_de_comprador(inm_comprador_id: $controler->registro_id,
            link:  $controler->link, todos: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener configuraciones de documentos',
                data:  $inm_conf_docs_comprador);
        }


        foreach ($inm_conf_docs_comprador as $indice=>$doc_tipo_documento){
            $inm_conf_docs_comprador = $this->inm_docs_comprador(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_comprador:  $inm_conf_docs_comprador,inm_docs_comprador:  $inm_docs_comprador);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar buttons',data:  $inm_conf_docs_comprador);
            }
        }
        return $inm_conf_docs_comprador;
    }

    private function inm_docs_comprador(controlador_inm_comprador $controler, array $doc_tipo_documento,
                                             int $indice, array $inm_conf_docs_comprador,array $inm_docs_comprador){
        $existe = false;
        foreach ($inm_docs_comprador as $inm_doc_comprador){

            $existe_doc_comprador = $this->doc_existente(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_comprador:  $inm_conf_docs_comprador,inm_doc_comprador:  $inm_doc_comprador);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar datos',data:  $existe_doc_comprador);
            }

            $inm_conf_docs_comprador = $existe_doc_comprador->inm_conf_docs_comprador;
            $existe = $existe_doc_comprador->existe;
            if($existe){
                break;
            }

        }
        if(!$existe){
            $inm_conf_docs_comprador = $this->integra_data(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_comprador:  $inm_conf_docs_comprador);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
            }
        }
        return $inm_conf_docs_comprador;
    }

    /**
     * Genera un select con datos de la ubicacion
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @param array $extra_params_keys Extra params para options
     * @param array $registros Registros para options
     * @return array|string
     * @version 1.103.1
     */
    final public function inm_ubicacion_id_input(
        controlador_inm_comprador $controler, array $extra_params_keys = array(),
        array $registros = array()): array|string
    {
        $columns_ds = array('inm_ubicacion_id','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_cp_descripcion','dp_colonia_descripcion','dp_calle_descripcion','inm_ubicacion_numero_exterior');

        $inm_ubicacion_id = (new inm_ubicacion_html(html: $controler->html_base))->select_inm_ubicacion_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $controler->link, columns_ds: $columns_ds,
            extra_params_keys: $extra_params_keys, registros: $registros);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inm_ubicacion_id',data:  $inm_ubicacion_id);
        }
        return $inm_ubicacion_id;
    }

    /**
     * Obtiene las ubicaciones asignadas a un comprador
     * @param int $inm_comprador_id Comprador identificador
     * @param PDO $link Conexion a a la base de datos
     * @return array
     * @version 1.104.1
     */
    final public function inm_ubicaciones(int $inm_comprador_id, PDO $link): array
    {
        if($inm_comprador_id<= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0',data:  $inm_comprador_id);
        }
        $filtro = array();
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_inm_rel_ubi_comp = (new inm_rel_ubi_comp(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener compradores',data:  $r_inm_rel_ubi_comp);
        }

        return $r_inm_rel_ubi_comp->registros;
    }

    private function integra_button_default(string $button, int $indice, array $inm_conf_docs_comprador): array
    {
        $inm_conf_docs_comprador[$indice]['descarga'] = $button;
        $inm_conf_docs_comprador[$indice]['vista_previa'] = $button;
        $inm_conf_docs_comprador[$indice]['descarga_zip'] = $button;
        $inm_conf_docs_comprador[$indice]['elimina_bd'] = $button;
        return $inm_conf_docs_comprador;
    }

    private function integra_data(controlador_inm_comprador $controler, array $doc_tipo_documento,
                                       int $indice, array $inm_conf_docs_comprador){
        $params = array('doc_tipo_documento_id'=>$doc_tipo_documento['doc_tipo_documento_id']);

        $button = $controler->html->button_href(accion: 'subir_documento',etiqueta:
            'Subir Documento',registro_id:  $controler->registro_id,
            seccion:  'inm_comprador',style:  'warning', params: $params);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }

        $inm_conf_docs_comprador = $this->integra_button_default(button: $button,
            indice:  $indice,inm_conf_docs_comprador:  $inm_conf_docs_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }
        return $inm_conf_docs_comprador;
    }

    final public function integra_inm_documentos(controlador_inm_comprador $controler){
        $inm_docs_comprador = (new inm_doc_comprador(link: $controler->link))->inm_docs_comprador(inm_comprador_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener documentos',data:  $inm_docs_comprador);
        }
        $inm_conf_docs_comprador = $this->inm_conf_docs_comprador(controler: $controler,inm_docs_comprador:  $inm_docs_comprador);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar buttons',data:  $inm_conf_docs_comprador);
        }
        return $inm_conf_docs_comprador;
    }

    /**
     * Integra los parametros de los inputs
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array
     */
    final public function keys_selects(controlador_inm_comprador $controler): array
    {

        $row_upd = $this->row_upd_base(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }

        $keys_selects = (new _keys_selects())->init(controler: $controler,row_upd: $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    /**
     * Genera los inputs de tipo radio para frontend
     * @param int $checked_default_cd Elemento default con discapacidad
     * @param int $checked_default_esc Elemento default es segundo credito
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 1.102.1
     */
    final public function radios(int $checked_default_cd, int $checked_default_esc,
                                 controlador_inm_comprador|controlador_inm_prospecto $controler): array|stdClass
    {
        if($checked_default_esc <=0){
            return $this->error->error(mensaje: 'Error checked_default debe ser mayor a 0',
                data: $checked_default_esc);
        }
        if($checked_default_esc > 2){
            return $this->error->error(mensaje: 'Error checked_default debe ser menor a 3', data: $checked_default_esc);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $es_segundo_credito = $controler->html->directivas->input_radio_doble(campo: 'es_segundo_credito',
            checked_default: $checked_default_esc,tag: 'Es Segundo Credito', val_1: 'SI',val_2: 'NO');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener es_segundo_credito',data:  $es_segundo_credito);
        }
        $controler->inputs->es_segundo_credito = $es_segundo_credito;

        $con_discapacidad = $controler->html->directivas->input_radio_doble(campo: 'con_discapacidad',
            checked_default: $checked_default_cd,tag: 'Con Discapacidad', val_1: 'NO',val_2: 'SI');


        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener con_discapacidad',data:  $con_discapacidad);
        }

        $controler->inputs->con_discapacidad = $con_discapacidad;

        return $controler->inputs;
    }

    /**
     * Integra los inputs de tipo radio para upd
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler Controlador en ejecucion
     * @return array|stdClass
     * @version 1.108.1
     */
    final public function radios_chk(controlador_inm_comprador|controlador_inm_prospecto $controler): array|stdClass
    {
        $keys = array('es_segundo_credito','con_discapacidad');
        $valida = (new validacion())->valida_existencia_keys(keys: $keys,registro:  $controler->row_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar row upd',data:  $valida);
        }
        if(is_array($controler->inputs)){
            return $this->error->error(mensaje: 'Error controler->inputs no esta inicializado',
                data: $controler->inputs);
        }

        $checkeds = $this->checkeds_default(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar checkeds',data:  $checkeds);
        }

        $radios = $this->radios(checked_default_cd: $checkeds->checked_default_cd,
            checked_default_esc: $checkeds->checked_default_esc, controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar radios',data:  $radios);
        }
        return $radios;
    }

    /**
     * Ajusta los key base para los inputs
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return array|stdClass
     */
    private function row_upd_base(controlador_inm_comprador $controler): array|stdClass
    {
        $row_upd = $this->row_upd_montos(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }

        $row_upd = $this->row_upd_ids(controler: $controler);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar row_upd',data:  $row_upd);
        }
        return $controler->row_upd;
    }

    /**
     * Inicializa los ids default
     * @param controlador_inm_comprador $controler Controlador en ejecucion
     * @return stdClass
     */
    private function row_upd_ids(controlador_inm_comprador $controler): stdClass
    {
        $modelo_preferido = $controler->modelo;

        $entidades_pref[] = 'inm_producto_infonavit';
        $entidades_pref[] = 'inm_attr_tipo_credito';
        $entidades_pref[] = 'inm_destino_credito';
        $entidades_pref[] = 'bn_cuenta';
        $entidades_pref[] = 'inm_sindicato';
        $entidades_pref[] = 'inm_ocupacion';


        foreach ($entidades_pref as $entidad){
            $entidad_id = $modelo_preferido->id_preferido_detalle(entidad_preferida:  $entidad);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener '.$entidad, data:  $entidad_id);
            }
            $key_entidad_id = $entidad.'_id';
            $controler->row_upd->$key_entidad_id = $entidad_id;

        }

        //if(!isset($))

        return $controler->row_upd;
    }


    /**
     * Asigna los montos a 0 en alta
     * @param controlador_inm_comprador $controler  Controlador en ejecucion
     * @return stdClass
     * @version 1.85.1
     */
    private function row_upd_montos(controlador_inm_comprador $controler): stdClass
    {

        $controler->row_upd->descuento_pension_alimenticia_dh = 0;
        $controler->row_upd->monto_credito_solicitado_dh = 0;
        $controler->row_upd->descuento_pension_alimenticia_fc = 0;
        $controler->row_upd->monto_ahorro_voluntario = 0;
        $controler->row_upd->sub_cuenta = 0;
        $controler->row_upd->monto_final = 0;
        $controler->row_upd->descuento = 0;
        $controler->row_upd->puntos = 0;
        return $controler->row_upd;
    }



}
