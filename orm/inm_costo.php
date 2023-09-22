<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_costo extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_costo';
        $columnas = array($tabla=>false,'inm_concepto'=>$tabla,'inm_ubicacion'=>$tabla,
            'inm_tipo_concepto'=>'inm_concepto');

        $campos_obligatorios = array('inm_concepto_id','inm_ubicacion_id','monto','fecha','referencia');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_ubicacion_id','inm_concepto_id','monto','fecha','referencia');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Costo Ubicaciones';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $inm_ubicacion = (new inm_ubicacion(link: $this->link))->registro(
            registro_id: $this->registro['inm_ubicacion_id'],retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener ubicacion',data:  $inm_ubicacion);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar opcion', data: $r_alta_bd);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_datas(
            inm_ubicacion_id: $r_alta_bd->registro_puro->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar costo', data: $regenera);
        }


        return $r_alta_bd;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar opinion', data: $r_elimina_bd);
        }

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_datas(
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

        $regenera = (new inm_ubicacion(link: $this->link))->regenera_datas(
            inm_ubicacion_id: $r_modifica_bd->registro_actualizado->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }

        return $r_modifica_bd;
    }

}