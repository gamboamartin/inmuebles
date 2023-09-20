<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_comprador_proceso extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_comprador_proceso';
        $columnas = array($tabla=>false,'pr_sub_proceso'=>$tabla,'inm_comprador'=>$tabla,
            'pr_proceso'=>'pr_sub_proceso');

        $campos_obligatorios = array('pr_sub_proceso_id','inm_comprador_id','fecha');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('pr_sub_proceso_id','inm_comprador_id','fecha');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Proceso de Clientes';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->registro;

        $valida = $this->valida_init(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }


        $registro = $this->init_row(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar comprador',data:  $r_alta_bd);
        }


        $row_udp['proceso'] = $r_alta_bd->registro_obj->pr_sub_proceso_descripcion;
        $upd = (new inm_comprador(link: $this->link))->modifica_bd(registro: $row_udp,
            id: $r_alta_bd->registro_obj->inm_comprador_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar etapa', data: $upd);
        }


        return $r_alta_bd;
    }

    /**
     * Inserta un elementos de comprador proceso
     * @param array $registro registro a insertar
     * @return array|stdClass
     * @version 2.40.0
     */
    public function alta_registro(array $registro): array|stdClass
    {
        $valida = $this->valida_init(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }

        $r_alta =  parent::alta_registro(registro: $registro); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_alta);
        }
        return $r_alta;
    }

    private function descripcion(array $registro): string|array
    {
        $valida = $this->valida_init(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }

        $descripcion = $registro['inm_comprador_id'];
        $descripcion .= ' '.$registro['pr_sub_proceso_id'];
        $descripcion .= ' '.$registro['fecha'];
        $descripcion .= ' '.time();
        return trim($descripcion);
    }

    private function init_row(array $registro){


        if(!isset($registro['descripcion'])){

            $registro = $this->integra_descripcion(registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
            }

        }
        return $registro;
    }

    private function integra_descripcion(array $registro){

        $descripcion = $this->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
        }


        $registro['descripcion'] = $descripcion;
        return $registro;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $r_modifica_bd = parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica_bd);
        }

        $registro = $this->registro(registro_id: $id, columnas_en_bruto: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener registro',data:  $registro);
        }

        unset($registro['descripcion']);
        $registro = $this->init_row(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }
        $r_modifica_bd_ds = parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar descripcion',data:  $r_modifica_bd_ds);
        }

        return $r_modifica_bd;

    }

    final public function valida_init(array $registro){
        $keys = array('inm_comprador_id','pr_sub_proceso_id','fecha');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }
        $keys = array('inm_comprador_id','pr_sub_proceso_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }

        $keys = array('fecha');
        $valida = $this->validacion->fechas_in_array(data: $registro, keys: $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }
        return true;
    }


}