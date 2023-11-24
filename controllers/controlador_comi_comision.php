<?php
/**
 * @author Martin Gamboa Vazquez
 * @version 1.0.0
 * @created 2022-05-14
 * @final En proceso
 *
 */
namespace gamboamartin\inmuebles\controllers;
use base\controller\init;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\errores\errores;
use gamboamartin\facturacion\models\fc_factura;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_ubicacion;
use PDO;
use stdClass;

class controlador_comi_comision extends \gamboamartin\comisiones\controllers\controlador_comi_comision {

    protected function campos_view(array $inputs = array()): array
    {
        $keys = new stdClass();
        $keys->inputs = array('monto_pago');
        $keys->selects = array();

        $init_data = array();
        $init_data['com_agente'] = "gamboamartin\\comercial";
        $init_data['inm_ubicacion'] = "gamboamartin\\inmuebles";
        $init_data['inm_comprador'] = "gamboamartin\\inmuebles";
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
        $columns_ds_com = array('inm_comprador_descripcion');
        $keys_selects = $this->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_comprador_id',
            keys_selects:$keys_selects, id_selected: -1, label: 'Cliente',columns_ds: $columns_ds_com);
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

        $keys_selects = (new init())->key_select_txt(cols: 6, key: 'monto_pago',
            keys_selects: $keys_selects, place_holder: 'Monto Pago');
        if (errores::$error) {
            return $this->errores->error(mensaje: 'Error al maquetar key_selects', data: $keys_selects);
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

    public function alta_bd(bool $header, bool $ws = false): array|stdClass
    {
        if(!isset($_POST['codigo'])){
            $_POST['codigo'] = $_POST['com_agente_id'].$_POST['com_agente_id'].rand();
        }
        if(!isset($_POST['descripcion'])){
            $_POST['descripcion'] = $_POST['codigo'].rand();
        }
        if(!isset($_POST['descripcion_select'])){
            $_POST['descripcion_select'] = $_POST['descripcion'];
        }

        $filtro['inm_comprador.id'] = $_POST['inm_comprador_id'];
        $r_inm_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente($this->link))->filtro_and(filtro: $filtro);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $r_inm_rel_comprador_com_cliente, header: $header,ws:  $ws);
        }
        if($r_inm_rel_comprador_com_cliente->n_registros < 0){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $r_inm_rel_comprador_com_cliente, header: $header,ws:  $ws);
        }

        $modelo_inm_rel_ubi_comp = new inm_rel_ubi_comp($this->link);
        $modelo_inm_rel_ubi_comp->registro['inm_ubicacion_id'] = $_POST['inm_ubicacion_id'];
        $modelo_inm_rel_ubi_comp->registro['inm_comprador_id'] = $_POST['inm_comprador_id'];
        $modelo_inm_rel_ubi_comp->registro['precio_operacion'] = '100000.00';
        $r_inm_rel_ubi_comp = $modelo_inm_rel_ubi_comp->alta_bd();
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $r_inm_rel_ubi_comp, header: $header,ws:  $ws);
        }

        if(!isset($_POSR['fc_factura_id'])){
            $filtro_fc['fc_factura.predeterminado'] = 'activo';
            $r_inm_rel_ubi_comp = (new fc_factura($this->link))->filtro_and(filtro: $filtro_fc);
            if(errores::$error){
                return $this->retorno_error(
                    mensaje: 'Error al obtener inputs',data:  $r_inm_rel_ubi_comp, header: $header,ws:  $ws);
            }
            $_POST['fc_factura_id'] = $r_inm_rel_ubi_comp->registros[0]['fc_factura_id'];
        }

        $r_alta_bd =  parent::alta_bd($header, $ws);
        if(errores::$error){
            return $this->retorno_error(
                mensaje: 'Error al obtener inputs',data:  $r_alta_bd, header: $header,ws:  $ws);
        }

        return $r_alta_bd;
    }
}
