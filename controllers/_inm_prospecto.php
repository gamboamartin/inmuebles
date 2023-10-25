<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\validacion\validacion;
use html\dp_estado_html;
use html\dp_municipio_html;
use PDO;
use stdClass;

class _inm_prospecto{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    /**
     * Obtiene los datos de un conyuge
     * @param PDO $link Conexion a la base de datos
     * @param int $inm_prospecto_id prospecto
     * @return array|stdClass
     * @version 2.263.2
     */
    final public function datos_conyuge(PDO $link, int $inm_prospecto_id): array|stdClass
    {

        $existe_conyuge = false;

        if($inm_prospecto_id > 0) {

            $existe_conyuge = (new inm_prospecto(link: $link))->existe_conyuge(inm_prospecto_id: $inm_prospecto_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar si existe conyuge', data: $existe_conyuge);
            }
        }

        $conyuge = $this->init_conyuge();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar conyuge',data:  $conyuge);
        }

        $tiene_dato_conyuge = $this->tiene_dato_conyuge(conyuge: $conyuge);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si tiene dato conyuge',data:  $tiene_dato_conyuge);
        }
        $datos = new stdClass();
        $datos->existe_conyuge = $existe_conyuge;
        $datos->conyuge = $conyuge;
        $datos->tiene_dato_conyuge = $tiene_dato_conyuge;
        return $datos;
    }

    /**
     * Valida el attr segundo credito
     * @param array $registro Registro en proceso
     * @return bool|array
     * @version 2.262.2
     */
    private function disabled_segundo_credito(array $registro): bool|array
    {
        $keys = array('inm_prospecto_es_segundo_credito');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $disabled = true;
        if($registro['inm_prospecto_es_segundo_credito'] === 'SI'){
            $disabled = false;
        }
        return $disabled;
    }

    /**
     * Genera un filtro con un user
     * @param array $adm_usuario Registro de usuario
     * @return array
     * @version 2.258.2
     */
    private function filtro_user(array $adm_usuario): array
    {
        $keys = array('adm_grupo_root');
        $valida = $this->validacion->valida_statuses(keys: $keys,registro:  $adm_usuario);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida adm_usuario',data:  $valida);
        }

        $filtro = array();
        if($adm_usuario['adm_grupo_root'] === 'inactivo'){
            $filtro['adm_usuario.id'] = $_SESSION['usuario_id'];
        }
        return $filtro;
    }

    /**
     * Genera un filtro para obtencion de datos ligado a un usuario
     * @param PDO $link Conexion a la base de datos
     * @return array
     * @version 2.260.2
     */
    private function genera_filtro_user(PDO $link): array
    {
        $adm_usuario = (new adm_usuario(link: $link))->registro(registro_id: $_SESSION['usuario_id'],
            columnas: array('adm_grupo_root'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_usuario ',data:  $adm_usuario);
        }

        $filtro = $this->filtro_user(adm_usuario: $adm_usuario);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro ',data:  $filtro);
        }
        return $filtro;
    }

    /**
     * Integra in key select basado en parametros
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @param array $identificadores identificadores a integrar
     * @param array $keys_selects parametros previos cargados
     * @return array
     */
    private function genera_keys_selects(controlador_inm_prospecto $controlador, array $identificadores,
                                         array $keys_selects): array
    {
        foreach ($identificadores as $identificador=>$data){
            $filtro = array();
            if(isset($data['filtro'])){
                $filtro = $data['filtro'];
            }
            $keys_selects = $controlador->key_select(cols: $data['cols'], con_registros: true,filtro: $filtro,
                key: $identificador, keys_selects:$keys_selects,
                id_selected: $controlador->registro[$identificador], label: $data['title'],
                disabled: $data['disabled']);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
            }
        }
        return $keys_selects;
    }
    
    final public function headers_front(controlador_inm_prospecto $controlador){
        $headers = $this->headers_prospecto();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers',data:  $headers);
        }

        $headers = (new \gamboamartin\inmuebles\html\_base(html: $controlador->html_base))->genera_headers(controler: $controlador,headers:  $headers);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers',data:  $headers);
        }
        return $headers;
    }

    private function headers_prospecto(): array
    {
        $headers = array();
        $headers['1'] = '1. DATOS PERSONALES';
        $headers['2'] = '2. DATOS DE CONTACTO';
        $headers['3'] = '3. DOMICILIO';
        $headers['4'] = '4. CREDITO';
        $headers['5'] = '5. MONTO CREDITO';
        $headers['6'] = '6. DISCAPACIDAD';
        $headers['7'] = '7. DATOS EMPRESA TRABAJADOR';
        $headers['8'] = '8. DATOS DE CONYUGE';
        return $headers;
    }

    /**
     * Genera los identificadores para keys selects
     * @param array $filtro Filtro de integracion par agente
     * @return array
     * @version 2.253.2
     */
    private function identificadores_comercial(array $filtro): array
    {
        $identificadores['com_agente_id']['title'] = 'Agente';
        $identificadores['com_agente_id']['cols'] = 12;
        $identificadores['com_agente_id']['disabled'] = false;
        $identificadores['com_agente_id']['filtro'] = $filtro;

        $identificadores['com_tipo_prospecto_id']['title'] = 'Tipo de prospecto';
        $identificadores['com_tipo_prospecto_id']['cols'] = 12;
        $identificadores['com_tipo_prospecto_id']['disabled'] = false;
        $identificadores['com_tipo_prospecto_id']['filtro'] = array();
        return $identificadores;
    }

    private function identificadores_dp(controlador_inm_prospecto $controlador): array
    {
        $identificadores['dp_pais_id']['title'] = 'Pais';
        $identificadores['dp_pais_id']['cols'] = 4;
        $identificadores['dp_pais_id']['disabled'] = false;
        $identificadores['dp_pais_id']['filtro'] = array();

        $filtro = array();
        $filtro['dp_pais.id'] = $controlador->registro['dp_pais_id'];
        $identificadores['dp_estado_id']['title'] = 'Estado';
        $identificadores['dp_estado_id']['cols'] = 4;
        $identificadores['dp_estado_id']['disabled'] = false;
        $identificadores['dp_estado_id']['filtro'] = $filtro;

        $filtro = array();
        $filtro['dp_estado.id'] = $controlador->registro['dp_estado_id'];
        $identificadores['dp_municipio_id']['title'] = 'Municipio';
        $identificadores['dp_municipio_id']['cols'] = 4;
        $identificadores['dp_municipio_id']['disabled'] = false;
        $identificadores['dp_municipio_id']['filtro'] = $filtro;

        $filtro = array();
        $filtro['dp_municipio.id'] = $controlador->registro['dp_municipio_id'];
        $identificadores['dp_cp_id']['title'] = 'CP';
        $identificadores['dp_cp_id']['cols'] = 12;
        $identificadores['dp_cp_id']['disabled'] = false;
        $identificadores['dp_cp_id']['filtro'] = $filtro;

        $filtro = array();
        $filtro['dp_cp.id'] = $controlador->registro['dp_cp_id'];
        $identificadores['dp_colonia_postal_id']['title'] = 'Colonia';
        $identificadores['dp_colonia_postal_id']['cols'] = 6;
        $identificadores['dp_colonia_postal_id']['disabled'] = false;
        $identificadores['dp_colonia_postal_id']['filtro'] = $filtro;

        $filtro = array();
        $filtro['dp_colonia_postal.id'] = $controlador->registro['dp_colonia_postal_id'];
        $identificadores['dp_calle_pertenece_id']['title'] = 'Calle';
        $identificadores['dp_calle_pertenece_id']['cols'] = 6;
        $identificadores['dp_calle_pertenece_id']['disabled'] = false;
        $identificadores['dp_calle_pertenece_id']['filtro'] = $filtro;
        return $identificadores;
    }

    /**
     * Integra los identificadores para la creacion de un parametro de tipo key select
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return array
     */
    private function identificadores_infonavit(controlador_inm_prospecto $controlador): array
    {
        $identificadores['inm_institucion_hipotecaria_id']['title'] = 'Institucion Hipotecaria';
        $identificadores['inm_institucion_hipotecaria_id']['cols'] = 12;
        $identificadores['inm_institucion_hipotecaria_id']['disabled'] = false;

        $identificadores['inm_producto_infonavit_id']['title'] = 'Producto Infonavit';
        $identificadores['inm_producto_infonavit_id']['cols'] = 6;
        $identificadores['inm_producto_infonavit_id']['disabled'] = false;

        $identificadores['inm_attr_tipo_credito_id']['title'] = 'Tipo de Credito';
        $identificadores['inm_attr_tipo_credito_id']['cols'] = 6;
        $identificadores['inm_attr_tipo_credito_id']['disabled'] = false;

        $identificadores['inm_destino_credito_id']['title'] = 'Destino de Credito';
        $identificadores['inm_destino_credito_id']['cols'] = 12;
        $identificadores['inm_destino_credito_id']['disabled'] = false;

        $identificadores['inm_tipo_discapacidad_id']['title'] = 'Tipo de Discapacidad';
        $identificadores['inm_tipo_discapacidad_id']['cols'] = 6;
        $identificadores['inm_tipo_discapacidad_id']['disabled'] = false;

        $identificadores['inm_persona_discapacidad_id']['title'] = 'Persona de Discapacidad';
        $identificadores['inm_persona_discapacidad_id']['cols'] = 6;
        $identificadores['inm_persona_discapacidad_id']['disabled'] = false;

        $disabled = $this->disabled_segundo_credito(registro: $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar disabled',data:  $disabled);
        }

        $identificadores['inm_plazo_credito_sc_id']['title'] = 'Plazo de Segundo Credito';
        $identificadores['inm_plazo_credito_sc_id']['cols'] = 6;
        $identificadores['inm_plazo_credito_sc_id']['disabled'] = $disabled;

        return $identificadores;
    }

    /**
     * Inicializa un registro de conyuge
     * @return array
     * @version 2.263.2
     */
    private function init_conyuge(): array
    {
        $conyuge = array();
        if(isset($_POST['conyuge'])){
            $conyuge = $_POST['conyuge'];
            if(is_string($conyuge)){
                return $this->error->error(mensaje: 'Error POST conyuge debe ser un array',data:  $conyuge);
            }
            unset($_POST['conyuge']);
        }
        return $conyuge;
    }

    final public function inputs_base(controlador_inm_prospecto $controlador){
        $keys_selects = array();

        $keys_selects = $this->keys_selects_infonavit(
            controlador: $controlador,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_selects_dp(controlador: $controlador,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $this->keys_selects_personal(controlador: $controlador,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $row = $this->row_base_fiscal(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al row',data:  $row);
        }

        $radios = (new \gamboamartin\inmuebles\models\_inm_comprador())->radios_chk(controler: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al integrar radios',data:  $radios);
        }
        $data = new stdClass();
        $data->keys_selects = $keys_selects;
        $data->row = $row;
        $data->radios = $radios;

        return $data;
    }
    
    final public function inputs_nacimiento(controlador_inm_prospecto $controlador){
        $dp_estado_nacimiento_id = (new dp_estado_html(html: $controlador->html_base))->select_dp_estado_id(cols: 6,
            con_registros: true, id_selected: $controlador->registro['dp_estado_nacimiento_id'], link: $controlador->link,
            label: 'Edo Nac', name: 'dp_estado_nacimiento_id');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_estado_nacimiento_id);
        }

        $controlador->inputs->dp_estado_nacimiento_id = $dp_estado_nacimiento_id;

        $filtro = array('dp_estado.id'=>$controlador->registro['dp_estado_nacimiento_id']);
        $dp_municipio_nacimiento_id = (new dp_municipio_html(html: $controlador->html_base))->select_dp_municipio_id(cols: 6,
            con_registros: true, id_selected: $controlador->registro['dp_municipio_nacimiento_id'], link: $controlador->link,
            filtro: $filtro, label: 'Mun Nac', name: 'dp_municipio_nacimiento_id');

        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $dp_municipio_nacimiento_id);
        }

        $controlador->inputs->dp_municipio_nacimiento_id = $dp_municipio_nacimiento_id;


        $fecha_nacimiento = $controlador->html->input_fecha(cols: 6, row_upd: $controlador->row_upd, value_vacio: false,
            name: 'fecha_nacimiento', place_holder: 'Fecha Nac', value: $controlador->row_upd->fecha_nacimiento);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener input',data:  $fecha_nacimiento);
        }

        $controlador->inputs->fecha_nacimiento = $fecha_nacimiento;

        return $controlador->inputs;
    }

    /**
     * Integra los parametros de selectores de tipo comercial
     * @param controlador_inm_prospecto $controlador
     * @param array $keys_selects
     * @return array
     */
    private function integra_keys_selects_comercial(controlador_inm_prospecto $controlador, array $keys_selects): array
    {
        $keys = array('com_agente_id','com_tipo_prospecto_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida controlador registro',data:  $valida);
        }

        $filtro = $this->genera_filtro_user(link: $controlador->link);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro ',data:  $filtro);
        }

        $keys_selects = $this->keys_selects_comercial(controlador: $controlador,filtro: $filtro,
            keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    /**
     * Genera los selectores parametros de tipo comercial
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @param array $filtro Filtro de tipo user
     * @param array $keys_selects Parametros previos cargados
     * @return array
     */
    private function keys_selects_comercial(controlador_inm_prospecto $controlador, array $filtro,
                                           array $keys_selects): array
    {
        $keys = array('com_agente_id','com_tipo_prospecto_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida controlador registro',data:  $valida);
        }


        $identificadores = $this->identificadores_comercial(filtro: $filtro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar identificadores',data:  $identificadores);
        }

        $keys_selects = $this->genera_keys_selects(controlador: $controlador,identificadores:  $identificadores,
            keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        return $keys_selects;
    }

    private function keys_selects_dp(controlador_inm_prospecto $controlador, array $keys_selects){



        $identificadores = $this->identificadores_dp(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar identificadores',data:  $identificadores);
        }

        $keys_selects = $this->genera_keys_selects(controlador: $controlador,identificadores:  $identificadores,
            keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        return $keys_selects;
    }

    private function keys_selects_infonavit(controlador_inm_prospecto $controlador, array $keys_selects){


        $keys_selects = $this->integra_keys_selects_comercial(controlador: $controlador,keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }


        $identificadores = $this->identificadores_infonavit(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar identificadores',data:  $identificadores);
        }

        $keys_selects = $this->genera_keys_selects(controlador: $controlador,identificadores:  $identificadores,
            keys_selects:  $keys_selects);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        return $keys_selects;
    }

    private function keys_selects_personal(controlador_inm_prospecto $controlador, array $keys_selects){
        $keys_selects = $controlador->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_sindicato_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_sindicato_id'], label: 'Sindicato');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_nacionalidad_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_nacionalidad_id'], label: 'Nacionalidad');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_ocupacion_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_ocupacion_id'], label: 'Ocupacion');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    private function row_base_fiscal(controlador_inm_prospecto $controlador): stdClass
    {
        if($controlador->registro['inm_prospecto_nss'] === ''){
            $controlador->row_upd->nss = '99999999999';
        }
        if($controlador->registro['inm_prospecto_curp'] === ''){
            $controlador->row_upd->curp = 'XEXX010101HNEXXXA4';
        }
        if($controlador->registro['inm_prospecto_rfc'] === ''){
            $controlador->row_upd->rfc = 'XAXX010101000';
        }
        return $controlador->row_upd;
    }

    /**
     * Verifica si hay datos para transaccionar de conyuge
     * @param array $conyuge Registro a verificar
     * @return bool
     * @version 2.260.2
     */
    private function tiene_dato_conyuge(array $conyuge): bool
    {
        $tiene_dato_conyuge = false;
        foreach ($conyuge as $value){
            if($value === null){
                $value = '';
            }
            $value = trim($value);
            if($value!==''){
                $tiene_dato_conyuge = true;
                break;
            }
        }
        return $tiene_dato_conyuge;
    }
}
