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

    final public function operaciones_referencia(int $indice, int $inm_comprador_id, array $inm_comprador_upd,
                                                 inm_comprador $modelo_inm_comprador){

        $result = new stdClass();

        $keys = array('apellido_paterno','nombre', 'lada', 'numero','celular','inm_comprador_id',
            'dp_calle_pertenece_id','numero_dom');

        $inm_ins = (new _relaciones_comprador())->inm_ins(entidad: 'inm_referencia', indice: $indice,
            inm_comprador_id: $inm_comprador_id, keys: $keys, registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_ins);
        }


        $result->inm_referencia_ins = $inm_ins;

        $aplica_alta = (new _relaciones_comprador())->aplica_alta(inm_ins: $inm_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta inm_referencia_ins', data: $inm_ins);
        }

        $result->aplica_alta_referencia = $aplica_alta;

        if($aplica_alta) {
            $data_referencia = (new _relaciones_comprador())->transacciones_referencia(
                indice: $indice, inm_referencia_ins: $inm_ins, inm_comprador_id: $inm_comprador_id,
                modelo_inm_comprador: $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_referencia',data:  $data_referencia);
            }
            $result->data_referencia = $data_referencia;
        }
        return $result;
    }



}
