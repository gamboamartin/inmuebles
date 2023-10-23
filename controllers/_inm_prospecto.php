<?php
namespace gamboamartin\inmuebles\controllers;

use gamboamartin\administrador\models\adm_usuario;
use gamboamartin\errores\errores;
use stdClass;

class _inm_prospecto{

    private errores $error;

    public function __construct(){
        $this->error = new errores();
    }

    private function disabled_segundo_credito(array $registro): bool
    {
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
     */
    private function filtro_user(array $adm_usuario): array
    {
        $filtro = array();
        if($adm_usuario['adm_grupo_root'] === 'inactivo'){
            $filtro['adm_usuario.id'] = $_SESSION['usuario_id'];
        }
        return $filtro;
    }

    final public function headers_prospecto(): array
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

    final public function keys_selects_dp(controlador_inm_prospecto $controlador, array $keys_selects){
        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'dp_pais_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_pais_id'], label: 'Pais');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_pais.id'] = $controlador->registro['dp_pais_id'];

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro: $filtro, key: 'dp_estado_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_estado_id'], label: 'Estado');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_estado.id'] = $controlador->registro['dp_estado_id'];

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_municipio_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_municipio_id'], label: 'Municipio');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_municipio.id'] = $controlador->registro['dp_municipio_id'];
        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_cp_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_cp_id'], label: 'CP');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_cp.id'] = $controlador->registro['dp_cp_id'];
        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_colonia_postal_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_colonia_postal_id'], label: 'Colonia');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $filtro = array();
        $filtro['dp_colonia_postal.id'] = $controlador->registro['dp_colonia_postal_id'];
        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  $filtro, key: 'dp_calle_pertenece_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['dp_calle_pertenece_id'], label: 'Calle');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function keys_selects_infonavit(controlador_inm_prospecto $controlador, array $keys_selects){

        $adm_usuario = (new adm_usuario(link: $controlador->link))->registro(registro_id: $_SESSION['usuario_id'],
            columnas: array('adm_grupo_root'));
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener adm_usuario ',data:  $adm_usuario);
        }
        
        $filtro = $this->filtro_user(adm_usuario: $adm_usuario);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al obtener filtro ',data:  $filtro);
        }

        $keys_selects = $controlador->key_select(cols:12, con_registros: true,filtro:  $filtro, key: 'com_agente_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['com_agente_id'], label: 'Agente');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:12, con_registros: true,filtro:  array(), key: 'com_tipo_prospecto_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['com_tipo_prospecto_id'], label: 'Tipo de prospecto');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_institucion_hipotecaria_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_institucion_hipotecaria_id'], label: 'Institucion Hipotecaria');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_producto_infonavit_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_producto_infonavit_id'], label: 'Producto Infonavit');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_attr_tipo_credito_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_attr_tipo_credito_id'], label: 'Tipo de Credito');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        $keys_selects = $controlador->key_select(cols:12, con_registros: true,filtro:  array(), key: 'inm_destino_credito_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_destino_credito_id'], label: 'Destino de Credito');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $disabled = $this->disabled_segundo_credito(registro: $controlador->registro);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar disabled',data:  $disabled);
        }


        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_plazo_credito_sc_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_plazo_credito_sc_id'], label: 'Plazo de Segundo Credito',disabled: $disabled);
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_tipo_discapacidad_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_tipo_discapacidad_id'], label: 'Tipo de Discapacidad');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }

        $keys_selects = $controlador->key_select(cols:6, con_registros: true,filtro:  array(), key: 'inm_persona_discapacidad_id',
            keys_selects:$keys_selects, id_selected: $controlador->registro['inm_persona_discapacidad_id'], label: 'Persona de Discapacidad');
        if(errores::$error){
            return $this->error->error(mensaje: 'Error al maquetar key_selects',data:  $keys_selects);
        }
        return $keys_selects;
    }

    final public function row_base_fiscal(controlador_inm_prospecto $controlador): stdClass
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
}
