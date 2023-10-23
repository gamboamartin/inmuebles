<?php
namespace gamboamartin\inmuebles\controllers;

use stdClass;

class _inm_prospecto{

    final public function disabled_segundo_credito(array $registro): bool
    {
        $disabled = true;
        if($registro['inm_prospecto_es_segundo_credito'] === 'SI'){
            $disabled = false;
        }
        return $disabled;
    }

    /**
     * @param array $adm_usuario
     * @return array
     */
    final public function filtro_user(array $adm_usuario): array
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
