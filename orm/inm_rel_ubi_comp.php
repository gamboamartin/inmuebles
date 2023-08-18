<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_rel_ubi_comp extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_rel_ubi_comp';
        $columnas = array($tabla=>false,'inm_comprador'=>$tabla,'inm_ubicacion'=>$tabla);

        $campos_obligatorios = array('inm_comprador_id','inm_ubicacion_id','precio_operacion');

        $columnas_extra= array();

        $renombres['dp_calle_pertenece_ubicacion']['nombre_original']= 'dp_calle_pertenece';
        $renombres['dp_calle_pertenece_ubicacion']['enlace']= 'inm_ubicacion';
        $renombres['dp_calle_pertenece_ubicacion']['key']= 'id';
        $renombres['dp_calle_pertenece_ubicacion']['key_enlace']= 'dp_calle_pertenece_id';

        $renombres['dp_colonia_postal_ubicacion']['nombre_original']= 'dp_colonia_postal';
        $renombres['dp_colonia_postal_ubicacion']['enlace']= 'dp_calle_pertenece_ubicacion';
        $renombres['dp_colonia_postal_ubicacion']['key']= 'id';
        $renombres['dp_colonia_postal_ubicacion']['key_enlace']= 'dp_colonia_postal_id';

        $renombres['dp_cp_ubicacion']['nombre_original']= 'dp_cp';
        $renombres['dp_cp_ubicacion']['enlace']= 'dp_colonia_postal_ubicacion';
        $renombres['dp_cp_ubicacion']['key']= 'id';
        $renombres['dp_cp_ubicacion']['key_enlace']= 'dp_cp_id';

        $renombres['dp_municipio_ubicacion']['nombre_original']= 'dp_municipio';
        $renombres['dp_municipio_ubicacion']['enlace']= 'dp_cp_ubicacion';
        $renombres['dp_municipio_ubicacion']['key']= 'id';
        $renombres['dp_municipio_ubicacion']['key_enlace']= 'dp_municipio_id';

        $renombres['dp_estado_ubicacion']['nombre_original']= 'dp_estado';
        $renombres['dp_estado_ubicacion']['enlace']= 'dp_municipio_ubicacion';
        $renombres['dp_estado_ubicacion']['key']= 'id';
        $renombres['dp_estado_ubicacion']['key_enlace']= 'dp_estado_id';

        $renombres['dp_colonia_ubicacion']['nombre_original']= 'dp_colonia';
        $renombres['dp_colonia_ubicacion']['enlace']= 'dp_colonia_postal_ubicacion';
        $renombres['dp_colonia_ubicacion']['key']= 'id';
        $renombres['dp_colonia_ubicacion']['key_enlace']= 'dp_colonia_id';

        $renombres['dp_calle_ubicacion']['nombre_original']= 'dp_calle';
        $renombres['dp_calle_ubicacion']['enlace']= 'dp_calle_pertenece_ubicacion';
        $renombres['dp_calle_ubicacion']['key']= 'id';
        $renombres['dp_calle_ubicacion']['key_enlace']= 'dp_calle_id';

        $atributos_criticos = array('inm_comprador_id','inm_ubicacion_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Relacion comprador cliente';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->registro['inm_ubicacion_id'];
            $descripcion .= ' '.$this->registro['inm_comprador_id'];
            $this->registro['descripcion'] = $descripcion;
        }

        $filtro['inm_ubicacion.id'] = $this->registro['inm_ubicacion_id'];
        $filtro['inm_comprador.id'] = $this->registro['inm_comprador_id'];

        $existe = $this->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe);
        }
        if(!$existe) {
            $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar', data: $r_alta_bd);
            }
        }
        else{

            $r_registro = $this->filtro_and(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener relacion', data: $r_registro);
            }

            $registro_puro = $this->registro(registro_id: $r_registro->registros[0]['inm_rel_ubi_comp_id'],
                columnas_en_bruto: true,retorno_obj: true);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener relacion', data: $registro_puro);
            }

            $registro = $r_registro->registros[0];
            

            $r_alta_bd = $this->data_result_transaccion(mensaje: 'Registro insertado con éxito', registro: $registro,
                registro_ejecutado: $this->registro, registro_id: $r_registro->registros[0]['inm_rel_ubi_comp_id'],
                registro_puro: $registro_puro,
                sql: 'Registro existente');
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar respuesta registro', data: $registro);
            }

        }



        return $r_alta_bd;

    }


}