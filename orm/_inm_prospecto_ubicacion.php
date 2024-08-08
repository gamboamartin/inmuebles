<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_doctos;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto_ubicacion;
use gamboamartin\validacion\validacion;
use stdClass;

class _inm_prospecto_ubicacion{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }


    private function button(string $accion, controlador_inm_prospecto_ubicacion $controler, string $etiqueta, int $indice,
                                 int $inm_doc_prospecto_id, array $inm_conf_docs_prospecto, array $params = array(),
                                 string $style = 'success', string $target = ''): array
    {
        $button = $controler->html->button_href(accion: $accion, etiqueta: $etiqueta,
            registro_id: $inm_doc_prospecto_id, seccion: 'inm_doc_prospecto', style: $style, params: $params,
            target: $target);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }
        $inm_conf_docs_prospecto[$indice][$accion] = $button;
        return $inm_conf_docs_prospecto;
    }

    final public function button_del(controlador_inm_prospecto_ubicacion $controler, int $indice, int $inm_prospecto_id,
                                array $inm_conf_docs_prospecto, array $inm_doc_prospecto){

        $params = array('accion_retorno'=>'documentos','seccion_retorno'=>$controler->seccion,
            'id_retorno'=>$inm_prospecto_id);

        $inm_conf_docs_comprador = (new _inm_prospecto_ubicacion())->button(accion: 'elimina_bd', controler: $controler,
            etiqueta: 'Elimina', indice: $indice, inm_doc_prospecto_id: $inm_doc_prospecto['inm_doc_prospecto_ubicacion_id'],
            inm_conf_docs_prospecto: $inm_conf_docs_prospecto, params: $params, style: 'danger');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_comprador);
        }
        return $inm_conf_docs_comprador;
    }

    private function buttons(controlador_inm_prospecto_ubicacion $controler, int $indice, array $inm_conf_docs_prospecto,
                                  array $inm_doc_prospecto){

        $inm_conf_docs_prospecto = $this->button(accion: 'descarga', controler: $controler,
            etiqueta: 'Descarga', indice: $indice, inm_doc_prospecto_id: $inm_doc_prospecto['inm_doc_prospecto_ubicacion_id'],
            inm_conf_docs_prospecto: $inm_conf_docs_prospecto);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }

        $inm_conf_docs_prospecto = $this->button(accion: 'vista_previa', controler: $controler,
            etiqueta: 'Vista Previa', indice: $indice, inm_doc_prospecto_id: $inm_doc_prospecto['inm_doc_prospecto_ubicacion_id'],
            inm_conf_docs_prospecto: $inm_conf_docs_prospecto, target: '_blank');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }

        $inm_conf_docs_prospecto = $this->button(accion: 'descarga_zip', controler: $controler,
            etiqueta: 'ZIP', indice: $indice, inm_doc_prospecto_id: $inm_doc_prospecto['inm_doc_prospecto_ubicacion_id'],
            inm_conf_docs_prospecto: $inm_conf_docs_prospecto);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }
        return $inm_conf_docs_prospecto;
    }

    private function buttons_base(controlador_inm_prospecto_ubicacion $controler, int $indice, int $inm_prospecto_id,
                                  array $inm_conf_docs_prospecto, array $inm_doc_prospecto): array
    {
        $inm_conf_docs_prospecto = $this->buttons(controler: $controler,indice:  $indice,
            inm_conf_docs_prospecto:  $inm_conf_docs_prospecto,inm_doc_prospecto:  $inm_doc_prospecto);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }

        $inm_conf_docs_prospecto = $this->button_del(controler: $controler,indice:  $indice,
            inm_prospecto_id:  $inm_prospecto_id,inm_conf_docs_prospecto:  $inm_conf_docs_prospecto,
            inm_doc_prospecto:  $inm_doc_prospecto);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }

        return $inm_conf_docs_prospecto;
    }

    /**
     * Integra los checkeds default para upd
     * @param controlador_inm_comprador|controlador_inm_prospecto $controler Controlador en ejecucion
     * @return stdClass|array
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

    private function doc_existente(controlador_inm_prospecto_ubicacion $controler, array $doc_tipo_documento, int $indice,
                                        array $inm_conf_docs_prospecto, array $inm_doc_prospecto){

        $existe = false;
        if($doc_tipo_documento['doc_tipo_documento_id'] === $inm_doc_prospecto['doc_tipo_documento_id']){

            $existe = true;

            $inm_conf_docs_prospecto = $this->buttons_base(
                controler: $controler,indice:  $indice,inm_prospecto_id:  $controler->registro_id,
                inm_conf_docs_prospecto:  $inm_conf_docs_prospecto,inm_doc_prospecto:  $inm_doc_prospecto);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
            }
        }

        $data = new stdClass();
        $data->existe = $existe;
        $data->inm_conf_docs_prospecto = $inm_conf_docs_prospecto;
        return $data;
    }



    private function inm_conf_docs_prospecto(controlador_inm_prospecto_ubicacion $controler, array $inm_docs_prospecto, array $tipos_documentos){
        $inm_conf_docs_prospecto = (new _doctos())->documentos_de_prospecto_ubicacion(inm_prospecto_ubicacion_id: $controler->registro_id,
            link:  $controler->link, todos: true, tipos_documentos: $tipos_documentos);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener configuraciones de documentos',
                data:  $inm_conf_docs_prospecto);
        }

        foreach ($inm_conf_docs_prospecto as $indice=>$doc_tipo_documento){
            $inm_conf_docs_prospecto = $this->inm_docs_prospecto(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_prospecto:  $inm_conf_docs_prospecto,inm_docs_prospecto:  $inm_docs_prospecto);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar buttons',data:  $inm_conf_docs_prospecto);
            }
        }
        return $inm_conf_docs_prospecto;
    }

    private function inm_docs_prospecto(controlador_inm_prospecto_ubicacion $controler, array $doc_tipo_documento,
                                             int $indice, array $inm_conf_docs_prospecto,array $inm_docs_prospecto){
        $existe = false;
        foreach ($inm_docs_prospecto as $inm_doc_prospecto){

            $existe_doc_prospecto = $this->doc_existente(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_prospecto:  $inm_conf_docs_prospecto,inm_doc_prospecto:  $inm_doc_prospecto);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar datos',data:  $existe_doc_prospecto);
            }

            $inm_conf_docs_prospecto = $existe_doc_prospecto->inm_conf_docs_prospecto;
            $existe = $existe_doc_prospecto->existe;
            if($existe){
                break;
            }

        }
        if(!$existe){
            $inm_conf_docs_prospecto = $this->integra_data(controler: $controler,
                doc_tipo_documento:  $doc_tipo_documento,indice:  $indice,
                inm_conf_docs_prospecto:  $inm_conf_docs_prospecto);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
            }
        }
        return $inm_conf_docs_prospecto;
    }


    /**
     * Genera un registro para la insersion de una relacion entre conyuge y prospecto
     * @param int $inm_conyuge_id Id de conyuge
     * @param int $inm_prospecto_id Id de prospecto
     * @return array
     * @version 2.266.2
     */
    final public function inm_rel_conyuge_prospecto_ins(int $inm_conyuge_id, int $inm_prospecto_ubicacion_id): array
    {
        if($inm_conyuge_id <= 0){
            return $this->error->error(mensaje: 'Error inm_conyuge_id debe ser mayor a 0',data:  $inm_conyuge_id);
        }
        if($inm_prospecto_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0',data:  $inm_prospecto_ubicacion_id);
        }
        $inm_rel_conyuge_prospecto_ins['inm_prospecto_ubicacion_id'] = $inm_prospecto_ubicacion_id;
        $inm_rel_conyuge_prospecto_ins['inm_conyuge_id'] = $inm_conyuge_id;

        return $inm_rel_conyuge_prospecto_ins;
    }



    private function integra_button_default(string $button, int $indice, array $inm_conf_docs_prospecto): array
    {
        $inm_conf_docs_prospecto[$indice]['descarga'] = $button;
        $inm_conf_docs_prospecto[$indice]['vista_previa'] = $button;
        $inm_conf_docs_prospecto[$indice]['descarga_zip'] = $button;
        $inm_conf_docs_prospecto[$indice]['elimina_bd'] = $button;
        return $inm_conf_docs_prospecto;
    }

    private function integra_data(controlador_inm_prospecto_ubicacion $controler, array $doc_tipo_documento,
                                       int $indice, array $inm_conf_docs_prospecto){
        $params = array('doc_tipo_documento_id'=>$doc_tipo_documento['doc_tipo_documento_id']);

        $button = $controler->html->button_href(accion: 'subir_documento',etiqueta:
            'Subir Documento',registro_id:  $controler->registro_id,
            seccion:  'inm_prospecto',style:  'warning', params: $params);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $button);
        }

        $inm_conf_docs_prospecto = $this->integra_button_default(button: $button,
            indice:  $indice,inm_conf_docs_prospecto:  $inm_conf_docs_prospecto);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar button',data:  $inm_conf_docs_prospecto);
        }
        return $inm_conf_docs_prospecto;
    }

    final public function integra_inm_documentos(controlador_inm_prospecto_ubicacion $controler){
        $inm_prospecto = (new inm_prospecto_ubicacion(link: $controler->link))->registro(registro_id: $controler->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_prospecto',data:  $inm_prospecto);
        }

        $filtro['inm_conf_docs_prospecto_ubicacion.es_foto'] = 'inactivo';
        $inm_conf_docs_prospecto = (new inm_conf_docs_prospecto_ubicacion(link: $controler->link))->filtro_and(
            columnas: ['doc_tipo_documento_id'],
            filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_conf_docs_prospecto',data:  $inm_conf_docs_prospecto);
        }

        $doc_ids = array_map(function($registro) {
            return $registro['doc_tipo_documento_id'];
        }, $inm_conf_docs_prospecto->registros);

        if (count($doc_ids) <= 0) {
            return array();
        }

        $inm_docs_prospecto = (new inm_doc_prospecto_ubicacion(link: $controler->link))->inm_docs_prospecto_ubicacion(
            inm_prospecto_ubicacion: $controler->registro_id, tipos_documentos: $doc_ids);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_docs_prospecto',data:  $inm_docs_prospecto);
        }

        $inm_docs_prospecto = $this->inm_conf_docs_prospecto(controler: $controler,inm_docs_prospecto:  $inm_docs_prospecto,
            tipos_documentos: $doc_ids);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar buttons',data:  $inm_docs_prospecto);
        }

        return $inm_docs_prospecto;
    }



}
