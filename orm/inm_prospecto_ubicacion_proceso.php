<?php

namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_prospecto_ubicacion_proceso extends _modelo_base_paquete {
    public function __construct(PDO $link)
    {
        $tabla = 'inm_prospecto_ubicacion_proceso';
        $columnas = array($tabla=>false,'pr_sub_proceso'=>$tabla,'inm_prospecto_ubicacion'=>$tabla,
            'pr_proceso'=>'pr_sub_proceso');

        $campos_obligatorios = array('pr_sub_proceso_id','inm_prospecto_ubicacion_id','fecha');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('pr_sub_proceso_id','inm_prospecto_ubicacion_id','fecha');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Proceso de Clientes';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->registro;

        $valida = $this->valida_init(key_entidad_base_id: 'inm_prospecto_ubicacion_id', key_entidad_id: 'pr_sub_proceso_id',
            registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }


        $registro = $this->init_row(key_entidad_base_id: 'inm_prospecto_ubicacion_id', key_entidad_id: 'pr_sub_proceso_id',
            registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar prospecto',data:  $r_alta_bd);
        }


        $row_udp['proceso'] = $r_alta_bd->registro_obj->pr_sub_proceso_descripcion;
        $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $row_udp,
            id: $r_alta_bd->registro_obj->inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar etapa', data: $upd);
        }

        return $r_alta_bd;
    }

    /**
     * Inserta un elementos de prospecto proceso
     * @param array $registro registro a insertar
     * @return array|stdClass
     * @version 2.206.1
     */
    public function alta_registro(array $registro): array|stdClass
    {
        $valida = $this->valida_init(key_entidad_base_id: 'inm_prospecto_ubicacion_id', key_entidad_id: 'pr_sub_proceso_id',
            registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro', data: $valida);
        }

        $r_alta =  parent::alta_registro(registro: $registro); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_alta);
        }
        return $r_alta;
    }





}