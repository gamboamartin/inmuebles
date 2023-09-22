<?php
namespace gamboamartin\inmuebles\html;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\system\html_controler;
use gamboamartin\template\directivas;
use PDO;
use stdClass;

class inm_ubicacion_html extends html_controler {

    final public function form_ubicacion(controlador_inm_ubicacion $controlador): string
    {
        $keys = array('dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id','dp_calle_pertenece_id',
            'numero_exterior','numero_interior','manzana','lote','inm_ubicacion_id','seccion_retorno',
            'btn_action_next','id_retorno');


        $inputs = '';
        foreach ($keys as $input){
            $inputs .= $controlador->inputs->$input;
        }

        return $inputs;
    }

    /**
     * Genera un input select de ubicaciones
     * @param int $cols Columnas css
     * @param bool $con_registros si no con registros da u input vacio
     * @param int $id_selected Seleccion default
     * @param PDO $link Conexion a la base de datos
     * @param array $columns_ds Columnas a mostrar en select
     * @param bool $disabled Si disabled integra el atributo disabled al input
     * @param array $extra_params_keys
     * @param array $filtro Si se integra filtro el resultado de los options se ajusta al filtro
     * @param array $registros Registros para options
     * @return array|string
     * @version 1.103.1
     */
    final public function select_inm_ubicacion_id(
        int $cols, bool $con_registros, int $id_selected, PDO $link, array $columns_ds = array(),
        bool $disabled = false, array $extra_params_keys = array(), array $filtro = array(),
        array $registros = array()): array|string
    {
        $valida = (new directivas(html:$this->html_base))->valida_cols(cols:$cols);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar cols', data: $valida);
        }

        $modelo = new inm_ubicacion(link: $link);


        $select = $this->select_catalogo(cols: $cols, con_registros: $con_registros, id_selected: $id_selected,
            modelo: $modelo, columns_ds: $columns_ds, disabled: $disabled, extra_params_keys: $extra_params_keys,
            filtro: $filtro, label: 'Ubicacion', registros: $registros, required: true);
        if (errores::$error) {
            return $this->error->error(mensaje: 'Error al generar select', data: $select);
        }
        return $select;
    }


}
