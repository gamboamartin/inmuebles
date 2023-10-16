<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\proceso\models\pr_sub_proceso;
use PDO;
use stdClass;

class inm_prospecto extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_prospecto';
        $columnas = array($tabla=>false,'com_prospecto'=>$tabla,'inm_producto_infonavit'=>$tabla,
            'inm_attr_tipo_credito'=>$tabla,'inm_destino_credito'=>$tabla,'inm_plazo_credito_sc'=>$tabla,
            'inm_tipo_discapacidad'=>$tabla,'inm_persona_discapacidad'=>$tabla,'inm_estado_civil'=>$tabla,
            'inm_institucion_hipotecaria'=>$tabla,'com_agente'=>'com_prospecto','com_tipo_prospecto'=>'com_prospecto',
            'adm_usuario'=>'com_agente','dp_calle_pertenece'=>$tabla,'dp_colonia_postal'=>'dp_calle_pertenece',
            'dp_calle'=>'dp_calle_pertenece','dp_colonia'=>'dp_colonia_postal','dp_cp'=>'dp_colonia_postal',
            'dp_municipio'=>'dp_cp','dp_estado'=>'dp_municipio','dp_pais'=>'dp_estado','inm_sindicato'=>$tabla);

        $campos_obligatorios = array('com_prospecto_id','razon_social','dp_calle_pertenece_id','rfc',
            'numero_exterior','numero_interior','inm_sindicato_id','dp_municipio_nacimiento_id','fecha_nacimiento',
            'monto_final','sub_cuenta','descuento','puntos');

        $columnas_extra= array();


        $adm_usuario = (new adm_usuario(link: $link))->registro(registro_id: $_SESSION['usuario_id'],
            columnas: array('adm_grupo_root'));
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al obtener adm_usuario ',data:  $adm_usuario);
            print_r($error);
            exit;
        }

        $sql = "(SELECT IF(adm_usuario.id = $_SESSION[usuario_id], $_SESSION[usuario_id], -1))";
        if($adm_usuario['adm_grupo_root'] === 'activo'){
            $sql = $_SESSION['usuario_id'];
        }
        $columnas_extra['usuario_permitido_id'] = $sql;

        $atributos_criticos = array('com_prospecto_id','razon_social','dp_calle_pertenece_id','rfc',
            'numero_exterior','numero_interior','inm_sindicato_id','dp_municipio_nacimiento_id','observaciones',
            'fecha_nacimiento','monto_final','sub_cuenta','descuento','puntos');


        $tipo_campos= array();
        $aplica_seguridad = true;

        $renombres = array();

        $renombres = (new _base_paquete())->rename_data_nac(enlace: $tabla, renombres: $renombres);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al integrar rename', data: $renombres);
            print_r($error);
            exit;
        }

        parent::__construct(link: $link, tabla: $tabla, aplica_seguridad: $aplica_seguridad,
            campos_obligatorios: $campos_obligatorios, columnas: $columnas, columnas_extra: $columnas_extra,
            renombres: $renombres, tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Prospecto de Vivienda';
    }

    /**
     * @param array $keys_integra_ds
     * @return array|stdClass
     */
    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $keys = array('nombre','apellido_paterno','numero_com','lada_com');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $registro = (new _prospecto())->previo_alta(modelo: $this, registro: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar row',data:  $registro);
        }
        $this->registro = $registro;


        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar prospecto',data:  $r_alta_bd);
        }


        $alta_inm_prospecto_proceso = $this->inserta_sub_proceso(inm_prospecto_id: $r_alta_bd->registro_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error insertar alta_inm_prospecto_proceso',data:  $alta_inm_prospecto_proceso);
        }


        return $r_alta_bd;
    }

    final public function convierte_cliente(int $inm_prospecto_id){
        $r_alta_comprador = $this->inserta_inm_comprador(inm_prospecto_id: $inm_prospecto_id);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_comprador);
        }


        $r_alta_rel = $this->inserta_rel_prospecto_cliente(
            inm_comprador_id: $r_alta_comprador->registro_id,inm_prospecto_id:  $inm_prospecto_id);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
        }
        $data = new stdClass();
        $data->r_alta_comprador = $r_alta_comprador;
        $data->r_alta_rel = $r_alta_rel;

        return $data;
    }

    private function data_prospecto(int $inm_prospecto_id){
        $inm_prospecto = $this->registro(registro_id: $inm_prospecto_id, columnas_en_bruto: true, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto);
        }

        $inm_prospecto_completo = $this->registro(registro_id: $inm_prospecto_id, retorno_obj: true);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $inm_prospecto);
        }
        $data = new stdClass();
        $data->inm_prospecto = $inm_prospecto;
        $data->inm_prospecto_completo = $inm_prospecto_completo;

        return $data;
    }

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

    public function elimina_bd(int $id): array|stdClass
    {

        $filtro['inm_prospecto.id'] = $id;

        $del = (new inm_doc_prospecto(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_comprador_etapa',
                data:  $del);
        }

        $del = (new inm_prospecto_proceso(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_comprador_etapa',
                data:  $del);
        }

        $r_elimina = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar ',data:  $r_elimina);
        }
        return $r_elimina;
    }

    /**
     * Obtiene los datos del cliente de fc basados en el comprador
     * @param int $inm_prospecto_id
     * @param bool $retorno_obj Retorna un objeto en caso de ser true
     * @return array|object
     */
    final public function get_com_prospecto(int $inm_prospecto_id, bool $retorno_obj = false): object|array
    {
        if($inm_prospecto_id<=0){
            return $this->error->error(mensaje: 'Error inm_prospecto_id es menor a 0',data:  $inm_prospecto_id);
        }

        $com_prospecto = (new com_prospecto(link: $this->link))->registro(registro_id: $inm_prospecto_id, retorno_obj: $retorno_obj);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_prospecto',data:  $com_prospecto);
        }
        return $com_prospecto;
    }

    private function inm_comprador_ins(stdClass $data){
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

        $inm_comprador_ins = $this->integra_ids_prefs(inm_comprador_ins: $inm_comprador_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
        }


        $inm_comprador_ins['rfc'] = $data->inm_prospecto_completo->com_prospecto_rfc;
        $inm_comprador_ins['numero_exterior'] = 'POR ASIGNAR';

        return $inm_comprador_ins;
    }

    private function inm_comprador_ins_init(stdClass $data, array $keys): array
    {
        $inm_comprador_ins = array();

        foreach ($keys as $key){
            $inm_comprador_ins[$key] = $data->inm_prospecto->$key;
        }
        return $inm_comprador_ins;
    }

    private function inm_prospecto_proceso_ins(int $inm_prospecto_id, int $pr_sub_proceso_id): array
    {
        $inm_prospecto_proceso_ins['pr_sub_proceso_id'] = $pr_sub_proceso_id;
        $inm_prospecto_proceso_ins['fecha'] = date('Y-m-d');
        $inm_prospecto_proceso_ins['inm_prospecto_id'] = $inm_prospecto_id;

        return $inm_prospecto_proceso_ins;
    }

    private function inserta_sub_proceso(int $inm_prospecto_id){
        $pr_sub_proceso = $this->pr_sub_proceso();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error obtener pr_sub_proceso',data:  $pr_sub_proceso);
        }

        $inm_prospecto_proceso_ins = $this->inm_prospecto_proceso_ins(inm_prospecto_id: $inm_prospecto_id,
            pr_sub_proceso_id: $pr_sub_proceso['pr_sub_proceso_id']);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error integrar pr_sub_proceso',data:  $inm_prospecto_proceso_ins);
        }

        $alta_inm_prospecto_proceso = (new inm_prospecto_proceso(link: $this->link))->alta_registro(registro: $inm_prospecto_proceso_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error insertar alta_inm_prospecto_proceso',data:  $alta_inm_prospecto_proceso);
        }
        return $alta_inm_prospecto_proceso;
    }

    private function integra_id_pref(string $entidad, array $inm_comprador_ins, inm_comprador|com_cliente $modelo){
        $key_id = $entidad.'_id';
        $id_pref = $modelo->id_preferido_detalle(entidad_preferida: $entidad);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $id_pref);
        }
        $inm_comprador_ins[$key_id] = $id_pref;
        return $inm_comprador_ins;
    }

    private function integra_ids_prefs(array $inm_comprador_ins){
        $entidades_pref = array('bn_cuenta');

        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        foreach ($entidades_pref as $entidad){
            $inm_comprador_ins = $this->integra_id_pref(entidad: $entidad, inm_comprador_ins:  $inm_comprador_ins,
                modelo: $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
            }
        }

        $entidades_pref = array('dp_calle_pertenece','cat_sat_regimen_fiscal','cat_sat_moneda',
            'cat_sat_forma_pago','cat_sat_metodo_pago','cat_sat_uso_cfdi','com_tipo_cliente','cat_sat_tipo_persona');

        $modelo_com_cliente = new com_cliente(link: $this->link);
        foreach ($entidades_pref as $entidad){
            $inm_comprador_ins = $this->integra_id_pref(entidad: $entidad, inm_comprador_ins:  $inm_comprador_ins,
                modelo: $modelo_com_cliente);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
            }
        }
        return $inm_comprador_ins;
    }


    private function keys_data_prospecto(): array
    {
        return array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nss','curp',
            'nombre','apellido_paterno','apellido_materno','con_discapacidad','nombre_empresa_patron','nrp_nep',
            'lada_nep','numero_nep','extension_nep','lada_com','numero_com','cel_com','genero','correo_com',
            'inm_tipo_discapacidad_id','inm_persona_discapacidad_id','inm_estado_civil_id',
            'inm_institucion_hipotecaria_id','inm_sindicato_id','dp_municipio_nacimiento_id','fecha_nacimiento',
            'sub_cuenta','monto_final','descuento','puntos');
    }

    private function inm_rel_prospecto_cliente_ins(int $inm_comprador_id, int $inm_prospecto_id): array
    {
        $inm_rel_prospecto_cliente_ins['inm_prospecto_id'] = $inm_prospecto_id;
        $inm_rel_prospecto_cliente_ins['inm_comprador_id'] = $inm_comprador_id;

        return $inm_rel_prospecto_cliente_ins;
    }

    private function inserta_inm_comprador(int $inm_prospecto_id){
        $data = $this->data_prospecto(inm_prospecto_id: $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener prospecto', data: $data);
        }


        $inm_comprador_ins = $this->inm_comprador_ins(data: $data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id_pref', data: $inm_comprador_ins);
        }

        $r_alta_comprador = (new inm_comprador(link: $this->link))->alta_registro(registro: $inm_comprador_ins);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_alta_comprador);
        }
        return $r_alta_comprador;
    }

    private function inserta_rel_prospecto_cliente(int $inm_comprador_id, int $inm_prospecto_id){
        $inm_rel_prospecto_cliente_ins = $this->inm_rel_prospecto_cliente_ins(
            inm_comprador_id: $inm_comprador_id,inm_prospecto_id:  $inm_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion', data: $inm_rel_prospecto_cliente_ins);
        }


        $r_alta_rel = (new inm_rel_prospecto_cliente(link: $this->link))->alta_registro(registro: $inm_rel_prospecto_cliente_ins);

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar inm_rel_prospecto_cliente_ins', data: $r_alta_rel);
        }
        return $r_alta_rel;
    }



    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {


        $r_modifica =  parent::modifica_bd(registro: $registro,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $r_modifica);
        }

        if(!isset($r_modifica->registro_actualizado->com_prospecto_rfc)){
            return $this->error->error(mensaje: 'Error al modificar prospecto no existe rfc en com_prospecto',
                data:  $r_modifica);
        }

        $registro = $r_modifica->registro_puro;
        $registro->rfc = $r_modifica->registro_actualizado->com_prospecto_rfc;

        if($registro->nss === ''){
            $registro->nss = '99999999999';
        }
        if($registro->curp === ''){
            $registro->curp = 'XEXX010101HNEXXXA4';
        }

        $descripcion = (new _base_paquete())->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
        }

        $registro_ds['descripcion'] = $descripcion;
        $r_modifica_descripcion =  parent::modifica_bd(registro: $registro_ds,id:  $id,reactiva:  $reactiva,
            keys_integra_ds:  $keys_integra_ds); // TODO: Change the autogenerated stub

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $r_modifica_descripcion);
        }

        $data_com_prospecto['nombre'] = $registro->nombre;
        $data_com_prospecto['apellido_paterno'] = $registro->apellido_paterno;
        $data_com_prospecto['apellido_materno'] = $registro->apellido_materno;
        $data_com_prospecto['telefono'] = $registro->lada_com.$registro->numero_com;
        $data_com_prospecto['correo'] = $registro->correo_com;
        $data_com_prospecto['razon_social'] = $registro->razon_social;

        $upd = (new com_prospecto(link: $this->link))->modifica_bd(registro: $data_com_prospecto,
            id:  $registro->com_prospecto_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar prospecto',data:  $upd);
        }


        return $r_modifica;
    }

    private function pr_sub_proceso(){
        $filtro = array();
        $filtro['pr_sub_proceso.descripcion'] = 'ALTA PROSPECTO';
        $filtro['adm_seccion.descripcion'] = $this->tabla;

        $r_pr_sub_proceso = (new pr_sub_proceso(link: $this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error obtener r_pr_sub_proceso',data:  $r_pr_sub_proceso);
        }

        if($r_pr_sub_proceso->n_registros === 0){
            return $this->error->error(mensaje: 'Error no existe sub proceso definido',data:  $filtro);
        }

        if($r_pr_sub_proceso->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad',data:  $r_pr_sub_proceso);
        }

        return $r_pr_sub_proceso->registros[0];
    }




}