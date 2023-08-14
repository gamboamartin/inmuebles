<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_ubicacion extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_ubicacion';
        $columnas = array($tabla=>false,'dp_calle_pertenece'=>$tabla,'dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_cp'=>'dp_colonia_postal','dp_colonia'=>'dp_colonia_postal','dp_municipio'=>'dp_cp',
            'dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado','dp_calle'=>'dp_calle_pertenece');

        $campos_obligatorios = array('manzana','lote','dp_calle_pertenece_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('manzana','lote','dp_calle_pertenece_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Ubicaciones';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro = $this->registro;


        $registro = $this->init_row(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
        }

        $this->registro = $registro;

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar ubicacion',data:  $r_alta_bd);
        }
        return $r_alta_bd;
    }

    private function descripcion(stdClass $dp_calle_pertenece, array $registro): string
    {
        $descripcion = $dp_calle_pertenece->dp_pais_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_estado_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_municipio_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_colonia_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_cp_descripcion;
        $descripcion .= ' '.$registro['manzana'].' '.$registro['lote'];
        $descripcion .= ' '.$registro['numero_exterior'].' '.$registro['numero_interior'];
        return trim($descripcion);
    }

    private function init_row(array $registro){
        $dp_calle_pertenece = (new dp_calle_pertenece(link: $this->link))->registro(
            registro_id: $registro['dp_calle_pertenece_id'],retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener dp_calle_pertenece',data:  $dp_calle_pertenece);
        }


        if(!isset($registro['descripcion'])){

            $registro = $this->integra_descripcion(dp_calle_pertenece: $dp_calle_pertenece,registro:  $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al integrar descripcion',data:  $registro);
            }

        }
        return $registro;
    }

    private function integra_descripcion(stdClass $dp_calle_pertenece, array $registro){
        if(!isset($registro['numero_interior'])){
            $registro['numero_interior'] = '';
        }

        $descripcion = $this->descripcion(dp_calle_pertenece: $dp_calle_pertenece, registro: $registro);
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
            return $this->error->error(mensaje: 'Error al modificar ubicacion',data:  $r_modifica_bd);
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


}