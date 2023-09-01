<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use PDO;
use stdClass;

class _inm_comprador{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }


    final public function button(string $accion, controlador_inm_comprador $controler, string $etiqueta, int $indice,
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

    final public function inm_ubicacion_id_input(controlador_inm_comprador $controler){
        $columns_ds = array('inm_ubicacion_id','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_cp_descripcion','dp_colonia_descripcion','dp_calle_descripcion','inm_ubicacion_numero_exterior');

        $inm_ubicacion_id = (new inm_ubicacion_html(html: $controler->html_base))->select_inm_ubicacion_id(
            cols: 12, con_registros: true,id_selected: -1,link:  $controler->link, columns_ds: $columns_ds);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inm_ubicacion_id',data:  $inm_ubicacion_id);
        }
        return $inm_ubicacion_id;
    }

    final public function inm_ubicaciones(int $inm_comprador_id, PDO $link){
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

    final public function integra_data(controlador_inm_comprador $controler, array $doc_tipo_documento,
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
    final public function keys_selects(controlador_inm_comprador $controler){
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
    final public function radios(controlador_inm_comprador $controler){
        $es_segundo_credito = $controler->html->directivas->input_radio_doble(campo: 'es_segundo_credito',
            checked_default: 2,tag: 'Es Segundo Credito', val_1: 'SI',val_2: 'NO');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener es_segundo_credito',data:  $es_segundo_credito);
        }
        $controler->inputs->es_segundo_credito = $es_segundo_credito;

        $con_discapacidad = $controler->html->directivas->input_radio_doble(campo: 'con_discapacidad',
            checked_default: 1,tag: 'Con Discapacidad', val_1: 'NO',val_2: 'SI');


        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener con_discapacidad',data:  $con_discapacidad);
        }

        $controler->inputs->con_discapacidad = $con_discapacidad;

        return $controler->inputs;
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
     * @version 1.87.1
     */
    private function row_upd_ids(controlador_inm_comprador $controler): stdClass
    {
        $controler->row_upd->inm_producto_infonavit_id = 1;
        $controler->row_upd->inm_attr_tipo_credito_id = 6;
        $controler->row_upd->inm_destino_credito_id = 1;
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
        return $controler->row_upd;
    }



}
