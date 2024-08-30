<?php
namespace gamboamartin\inmuebles\controllers;

use base\controller\init;
use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_prospecto_ubicacion;
use gamboamartin\validacion\validacion;
use html\dp_estado_html;
use html\dp_municipio_html;
use PDO;
use stdClass;

class _inm_prospecto_ubicacion{

    private errores $error;
    private validacion $validacion;

    public function __construct(){
        $this->error = new errores();
        $this->validacion = new validacion();
    }

    /**
     * Obtiene los datos de children
     * @param bool $existe Init existe bool
     * @param string $key_data Keys a integrar en la actualizacion
     * @return array|stdClass
     */
    final public function dato(bool $existe, string $key_data): array|stdClass
    {
        $key_data = trim($key_data);
        if($key_data === ''){
            return $this->error->error(mensaje: 'Error key_data esta vacio',data:  $key_data);
        }

        $row = $this->init_post(key_data: $key_data);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar row',data:  $row);
        }

        $tiene_dato = $this->tiene_dato(row: $row);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar si tiene dato tiene_dato',data:  $tiene_dato);
        }
        $datos = new stdClass();
        $datos->existe = $existe;
        $datos->row = $row;
        $datos->tiene_dato = $tiene_dato;
        return $datos;
    }



    /**
     * Obtiene los datos de un conyuge
     * @param PDO $link Conexion a la base de datos
     * @param int $inm_prospecto_id prospecto
     * @return array|stdClass
     * @version 2.323.2
     */
    final public function datos_conyuge(PDO $link, int $inm_prospecto_id): array|stdClass
    {

        $existe_conyuge = false;

        if($inm_prospecto_id > 0) {

            $existe_conyuge = (new inm_prospecto_ubicacion(link: $link))->existe_conyuge(inm_prospecto_id: $inm_prospecto_id);
            if (errores::$error) {
                return $this->error->error(mensaje: 'Error al validar si existe conyuge', data: $existe_conyuge);
            }
        }

        $datos = $this->dato(existe: $existe_conyuge,key_data:  'conyuge');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al inicializar datos',data:  $datos);
        }

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
    private function genera_keys_selects(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controlador, array $identificadores,
                                         array $keys_selects): array
    {
        foreach ($identificadores as $identificador=>$data){
            $identificador = trim($identificador);
            if($identificador === ''){
                return $this->error->error(mensaje: 'Error identificador esta vacio',data:  $identificador);
            }
            if(!is_array($data)){
                return $this->error->error(mensaje: 'Error data debe ser un array',data:  $data);
            }
            $filtro = array();
            if(isset($data['filtro'])){
                $filtro = $data['filtro'];
            }
            $cols = 12;
            if(isset($data['cols'])){
                $cols = $data['cols'];
            }
            $id_selected = -1;
            if(isset($controlador->registro[$identificador])){
                $id_selected = $controlador->registro[$identificador];
            }
            $title = $identificador;
            if(isset($data['title'])){
                $title = $data['title'];
            }
            $disabled = false;
            if(isset($data['disabled'])){
                $disabled = $data['disabled'];
            }
            $columns_ds = array();
            if(isset($data['columns_ds'])){
                $columns_ds = $data['columns_ds'];
            }

            $keys_selects = $controlador->key_select(cols: $cols, con_registros: true, filtro: $filtro,
                key: $identificador, keys_selects: $keys_selects, id_selected: $id_selected, label: $title,
                columns_ds: $columns_ds, disabled: $disabled);
            if(errores::$error){
                return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
            }
        }
        return $keys_selects;
    }

    /**
     * Genera los headers para ser mostrados en front por seccion
     * @param controlador_inm_prospecto $controlador
     * @return array
     */
    final public function headers_front(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controlador): array
    {
        $headers = $this->headers_prospecto();
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers',data:  $headers);
        }

        $headers = (new \gamboamartin\inmuebles\html\_base(html: $controlador->html_base))->genera_headers(
            controler: $controlador,headers:  $headers, acciones_headers: $controlador->acciones_headers);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al generar headers',data:  $headers);
        }
        return $headers;
    }

    /**
     * Obtiene los headers para frontend de views
     * @return array
     * @version 2.302.2
     */
    private function headers_prospecto(): array
    {
        $headers = array();
        $headers['1'] = '1. DATOS PERSONALES';
        $headers['2'] = '2. DATOS DE CONTACTO';
        $headers['3'] = '3. VIVIENDA';
        $headers['4'] = '4. ADEUDO';
        $headers['5'] = '5. CONYUGE';

        return $headers;
    }

    /**
     * Genera los identificadores para keys selects
     * @param array $filtro Filtro de integracion par agente
     * @return array
     */
    private function identificadores_comercial(array $filtro): array
    {
        $identificadores['com_agente_id']['title'] = 'Agente';
        $identificadores['com_agente_id']['cols'] = 12;
        $identificadores['com_agente_id']['disabled'] = false;
        $identificadores['com_agente_id']['filtro'] = $filtro;
        $identificadores['com_agente_id']['columns_ds'] = array();

        $identificadores['com_tipo_prospecto_id']['title'] = 'Tipo de prospecto';
        $identificadores['com_tipo_prospecto_id']['cols'] = 12;
        $identificadores['com_tipo_prospecto_id']['disabled'] = false;
        $identificadores['com_tipo_prospecto_id']['columns_ds'] = array('com_tipo_prospecto_descripcion');

        $identificadores['com_medio_prospeccion_id']['title'] = 'Medio Prospeccion';
        $identificadores['com_medio_prospeccion_id']['cols'] = 12;
        $identificadores['com_medio_prospeccion_id']['disabled'] = false;
        $identificadores['com_medio_prospeccion_id']['columns_ds'] = array('com_medio_prospeccion_descripcion');

        $identificadores['com_prospecto_id']['title'] = 'Prospecto';
        $identificadores['com_prospecto_id']['cols'] = 12;
        $identificadores['com_prospecto_id']['disabled'] = false;
        $identificadores['com_prospecto_id']['columns_ds'] = array('com_prospecto_descripcion');

        $identificadores['com_tipo_direccion_id']['title'] = 'Tipo Dirección';
        $identificadores['com_tipo_direccion_id']['cols'] = 12;
        $identificadores['com_tipo_direccion_id']['disabled'] = false;
        $identificadores['com_tipo_direccion_id']['columns_ds'] = array('com_tipo_direccion_descripcion');

        $identificadores['inm_prototipo_id']['title'] = 'Prototipo';
        $identificadores['inm_prototipo_id']['cols'] = 12;
        $identificadores['inm_prototipo_id']['disabled'] = false;
        $identificadores['inm_prototipo_id']['columns_ds'] = array('inm_prototipo_descripcion');
        
        $identificadores['inm_complemento_id']['title'] = 'Complemento';
        $identificadores['inm_complemento_id']['cols'] = 12;
        $identificadores['inm_complemento_id']['disabled'] = false;
        $identificadores['inm_complemento_id']['columns_ds'] = array('inm_complemento_descripcion');

        $identificadores['inm_estado_vivienda_id']['title'] = 'Estado Vivienda';
        $identificadores['inm_estado_vivienda_id']['cols'] = 12;
        $identificadores['inm_estado_vivienda_id']['disabled'] = false;
        $identificadores['inm_estado_vivienda_id']['columns_ds'] = array('inm_estado_vivienda_descripcion');

        $identificadores['dp_municipio_id']['title'] = 'Municipio';
        $identificadores['dp_municipio_id']['cols'] = 6;
        $identificadores['dp_municipio_id']['disabled'] = false;
        $identificadores['dp_municipio_id']['columns_ds'] = array('dp_municipio_descripcion');

        $identificadores['dp_estado_id']['title'] = 'Estado';
        $identificadores['dp_estado_id']['cols'] = 6;
        $identificadores['dp_estado_id']['disabled'] = false;
        $identificadores['dp_estado_id']['columns_ds'] = array('dp_estado_descripcion');

        $identificadores['dp_pais_id']['title'] = 'Pais';
        $identificadores['dp_pais_id']['cols'] = 6;
        $identificadores['dp_pais_id']['disabled'] = false;
        $identificadores['dp_pais_id']['columns_ds'] = array('dp_pais_descripcion');

        return $identificadores;
    }

    /**
     * Genera los identificadores para direcciones
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return array
     */
    private function identificadores_dp(controlador_inm_prospecto $controlador): array
    {
        $row = $controlador->registro;
        $keys = array('dp_pais_id','dp_estado_id','dp_municipio_id','dp_cp_id','dp_colonia_postal_id');
        foreach ($keys as $key){
            if(!isset($row[$key])){
                $row[$key] = -1;
            }
        }

        $identificadores['dp_pais_id']['title'] = 'Pais';
        $identificadores['dp_pais_id']['cols'] = 6;
        $identificadores['dp_pais_id']['disabled'] = false;
        $identificadores['dp_pais_id']['filtro'] = array();
        $identificadores['dp_pais_id']['columns_ds'] = array('dp_pais_descripcion');

        $filtro = array();
        $filtro['dp_pais.id'] = $row['dp_pais_id'];
        $identificadores['dp_estado_id']['title'] = 'Estado';
        $identificadores['dp_estado_id']['cols'] = 6;
        $identificadores['dp_estado_id']['disabled'] = false;
        $identificadores['dp_estado_id']['filtro'] = $filtro;
        $identificadores['dp_estado_id']['columns_ds'] =  array('dp_estado_descripcion');

        $filtro = array();
        $filtro['dp_estado.id'] = $row['dp_estado_id'];
        $identificadores['dp_municipio_id']['title'] = 'Municipio';
        $identificadores['dp_municipio_id']['cols'] = 6;
        $identificadores['dp_municipio_id']['disabled'] = false;
        $identificadores['dp_municipio_id']['filtro'] = $filtro;
        $identificadores['dp_municipio_id']['columns_ds'] =  array('dp_municipio_descripcion');

        $filtro = array();
        $filtro['dp_municipio.id'] = $row['dp_municipio_id'];
        $identificadores['dp_cp_id']['title'] = 'CP';
        $identificadores['dp_cp_id']['cols'] = 6;
        $identificadores['dp_cp_id']['disabled'] = false;
        $identificadores['dp_cp_id']['filtro'] = $filtro;
        $identificadores['dp_cp_id']['columns_ds'] =  array('dp_cp_codigo');

        $filtro = array();
        $filtro['dp_cp.id'] = $row['dp_cp_id'];
        $identificadores['dp_colonia_postal_id']['title'] = 'Colonia';
        $identificadores['dp_colonia_postal_id']['cols'] = 6;
        $identificadores['dp_colonia_postal_id']['disabled'] = false;
        $identificadores['dp_colonia_postal_id']['filtro'] = $filtro;
        $identificadores['dp_colonia_postal_id']['columns_ds'] =  array('dp_colonia_descripcion');

        $filtro = array();
        $filtro['dp_colonia_postal.id'] = $row['dp_colonia_postal_id'];
        $identificadores['dp_calle_pertenece_id']['title'] = 'Calle';
        $identificadores['dp_calle_pertenece_id']['cols'] = 6;
        $identificadores['dp_calle_pertenece_id']['disabled'] = false;
        $identificadores['dp_calle_pertenece_id']['filtro'] = $filtro;
        $identificadores['dp_calle_pertenece_id']['columns_ds'] = array('dp_calle_descripcion');
        return $identificadores;
    }

    /**
     * Integra los identificadores para la creacion de un parametro de tipo key select
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return array
     */
    private function identificadores_infonavit(controlador_inm_prospecto $controlador): array
    {
        $keys = array('inm_prospecto_es_segundo_credito');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        $identificadores['inm_institucion_hipotecaria_id']['title'] = 'Institucion Hipotecaria';
        $identificadores['inm_institucion_hipotecaria_id']['cols'] = 12;
        $identificadores['inm_institucion_hipotecaria_id']['disabled'] = false;
        $identificadores['inm_institucion_hipotecaria_id']['columns_ds'] = array('inm_institucion_hipotecaria_descripcion');

        $identificadores['inm_producto_infonavit_id']['title'] = 'Producto Infonavit';
        $identificadores['inm_producto_infonavit_id']['cols'] = 6;
        $identificadores['inm_producto_infonavit_id']['disabled'] = false;
        $identificadores['inm_producto_infonavit_id']['columns_ds'] = array('inm_producto_infonavit_descripcion');

        $identificadores['inm_attr_tipo_credito_id']['title'] = 'Tipo de Credito';
        $identificadores['inm_attr_tipo_credito_id']['cols'] = 6;
        $identificadores['inm_attr_tipo_credito_id']['disabled'] = false;
        $identificadores['inm_attr_tipo_credito_id']['columns_ds'] = array('inm_attr_tipo_credito_descripcion');

        $identificadores['inm_destino_credito_id']['title'] = 'Destino de Credito';
        $identificadores['inm_destino_credito_id']['cols'] = 12;
        $identificadores['inm_destino_credito_id']['disabled'] = false;
        $identificadores['inm_destino_credito_id']['columns_ds'] = array('inm_destino_credito_descripcion');

        $identificadores['inm_tipo_discapacidad_id']['title'] = 'Tipo de Discapacidad';
        $identificadores['inm_tipo_discapacidad_id']['cols'] = 6;
        $identificadores['inm_tipo_discapacidad_id']['disabled'] = false;
        $identificadores['inm_tipo_discapacidad_id']['columns_ds'] = array('inm_tipo_discapacidad_descripcion');

        $identificadores['inm_persona_discapacidad_id']['title'] = 'Persona de Discapacidad';
        $identificadores['inm_persona_discapacidad_id']['cols'] = 6;
        $identificadores['inm_persona_discapacidad_id']['disabled'] = false;
        $identificadores['inm_persona_discapacidad_id']['columns_ds'] = array('inm_persona_discapacidad_descripcion');

        $disabled = $this->disabled_segundo_credito(registro: $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar disabled',data:  $disabled);
        }

        $identificadores['inm_plazo_credito_sc_id']['title'] = 'Plazo de Segundo Credito';
        $identificadores['inm_plazo_credito_sc_id']['cols'] = 6;
        $identificadores['inm_plazo_credito_sc_id']['disabled'] = $disabled;
        $identificadores['inm_plazo_credito_sc_id']['columns_ds'] = array('inm_plazo_credito_sc_descripcion');

        return $identificadores;
    }

    /**
     * Genera los identificadores para creacion de keys selects
     * @return array
     */
    private function identificadores_personal(): array
    {
        $identificadores['inm_sindicato_id']['title'] = 'Sindicato';
        $identificadores['inm_sindicato_id']['cols'] = 12;
        $identificadores['inm_sindicato_id']['disabled'] = false;
        $identificadores['inm_sindicato_id']['columns_ds'] = array('inm_sindicato_descripcion');

        $identificadores['inm_nacionalidad_id']['title'] = 'Nacionalidad';
        $identificadores['inm_nacionalidad_id']['cols'] = 6;
        $identificadores['inm_nacionalidad_id']['disabled'] = false;
        $identificadores['inm_nacionalidad_id']['columns_ds'] = array('inm_nacionalidad_descripcion');

        $identificadores['inm_ocupacion_id']['title'] = 'Ocupacion';
        $identificadores['inm_ocupacion_id']['cols'] = 6;
        $identificadores['inm_ocupacion_id']['disabled'] = false;
        $identificadores['inm_ocupacion_id']['columns_ds'] = array('inm_ocupacion_descripcion');
        return $identificadores;
    }

    /**
     * Inicializa la entrada de POST para dependencias
     * @param string $key_data Keys a integrar
     * @return array
     */
    private function init_post(string $key_data): array
    {
        $key_data = trim($key_data);
        if($key_data === ''){
            return $this->error->error(mensaje: 'Error key_data esta vacio',data:  $key_data);
        }
        $data = array();
        if(isset($_POST[$key_data])){
            $data = $_POST[$key_data];
            if(is_string($data)){
                return $this->error->error(mensaje: 'Error POST '.$key_data.' debe ser un array',data:  $data);
            }
            unset($_POST[$key_data]);
        }
        return $data;
    }

    /**
     * Genera lso inputs base de un prospecto
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return array|stdClass
     */
    final public function inputs_base(controlador_inm_prospecto_ubicacion $controlador): array|stdClass
    {
        $valida = $this->valida_base(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

        if(is_array($controlador->inputs)){
            return $this->error->error(mensaje: 'Error controler->inputs no esta inicializado',
                data: $controlador->inputs);
        }

        $keys_selects = array();

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'nss_extra',
            keys_selects:$keys_selects, place_holder: 'NSS', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects['nss_extra']->disabled = true;
        $controlador->row_upd->nss_extra = $controlador->row_upd->nss;

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'correo_mi_cuenta_infonavit',
            keys_selects:$keys_selects, place_holder: 'Correo', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        if($controlador->row_upd->correo_mi_cuenta_infonavit === 'sincorreo@correo.com'){
            $controlador->row_upd->correo_mi_cuenta_infonavit = $controlador->row_upd->correo_com;
        }

        $keys_selects['correo_mi_cuenta_infonavit']->regex = $this->validacion->patterns['correo_html5'];

        $keys_selects = (new init())->key_select_txt(cols: 6,key: 'password_mi_cuenta_infonavit',
            keys_selects:$keys_selects, place_holder: 'Contraseña', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'liga_red_social',
            keys_selects:$keys_selects, place_holder: 'Liga Red Social', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects['liga_red_social']->disabled = true;

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'direccion_empresa',
            keys_selects:$keys_selects, place_holder: 'Direccion Empresa', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = (new init())->key_select_txt(cols: 12,key: 'area_empresa',
            keys_selects:$keys_selects, place_holder: 'Area Empresa', required: false);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

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

        $extra_params_keys[] = 'com_medio_prospeccion_id';
        $extra_params_keys[] = 'com_medio_prospeccion_es_red_social';
        $keys_selects['com_medio_prospeccion_id']->extra_params_keys = $extra_params_keys;

        $row = $this->row_base_fiscal(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al row',data:  $row);
        }

        if(!isset($controlador->row_upd->es_segundo_credito)){
            $controlador->row_upd->es_segundo_credito = 'NO';
        }
        if(!isset($controlador->row_upd->con_discapacidad)){
            $controlador->row_upd->con_discapacidad = 'NO';
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

    /**
     * Genera los inputs de nacimiento
     * @param controlador_inm_prospecto $controlador
     * @return array|stdClass
     */
    final public function inputs_nacimiento(controlador_inm_prospecto $controlador): array|stdClass
    {
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

    private function integra_button_del(controlador_inm_prospecto $controlador, array $data, array $datas,
                                        int $indice, array $params, string $seccion_exe){
        $key_id = $seccion_exe.'_id';
        $btn_del = $controlador->html->button_href(accion: 'elimina_bd',etiqueta: 'Elimina',
            registro_id:  $data[$key_id],seccion: $seccion_exe,style: 'danger',
            params: $params);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener link_del',data:  $btn_del);
        }
        $datas[$indice]['btn_del'] = $btn_del;
        return $datas;
    }

    private function integra_button_mod(controlador_inm_prospecto $controlador, array $data, array $datas,
                                        int $indice, array $params, string $seccion_exe){
        $key_id = $seccion_exe.'_id';
        $btn_mod = $controlador->html->button_href(accion: 'modifica',etiqueta: 'Modifica',
            registro_id:  $data[$key_id],seccion: $seccion_exe,style: 'warning',
            params: $params);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener link_mod',data:  $btn_mod);
        }
        $datas[$indice]['btn_mod'] = $btn_mod;
        return $datas;
    }

    /**
     * Integra los parametros de selectores de tipo comercial
     * @param controlador_inm_prospecto $controlador
     * @param array $keys_selects
     * @return array
     * @version 2.266.2
     */
    public function integra_keys_selects_comercial(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controlador, array $keys_selects): array
    {
        $keys = array('com_agente_id','com_tipo_prospecto_id','com_medio_prospeccion_id', 'com_prospecto_id',
            'inm_prototipo_id','inm_complemento_id','inm_estado_vivienda_id','dp_pais_id','dp_estado_id','dp_municipio_id');
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
    private function keys_selects_comercial(controlador_inm_prospecto|controlador_inm_prospecto_ubicacion $controlador, array $filtro,
                                           array $keys_selects): array
    {
        $keys = array('com_agente_id','com_tipo_prospecto_id','com_medio_prospeccion_id', 'com_prospecto_id',
            'inm_prototipo_id','inm_complemento_id','inm_estado_vivienda_id','dp_pais_id','dp_estado_id','dp_municipio_id');
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

    /**
     * Genera los selectores de tipo domicilio
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @param array $keys_selects Parametros cargados previamente
     * @return array
     */
    private function keys_selects_dp(controlador_inm_prospecto $controlador, array $keys_selects): array
    {

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

    /**
     * Genera los selectores base de infonavit
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @param array $keys_selects Parametros previos cargados
     * @return array
     */
    private function keys_selects_infonavit(controlador_inm_prospecto $controlador, array $keys_selects): array
    {

        $valida = $this->valida_base(controlador: $controlador);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }

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

    /**
     * Genera los selectores personales
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @param array $keys_selects Parametros previos cargados
     * @return array
     */
    private function keys_selects_personal(controlador_inm_prospecto $controlador, array $keys_selects): array
    {

        $identificadores = $this->identificadores_personal();
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

    final public function params_btn(string $accion_retorno, int $registro_id, string $seccion_retorno ): array
    {
        $params['siguiente_view'] = $accion_retorno;
        $params['accion_retorno'] = $accion_retorno;
        $params['seccion_retorno'] = $seccion_retorno;
        $params['id_retorno'] = $registro_id;
        return $params;
    }

    /**
     * Inicializa los elementos fiscales de identificacion
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return stdClass|array
     * @version 2.287.2
     */
    private function row_base_fiscal(controlador_inm_prospecto $controlador): stdClass|array
    {
        if(!isset($controlador->registro['inm_prospecto_nss'])){
            $controlador->registro['inm_prospecto_nss'] = '99999999999';
            $controlador->row_upd->nss = '99999999999';
        }
        if(!isset($controlador->registro['inm_prospecto_curp'] )){
            $controlador->registro['inm_prospecto_curp'] = 'XEXX010101HNEXXXA4';
            $controlador->row_upd->curp = 'XEXX010101HNEXXXA4';
        }
        if(!isset($controlador->registro['inm_prospecto_rfc'] )){
            $controlador->registro['inm_prospecto_rfc'] = 'XAXX010101000';
            $controlador->row_upd->rfc = 'XAXX010101000';
        }

        if($controlador->registro['inm_prospecto_nss'] === ''){
            $controlador->row_upd->nss = '99999999999';
            $controlador->row_upd->rfc = 'XAXX010101000';
        }
        if($controlador->registro['inm_prospecto_curp'] === ''){
            $controlador->row_upd->curp = 'XEXX010101HNEXXXA4';
        }
        if($controlador->registro['inm_prospecto_rfc'] === ''){
            $controlador->row_upd->rfc = 'XAXX010101000';
        }
        return $controlador->row_upd;
    }

    final public function rows(controlador_inm_prospecto $controlador, array $datas, array $params, string $seccion_exe){

        foreach ($datas as $indice=>$data){

            $datas = $this->integra_button_del(
                controlador: $controlador, data: $data,datas:  $datas,indice:  $indice,params:  $params,seccion_exe:  $seccion_exe);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener beneficiarios link del',data:  $datas);
            }
        }
        return $datas;

    }

    final public function rows_direccion(controlador_inm_prospecto $controlador, array $datas, array $params,
                                         string $seccion_exe, string  $seccion_sec){

        foreach ($datas as $indice=>$data){

            $datas = $this->integra_button_del(
                controlador: $controlador, data: $data,datas:  $datas,indice:  $indice,params:  $params,seccion_exe:  $seccion_sec);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener beneficiarios link del',data:  $datas);
            }

            $datas = $this->integra_button_mod(
                controlador: $controlador, data: $data,datas:  $datas,indice:  $indice,params:  $params,seccion_exe:  $seccion_exe);

            if(errores::$error){
                return $this->error->error(mensaje: 'Error al obtener beneficiarios link del',data:  $datas);
            }
        }
        return $datas;

    }

    /**
     * Valida si un registro tiene o no un datos
     * @param array $row Registro en proceso
     * @return bool
     * @version 2.289.2
     */
    private function tiene_dato(array $row): bool
    {
        $tiene_dato = false;
        foreach ($row as $value){
            if($value === null){
                $value = '';
            }
            $value = trim($value);
            if($value!==''){
                $tiene_dato = true;
                break;
            }
        }
        return $tiene_dato;
    }

    /**
     * Valida los elementos base de un prospecto
     * @param controlador_inm_prospecto $controlador Controlador en ejecucion
     * @return array|true
     * @version 2.296.2
     */
    private function valida_base(controlador_inm_prospecto $controlador): bool|array
    {
        $keys = array('com_agente_id','com_tipo_prospecto_id');
        $valida = $this->validacion->valida_ids(keys: $keys,registro:  $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al valida controlador registro',data:  $valida);
        }
        $keys = array('inm_prospecto_es_segundo_credito');
        $valida = $this->validacion->valida_existencia_keys(keys: $keys,registro:  $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al validar registro',data:  $valida);
        }
        return true;
    }

}
