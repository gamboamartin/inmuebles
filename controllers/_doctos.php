<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\documento\models\doc_tipo_documento;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_comprador_proceso;
use gamboamartin\inmuebles\models\inm_conf_docs_comprador;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto;
use gamboamartin\inmuebles\models\inm_conf_docs_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto_proceso;
use gamboamartin\inmuebles\models\inm_prospecto_ubicacion;
use gamboamartin\inmuebles\models\inm_prospecto_ubicacion_proceso;
use gamboamartin\proceso\models\pr_sub_proceso;
use PDO;

class _doctos{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    final public function documentos_de_comprador(int $inm_comprador_id, PDO $link, bool $todos){

        $inm_comprador = (new inm_comprador(link: $link))->registro(registro_id: $inm_comprador_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener comprador',data:  $inm_comprador);
        }

        $filtro['inm_comprador.id'] = $inm_comprador_id;


        $r_inm_comprador_proceso = (new inm_comprador_proceso(link: $link))->filtro_and(filtro: $filtro, limit: 1,
            order: array('inm_comprador_proceso.id'=>'DESC'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al sub proceso',data:  $r_inm_comprador_proceso);
        }

        if($r_inm_comprador_proceso->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe proceso para el comprador',data:  $r_inm_comprador_proceso);
        }
        if($r_inm_comprador_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad',data:  $r_inm_comprador_proceso);
        }
        $inm_comprador_proceso = $r_inm_comprador_proceso->registros[0];

        $filtro = array();
        $filtro['inm_attr_tipo_credito.id'] = $inm_comprador->inm_attr_tipo_credito_id;
        $filtro['inm_destino_credito.id'] = $inm_comprador->inm_destino_credito_id;
        $filtro['inm_producto_infonavit.id'] = $inm_comprador->inm_producto_infonavit_id;
        if(!$todos) {
            $filtro['pr_sub_proceso.id'] = $inm_comprador_proceso['pr_sub_proceso_id'];
        }

        $r_inm_conf_docs_comprador = (new inm_conf_docs_comprador(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener configuraciones',data:  $r_inm_conf_docs_comprador);
        }


        $confs = $r_inm_conf_docs_comprador->registros;


        $values_in = array();
        foreach ($confs as $value){
            $values_in[] = $value['doc_tipo_documento_id'];
        }
        $in['llave'] = 'doc_tipo_documento.id';
        $in['values'] = $values_in;

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $link))->filtro_and(in: $in);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener tipos de documento',data:  $r_doc_tipo_documento);
        }

