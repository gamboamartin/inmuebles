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
     * TOTAL
     * Esta función es privada y se llama id_retorno.
     *
     * @return int|array Devuelve un entero o un array en caso de error.
     *
     * Este método se encarga de gestionar la tarea de obtener el valor POST de 'id_retorno'.
     * Primero, inicializa la variable id_retorno con un valor de -1.
     * Comprueba si 'id_retorno' se ha establecido y, si es así, recupera el valor, lo limpia y lo asigna a la variable id_retorno.
     * Luego, comprueba si id_retorno no es un número. En ese caso, devuelve un mensaje de error que indica que el
     * id_retorno debe ser un número entero.
     * Finalmente, devuelve el id_retorno como un número entero.
     * Se utiliza principalmente para obtener el id_retorno de una entrada POST y realizar un control de errores básico.
     * @version 3.0.0
     * @url https://github.com/gamboamartin/inmuebles/wiki/controllers._base.id_retorno.4.30.2
     */
    private function id_retorno(): int|array
    {
        $id_retorno = -1;
        if(isset($_POST['id_retorno'])){
            $id_retorno = trim($_POST['id_retorno']);
            unset($_POST['id_retorno']);
        }
        if(!is_numeric($id_retorno)){
            return $this->error->error(mensaje: 'Error id_retorno debe ser un entero', data: $id_retorno,
                es_final: true);
        }
        return (int)$id_retorno;
    }

    /**
     * TOTAL
     * Inicializa los datos de retorno de una transaccion via POST
     * @return array|stdClass
     * @url https://github.com/gamboamartin/inmuebles/wiki/controllers._base.init_retorno.4.30.2
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

    /**
     * Genera el resultado de salida despues del commit
     * @param controlador_inm_prospecto|controlador_inm_comprador|controlador_inm_prospecto_ubicacion $controlador Controlador en ejecucion
     * @param bool $header retorna resultado en web
     * @param mixed $result Resultado de transaccion
     * @param stdClass $retorno Datos de retorno finalizacion
     * @param bool $ws salida json
     * @return mixed|void
     * @version 2.253.1
     */
    final public function out(controlador_inm_prospecto|controlador_inm_comprador|controlador_inm_prospecto_ubicacion $controlador, bool $header,
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
