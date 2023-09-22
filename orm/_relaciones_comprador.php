<?php
namespace gamboamartin\inmuebles\models;

use gamboamartin\errores\errores;
use gamboamartin\validacion\validacion;

class _relaciones_comprador{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();

    }

    /**
     * Asigna un valor de un campo de referencia para su integracion con otro catalogo
     * @param string $campo Campo a integrar
     * @param array $inm_ins registro previo a insertar
     * @param string $key Key a integrar
     * @param array $registro Registro en proceso
     * @return array
     * @version 1.172.1
     */
    final public function asigna_campo(string $campo, array $inm_ins, string $key, array $registro): array
    {
        $campo = trim($campo);
        if($campo === ''){
            return $this->error->error(mensaje: 'Error campo esta vacio',data:  $campo);
        }
        $key = trim($key);
        if($key === ''){
            return $this->error->error(mensaje: 'Error key esta vacio',data:  $key);
        }

        $keys = array($key);
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro,valida_vacio: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }


        $inm_ins[$campo] = $registro[$key];

        return $inm_ins;

    }
}