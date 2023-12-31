<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_referencia extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_referencia';
        $columnas = array($tabla=>false, 'inm_parentesco'=>$tabla, 'inm_comprador'=>$tabla, 'dp_calle_pertenece'=>$tabla,
            'dp_colonia_postal'=>'dp_calle_pertenece','dp_cp'=>'dp_colonia_postal','dp_municipio'=>'dp_cp',
            'dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado','dp_colonia'=>'dp_colonia_postal',
            'dp_calle'=>'dp_calle_pertenece');

        $campos_obligatorios = array('inm_comprador_id','apellido_paterno', 'nombre','lada', 'numero', 'celular',
            'dp_calle_pertenece_id','numero_dom','inm_parentesco_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_comprador_id','apellido_paterno','apellido_materno', 'nombre','lada',
            'numero', 'celular','dp_calle_pertenece_id','inm_parentesco_id','numero_dom');

        $tipo_campos['lada'] = 'lada';
        $tipo_campos['numero'] = 'tel_sin_lada';
        $tipo_campos['celular'] = 'telefono_mx';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Referencia';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $valida = $this->valida_alta_referencia(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

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
     * @return string|array
     */
    private function descripcion(array $registro): string|array
    {

        $valida = $this->valida_data_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

        if(!isset($registro['apellido_materno'])){
            $registro['apellido_materno'] = '';
        }


        $descripcion = $registro['nombre'];
        $descripcion .= ' '.$registro['apellido_paterno'];
        $descripcion .= ' '.$registro['apellido_materno'];
        return $descripcion;
    }

    /**
     * Obtiene las referencias basadas en un comprador
     * @param int $inm_comprador_id Identificador de comprador
     * @return array
     * @version 1.114.1
     */
    final public function inm_referencias(int $inm_comprador_id): array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }

        $filtro = array();
        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_inm_referencia = $this->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_inm_referencia',data:  $r_inm_referencia);
        }
        return $r_inm_referencia->registros;
    }

    final public function valida_alta_referencia(array $registro){
        $valida = $this->valida_data_descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }
        $keys = array('inm_comprador_id','dp_calle_pertenece_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

        $keys = array('lada','numero','celular','numero_dom');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

        $valida = $this->validacion->valida_lada(lada: $registro['lada']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar lada',data: $valida);
        }

        $valida = $this->validacion->valida_numero_sin_lada(tel: $registro['numero']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar numero',data: $valida);
        }
        $valida = $this->validacion->valida_numero_tel_mx(tel: $registro['celular']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar celular',data: $valida);
        }
        return true;
    }

    private function valida_data_descripcion(array $registro){
        $keys = array('nombre','apellido_paterno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar $registro',data: $valida);
        }
        return true;
    }



}