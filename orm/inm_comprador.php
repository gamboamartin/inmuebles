<?php

namespace gamboamartin\inmuebles\models;

use base\orm\_modelo_parent;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use PDO;
use stdClass;


class inm_comprador extends _modelo_parent{
    public function __construct(PDO $link)
    {
        $tabla = 'inm_comprador';
        $columnas = array($tabla=>false,'inm_producto_infonavit'=>$tabla,'inm_attr_tipo_credito'=>$tabla,
            'inm_tipo_credito'=>'inm_attr_tipo_credito','inm_destino_credito'=>$tabla,'inm_plazo_credito_sc'=>$tabla);

        $campos_obligatorios = array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nombre',
            'apellido_paterno');

        $columnas_extra= array();
        $renombres= array();

        $atributos_criticos = array('inm_producto_infonavit_id','inm_attr_tipo_credito_id','inm_destino_credito_id',
            'es_segundo_credito','inm_plazo_credito_sc_id','descuento_pension_alimenticia_dh',
            'descuento_pension_alimenticia_fc','monto_credito_solicitado_dh','monto_ahorro_voluntario','nombre',
            'apellido_paterno','apellido_materno');

        parent::__construct(link: $link, tabla: $tabla, campos_obligatorios: $campos_obligatorios,
            columnas: $columnas, columnas_extra: $columnas_extra, renombres: $renombres,
            atributos_criticos: $atributos_criticos);

