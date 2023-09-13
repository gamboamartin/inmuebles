<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class _co_acreditado{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }
    private function aplica_alta_co_acreditado(array $inm_co_acreditado_ins): bool
    {
        $aplica_alta_co_acreditado = false;
        if(count($inm_co_acreditado_ins)>0){
            $aplica_alta_co_acreditado = true;
            if(count($inm_co_acreditado_ins) === 1){
                if(isset($inm_co_acreditado_ins['genero'])){
                    $aplica_alta_co_acreditado = false;
                }
            }
        }
        return $aplica_alta_co_acreditado;
    }

    private function asigna_campo_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins,
                                                string $key_co_acreditado, array $registro): array
    {
        $inm_co_acreditado_ins[$campo_co_acreditado] = $registro[$key_co_acreditado];

        return $inm_co_acreditado_ins;

    }

    private function data_co_acreditado(array $co_acreditados): stdClass
    {
        $existe_co_acreditado = false;
        $inm_co_acreditado = new stdClass();
        if(count($co_acreditados) === 1){
            $existe_co_acreditado = true;
            $inm_co_acreditado = (object)$co_acreditados[0];

        }

        $data = new stdClass();
        $data->existe_co_acreditado = $existe_co_acreditado;
        $data->inm_co_acreditado = $inm_co_acreditado;

        return $data;
    }

    private function get_data_co_acreditado(int $inm_comprador_id, inm_comprador $modelo_inm_comprador){
        $co_acreditados = $modelo_inm_comprador->get_co_acreditados(inm_comprador_id: $inm_comprador_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener co_acreditados',data:  $co_acreditados);
        }

        $data_co_acreditado = $this->data_co_acreditado(co_acreditados: $co_acreditados);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        return $data_co_acreditado;
    }

    private function inm_co_acreditado_ins(array $registro){
        $keys_co_acreditado = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $inm_co_acreditado_ins = array();
        foreach ($keys_co_acreditado as $campo_co_acreditado){
            $inm_co_acreditado_ins = $this->integra_value_co_acreditado(
                campo_co_acreditado: $campo_co_acreditado,inm_co_acreditado_ins:  $inm_co_acreditado_ins, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    private function inm_rel_co_acreditado_ins(int $inm_co_acreditado_id, int $inm_comprador_id): array
    {
        $inm_rel_co_acred_ins['inm_co_acreditado_id'] = $inm_co_acreditado_id;
        $inm_rel_co_acred_ins['inm_comprador_id'] = $inm_comprador_id;
        return $inm_rel_co_acred_ins;
    }

    private function inserta_data_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id, PDO $link){
        $alta_inm_co_acreditado = (new inm_co_acreditado(link: $link))->alta_registro(registro: $inm_co_acreditado_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar co_acreditado', data: $alta_inm_co_acreditado);
        }

        $inm_rel_co_acred_ins = $this->inm_rel_co_acreditado_ins(
            inm_co_acreditado_id: $alta_inm_co_acreditado->registro_id,inm_comprador_id:  $inm_comprador_id);

        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar inm_rel_co_acred_ins', data: $inm_rel_co_acred_ins);
        }

        $alta_inm_rel_co_acred = (new inm_rel_co_acred(link: $link))->alta_registro(registro: $inm_rel_co_acred_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_inm_rel_co_acred', data: $alta_inm_rel_co_acred);
        }

        $data = new stdClass();
        $data->alta_inm_co_acreditado = $alta_inm_co_acreditado;
        $data->alta_inm_rel_co_acred = $alta_inm_rel_co_acred;
        return $data;
    }


    private function integra_campo_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins, string $key_co_acreditado, array $registro){
        $value = trim($registro[$key_co_acreditado]);
        if($value !=='') {
            $inm_co_acreditado_ins = $this->asigna_campo_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
                inm_co_acreditado_ins:  $inm_co_acreditado_ins, key_co_acreditado:  $key_co_acreditado,
                registro:  $registro);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al asignar campo',data:  $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    private function integra_value_co_acreditado(string $campo_co_acreditado, array $inm_co_acreditado_ins, array $registro){
        $key_co_acreditado = 'inm_co_acreditado_'.$campo_co_acreditado;
        if(isset($registro[$key_co_acreditado])) {
            $inm_co_acreditado_ins = $this->integra_campo_co_acreditado(campo_co_acreditado: $campo_co_acreditado,
                inm_co_acreditado_ins: $inm_co_acreditado_ins, key_co_acreditado: $key_co_acreditado,
                registro: $registro);

            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
            }
        }
        return $inm_co_acreditado_ins;
    }

    final public function operaciones_co_acreditado(int $inm_comprador_id, array $inm_comprador_upd, inm_comprador $modelo_inm_comprador){

        $result = new stdClass();

        $inm_co_acreditado_ins = $this->inm_co_acreditado_ins(registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_co_acreditado_ins);
        }
        $result->inm_co_acreditado_ins = $inm_co_acreditado_ins;

        $aplica_alta_co_acreditado = $this->aplica_alta_co_acreditado(inm_co_acreditado_ins: $inm_co_acreditado_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta co acreditado', data: $inm_co_acreditado_ins);
        }
        $result->aplica_alta_co_acreditado = $aplica_alta_co_acreditado;

        if($aplica_alta_co_acreditado) {
            $data_co_acreditado = $this->transacciones_co_acreditado(
                inm_co_acreditado_ins: $inm_co_acreditado_ins,inm_comprador_id:  $inm_comprador_id,modelo_inm_comprador:  $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
            }
            $result->data_co_acreditado = $data_co_acreditado;
        }
        return $result;
    }

    private function transacciones_co_acreditado(array $inm_co_acreditado_ins, int $inm_comprador_id, inm_comprador $modelo_inm_comprador){

        $data_result = new stdClass();

        $data_co_acreditado = $this->get_data_co_acreditado(inm_comprador_id: $inm_comprador_id,modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
        }
        $data_result->data_co_acreditado = $data_co_acreditado;

        if(!$data_co_acreditado->existe_co_acreditado) {
            $data_ins = $this->inserta_data_co_acreditado(inm_co_acreditado_ins: $inm_co_acreditado_ins,
                inm_comprador_id:  $inm_comprador_id,link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de co acreditado', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_co_acreditado = (new inm_co_acreditado(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_co_acreditado_ins,id:  $data_co_acreditado->inm_co_acreditado->inm_co_acreditado_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar co acreditado', data: $modifica_co_acreditado);
            }
            $data_result->modifica_co_acreditado = $modifica_co_acreditado;
        }
        return $data_result;

    }
}
