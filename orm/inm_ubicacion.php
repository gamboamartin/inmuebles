<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_proceso;
use PDO;
use stdClass;


class inm_ubicacion extends _inm_ubicaciones {

    private _modelo_parent $modelo_etapa;
    public function __construct(PDO $link)
    {
        $tabla = 'inm_ubicacion';
        $columnas = array($tabla=>false,'dp_calle_pertenece'=>$tabla,'dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_cp'=>'dp_colonia_postal','dp_colonia'=>'dp_colonia_postal','dp_municipio'=>'dp_cp',
            'dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado','dp_calle'=>'dp_calle_pertenece',
            'inm_tipo_ubicacion'=>$tabla);

        $campos_obligatorios = array('dp_calle_pertenece_id','cuenta_predial','inm_tipo_ubicacion_id');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('manzana','lote','dp_calle_pertenece_id','etapa','cuenta_predial',
            'inm_tipo_ubicacion_id','n_opiniones_valor','monto_opinion_promedio','costo');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Ubicaciones';

        $this->modelo_etapa = new inm_ubicacion_etapa(link: $this->link);

    }

    /**
     * Inserta una ubicacion
     * @param array $keys_integra_ds Keys par ala generacion de la descripcion select
     * @return array|stdClass
     * @version 2.105.0
     */
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $valida = $this->valida_row(registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida $registro',data:  $valida);
        }
        $keys = array('cuenta_predial');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        $keys = array('inm_tipo_ubicacion_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar ubicacion',data:  $r_alta_bd);
        }

        $r_alta_etapa = (new pr_proceso(link: $this->link))->inserta_etapa(adm_accion: __FUNCTION__, fecha: '',
            modelo: $this, modelo_etapa: $this->modelo_etapa, registro_id: $r_alta_bd->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar etapa', data: $r_alta_etapa);
        }

        $regenera = $this->regenera_datas(inm_ubicacion_id: $r_alta_bd->registro_puro->id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }


        return $r_alta_bd;
    }

    /**
     * Asigna un precio de venta a un registro de ubicacion
     * @param int $indice Indice de arreglo de ubicaciones
     * @param stdClass $inm_comprador Datos de comprador
     * @param array $inm_ubicacion datos de ubicacion
     * @param array $inm_ubicaciones Conjunto de ubicaciones
     * @return array
     */
    private function asigna_precio_venta(int $indice, stdClass $inm_comprador, array $inm_ubicacion,
                                         array $inm_ubicaciones): array
    {
        $inm_precio_precio_venta = $this->inm_precio_venta(inm_comprador: $inm_comprador,
            inm_ubicacion: $inm_ubicacion);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_precio_precio_venta',
                data: $inm_precio_precio_venta);
        }
        $inm_ubicaciones[$indice]['inm_ubicacion_precio'] = round($inm_precio_precio_venta,2);
        return $inm_ubicaciones;
    }

    /**
     * @param stdClass $inm_comprador
     * @param stdClass $r_inm_ubicacion
     * @return array
     */
    private function asigna_precios(stdClass $inm_comprador,stdClass $r_inm_ubicacion): array
    {
        $inm_ubicaciones = $r_inm_ubicacion->registros;

        foreach ($inm_ubicaciones as $indice=>$inm_ubicacion){
            $inm_ubicaciones = $this->asigna_precio_venta(indice: $indice,inm_comprador:  $inm_comprador,
                inm_ubicacion:  $inm_ubicacion,inm_ubicaciones:  $inm_ubicaciones);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar precio de venta inm_ubicaciones',data: $inm_ubicaciones);
            }
        }
        return $inm_ubicaciones;
    }

    private function data_ubicaciones(string $etapa, int $inm_comprador_id, bool $todas){
        $inm_comprador = (new inm_comprador(link: $this->link))->registro(registro_id: $inm_comprador_id,
            retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener comprador',data: $inm_comprador);
        }

        $r_inm_ubicacion = $this->r_inm_ubicacion(etapa: $etapa,todas:  $todas);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener ubicaciones', data: $r_inm_ubicacion);
        }
        $data = new stdClass();
        $data->inm_comprador = $inm_comprador;
        $data->r_inm_ubicacion = $r_inm_ubicacion;
        return $data;
    }

    /**
     * Genera la descripcion de una ubicacion
     * @param string $key_entidad_base_id Key de entidad base = inm_ubicacion_id
     * @param string $key_entidad_id Key de entidad proceso No aplica aqui
     * @param array $registro Registro en proceso
     * @param stdClass $dp_calle_pertenece Datos de calle
     * @return string|array
     * @version 2.106.0
     */
    final protected function descripcion(string $key_entidad_base_id, string $key_entidad_id, array $registro,
                                         stdClass $dp_calle_pertenece = new stdClass()): string|array
    {
        $keys = array('dp_pais_descripcion','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_colonia_descripcion','dp_cp_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $dp_calle_pertenece);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida dp_calle_pertenece',data:  $valida);
        }

        $keys = array('manzana','lote','numero_exterior','numero_interior');

        foreach ($keys as $key){
            if(!isset($registro[$key])){
                $registro[$key] = '';
            }
        }
        $descripcion = $dp_calle_pertenece->dp_pais_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_estado_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_municipio_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_colonia_descripcion;
        $descripcion .= ' '.$dp_calle_pertenece->dp_cp_descripcion;
        $descripcion .= ' '.$registro['manzana'].' '.$registro['lote'];
        $descripcion .= ' '.$registro['numero_exterior'].' '.$registro['numero_interior'];
        return trim($descripcion);
    }


    /**
     * Elimina una ubicacion junto con sus dependencias inm_ubicacion_etapa,inm_costo,inm_opinion_valor,inm_precio,
     * inm_rel_ubi_comp
     * @param int $id Id de ubicacion a eliminar
     * @return array|stdClass
     * @version 2.106.0
     */
    public function elimina_bd(int $id): array|stdClass
    {
        if($id <= 0){
            return $this->error->error(mensaje: 'Error id es menor a 0',data:  $id);
        }
        $filtro['inm_ubicacion.id'] = $id;
        $del = (new inm_ubicacion_etapa(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_ubicacion_etapa',data:  $del);
        }
        $del = (new inm_costo(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_costo',data:  $del);
        }
        $del = (new inm_opinion_valor(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_opinion_valor',data:  $del);
        }
        $del = (new inm_precio(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_precio',data:  $del);
        }
        $del = (new inm_rel_ubi_comp(link: $this->link))->elimina_con_filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_ubi_comp',data:  $del);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar',data:  $r_elimina_bd);
        }


        return $r_elimina_bd;
    }

    /**
     * Obtiene el costo origen
     * @param int $inm_ubicacion_id Ubicacion id
     * @return array|float
     * @version 2.103.0
     */
    final public function get_costo(int $inm_ubicacion_id): float|array
    {
        if($inm_ubicacion_id<=0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0', data: $inm_ubicacion_id);
        }
        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;

        $campos['costo'] = 'inm_costo.monto';

        $r_inm_costo = (new inm_costo(link: $this->link))->suma(campos: $campos,filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener costo', data: $r_inm_costo);
        }

        return round($r_inm_costo['costo'],2);

    }


    /**
     * Inicializa un registro para su alta
     * @param string $key_entidad_base_id Entidad base inm_ubicacion
     * @param string $key_entidad_id Entidad de avance
     * @param array $registro Registro en proceso
     * @return array
     * @version 2.107.0
     */
    final public function init_row(string $key_entidad_base_id,string $key_entidad_id, array $registro):array{

        $valida = $this->valida_row(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida $registro',data:  $valida);
        }

        if(!isset($registro['manzana'])){
            $registro['manzana'] = '';
        }
        if(!isset($registro['lote'])){
            $registro['lote'] = '';
        }


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

    /**
     * Obtiene el precio de venta de una ubicacion
     * @param stdClass $inm_comprador Comprador
     * @param array $inm_ubicacion Ubicacion
     * @return array|float|int
     * @version 2.118.0
     */
    private function inm_precio_venta(stdClass $inm_comprador, array $inm_ubicacion): float|array|int
    {
        $keys = array('inm_ubicacion_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $inm_ubicacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar ubicacion',data: $valida);
        }

        $keys = array('inm_institucion_hipotecaria_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al al validar inm_comprador',data: $valida);
        }

        $inm_precio = (new inm_precio(link: $this->link))->precio(fecha: date('Y-m-d'),
            inm_ubicacion_id:  $inm_ubicacion['inm_ubicacion_id'],
            inm_institucion_hipotecaria_id:  $inm_comprador->inm_institucion_hipotecaria_id,valida_existe: false);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_precio',data: $inm_precio);
        }

        $inm_precio_precio_venta = 0.0;

        if(isset($inm_precio->inm_precio_precio_venta)){
            $inm_precio_precio_venta = round($inm_precio->inm_precio_precio_venta,2);
        }
        return $inm_precio_precio_venta;
    }

    /**
     * Integra una descripcion al dar de alta registro
     * @param stdClass $dp_calle_pertenece Registro de domicilio
     * @param array $registro registro en proceso
     * @return array
     * @version 2.106.0
     */
    private function integra_descripcion(stdClass $dp_calle_pertenece, array $registro): array
    {
        $keys = array('dp_pais_descripcion','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_colonia_descripcion','dp_cp_descripcion');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $dp_calle_pertenece);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida dp_calle_pertenece',data:  $valida);
        }

        if(!isset($registro['numero_interior'])){
            $registro['numero_interior'] = '';
        }

        $descripcion = $this->descripcion(key_entidad_base_id: $this->key_id, key_entidad_id: '',
            registro: $registro, dp_calle_pertenece: $dp_calle_pertenece);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
        }


        $registro['descripcion'] = $descripcion;
        return $registro;
    }

    /**
     * Modifica un registro de tipo ubicacion
     * @param array $registro Registro en proceso de modificacion
     * @param int $id Ide de ubicacion
     * @param bool $reactiva Si reactiva valida elementos de etapa
     * @param array $keys_integra_ds Keys para integracion de una descripcion select
     * @return array|stdClass
     * @version 2.108.0
     */
    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        if($id<=0){
            return $this->error->error(mensaje: 'Error id debe ser mayor a 0',data: $registro);
        }

        $r_modifica_bd =  parent::modifica_bd(registro: $registro,id:  $id,
            reactiva:  $reactiva,keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar inm_ubicacion', data: $r_modifica_bd);
        }

        $regenera = $this->regenera_datas(inm_ubicacion_id: $r_modifica_bd->registro_actualizado->inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera);
        }

        return $r_modifica_bd;
    }

    /**
     * Obtiene el monto de opiniones promedio
     * @param int $inm_ubicacion_id Ubicacion id
     * @return array|float
     * @version 2.97.0
     */
    final public function monto_opinion_promedio(int $inm_ubicacion_id): float|array
    {
        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0',data: $inm_ubicacion_id);
        }

        $n_opiniones = $this->n_opiniones_valor(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener n_opiniones',data: $n_opiniones);
        }

        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;

        $campos['total_montos'] = 'inm_opinion_valor.monto_resultado';
        $r_inm_opinion_valor = (new inm_opinion_valor(link: $this->link))->suma(campos: $campos,filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener montos',data: $r_inm_opinion_valor);
        }

        $total_montos = 0;
        if($n_opiniones > 0){
            $total_montos = round($r_inm_opinion_valor['total_montos'],2) / $n_opiniones;
        }

        return round($total_montos ,2);
    }

    /**
     * Obtiene el numero de opiniones de valor generadas en una ubicacion
     * @param int $inm_ubicacion_id Ubicacion Id
     * @return array|int
     * @version 2.96.0
     */
    private function n_opiniones_valor(int $inm_ubicacion_id): int|array
    {
        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0',data: $inm_ubicacion_id);
        }

        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;
        $n_opiniones = (new inm_opinion_valor(link: $this->link))->cuenta(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener n_opiniones',data: $n_opiniones);
        }
        return $n_opiniones;
    }

    /**
     * Obtiene las opiniones de valor de una ubicacion
     * @param int $inm_ubicacion_id Identificador de ubicacion
     * @return array
     * @version 2.109.0
     */
    final public function opiniones_valor(int $inm_ubicacion_id): array
    {
        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0',data: $inm_ubicacion_id);
        }
        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;
        $r_inm_opinion_valor = (new inm_opinion_valor(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener opiniones',data: $r_inm_opinion_valor);
        }
        return $r_inm_opinion_valor->registros;
    }

    /**
     * @param string $etapa
     * @param bool $todas
     * @return array|stdClass
     */
    private function r_inm_ubicacion(string $etapa, bool $todas): array|stdClass
    {
        $filtro['inm_ubicacion.etapa'] = $etapa;

        if(!$todas) {
            $r_inm_ubicacion = $this->filtro_and(filtro: $filtro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener ubicaciones', data: $r_inm_ubicacion);
            }
        }
        else{
            $r_inm_ubicacion_data = $this->registros_activos();
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener ubicaciones', data: $r_inm_ubicacion_data);
            }

            $r_inm_ubicacion = new stdClass();
            $r_inm_ubicacion->registros = $r_inm_ubicacion_data;
        }
        return $r_inm_ubicacion;
    }

    /**
     * Regenera el costo de una ubicacion
     * @param int $inm_ubicacion_id Ubicacion en proceso
     * @return array|stdClass
     * @version 2.103.0
     */
    private function regenera_costo(int $inm_ubicacion_id): array|stdClass
    {
        if($inm_ubicacion_id<=0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0', data: $inm_ubicacion_id);
        }

        $costo = $this->get_costo(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener costo',data: $costo);
        }
        $inm_ubicacion_upd['costo'] = $costo;

        $upd = parent::modifica_bd(registro: $inm_ubicacion_upd,id:  $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar n_opiniones',data: $upd);
        }
        return $upd;

    }

    /**
     * Regenera los datos de opiniones de valor
     * @param int $inm_ubicacion_id Ubicacion id
     * @return array|stdClass
     * @version 2.102.0
     */
    private function regenera_data_opinion(int $inm_ubicacion_id): array|stdClass
    {
        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0',data: $inm_ubicacion_id);
        }

        $n_opiniones = $this->n_opiniones_valor(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener n_opiniones',data: $n_opiniones);
        }
        $inm_ubicacion_upd['n_opiniones_valor'] = $n_opiniones;
        $promedio = $this->monto_opinion_promedio(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener promedio',data: $promedio);
        }
        $inm_ubicacion_upd['monto_opinion_promedio'] = $promedio;

        $upd = parent::modifica_bd(registro: $inm_ubicacion_upd,id:  $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al actualizar n_opiniones',data: $upd);
        }
        return $upd;

    }

    /**
     * Regenera el costo y los valor promedio de opinion
     * @param int $inm_ubicacion_id Ubicacion en proceso
     * @return array|stdClass
     * @version 2.104.0
     */
    final public function regenera_datas(int $inm_ubicacion_id): array|stdClass
    {

        if($inm_ubicacion_id <= 0){
            return $this->error->error(mensaje: 'Error inm_ubicacion_id es menor a 0',data: $inm_ubicacion_id);
        }

        $regenera_op = $this->regenera_data_opinion(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar opinion de valor', data: $regenera_op);
        }
        $regenera_costo = $this->regenera_costo(inm_ubicacion_id: $inm_ubicacion_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al regenerar costo', data: $regenera_costo);
        }
        $data = new stdClass();
        $data->regenera_op = $regenera_op;
        $data->regenera_costo = $regenera_costo;

        return $data;

    }

    final public function ubicaciones_con_precio(string $etapa, int $inm_comprador_id, bool $todas = false){

        $data = $this->data_ubicaciones(etapa: $etapa,inm_comprador_id:  $inm_comprador_id,todas:  $todas);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener data de ubicaciones', data: $data);
        }
        $inm_ubicaciones = $this->asigna_precios(inm_comprador: $data->inm_comprador,r_inm_ubicacion:  $data->r_inm_ubicacion);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al asignar precio de venta inm_ubicaciones',data: $inm_ubicaciones);
        }

        return $inm_ubicaciones;

    }


}