<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _referencias{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * @param array $inm_referencia_ins
     * @return bool
     */
    private function aplica_alta_referencia(array $inm_referencia_ins): bool
    {
        $aplica_alta_referencia = false;
        if(count($inm_referencia_ins)>0){
            $aplica_alta_referencia = true;
        }
        return $aplica_alta_referencia;
    }




    private function data_referencia(int $indice,array $referencias): stdClass
    {
        $existe_referencia = false;
        $inm_referencia = new stdClass();
        if(isset($referencias[$indice-1])){
            $existe_referencia = true;
            $inm_referencia = (object)$referencias[$indice-1];
        }

        $data = new stdClass();
        $data->existe_referencia = $existe_referencia;
        $data->inm_referencia = $inm_referencia;

        return $data;
    }

    private function get_data_referencia(int $indice,int $inm_comprador_id, inm_comprador $modelo_inm_comprador){
        $referencias = $modelo_inm_comprador->get_referencias(inm_comprador_id: $inm_comprador_id);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener referencias',data:  $referencias);
        }

        $data_referencias = $this->data_referencia(indice: $indice, referencias: $referencias);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencias',data:  $data_referencias);
        }
        return $data_referencias;
    }

    /**
     * @param int $indice
     * @param int $inm_comprador_id
     * @param array $registro
     * @return array
     */
    private function inm_referencia_ins(int $indice, int $inm_comprador_id,array $registro): array
    {
        $keys_referencia = array('apellido_paterno','nombre', 'lada', 'numero','celular','inm_comprador_id',
            'dp_calle_pertenece_id','numero_dom');

        $inm_referencia_ins = array();
        foreach ($keys_referencia as $campo_referencia){
            $inm_referencia_ins = (new _relaciones_comprador())->integra_value(campo: $campo_referencia,
                entidad: 'inm_referencia', indice: $indice, inm_ins: $inm_referencia_ins, registro: $registro);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_referencia_ins);
            }
        }
        if(count($inm_referencia_ins)>0) {
            $inm_referencia_ins['inm_comprador_id'] = $inm_comprador_id;
        }

        return $inm_referencia_ins;
    }

    private function inserta_data_referencia(array $inm_referencia_ins, PDO $link){
        $alta_inm_referencia = (new inm_referencia(link: $link))->alta_registro(registro: $inm_referencia_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al insertar alta_inm_referencia', data: $alta_inm_referencia);
        }


        $data = new stdClass();
        $data->alta_inm_referencia = $alta_inm_referencia;

        return $data;
    }





    final public function operaciones_referencia(int $indice, int $inm_comprador_id, array $inm_comprador_upd, inm_comprador $modelo_inm_comprador){

        $result = new stdClass();

        $inm_referencia_ins = $this->inm_referencia_ins(indice: $indice, inm_comprador_id: $inm_comprador_id,
            registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_referencia_ins);
        }


        $result->inm_referencia_ins = $inm_referencia_ins;

        $aplica_alta_referencia = $this->aplica_alta_referencia(inm_referencia_ins: $inm_referencia_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta inm_referencia_ins', data: $inm_referencia_ins);
        }

        $result->aplica_alta_referencia = $aplica_alta_referencia;

        if($aplica_alta_referencia) {
            $data_referencia = $this->transacciones_referencia(indice: $indice, inm_referencia_ins: $inm_referencia_ins,
                inm_comprador_id: $inm_comprador_id, modelo_inm_comprador: $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
            }
            $result->data_referencia = $data_referencia;
        }
        return $result;
    }

    private function transacciones_referencia(int $indice,array $inm_referencia_ins, int $inm_comprador_id, inm_comprador $modelo_inm_comprador){

        $data_result = new stdClass();

        $data_referencia = $this->get_data_referencia(indice: $indice, inm_comprador_id: $inm_comprador_id,
            modelo_inm_comprador: $modelo_inm_comprador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
        }

        $data_result->data_referencia = $data_referencia;

        if(!$data_referencia->existe_referencia) {
            $data_ins = $this->inserta_data_referencia(inm_referencia_ins: $inm_referencia_ins,
                link:  $modelo_inm_comprador->link);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al insertar datos de referencia', data: $data_ins);
            }
            $data_result->data_ins = $data_ins;
        }
        else{
            $modifica_referencia = (new inm_referencia(link: $modelo_inm_comprador->link))->modifica_bd(
                registro: $inm_referencia_ins,id:  $data_referencia->inm_referencia->inm_referencia_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al modificar modifica_referencia', data: $modifica_referencia);
            }
            $data_result->modifica_referencia = $modifica_referencia;
        }
        return $data_result;

    }

}
