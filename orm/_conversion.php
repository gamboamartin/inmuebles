<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
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
     */
    private function defaults_alta_comprador(array $inm_comprador_ins): array
    {
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
     * @param stdClass $data
     * @param PDO $link
     * @return array
     */
    private function inm_comprador_ins(stdClass $data, PDO $link): array
    {
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
     */
    private function inm_comprador_ins_init(stdClass $data, array $keys): array
    {
        $inm_comprador_ins = array();

        foreach ($keys as $key){
            $inm_comprador_ins[$key] = $data->inm_prospecto->$key;
        }
        return $inm_comprador_ins;
    }

    private function inm_rel_prospecto_cliente_ins(int $inm_comprador_id, int $inm_prospecto_id): array
    {
        $inm_rel_prospecto_cliente_ins['inm_prospecto_id'] = $inm_prospecto_id;
        $inm_rel_prospecto_cliente_ins['inm_comprador_id'] = $inm_comprador_id;

        return $inm_rel_prospecto_cliente_ins;
    }

    final public function inserta_inm_comprador(int $inm_prospecto_id, inm_prospecto $modelo){
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

    final public function inserta_rel_prospecto_cliente(int $inm_comprador_id, int $inm_prospecto_id, PDO $link){
        $inm_rel_prospecto_cliente_ins = $this->inm_rel_prospecto_cliente_ins(
            inm_comprador_id: $inm_comprador_id,inm_prospecto_id:  $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion', data: $inm_rel_prospecto_cliente_ins);
        }


        $r_alta_rel = (new inm_rel_prospecto_cliente(link: $link))->alta_registro(registro: $inm_rel_prospecto_cliente_ins);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
        }
        return $r_alta_rel;
    }

    /**
     * @param string $entidad
     * @param array $inm_comprador_ins
     * @param inm_comprador|com_cliente $modelo
     * @return array
     */
    private function integra_id_pref(string $entidad, array $inm_comprador_ins, inm_comprador|com_cliente $modelo): array
    {
        $key_id = $entidad.'_id';
        $id_pref = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $id_pref);
        }
        $inm_comprador_ins[$key_id] = $id_pref;
        return $inm_comprador_ins;
    }

    /**
     * @param array $inm_comprador_ins
     * @param PDO $link
     * @return array
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
            'sub_cuenta','monto_final','descuento','puntos','inm_nacionalidad_id');
    }
}
