<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _conversion{

    private errores $error;
    public function __construct(){
        $this->error = new errores();
    }


    /**
     * Obtiene los datos de un prospecto
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param inm_prospecto $modelo Modelo en ejecucion
     * @return array|stdClass
     * @version 2.211.1
     */
    private function data_prospecto(int $inm_prospecto_id, inm_prospecto $modelo): array|stdClass
    {
        if($inm_prospecto_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id es menor a 0', data: $inm_prospecto_id);
        }

        $inm_prospecto = $modelo->registro(registro_id: $inm_prospecto_id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto);
        }

        $inm_prospecto_completo = $modelo->registro(registro_id: $inm_prospecto_id, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto);
        }
        $data = new stdClass();
        $data->inm_prospecto = $inm_prospecto;
        $data->inm_prospecto_completo = $inm_prospecto_completo;

        return $data;
    }
    /**
     * Campos de inicializacion
     * @param array $inm_comprador_ins comprador registro
     * @return array
     * @version 2.214.1
     */
    private function defaults_alta_comprador(array $inm_comprador_ins): array
    {
        if(!isset($inm_comprador_ins['nss'])){
            $inm_comprador_ins['nss'] = '99999999999';
        }
        if(!isset($inm_comprador_ins['curp'] )){
            $inm_comprador_ins['curp'] = 'XEXX010101MNEXXXA8';
        }
        if(!isset($inm_comprador_ins['lada_nep'] )){
            $inm_comprador_ins['lada_nep'] = '33';
        }
        if(!isset($inm_comprador_ins['numero_nep'] )){
            $inm_comprador_ins['numero_nep'] = '33333333';
        }
        if(!isset($inm_comprador_ins['nombre_empresa_patron'] )){
            $inm_comprador_ins['nombre_empresa_patron'] = 'POR DEFINIR';
        }
        if(!isset($inm_comprador_ins['nrp_nep'] )){
            $inm_comprador_ins['nrp_nep'] = 'POR DEFINIR';
        }

        if($inm_comprador_ins['nss'] === ''){
            $inm_comprador_ins['nss'] = '99999999999';
        }
        if($inm_comprador_ins['curp'] === ''){
            $inm_comprador_ins['curp'] = 'XEXX010101MNEXXXA8';
        }
        if($inm_comprador_ins['lada_nep'] === ''){
            $inm_comprador_ins['lada_nep'] = '33';
        }
        if($inm_comprador_ins['numero_nep'] === ''){
            $inm_comprador_ins['numero_nep'] = '33333333';
        }
        if($inm_comprador_ins['nombre_empresa_patron'] === ''){
            $inm_comprador_ins['nombre_empresa_patron'] = 'POR DEFINIR';
        }
        if($inm_comprador_ins['nrp_nep'] === ''){
            $inm_comprador_ins['nrp_nep'] = 'POR DEFINIR';
        }
        return $inm_comprador_ins;
    }

    /**
     * Integra los campos de un comprador para la insersion
     * @param stdClass $data Datos de prospecto
     * @param PDO $link Conexion a la base de datos
     * @return array
     * @version 2.217.1
     */
    private function inm_comprador_ins(stdClass $data, PDO $link): array
    {
        if(!isset($data->inm_prospecto)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto no existe', data: $data);
        }
        if(!is_object($data->inm_prospecto)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto debe ser un objeto', data: $data);
        }
        if(!isset($data->inm_prospecto_completo)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto_completo no existe', data: $data);
        }
        if(!is_object($data->inm_prospecto_completo)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto_completo debe ser un objeto', data: $data);
        }
        if(!isset($data->inm_prospecto_completo->com_prospecto_rfc)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto_completo->com_prospecto_rfc no existe',
                data: $data);
        }

        $keys = $this->keys_data_prospecto();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys', data: $keys);
        }

        $inm_comprador_ins = $this->inm_comprador_ins_init(data: $data,keys:  $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar inm_comprador', data: $inm_comprador_ins);
        }


        $inm_comprador_ins = $this->defaults_alta_comprador(inm_comprador_ins: $inm_comprador_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener inm_comprador_ins', data: $inm_comprador_ins);
        }

        $inm_comprador_ins = $this->integra_ids_prefs(inm_comprador_ins: $inm_comprador_ins,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
        }


        $inm_comprador_ins['rfc'] = $data->inm_prospecto_completo->com_prospecto_rfc;
        $inm_comprador_ins['numero_exterior'] = 'POR ASIGNAR';

        return $inm_comprador_ins;
    }


    /**
     * Inicializa inm_comprador en vacio
     * @param stdClass $data datos para asignacion
     * @param array $keys Keys para inicializar
     * @return array
     * @version 2.213.1
     */
    private function inm_comprador_ins_init(stdClass $data, array $keys): array
    {
        if(!isset($data->inm_prospecto)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto no existe', data: $data);
        }
        if(!is_object($data->inm_prospecto)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto debe ser un objeto', data: $data);
        }
        $inm_comprador_ins = array();

        foreach ($keys as $key){
            $key = trim($key);
            if($key === ''){
                return $this->error->error(mensaje: 'Error key esta vacio', data: $key);
            }
            if(is_numeric($key)){
                return $this->error->error(mensaje: 'Error key debe ser un texto', data: $key);
            }
            if(!isset($data->inm_prospecto->$key)){
                return $this->error->error(mensaje: 'Error no existe atributo', data: $key);
            }

            $inm_comprador_ins[$key] = $data->inm_prospecto->$key;
        }
        return $inm_comprador_ins;
    }

    /**
     * Genera un registro de relacion de prospecto
     * @param int $inm_comprador_id Comprador id
     * @param int $inm_prospecto_id Prospecto id
     * @return array
     * @version 2.219.1
     */
    private function inm_rel_prospecto_cliente_ins(int $inm_comprador_id, int $inm_prospecto_id): array
    {
        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0', data: $inm_prospecto_id);
        }
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }
        $inm_rel_prospecto_cliente_ins['inm_prospecto_id'] = $inm_prospecto_id;
        $inm_rel_prospecto_cliente_ins['inm_comprador_id'] = $inm_comprador_id;

        return $inm_rel_prospecto_cliente_ins;
    }


    private function inm_referencia(int $inm_comprador_id, stdClass $inm_referencia_prospecto): array
    {
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }

        $inm_referencia_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_referencia_ins['apellido_paterno'] = $inm_referencia_prospecto->inm_referencia_prospecto_apellido_paterno;
        $inm_referencia_ins['apellido_materno'] = $inm_referencia_prospecto->inm_referencia_prospecto_apellido_materno;
        $inm_referencia_ins['nombre'] = $inm_referencia_prospecto->inm_referencia_prospecto_nombre;
        $inm_referencia_ins['lada'] = $inm_referencia_prospecto->inm_referencia_prospecto_lada;
        $inm_referencia_ins['numero'] = $inm_referencia_prospecto->inm_referencia_prospecto_numero;
        $inm_referencia_ins['celular'] = $inm_referencia_prospecto->inm_referencia_prospecto_celular;
        $inm_referencia_ins['dp_calle_pertenece_id'] = $inm_referencia_prospecto->inm_referencia_prospecto_dp_calle_pertenece_id;
        $inm_referencia_ins['inm_parentesco_id'] = $inm_referencia_prospecto->inm_referencia_prospecto_inm_parentesco_id;
        $inm_referencia_ins['numero_dom'] = $inm_referencia_prospecto->inm_referencia_prospecto_numero_dom;

        return $inm_referencia_ins;
    }

    /**
     * Inserta un comprador
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param inm_prospecto $modelo Modelo inm_prospecto
     * @return array|stdClass
     * @version 2.218.1
     */
    final public function inserta_inm_comprador(int $inm_prospecto_id, inm_prospecto $modelo): array|stdClass
    {
        if($inm_prospecto_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id es menor a 0', data: $inm_prospecto_id);
        }

        $data = $this->data_prospecto(inm_prospecto_id: $inm_prospecto_id,modelo: $modelo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $data);
        }


        $inm_comprador_ins = $this->inm_comprador_ins(data: $data,link: $modelo->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
        }


        $r_alta_comprador = (new inm_comprador(link: $modelo->link))->alta_registro(registro: $inm_comprador_ins);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_comprador);
        }
        return $r_alta_comprador;
    }

    public function inserta_inm_prospecto(int $inm_comprador_id, inm_comprador $modelo): array|stdClass
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id es menor a 0', data: $inm_comprador_id);
        }

        $data = $this->data_comprador(inm_comprador_id: $inm_comprador_id,modelo: $modelo);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $data);
        }

        $inm_prospecto_ins = $this->inm_prospecto_ins(data: $data,link: $modelo->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_prospecto_ins);
        }

        $r_alta_prospecto = (new inm_prospecto(link: $modelo->link))->alta_registro(registro: $inm_prospecto_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_prospecto);
        }

        return $r_alta_prospecto;
    }

    private function data_comprador(int $inm_comprador_id, inm_comprador $modelo): array|stdClass
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id es menor a 0', data: $inm_comprador_id);
        }

        $inm_comprador = $modelo->registro(registro_id: $inm_comprador_id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener comprador', data: $inm_comprador);
        }

        $data = new stdClass();
        $data->inm_comprador = $inm_comprador;

        return $data;
    }

    private function inm_prospecto_ins(stdClass $data, PDO $link): array
    {
        if(!isset($data->inm_comprador)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto no existe', data: $data);
        }
        if(!is_object($data->inm_comprador)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto debe ser un objeto', data: $data);
        }

        $keys = $this->keys_data_comprador();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys', data: $keys);
        }

        $inm_prospecto_ins = $this->inm_prospecto_ins_init(data: $data,keys:  $keys);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar inm_prospecto', data: $inm_prospecto_ins);
        }

        return $inm_prospecto_ins;
    }

    private function inm_prospecto_ins_init(stdClass $data, array $keys): array
    {
        if(!isset($data->inm_comprador)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto no existe', data: $data);
        }
        if(!is_object($data->inm_comprador)){
            return $this->error->error(mensaje: 'Error $data->inm_prospecto debe ser un objeto', data: $data);
        }

        $inm_prospecto_ins = array();
        foreach ($keys as $key){
            $key = trim($key);
            if($key === ''){
                return $this->error->error(mensaje: 'Error key esta vacio', data: $key);
            }
            if(is_numeric($key)){
                return $this->error->error(mensaje: 'Error key debe ser un texto', data: $key);
            }
            if(!isset($data->inm_comprador->$key)){
                return $this->error->error(mensaje: 'Error no existe atributo', data: $key);
            }

            $inm_prospecto_ins[$key] = $data->inm_comprador->$key;
        }
        return $inm_prospecto_ins;
    }

    private function keys_data_comprador(): array
    {
        return array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'nombre','apellido_paterno','apellido_materno','con_discapacidad','nombre_empresa_patron','nrp_nep',
            'lada_nep','numero_nep','extension_nep','lada_com','numero_com','cel_com','genero','correo_com',
            'inm_tipo_discapacidad_id','inm_persona_discapacidad_id','inm_estado_civil_id',
            'inm_institucion_hipotecaria_id','inm_sindicato_id','dp_municipio_nacimiento_id','fecha_nacimiento',
            'sub_cuenta','monto_final','descuento','puntos','inm_nacionalidad_id','inm_ocupacion_id','telefono_casa',
            'correo_empresa');
    }

    /**
     * Inserta una relacion entre prospecto y cliente
     * @param int $inm_comprador_id Identificador de comprador
     * @param int $inm_prospecto_id Identificador de prospecto
     * @param PDO $link Conexion a la base de datos
     * @return array|stdClass
     * @version 2.220.1
     */
    final public function inserta_rel_prospecto_cliente(
        int $inm_comprador_id, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        if($inm_prospecto_id <= 0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0', data: $inm_prospecto_id);
        }
        if($inm_comprador_id <= 0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }

        $inm_rel_prospecto_cliente_ins = $this->inm_rel_prospecto_cliente_ins(
            inm_comprador_id: $inm_comprador_id,inm_prospecto_id:  $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion', data: $inm_rel_prospecto_cliente_ins);
        }


        $r_alta_rel = (new inm_rel_prospecto_cliente(link: $link))->alta_registro(
            registro: $inm_rel_prospecto_cliente_ins);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
        }
        return $r_alta_rel;
    }

    public function inserta_referencia(int $inm_comprador_id, int $inm_prospecto_id, PDO $link): array|stdClass
    {
        if ($inm_prospecto_id <= 0) {
            return $this->error->error(mensaje: 'Error inm_prospecto_id debe ser mayor a 0', data: $inm_prospecto_id);
        }
        if ($inm_comprador_id <= 0) {
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0', data: $inm_comprador_id);
        }

        $filtro['inm_prospecto.id'] = $inm_prospecto_id;
        $inm_referencia_prospecto = (new inm_referencia_prospecto(link: $link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_referencia_prospecto);
        }

        $r_alta_rels = array();
        if($inm_referencia_prospecto->n_registros > 0){
            foreach ($inm_referencia_prospecto->registros_obj as $registro){
                $inm_referencia_ins = $this->inm_referencia(
                    inm_comprador_id: $inm_comprador_id, inm_referencia_prospecto: $registro);
                if (errores::$error) {
                    return $this->error->error(mensaje: 'Error al insertar relacion', data: $inm_referencia_ins);
                }

                $r_alta_rel = (new inm_referencia(link: $link))->alta_registro(registro: $inm_referencia_ins);
                if(errores::$error){
                    return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
                }
                $r_alta_rels[] = $r_alta_rel;
            }
        }
        return $r_alta_rels;
    }

    /**
     * Integra un identificador de uso comun
     * @param string $entidad Entidad para obtener identificador
     * @param array $inm_comprador_ins Registro de comprador
     * @param inm_comprador|com_cliente $modelo Modelo de integracion
     * @return array
     * @version 2.215.1
     */
    private function integra_id_pref(string $entidad, array $inm_comprador_ins,
                                     inm_comprador|com_cliente $modelo): array
    {
        $entidad = trim($entidad);
        if($entidad === ''){
            return $this->error->error(mensaje: 'Error entidad esta vacia', data: $entidad);
        }
        $key_id = $entidad.'_id';
        $id_pref = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $id_pref);
        }
        $inm_comprador_ins[$key_id] = $id_pref;
        return $inm_comprador_ins;
    }

    /**
     * Integra los identificadores mas usados
     * @param array $inm_comprador_ins Registro de comprador
     * @param PDO $link Conexion a la base de datos
     * @return array
     * @version 2.216.1
     */
    private function integra_ids_prefs(array $inm_comprador_ins, PDO $link): array
    {
        $entidades_pref = array('bn_cuenta');

        $modelo_inm_comprador = new inm_comprador(link: $link);

        foreach ($entidades_pref as $entidad){
            $inm_comprador_ins = $this->integra_id_pref(entidad: $entidad, inm_comprador_ins:  $inm_comprador_ins,
                modelo: $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
            }
        }

        $entidades_pref = array('dp_calle_pertenece','cat_sat_regimen_fiscal','cat_sat_moneda',
            'cat_sat_forma_pago','cat_sat_metodo_pago','cat_sat_uso_cfdi','com_tipo_cliente','cat_sat_tipo_persona');

        $modelo_com_cliente = new com_cliente(link: $link);
        foreach ($entidades_pref as $entidad){
            $inm_comprador_ins = $this->integra_id_pref(entidad: $entidad, inm_comprador_ins:  $inm_comprador_ins,
                modelo: $modelo_com_cliente);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
            }
        }
        return $inm_comprador_ins;
    }

    /**
     * Obtiene los keys de un prospecto para integrarlos con un cliente
     * @return string[]
     * @version 2.212.1
     */
    private function keys_data_prospecto(): array
    {
        return array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'nombre','apellido_paterno','apellido_materno','con_discapacidad','nombre_empresa_patron','nrp_nep',
            'lada_nep','numero_nep','extension_nep','lada_com','numero_com','cel_com','genero','correo_com',
            'inm_tipo_discapacidad_id','inm_persona_discapacidad_id','inm_estado_civil_id',
            'inm_institucion_hipotecaria_id','inm_sindicato_id','dp_municipio_nacimiento_id','fecha_nacimiento',
            'sub_cuenta','monto_final','descuento','puntos','inm_nacionalidad_id','inm_ocupacion_id','telefono_casa',
            'correo_empresa');
    }
}
