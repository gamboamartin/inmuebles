<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_ubicacion;
use PDO;
use stdClass;

class controlador_comi_comision extends \gamboamartin\comisiones\controllers\controlador_comi_comision {

    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array();
        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['inm_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['comi_conf_comision'] = "gamboamartin\\comisiones";

        $campos_view = $this->campos_view_base(init_data: $init_data, keys: $keys);
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al inicializar campo view', data: $campos_view);
        }

        return $campos_view;
    }

    public function alta(bool $header, bool $ws = false): array|string
    {
        $r_alta = $this->init_alta();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $keys_selects = array();
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'comi_conf_comision_id',
            keys_selects:$keys_selects, id_selected: -1, label: 'Configuracion Comision');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }
        $keys_selects = $this->key_select(cols:6, con_registros: true,filtro:  array(), key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: -1, label: 'Agente');
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $columns_ds = array('inm_ubicacion_id','dp_estado_descripcion','dp_municipio_descripcion',
            'dp_cp_descripcion','dp_colonia_descripcion','dp_calle_descripcion','inm_ubicacion_numero_exterior');

        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_ubicacion_id',
            keys_selects:$keys_selects, id_selected: -1, label: 'Ubicacion', columns_ds: $columns_ds);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al inicializar alta',data:  $r_alta, header: $header,ws:  $ws);
        }

        $inputs = $this->inputs(keys_selects: $keys_selects);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $inputs, header: $header,ws:  $ws);
        }

        return $r_alta;
    }

    public function id_selected_agente(PDO $link): int|array
    {
        $com_agentes = (new com_agente(link: $link))->com_agentes_session();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener agentes',data:  $com_agentes);
        }
        $id_selected = -1;
        if(count($com_agentes) > 0){
            $id_selected = (int)$com_agentes[0]['com_agente_id'];
        }
        return $id_selected;
    }

    public function id_selected_ubicacion(PDO $link): int|array
    {
        $com_ubicacion = $this->inm_ubicacion_session();
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener ubicacions',data:  $com_ubicacion);
        }
        $id_selected = -1;
        if(count($com_ubicacion) > 0){
            $id_selected = (int)$com_ubicacion[0]['com_ubicacion_id'];
        }
        return $id_selected;
    }

    public function inm_ubicacion_session(): array
    {
        $filtro['adm_usuario.id'] = $_SESSION['usuario_id'];
        $filtro['com_agente.status'] = 'activo';
        $r_com_ubicacion = (new inm_ubicacion($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->errores->error(mensaje: 'Error al obtener com_ubicacion',data:  $r_com_ubicacion);
        }
        return $r_com_ubicacion->registros;
    }
}
