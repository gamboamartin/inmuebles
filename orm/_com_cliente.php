<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _com_cliente{
    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    private function actualiza_com_cliente(array $com_cliente_upd, int $inm_comprador_id, PDO $link){
        $com_cliente_id = $this->com_cliente_id(inm_comprador_id: $inm_comprador_id,link: $link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_id',data:  $com_cliente_id);
        }

        $r_com_cliente = (new com_cliente(link: $link))->modifica_bd(registro: $com_cliente_upd,id:  $com_cliente_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
        }
        return $r_com_cliente;
    }

    /**
     * @param int $inm_comprador_id
     * @param PDO $link
     * @return array|int
     */
    private  function com_cliente_id(int $inm_comprador_id, PDO $link): int|array
    {
        $filtro['inm_comprador.id'] = $inm_comprador_id;
        $r_im_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener relacion',data:  $r_im_rel_comprador_com_cliente);
        }
        if($r_im_rel_comprador_com_cliente->n_registros === 0){
            return $this->error->error(mensaje: 'Error inm_rel_comprador_com_cliente no existe',data:  $r_im_rel_comprador_com_cliente);
        }
        if($r_im_rel_comprador_com_cliente->n_registros > 1){
            return $this->error->error(mensaje: 'Error de integridad inm_rel_comprador_com_cliente tiene mas de un registro',data:  $r_im_rel_comprador_com_cliente);
        }
        $inm_rel_comprador_com_cliente = $r_im_rel_comprador_com_cliente->registros[0];
        return (int)$inm_rel_comprador_com_cliente['com_cliente_id'];
    }

    private function com_cliente_ins(string $numero_interior, string $razon_social, array $registro_entrada): array
    {
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $razon_social = trim($razon_social);
        if($razon_social === ''){
            return $this->error->error(mensaje: 'Error razon_social vacia',data:  $razon_social);
        }

        $com_cliente_ins['razon_social'] = trim($razon_social);
        $com_cliente_ins['rfc'] = $registro_entrada['rfc'];
        $com_cliente_ins['dp_calle_pertenece_id'] = $registro_entrada['dp_calle_pertenece_id'];
        $com_cliente_ins['numero_exterior'] = $registro_entrada['numero_exterior'];
        $com_cliente_ins['numero_interior'] = $numero_interior;
        $com_cliente_ins['telefono'] = $registro_entrada['lada_com'].$registro_entrada['numero_com'];
        $com_cliente_ins['cat_sat_regimen_fiscal_id'] = $registro_entrada['cat_sat_regimen_fiscal_id'];
        $com_cliente_ins['cat_sat_moneda_id'] = $registro_entrada['cat_sat_moneda_id'];
        $com_cliente_ins['cat_sat_forma_pago_id'] = $registro_entrada['cat_sat_forma_pago_id'];
        $com_cliente_ins['cat_sat_metodo_pago_id'] = $registro_entrada['cat_sat_metodo_pago_id'];
        $com_cliente_ins['cat_sat_uso_cfdi_id'] = $registro_entrada['cat_sat_uso_cfdi_id'];
        $com_cliente_ins['cat_sat_tipo_de_comprobante_id'] = 1;
        $com_cliente_ins['codigo'] = $registro_entrada['rfc'];
        $com_cliente_ins['com_tipo_cliente_id'] = $registro_entrada['com_tipo_cliente_id'];
        $com_cliente_ins['cat_sat_tipo_persona_id'] = $registro_entrada['cat_sat_tipo_persona_id'];
        return $com_cliente_ins;
    }

    /**
     * @param stdClass $registro
     * @return array
     */
    private function com_cliente_upd(stdClass $registro): array
    {
        $com_cliente_upd = array();

        $razon_social = $this->razon_social(con_prefijo: true, registro: $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener razon_social',data:  $razon_social);
        }
        $com_cliente_upd['razon_social'] = $razon_social;


        $com_cliente_upd = $this->init_keys_com_cliente(com_cliente_upd: $com_cliente_upd);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_upd',data:  $com_cliente_upd);
        }
        return $com_cliente_upd;
    }

    /**
     * @param array $com_cliente_upd
     * @return array
     */
    private function init_keys_com_cliente(array $com_cliente_upd): array
    {
        $keys_com_cliente = $this->key_com_cliente();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener keys_com_cliente',data:  $keys_com_cliente);
        }

        foreach ($keys_com_cliente as $key_com_cliente){
            if(isset($registro[$key_com_cliente])){
                $com_cliente_upd[$key_com_cliente] = $registro[$key_com_cliente];
            }
        }

        return $com_cliente_upd;
    }

    private function inm_rel_com_cliente_ins(int $com_cliente_id, int $inm_comprador_id): array
    {
        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = $inm_comprador_id;
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = $com_cliente_id;
        return $inm_rel_comprador_com_cliente_ins;
    }

    private function inserta_com_cliente(PDO $link, array $registro_entrada){

        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }


        $com_cliente_ins = $this->row_com_cliente_ins(registro_entrada: $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }

        $r_com_cliente = (new com_cliente(link: $link))->alta_registro(registro: $com_cliente_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }

    final public function inserta_inm_rel_comprador_com_cliente(int $com_cliente_id, int $inm_comprador_id, PDO $link){
        $inm_rel_comprador_com_cliente_ins = $this->inm_rel_com_cliente_ins(com_cliente_id: $com_cliente_id,
            inm_comprador_id: $inm_comprador_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar row rel com cliente', data: $inm_rel_comprador_com_cliente_ins);
        }

        $r_inm_rel_comprador_com_cliente_ins = (new inm_rel_comprador_com_cliente(link: $link))->alta_registro(
            registro: $inm_rel_comprador_com_cliente_ins);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al insertar relacion',
                data:  $r_inm_rel_comprador_com_cliente_ins);
        }
        return $r_inm_rel_comprador_com_cliente_ins;
    }

    /**
     *
     * @return string[]
     */
    private function key_com_cliente(): array
    {
        return array('com_tipo_cliente_id','rfc','dp_calle_pertenece_id','numero_exterior',
            'numero_interior','telefono','cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id',
            'cat_sat_metodo_pago_id','cat_sat_uso_cfdi_id','cat_sat_tipo_persona_id');
    }

    final public function modifica_com_cliente(stdClass $inm_comprador, PDO $link){
        $com_cliente_upd = $this->com_cliente_upd(registro: $inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener com_cliente_upd',data:  $com_cliente_upd);
        }
        $r_com_cliente = new stdClass();
        if(count($com_cliente_upd) > 0){

            $r_com_cliente = $this->actualiza_com_cliente(com_cliente_upd: $com_cliente_upd,
                inm_comprador_id: $inm_comprador->inm_comprador_id,link: $link);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al modificar cliente',data:  $r_com_cliente);
            }
        }

        return $r_com_cliente;
    }

    private function numero_interior(array $registro_entrada){
        $numero_interior = '';
        if(isset($registro_entrada['numero_interior'])){
            $numero_interior = $registro_entrada['numero_interior'];
        }
        return $numero_interior;
    }

    private function r_com_cliente(array $filtro, PDO $link, array $registro_entrada){
        $r_com_cliente_f = (new com_cliente(link: $link))->filtro_and(filtro: $filtro);
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

        $row_upd['rfc'] = $registro_entrada['rfc'];

        $razon_social = trim($registro_entrada['nombre']);
        $razon_social .= ' '.trim($registro_entrada['apellido_paterno']);
        $razon_social .= ' '.trim($registro_entrada['apellido_materno']);
        $razon_social = trim($razon_social);

        $row_upd['razon_social'] = $razon_social;
        $row_upd['dp_calle_pertenece_id'] = $registro_entrada['dp_calle_pertenece_id'];
        $row_upd['numero_exterior'] = $registro_entrada['numero_exterior'];

        $telefono = trim($registro_entrada['lada_com']).trim($registro_entrada['numero_com']);

        $row_upd['telefono'] = trim($telefono);
        $row_upd['cat_sat_regimen_fiscal_id'] = $registro_entrada['cat_sat_regimen_fiscal_id'];
        $row_upd['cat_sat_moneda_id'] = $registro_entrada['cat_sat_moneda_id'];
        $row_upd['cat_sat_forma_pago_id'] = $registro_entrada['cat_sat_forma_pago_id'];
        $row_upd['cat_sat_metodo_pago_id'] = $registro_entrada['cat_sat_metodo_pago_id'];
        $row_upd['cat_sat_uso_cfdi_id'] = $registro_entrada['cat_sat_uso_cfdi_id'];
        $row_upd['cat_sat_tipo_persona_id'] = $registro_entrada['cat_sat_tipo_persona_id'];
        $row_upd['com_tipo_cliente_id'] = $registro_entrada['com_tipo_cliente_id'];



        $r_com_cliente_upd = (new com_cliente(link: $link))->modifica_bd(registro: $row_upd,
            id:  $r_com_cliente->registro_id);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al actualizar cliente', data: $r_com_cliente_upd);
        }

        return $r_com_cliente;

    }

    /**
     * Integra la razon social para el alta de com cliente
     * @param bool $con_prefijo Si integra prefijo o no de inm_comprador
     * @param stdClass $registro Registro en proceso
     * @return string
     */
    private function razon_social(bool $con_prefijo, stdClass $registro): string
    {
        $key_nombre = 'nombre';
        $key_apellido_paterno = 'apellido_paterno';
        $key_apellido_materno = 'apellido_materno';

        if($con_prefijo){
            $key_nombre = 'inm_comprador_'.$key_nombre;
            $key_apellido_paterno = 'inm_comprador_'.$key_apellido_paterno;
            $key_apellido_materno = 'inm_comprador_'.$key_apellido_materno;
        }

        if(!isset($registro->$key_apellido_materno)){
            $registro->$key_apellido_materno = '';
        }

        $razon_social = $registro->$key_nombre;
        $razon_social .= ' '.$registro->$key_apellido_paterno;
        $razon_social .= ' '.$registro->$key_apellido_materno;

        return trim($razon_social);
    }

    private function result_com_cliente(bool $existe_cliente, array $filtro, PDO $link, array $registro_entrada){
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        if(!$existe_cliente) {
            $r_com_cliente = $this->inserta_com_cliente(link: $link, registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar cliente', data: $r_com_cliente);
            }
        }
        else{
            $r_com_cliente = $this->r_com_cliente(filtro: $filtro, link: $link, registro_entrada: $registro_entrada);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
            }
        }
        return $r_com_cliente;
    }



    private function row_com_cliente_ins(array $registro_entrada){

        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }


        $razon_social = $this->razon_social(con_prefijo: false, registro: (object)$registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar razon social',data:  $razon_social);
        }

        $numero_interior = $this->numero_interior(registro_entrada: $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar numero_interior',data:  $numero_interior);
        }

        $com_cliente_ins = $this->com_cliente_ins(numero_interior: $numero_interior,
            razon_social:  $razon_social,registro_entrada:  $registro_entrada);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al integrar registro de cliente', data: $com_cliente_ins);
        }
        return $com_cliente_ins;
    }

    final public function transacciona_com_cliente(PDO $link, array $registro_entrada){
        $keys = array('rfc','dp_calle_pertenece_id','numero_exterior','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $keys = array('dp_calle_pertenece_id','lada_com','numero_com',
            'cat_sat_regimen_fiscal_id','cat_sat_moneda_id','cat_sat_forma_pago_id','cat_sat_metodo_pago_id',
            'cat_sat_uso_cfdi_id','com_tipo_cliente_id','cat_sat_tipo_persona_id');

        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $registro_entrada);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro_entrada',data:  $valida);
        }

        $filtro['com_cliente.rfc'] = $registro_entrada['rfc'];
        $existe_cliente = (new com_cliente(link: $link))->existe(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si existe',data:  $existe_cliente);
        }

        $r_com_cliente = $this->result_com_cliente(existe_cliente: $existe_cliente, filtro: $filtro, link: $link,
            registro_entrada: $registro_entrada);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al obtener cliente', data: $r_com_cliente);
        }
        return $r_com_cliente;
    }
}
