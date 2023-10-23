<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\errores\errores;
use gamboamartin\system\actions;
use stdClass;

class _base{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    private function id_retorno(): int
    {
        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = $_POST['id_retorno'];
            unset($_POST['id_retorno']);
        }
        return (int)$id_retorno;
    }
    
    final public function init_retorno(){
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
}
