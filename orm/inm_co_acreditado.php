<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_co_acreditado extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_co_acreditado';
        $columnas = array($tabla=>false);

        $campos_obligatorios = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','genero', 'correo','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','genero', 'correo','nombre_empresa_patron','nrp','lada_nep','numero_nep',
            'extension_nep');

        $tipo_campos['nss'] = 'nss';
        $tipo_campos['curp'] = 'curp';
        $tipo_campos['rfc'] = 'rfc';
        $tipo_campos['lada'] = 'lada';
        $tipo_campos['numero'] = 'tel_sin_lada';
        $tipo_campos['celular'] = 'telefono_mx';
        $tipo_campos['correo'] = 'correo';

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            tipo_campos: $tipo_campos, atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Co acreditado';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {

        $valida = $this->valida_data_alta(inm_co_acreditado: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inm_co_acreditado',data:  $valida);
        }


        $valida = $this->valida_alta(inm_co_acreditado: $this->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
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
        $valida = $this->valida_data_alta(inm_co_acreditado: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar inm_co_acreditado',data:  $valida);
        }

        $descripcion = (new _base_paquete())->descripcion(registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener descripcion', data: $descripcion);
        }
        return $descripcion;
    }

    /**
     * Obtiene los co acreditados de un cliente
     * @param int $inm_comprador_id  Comprador id
     * @return array
     * @version 1.113.1
     */
    final public function inm_co_acreditados(int $inm_comprador_id): array
    {
        if($inm_comprador_id<=0){
            return $this->error->error(mensaje: 'Error inm_comprador_id debe ser mayor a 0',data:  $inm_comprador_id);
        }
        $filtro = array();
        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_imp_rel_co_acred = (new inm_rel_co_acred(link: $this->link))->filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener r_imp_rel_co_acred',data:  $r_imp_rel_co_acred);
        }
        return $r_imp_rel_co_acred->registros;
    }

    /**
     * .Valida los tipos de datos
     * @param array $inm_co_acreditado Registro a validar
     * @return array|true
     * @version 2.50.0
     */
    final public function valida_alta(array $inm_co_acreditado): bool|array
    {
        $keys = array('lada','numero','celular','genero','correo','nombre_empresa_patron','nrp','lada_nep',
            'numero_nep','nss');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys_val = array('nss','curp','rfc','lada','correo');

        foreach ($keys_val as $key){
            $valida = $this->validacion->valida_pattern($key, $inm_co_acreditado[$key]);
            if(!$valida){
                return $this->error->error(mensaje: 'Error al validar '.$key,data:  $this->validacion->patterns[$key]);
            }
        }

        $valida_numero = $this->validacion->valida_pattern('tel_sin_lada', $inm_co_acreditado['numero']);
        if(!$valida_numero){
            return $this->error->error(mensaje: 'Error al validar numero',data:
                $this->validacion->patterns['tel_sin_lada']);
        }

        $valida_celular = $this->validacion->valida_pattern('telefono_mx', $inm_co_acreditado['celular']);
        if(!$valida_celular){
            return $this->error->error(mensaje: 'Error al validar celular',data:
                $this->validacion->patterns['telefono_mx']);
        }

        return true;
    }

    /**
     * Valida que los elementos base de un co acreditado sean validos
     * @param array $inm_co_acreditado Registro a validar
     * @return array|true
     * @version 2.50.0
     */
    final public function valida_data_alta(array $inm_co_acreditado): bool|array
    {
        $keys = array('nombre','apellido_paterno','nss','curp','rfc','apellido_materno');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $inm_co_acreditado,
            valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $keys = array('nombre','apellido_paterno','nss','curp','rfc');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $inm_co_acreditado);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        return true;
    }


}