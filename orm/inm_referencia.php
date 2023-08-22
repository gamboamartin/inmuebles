<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_referencia extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_referencia';
        $columnas = array($tabla=>false,'inm_comprador'=>$tabla, 'dp_calle_pertenece'=>$tabla,
            'dp_colonia_postal'=>'dp_calle_pertenece','dp_cp'=>'dp_colonia_postal','dp_municipio'=>'dp_cp',
            'dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado','dp_colonia'=>'dp_colonia_postal',
            'dp_calle'=>'dp_calle_pertenece');

        $campos_obligatorios = array('inm_comprador_id','apellido_paterno','apellido_materno', 'nombre','lada',
            'numero', 'celular','dp_calle_pertenece_id','numero_dom');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_comprador_id','apellido_paterno','apellido_materno', 'nombre','lada',
            'numero', 'celular','dp_calle_pertenece_id','numero_dom');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Referencia';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
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
        $descripcion = $registro['nombre'];
        $descripcion .= ' '.$registro['apellido_paterno'];
        $descripcion .= ' '.$registro['apellido_materno'];
        return $descripcion;
    }



}