        return $r_doc_tipo_documento->registros;
    }

    final public function documentos_de_prospecto(int $inm_prospecto_id, PDO $link, bool $todos, array $tipos_documentos){

        $inm_prospecto = (new inm_prospecto(link: $link))->registro(registro_id: $inm_prospecto_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener prospecto',data:  $inm_prospecto);
        }

        $filtro['inm_prospecto.id'] = $inm_prospecto_id;


        $r_inm_prospecto_proceso = (new inm_prospecto_proceso(link: $link))->filtro_and(filtro: $filtro, limit: 1,
            order: array('inm_prospecto_proceso.id'=>'DESC'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al sub proceso',data:  $r_inm_prospecto_proceso);
        }

        if($r_inm_prospecto_proceso->n_registros === 0){

            $filtro = array();
            $filtro['pr_sub_proceso.descripcion'] = 'ALTA PROSPECTO';
            $filtro['adm_seccion.descripcion'] = 'inm_prospecto';

            $r_pr_sub_proceso = (new pr_sub_proceso(link: $link))->filtro_and(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error obtener r_pr_sub_proceso',data:  $r_pr_sub_proceso);
            }

            if($r_pr_sub_proceso->n_registros === 0){
                return $this->error->error(mensaje: 'Error no existe sub proceso definido',data:  $filtro);
            }

            if($r_pr_sub_proceso->n_registros > 1){
                return $this->error->error(mensaje: 'Error de integridad',data:  $r_pr_sub_proceso);
            }

            $pr_sub_proceso = $r_pr_sub_proceso->registros[0];

            $inm_prospecto_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso['pr_sub_proceso_id'];
            $inm_prospecto_proceso_ins['fecha'] = date('Y-m-d');
            $inm_prospecto_proceso_ins['inm_prospecto_id'] = $inm_prospecto->inm_prospecto_id;

            $alta_inm_prospecto_proceso = (new inm_prospecto_proceso(link: $link))->alta_registro(registro: $inm_prospecto_proceso_ins);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error insertar alta_inm_prospecto_proceso',data:  $alta_inm_prospecto_proceso);
            }
            $r_inm_prospecto_proceso->registros[0] = $alta_inm_prospecto_proceso->registro;

        }
        if($r_inm_prospecto_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad',data:  $r_inm_prospecto_proceso);
        }
        $inm_prospecto_proceso = $r_inm_prospecto_proceso->registros[0];

        $filtro = array();
        //$filtro['inm_attr_tipo_credito.id'] = $inm_prospecto->inm_attr_tipo_credito_id;
        //$filtro['inm_destino_credito.id'] = $inm_prospecto->inm_destino_credito_id;
        //$filtro['inm_producto_infonavit.id'] = $inm_prospecto->inm_producto_infonavit_id;
        $filtro['inm_conf_docs_prospecto.status'] = 'activo';
        if(!$todos) {
            //$filtro['pr_sub_proceso.id'] = $inm_prospecto_proceso['pr_sub_proceso_id'];
        }

        $r_inm_conf_docs_prospecto = (new inm_conf_docs_prospecto(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener configuraciones',data:  $r_inm_conf_docs_prospecto);
        }

        $in = array();

        if (count($tipos_documentos) > 0) {
            $in['llave'] = 'doc_tipo_documento.id';
            $in['values'] = $tipos_documentos;
        }

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $link))->filtro_and(in: $in);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener tipos de documento',data:  $r_doc_tipo_documento);
        }


        return $r_doc_tipo_documento->registros;
    }
    final public function documentos_de_prospecto_ubicacion(int $inm_prospecto_ubicacion_id, PDO $link, bool $todos, array $tipos_documentos){

        $inm_prospecto_ubicacion = (new inm_prospecto_ubicacion(link: $link))->registro(registro_id: $inm_prospecto_ubicacion_id,retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener prospecto_ubicacion',data:  $inm_prospecto_ubicacion);
        }

        $filtro['inm_prospecto_ubicacion.id'] = $inm_prospecto_ubicacion_id;


        $r_inm_prospecto_ubicacion_proceso = (new inm_prospecto_ubicacion_proceso(link: $link))->filtro_and(filtro: $filtro, limit: 1,
            order: array('inm_prospecto_ubicacion_proceso.id'=>'DESC'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al sub proceso',data:  $r_inm_prospecto_ubicacion_proceso);
        }

        if($r_inm_prospecto_ubicacion_proceso->n_registros === 0){

            $filtro = array();
            $filtro['pr_sub_proceso.descripcion'] = 'ALTA PROSPECTO UBICACION';
            $filtro['adm_seccion.descripcion'] = 'inm_prospecto_ubicacion';

            $r_pr_sub_proceso = (new pr_sub_proceso(link: $link))->filtro_and(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error obtener r_pr_sub_proceso',data:  $r_pr_sub_proceso);
            }

            if($r_pr_sub_proceso->n_registros === 0){
                return $this->error->error(mensaje: 'Error no existe sub proceso definido',data:  $filtro);
            }

            if($r_pr_sub_proceso->n_registros > 1){
                return $this->error->error(mensaje: 'Error de integridad',data:  $r_pr_sub_proceso);
            }

            $pr_sub_proceso = $r_pr_sub_proceso->registros[0];

            $inm_prospecto_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso['pr_sub_proceso_id'];
            $inm_prospecto_proceso_ins['fecha'] = date('Y-m-d');
            $inm_prospecto_proceso_ins['inm_prospecto_ubicacion_id'] = $inm_prospecto_ubicacion->inm_prospecto_ubicacion_id;

            $alta_inm_prospecto_proceso = (new inm_prospecto_ubicacion_proceso(link: $link))->alta_registro(registro: $inm_prospecto_proceso_ins);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error insertar alta_inm_prospecto_proceso',data:  $alta_inm_prospecto_proceso);
            }
            $r_inm_prospecto_ubicacion_proceso->registros[0] = $alta_inm_prospecto_proceso->registro;

        }
        if($r_inm_prospecto_ubicacion_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad',data:  $r_inm_prospecto_ubicacion_proceso);
        }
        $inm_prospecto_proceso = $r_inm_prospecto_ubicacion_proceso->registros[0];

        $filtro = array();
        //$filtro['inm_attr_tipo_credito.id'] = $inm_prospecto->inm_attr_tipo_credito_id;
        //$filtro['inm_destino_credito.id'] = $inm_prospecto->inm_destino_credito_id;
        $filtro['inm_conf_docs_prospecto_ubicacion.es_foto'] = 'inactivo';
        $filtro['inm_conf_docs_prospecto_ubicacion.status'] = 'activo';
        if(!$todos) {
            //$filtro['pr_sub_proceso.id'] = $inm_prospecto_proceso['pr_sub_proceso_id'];
        }

        $r_inm_conf_docs_prospecto = (new inm_conf_docs_prospecto_ubicacion(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener configuraciones',data:  $r_inm_conf_docs_prospecto);
        }

        $in = array();

        if (count($tipos_documentos) > 0) {
            $in['llave'] = 'doc_tipo_documento.id';
            $in['values'] = $tipos_documentos;
        }

        $r_doc_tipo_documento = (new doc_tipo_documento(link: $link))->filtro_and(in: $in);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al Obtener tipos de documento',data:  $r_doc_tipo_documento);
        }


        return $r_doc_tipo_documento->registros;
    }

}
