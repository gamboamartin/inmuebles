<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_conf_empresa extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_conf_empresa';
        $columnas = array($tabla=>false,'inm_tipo_inmobiliaria'=>$tabla,'org_empresa'=>$tabla);

        $campos_obligatorios = array('inm_tipo_inmobiliaria_id','org_empresa_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_tipo_inmobiliaria_id','org_empresa_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Configuracion de empresa';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro_entrada = $this->registro;

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->descripcion(registro: $this->registro );
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }

            $this->registro['descripcion'] = $descripcion;
        }


        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }


        return $r_alta_bd;

    }

    /**
     * Genera la descripcion de un comprador basado en datos del registro a insertar
     * @param array $registro Registro en proceso
     * @return string
     */
    private function descripcion(array $registro): string
    {
        $descripcion = $registro['inm_tipo_inmobiliaria_id'];
        $descripcion .= ' '.$registro['org_empresa_id'];
        return $descripcion;
    }




}