        $this->NAMESPACE = __NAMESPACE__;
        $this->etiqueta = 'Comprador de Vivienda';
    }

    public function alta_bd(array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        $registro_entrada = $this->registro;

        if(!isset($this->registro['descripcion'])){
            $descripcion = $this->descripcion(registro: $this->registro );
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener descripcion',data:  $descripcion);
            }

            $this->registro['descripcion'] = $descripcion;
        }
        if(!isset($this->registro['inm_plazo_credito_sc_id'])){
            $this->registro['inm_plazo_credito_sc_id'] = 7;
        }

        $r_alta_bd = parent::alta_bd(keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar',data:  $r_alta_bd);
        }

        $filtro['com_cliente.rfc'] = $registro_entrada['rfc'];
        $existe_cliente = (new com_cliente(link: $this->link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe_cliente);
        }

        if(!$existe_cliente) {

            $razon_social = $this->razon_social(registro_entrada: $registro_entrada);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al generar razon social',data:  $razon_social);
            }

            $com_cliente_ins['razon_social'] = trim($razon_social);
            $com_cliente_ins['rfc'] = $registro_entrada['rfc'];
            $com_cliente_ins['dp_calle_pertenece_id'] = $registro_entrada['dp_calle_pertenece_id'];
            $com_cliente_ins['numero_exterior'] = $registro_entrada['numero_exterior'];
            $com_cliente_ins['numero_interior'] = $registro_entrada['numero_interior'];
            $com_cliente_ins['telefono'] = $registro_entrada['telefono'];
            $com_cliente_ins['cat_sat_regimen_fiscal_id'] = $registro_entrada['cat_sat_regimen_fiscal_id'];
            $com_cliente_ins['cat_sat_moneda_id'] = $registro_entrada['cat_sat_moneda_id'];
            $com_cliente_ins['cat_sat_forma_pago_id'] = $registro_entrada['cat_sat_forma_pago_id'];
            $com_cliente_ins['cat_sat_metodo_pago_id'] = $registro_entrada['cat_sat_metodo_pago_id'];
            $com_cliente_ins['cat_sat_uso_cfdi_id'] = $registro_entrada['cat_sat_uso_cfdi_id'];
            $com_cliente_ins['cat_sat_tipo_de_comprobante_id'] = 1;
            $com_cliente_ins['codigo'] = $registro_entrada['rfc'];
            $com_cliente_ins['com_tipo_cliente_id'] = $registro_entrada['com_tipo_cliente_id'];
            $com_cliente_ins['cat_sat_tipo_persona_id'] = $registro_entrada['cat_sat_tipo_persona_id'];

            $r_com_cliente = (new com_cliente(link: $this->link))->alta_registro(registro: $com_cliente_ins);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
            }
        }
        else{
            $r_com_cliente_f = (new com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente_f);
            }
            if($r_com_cliente_f->n_registros === 0){
                return $this->error->error(mensaje: 'Error no existe cliente', data: $r_com_cliente_f);
            }
            if($r_com_cliente_f->n_registros > 1){
                return $this->error->error(mensaje: 'Error existe mas de un cliente', data: $r_com_cliente_f);
            }
            $r_com_cliente = new stdClass();
            $r_com_cliente->registro_id = $r_com_cliente_f->registros[0]['com_cliente_id'];

        }


        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = $r_alta_bd->registro_id;
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = $r_com_cliente->registro_id;

        $r_inm_rel_comprador_com_cliente_ins = (new inm_rel_comprador_com_cliente(link: $this->link))->alta_registro(
            registro: $inm_rel_comprador_com_cliente_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion',
                data:  $r_inm_rel_comprador_com_cliente_ins);
        }

        return $r_alta_bd;

    }

    /**
     * Genera la descripcion de un comprador basado en datos del registro a insertar
     * @param array $registro Registro en proceso
     * @return string
     */
    private function descripcion(array $registro): string
    {
        $descripcion = $registro['nombre'];
        $descripcion .= ' '.$registro['apellido_paterno'];
        $descripcion .= ' '.$registro['apellido_materno'];
        $descripcion .= ' '.$registro['nss'];
        $descripcion .= ' '.$registro['curp'];
        $descripcion .= ' '.$registro['rfc'];
        return $descripcion;
    }

    public function elimina_bd(int $id): array|stdClass
    {
        $filtro['inm_comprador.id'] = $id;
        $del = (new inm_rel_comprador_com_cliente(link: $this->link))->elimina_con_filtro_and(filtro:$filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar inm_rel_comprador_com_cliente',
                data:  $del);
        }

        $r_elimina_bd = parent::elimina_bd(id: $id); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al eliminar registro de comprador',data:  $r_elimina_bd);
        }
        return $r_elimina_bd;
    }

    public function modifica_bd(array $registro, int $id, bool $reactiva = false,
                                array $keys_integra_ds = array('codigo', 'descripcion')): array|stdClass
    {
        //print_r($registro);exit;
        $r_modifica = parent::modifica_bd(registro: $registro,id:  $id, reactiva: $reactiva,
            keys_integra_ds: $keys_integra_ds); // TODO: Change the autogenerated stub
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar comprador',data:  $r_modifica);
        }

        $com_cliente_upd = array();
        $com_cliente_upd['razon_social'] = $r_modifica->registro_actualizado->inm_comprador_nombre;
        $com_cliente_upd['razon_social'] .= ' '.$r_modifica->registro_actualizado->inm_comprador_apellido_paterno;
        $com_cliente_upd['razon_social'] .= ' '.$r_modifica->registro_actualizado->inm_comprador_apellido_materno;
        $keys_com_cliente = array('com_tipo_cliente_id','rfc','dp_calle_pertenece_id','numero_exterior',
            'numero_interior','telefono','cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id',
            'cat_sat_metodo_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_persona_id');

        foreach ($keys_com_cliente as $key_com_cliente){
           if(isset($registro[$key_com_cliente])){
               $com_cliente_upd[$key_com_cliente] = $registro[$key_com_cliente];
           }
        }
        if(count($com_cliente_upd) > 0){
            $filtro['inm_comprador.id'] = $id;
            $r_im_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener relacion',data:  $r_im_rel_comprador_com_cliente);
            }
            if($r_im_rel_comprador_com_cliente->n_registros === 0){
                return $this->error->error(mensaje: 'Error inm_rel_comprador_com_cliente no existe',data:  $r_im_rel_comprador_com_cliente);
            }
            if($r_im_rel_comprador_com_cliente->n_registros > 1){
                return $this->error->error(mensaje: 'Error de integridad inm_rel_comprador_com_cliente tiene mas de un registro',data:  $r_im_rel_comprador_com_cliente);
            }
            $im_rel_comprador_com_cliente = $r_im_rel_comprador_com_cliente->registros[0];

            $r_com_cliente = (new com_cliente(link: $this->link))->modifica_bd(registro: $registro,id:  $im_rel_comprador_com_cliente['com_cliente_id']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
            }
        }


        return $r_modifica;
    }

    private function razon_social(array $registro_entrada): string
    {
        $razon_social = $registro_entrada['nombre'];
        $razon_social .= $registro_entrada['apellido_paterno'];
        $razon_social .= $registro_entrada['apellido_materno'];
        return $razon_social;
    }


}