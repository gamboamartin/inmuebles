<?php
namespace gamboamartin\inmuebles\models;
use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;
use PDO;
use stdClass;

class _co_acreditado{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }



    /**
     * Transacciones datos de comprador, inserta un co acreditado y una relacion
     * @param int $inm_comprador_id Comprador a integrar
     * @param array $inm_comprador_upd datos de registro en proceso
     * @param inm_comprador $modelo_inm_comprador Modelo de comprador
     * @return array|stdClass
     */
    final public function operaciones_co_acreditado(int $inm_comprador_id, array $inm_comprador_upd,
                                                    inm_comprador $modelo_inm_comprador): array|stdClass
    {

        $result = new stdClass();

        $keys = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $inm_ins = (new _relaciones_comprador())->inm_ins(entidad: 'inm_co_acreditado', indice: -1,
            inm_comprador_id: $inm_comprador_id, keys: $keys, registro: $inm_comprador_upd);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al asignar campo', data: $inm_ins);
        }
        $result->inm_co_acreditado_ins = $inm_ins;

        $aplica_alta = (new _relaciones_comprador())->aplica_alta(inm_ins: $inm_ins);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al validar si aplica alta co acreditado',
                data: $inm_ins);
        }
        $result->aplica_alta_co_acreditado = $aplica_alta;

        if($aplica_alta) {
            $data_co_acreditado = (new _relaciones_comprador())->transacciones_co_acreditado(
                inm_co_acreditado_ins: $inm_ins,inm_comprador_id:  $inm_comprador_id,
                modelo_inm_comprador:  $modelo_inm_comprador);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener data_co_acreditado',data:  $data_co_acreditado);
            }
            $result->data_co_acreditado = $data_co_acreditado;
        }
        return $result;
    }




}
