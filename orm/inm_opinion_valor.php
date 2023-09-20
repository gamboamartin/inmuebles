<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_opinion_valor extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_opinion_valor';
        $columnas = array($tabla=>false,'inm_ubicacion'=>$tabla,'inm_valuador'=>$tabla,
            'dp_calle_pertenece'=>'inm_ubicacion','dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_cp'=>'dp_colonia_postal','dp_municipio'=>'dp_cp','dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado',
            'dp_colonia'=>'dp_colonia_postal','dp_calle'=>'dp_calle_pertenece');

        $campos_obligatorios = array('inm_ubicacion_id','inm_valuador_id','monto_resultado','fecha','costo');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_ubicacion_id','inm_valuador_id','monto_resultado','fecha','costo');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Opiniones de valor';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $inm_ubicacion = (new inm_ubicacion(link: $this->link))->registro(
            registro_id: $this->registro['inm_ubicacion_id'],retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ubicacion',data:  $inm_ubicacion);
        }
        $inm_valuador = (new inm_valuador(link: $this->link))->registro(
            registro_id: $this->registro['inm_valuador_id'],retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_valuador',data:  $inm_valuador);
        }

        if(!isset($this->registro['descripcion'])){
            $descripcion = $inm_ubicacion->dp_estado_descripcion.' ';
            $descripcion .= $inm_ubicacion->dp_municipio_descripcion.' ';
            $descripcion .= $inm_ubicacion->dp_cp_descripcion.' ';
            $descripcion .= $inm_ubicacion->dp_colonia_descripcion.' ';
            $descripcion .= $inm_ubicacion->dp_calle_descripcion.' ';
            $descripcion .= $inm_ubicacion->inm_ubicacion_numero_exterior.' ';
            $descripcion .= $this->registro['fecha'].' ';
            $descripcion .= $inm_valuador->inm_valuador_descripcion;
            $descripcion = trim($descripcion);

            $this->registro['descripcion'] = $descripcion;
        }
        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar opcion', data: $r_alta_bd);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_opinion_valor(
            inm_ubicacion_id: $r_alta_bd->registro_puro->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_monto_opinion_promedio(
            inm_ubicacion_id: $r_alta_bd->registro_puro->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }

        return $r_alta_bd;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar opinion', data: $r_elimina_bd);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_opinion_valor(
            inm_ubicacion_id: $r_elimina_bd->registro_puro->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_monto_opinion_promedio(
            inm_ubicacion_id: $r_elimina_bd->registro_puro->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }
        return $r_elimina_bd;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar opinion', data: $r_modifica_bd);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_opinion_valor(
            inm_ubicacion_id: $r_modifica_bd->registro_actualizado->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }
        $regenera = (new inm_ubicacion(link: $this->link))->regenera_monto_opinion_promedio(
            inm_ubicacion_id: $r_modifica_bd->registro_actualizado->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }
        return $r_modifica_bd;
    }


}