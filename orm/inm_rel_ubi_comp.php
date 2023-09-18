<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_proceso;
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


        $registro = $this->integra_descripcion_aut(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar descripcion',data:  $registro);
        }

        $inm_comprador = (new inm_comprador(link: $this->link))->registro(registro_id: $this->registro['inm_comprador_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_comprador',data:  $inm_comprador);
        }

        $inm_ubicacion = (new inm_ubicacion(link: $this->link))->registro(registro_id: $this->registro['inm_ubicacion_id']);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_ubicacion',data:  $inm_ubicacion);
        }

        $datos = $this->datos_row(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener datos',data:  $datos);
        }


        $this->registro = $registro;


        $r_alta_bd = $this->alta_bd_base(keys_integra_ds: $keys_integra_ds,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar respuesta registro', data: $r_alta_bd);
        }


        $etapas = $this->inserta_etapas(function: __FUNCTION__,registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapas', data: $etapas);
        }



        return $r_alta_bd;

    }

    private function alta_bd_base(array $keys_integra_ds, array $registro){
        $existe = $this->existe_row(registro: $registro);
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
            $r_alta_bd = $this->alta_existente_row(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar respuesta registro', data: $r_alta_bd);
            }
        }
        return $r_alta_bd;
    }

    private function alta_existente_row(array $registro){
        $data = $this->data_rel(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener relacion', data: $data);
        }

        $r_alta_bd = $this->data_result_transaccion(mensaje: 'Registro insertado con éxito', registro: $data->registro,
            registro_ejecutado: $this->registro, registro_id: $data->r_registro->registros[0]['inm_rel_ubi_comp_id'],
            registro_puro: $data->registro_puro,
            sql: 'Registro existente');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar respuesta registro', data: $r_alta_bd);
        }
        return $r_alta_bd;
    }

    private function data_rel(array $registro){
        $filtro = $this->filtro_unique(registro: $registro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener filtro', data: $filtro);
        }

        $data = $this->imp_rel_ubi_comp_filtro($filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener relacion', data: $data);
        }
        return $data;
    }

    private function datos_row(array $registro){
        $inm_comprador = (new inm_comprador(link: $this->link))->registro(registro_id: $registro['inm_comprador_id'], retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_comprador',data:  $inm_comprador);
        }

        $inm_ubicacion = (new inm_ubicacion(link: $this->link))->registro(registro_id: $registro['inm_ubicacion_id'], retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_ubicacion',data:  $inm_ubicacion);
        }

        $inm_precio = (new inm_precio(link: $this->link))->precio(fecha: date('Y-m-d'),
            inm_ubicacion_id: $registro['inm_ubicacion_id'],
            inm_institucion_hipotecaria_id: $inm_comprador->inm_institucion_hipotecaria_id);;
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_precio',data:  $inm_precio);
        }

        $data = new stdClass();
        $data->inm_comprador_id = $inm_comprador;
        $data->inm_ubicacion = $inm_ubicacion;
        $data->inm_precio = $inm_precio;

        return $data;
    }
    private function descripcion(array $registro): string
    {
        $descripcion = $registro['inm_ubicacion_id'];
        $descripcion .= ' '.$registro['inm_comprador_id'];
        return $descripcion;
    }

    private function inserta_etapas(string $function, array $registro){
        $data = new stdClass();
        $modelo_etapa_cliente = new inm_comprador_etapa(link: $this->link);
        $this->key_id = 'inm_comprador_id';
        $r_alta_etapa = (new pr_proceso(link: $this->link))->inserta_etapa(adm_accion: $function, fecha: '',
            modelo: $this, modelo_etapa: $modelo_etapa_cliente, registro_id: $registro['inm_comprador_id'],
            pr_etapa_descripcion: 'UBICACION ASIGNADA');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_alta_etapa);
        }
        $data->alta_etapa_cliente = $r_alta_etapa;

        $modelo_etapa_ubicacion = new inm_ubicacion_etapa(link: $this->link);
        $this->key_id = 'inm_ubicacion_id';
        $r_alta_etapa = (new pr_proceso(link: $this->link))->inserta_etapa(adm_accion: $function, fecha: '',
            modelo: $this, modelo_etapa: $modelo_etapa_ubicacion, registro_id: $registro['inm_ubicacion_id'],
            pr_etapa_descripcion: 'ASIGNADO A CLIENTE');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_alta_etapa);
        }
        $data->alta_etapa_ubicacion = $r_alta_etapa;

        return $data;
    }

    private function existe_row(array $registro){
        $filtro = $this->filtro_unique(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar filtro si existe',data:  $filtro);
        }
        $existe = $this->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe);
        }
        return $existe;
    }

    private function filtro_unique(array $registro): array
    {
        $filtro['inm_ubicacion.id'] = $registro['inm_ubicacion_id'];
        $filtro['inm_comprador.id'] = $registro['inm_comprador_id'];

        return $filtro;
    }

    /**
     * Obtiene la relacion entre una ubicacion y un cliente
     * @param int $inm_comprador_id Identificador de comprador
     * @return array
     * @version 1.111.1
     */
    final public function imp_rel_ubi_comp(int $inm_comprador_id): array
    {

        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }

        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_imp_rel_ubi_comp = $this->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_imp_rel_ubi_comp',data:  $r_imp_rel_ubi_comp);
        }

        if($r_imp_rel_ubi_comp->n_registros === 0){
            return $this->error->error(
                mensaje: 'Error no existe inm_rel_ubi_comp',data:  $r_imp_rel_ubi_comp);
        }
        return $r_imp_rel_ubi_comp->registros[0];
    }

    private function imp_rel_ubi_comp_filtro(array $filtro){
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

        $data = new stdClass();
        $data->r_registro = $r_registro;
        $data->registro_puro = $registro_puro;
        $data->registro = $registro;
        return $data;
    }

    private function integra_descripcion(array $registro){
        $descripcion = $this->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar descripcion',data:  $descripcion);
        }

        $registro['descripcion'] = $descripcion;

        return $registro;
    }

    private function integra_descripcion_aut(array $registro){
        if(!isset($registro['descripcion'])){
            $registro = $this->integra_descripcion(registro: $registro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar descripcion',data:  $registro);
            }
        }
        return $registro;
    }


}