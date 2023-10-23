<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\system\actions;
use stdClass;
use Throwable;

class _base{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    /**
     * @return int|array
     * @version 2.251.2
     */
    private function id_retorno(): int|array
    {
        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = trim($_POST['id_retorno']);
            unset($_POST['id_retorno']);
        }
        if(!is_int($id_retorno)){
            return $this->error->error(mensaje: 'Error id_retorno debe ser un entero', data: $id_retorno);
        }
        return $id_retorno;
    }

    /**
     * @return array|stdClass
     * @version 2.252.2
     */
    final public function init_retorno(): array|stdClass
    {
        $id_retorno = $this->id_retorno();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener id siguiente', data: $id_retorno);
        }

        $siguiente_view = (new actions())->init_alta_bd();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener siguiente view', data: $siguiente_view);
        }
        $data = new stdClass();

        $data->id_retorno = $id_retorno;
        $data->siguiente_view = $siguiente_view;
        return $data;

    }

    final public function out(controlador_inm_prospecto|controlador_inm_comprador $controlador, bool $header,
                              mixed $result, stdClass $retorno, bool $ws){
        if($header){
            if($retorno->id_retorno === -1) {
                $retorno->id_retorno = $controlador->registro_id;
            }
            $controlador->retorno_base(registro_id:$retorno->id_retorno, result: $result,
                siguiente_view: $retorno->siguiente_view, ws:  $ws,seccion_retorno: $controlador->seccion,
                valida_permiso: true);
        }
        if($ws){
            header('Content-Type: application/json');
            try {
                echo json_encode($result, JSON_THROW_ON_ERROR);
            }
            catch (Throwable $e){
                $error = (new errores())->error(mensaje: 'Error al maquetar JSON' , data: $e);
                print_r($error);
            }
            exit;
        }
        return $result;
    }
}
