<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_precio extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_precio';
        $columnas = array($tabla=>false,'inm_ubicacion'=>$tabla,'inm_tipo_ubicacion'=>'inm_ubicacion',
            'dp_calle_pertenece'=>'inm_ubicacion','dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_cp'=>'dp_colonia_postal','dp_municipio'=>'dp_cp','dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado',
            'dp_calle'=>'dp_calle_pertenece','dp_colonia'=>'dp_colonia_postal','inm_institucion_hipotecaria'=>$tabla);

        $campos_obligatorios = array('precio_venta','porcentaje_descuento_maximo','porcentaje_comisiones_maximo',
            'monto_descuento_maximo','monto_comisiones_maximo','fecha_inicial','fecha_final',
            'porcentaje_devolucion_maximo','monto_devolucion_maximo','inm_ubicacion_id',
            'inm_institucion_hipotecaria_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('precio_venta','porcentaje_descuento_maximo','porcentaje_comisiones_maximo',
            'monto_descuento_maximo','monto_comisiones_maximo','fecha_inicial','fecha_final',
            'porcentaje_devolucion_maximo','monto_devolucion_maximo','inm_ubicacion_id',
            'inm_institucion_hipotecaria_id');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Precios de Ubicacion';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if(!isset($this->registro['porcentaje_descuento_maximo'])){
            $this->registro['porcentaje_descuento_maximo'] = 0;
        }
        if(!isset($this->registro['monto_descuento_maximo'])){
            $this->registro['monto_descuento_maximo'] = 0;
        }
        if(!isset($this->registro['porcentaje_comisiones_maximo'])){
            $this->registro['porcentaje_comisiones_maximo'] = 0;
        }
        if(!isset($this->registro['monto_comisiones_maximo'])){
            $this->registro['monto_comisiones_maximo'] = 0;
        }
        if(!isset($this->registro['porcentaje_devolucion_maximo'])){
            $this->registro['porcentaje_devolucion_maximo'] = 0;
        }
        if(!isset($this->registro['monto_devolucion_maximo'])){
            $this->registro['monto_devolucion_maximo'] = 0;
        }

        $keys = array('inm_ubicacion_id','precio_venta','fecha_inicial','fecha_final','inm_institucion_hipotecaria_id');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }


        $keys = array('inm_ubicacion_id','inm_institucion_hipotecaria_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }

        $keys = array('porcentaje_descuento_maximo','monto_descuento_maximo','porcentaje_comisiones_maximo',
            'monto_comisiones_maximo','porcentaje_devolucion_maximo','monto_devolucion_maximo','precio_venta');
        $valida = $this->validacion->valida_double_mayores_igual_0(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }

        $keys = array('precio_venta');
        $valida = $this->validacion->valida_double_mayores_0(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }

        $valida = $this->validacion->valida_rango_fecha($this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida registro',data:  $valida);
        }


        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->registro['inm_ubicacion_id'];
            $descripcion .= " ".$this->registro['precio_venta'];
            $descripcion .= " ".$this->registro['fecha_inicial'];
            $descripcion .= " ".$this->registro['fecha_final'];
            $descripcion .= " ".$this->registro['inm_institucion_hipotecaria_id'];
            $this->registro['descripcion'] = $descripcion;
        }



        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }
        return $r_alta_bd;
    }

    /**
     * Genera un filtro base para la obtencion de un precio
     * @param int $inm_institucion_hipotecaria_id Institucion id
     * @param int $inm_ubicacion_id Ubicacion id
     * @return array
     */
    private function filtro_base(int $inm_institucion_hipotecaria_id,int $inm_ubicacion_id): array
    {
        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;
        $filtro['inm_institucion_hipotecaria.id'] = $inm_institucion_hipotecaria_id;
        return $filtro;
    }

    /**
     * @param string $fecha
     * @return array
     */
    private function filtro_fecha(string $fecha): array
    {
        $filtro_fecha[0]['campo_1'] = 'inm_precio.fecha_inicial';
        $filtro_fecha[0]['campo_2'] = 'inm_precio.fecha_final';
        $filtro_fecha[0]['fecha'] = $fecha;

        return $filtro_fecha;
    }

    /**
     * @param string $fecha
     * @param int $inm_institucion_hipotecaria_id
     * @param int $inm_ubicacion_id
     * @return array|stdClass
     */
    private function filtros(string $fecha, int $inm_institucion_hipotecaria_id, int $inm_ubicacion_id): array|stdClass
    {
        $filtro = $this->filtro_base(inm_institucion_hipotecaria_id: $inm_institucion_hipotecaria_id,
            inm_ubicacion_id:  $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro',data:  $filtro);
        }

        $filtro_fecha = $this->filtro_fecha(fecha: $fecha);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro de fecha',data:  $filtro_fecha);
        }

        $data = new stdClass();
        $data->filtro = $filtro;
        $data->filtro_fecha = $filtro_fecha;
        return $data;
    }

    /**
     * @param string $fecha
     * @param int $inm_institucion_hipotecaria_id
     * @param int $inm_ubicacion_id
     * @param bool $valida_existe
     * @return array
     */
    private function inm_precio_result(string $fecha, int $inm_institucion_hipotecaria_id, int $inm_ubicacion_id, bool $valida_existe): array
    {
        $r_inm_precio = $this->r_inm_precio_by_fecha(fecha: $fecha,
            inm_institucion_hipotecaria_id:  $inm_institucion_hipotecaria_id,inm_ubicacion_id:  $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $r_inm_precio);
        }

        $valida_existe_r = $this->valida_existe(r_inm_precio: $r_inm_precio,valida_existe:  $valida_existe);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $valida_existe_r);
        }

        if($r_inm_precio->n_registros === 0){
            $r_inm_precio->registros[0] = array();
        }

        return $r_inm_precio->registros[0];
    }

    /**
     * @param string $fecha
     * @param int $inm_ubicacion_id
     * @param int $inm_institucion_hipotecaria_id
     * @param bool $retorno_obj
     * @param bool $valida_existe
     * @return array|stdClass
     */
    final public function precio(string $fecha, int $inm_ubicacion_id, int $inm_institucion_hipotecaria_id,
                                 bool $retorno_obj = true, bool $valida_existe = true): array|stdClass
    {

        $valida = $this->valida_get_precio(fecha: $fecha,
            inm_institucion_hipotecaria_id: $inm_institucion_hipotecaria_id, inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar datos',
                data:  $valida);
        }

        $inm_precio = $this->inm_precio_result(fecha: $fecha,
            inm_institucion_hipotecaria_id:  $inm_institucion_hipotecaria_id,inm_ubicacion_id:  $inm_ubicacion_id,
            valida_existe:  $valida_existe);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $inm_precio);
        }

        if($retorno_obj){
            $inm_precio = (object)$inm_precio;
        }

        return $inm_precio;

    }

    /**
     * @param string $fecha
     * @param int $inm_institucion_hipotecaria_id
     * @param int $inm_ubicacion_id
     * @return array|stdClass
     */
    private function r_inm_precio_by_fecha(string $fecha, int $inm_institucion_hipotecaria_id, int $inm_ubicacion_id): array|stdClass
    {
        $filtros = $this->filtros(fecha: $fecha,inm_institucion_hipotecaria_id:  $inm_institucion_hipotecaria_id,
            inm_ubicacion_id:  $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtros',data:  $filtros);
        }

        $r_inm_precio = $this->filtro_and(filtro: $filtros->filtro,filtro_fecha: $filtros->filtro_fecha);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener precio',data:  $r_inm_precio);
        }
        return $r_inm_precio;
    }

    /**
     * @param stdClass $r_inm_precio
     * @param bool $valida_existe
     * @return bool|array
     */
    private function valida_existe(stdClass $r_inm_precio, bool $valida_existe): bool|array
    {
        if($valida_existe) {
            if ($r_inm_precio->n_registros === 0) {
                return $this->error->error(mensaje: 'Error no existe precio configurado', data: $r_inm_precio);
            }
            if ($r_inm_precio->n_registros > 1) {
                return $this->error->error(mensaje: 'Error existe mas de un precio configurado', data: $r_inm_precio);
            }
        }
        return true;
    }

    /**
     * Valida que los elementos para calculo de un precio sean validos
     * @param string $fecha Fecha de obtencion
     * @param int $inm_institucion_hipotecaria_id Id institucion
     * @param int $inm_ubicacion_id Id de ubicacion
     * @return bool|array
     * @version 2.112.0
     */
    private function valida_get_precio(string $fecha, int $inm_institucion_hipotecaria_id,
                                       int $inm_ubicacion_id): bool|array
    {
        $fecha = trim($fecha);
        if($fecha === ''){
            return $this->error->error(mensaje: 'Error fecha esta vacio',data:  $fecha);
        }
        $fecha = $this->validacion->valida_fecha(fecha: $fecha);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar fecha',data:  $fecha);
        }

        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id debe ser mayor a 0',data:  $inm_ubicacion_id);
        }

        if($inm_institucion_hipotecaria_id <= 0){
            return $this->error->error(mensaje: 'Error inm_institucion_hipotecaria_id debe ser mayor a 0',
                data:  $inm_institucion_hipotecaria_id);
        }

        return true;
    }


}