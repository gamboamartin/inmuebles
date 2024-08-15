<?php
namespace gamboamartin\inmuebles\instalacion;
use gamboamartin\administrador\instalacion\_adm;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\direccion_postal\models\dp_calle_pertenece;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_base_comprador;
use gamboamartin\inmuebles\models\inm_comprador;
use PDO;
use stdClass;

class instalacion
{



    private function _add_inm_comprador(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_comprador');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $campos_new = array('descuento_pension_alimenticia_dh','descuento_pension_alimenticia_fc',
            'monto_credito_solicitado_dh','monto_ahorro_voluntario','monto_final','sub_cuenta','descuento','puntos');

        $columnas = $init->campos_double(campos: $columnas,campos_new:  $campos_new);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar campo double', data:  $columnas);
        }


        $columnas->es_segundo_credito = new stdClass();
        $columnas->es_segundo_credito->default = 'NO';

        $columnas->nss = new stdClass();
        $columnas->curp = new stdClass();
        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();
        $columnas->apellido_materno = new stdClass();

        $columnas->con_discapacidad = new stdClass();
        $columnas->con_discapacidad->default = 'NO';

        $columnas->nombre_empresa_patron = new stdClass();
        $columnas->nrp_nep = new stdClass();
        $columnas->lada_nep = new stdClass();
        $columnas->numero_nep = new stdClass();
        $columnas->extension_nep = new stdClass();
        $columnas->lada_com = new stdClass();
        $columnas->numero_com = new stdClass();
        $columnas->cel_com = new stdClass();
        $columnas->genero = new stdClass();
        $columnas->correo_com = new stdClass();
        $columnas->etapa = new stdClass();
        $columnas->proceso = new stdClass();

        $columnas->telefono_casa = new stdClass();
        $columnas->telefono_casa->default = '3333333333';

        $columnas->correo_empresa = new stdClass();
        $columnas->correo_empresa->default = 'sincorreo@correo.com';

        $columnas->fecha_nacimiento = new stdClass();
        $columnas->fecha_nacimiento->tipo_dato = 'DATE';
        $columnas->fecha_nacimiento->default = '1900-01-01';

        $add_colums = $init->add_columns(campos: $columnas,table:  'inm_comprador');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['inm_producto_infonavit_id'] = new stdClass();
        $foraneas['inm_attr_tipo_credito_id'] = new stdClass();
        $foraneas['inm_destino_credito_id'] = new stdClass();
        $foraneas['inm_plazo_credito_sc_id'] = new stdClass();
        $foraneas['inm_tipo_discapacidad_id'] = new stdClass();
        $foraneas['inm_estado_civil_id'] = new stdClass();
        $foraneas['bn_cuenta_id'] = new stdClass();
        $foraneas['inm_persona_discapacidad_id'] = new stdClass();
        $foraneas['inm_institucion_hipotecaria_id'] = new stdClass();
        $foraneas['dp_municipio_nacimiento_id'] = new stdClass();
        $foraneas['inm_nacionalidad_id'] = new stdClass();
        $foraneas['inm_ocupacion_id'] = new stdClass();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['dp_calle_pertenece_id']->default = '100';
        $foraneas['dp_calle_pertenece_id']->modelo = new inm_comprador(link: $link, valida_atributos_criticos: false);


        $result = $init->foraneas(foraneas: $foraneas,table:  'inm_comprador');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        $modelo = new inm_comprador(link: $link);

        $registros = $modelo->registros(columnas_en_bruto: true, return_obj: true);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener registros', data:  $result);
        }

        foreach ($registros as $registro){
            $registro_id = $registro->id;

            $tiene_cliente = (new inm_comprador(link: $link))->tiene_cliente(inm_comprador_id: $registro_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si tiene cliente', data:  $tiene_cliente);
            }

            if(!$tiene_cliente){

                if(!isset($registro->rfc)|| trim($registro->rfc) === ''){
                    $registro->rfc = 'AAA010101AAA';
                }
                if(!isset($registro->numero_exterior)|| trim($registro->numero_exterior) === ''){
                    $registro->numero_exterior = 'SIN NUM';
                }
                if(!isset($registro->cat_sat_regimen_fiscal_id)|| trim($registro->cat_sat_regimen_fiscal_id) === ''){
                    $registro->cat_sat_regimen_fiscal_id = 605;
                }
                if(!isset($registro->cat_sat_moneda_id)|| trim($registro->cat_sat_moneda_id) === ''){
                    $registro->cat_sat_moneda_id = 161;
                }
                if(!isset($registro->cat_sat_forma_pago_id)|| trim($registro->cat_sat_forma_pago_id) === ''){
                    $registro->cat_sat_forma_pago_id = 99;
                }
                if(!isset($registro->cat_sat_metodo_pago_id)|| trim($registro->cat_sat_metodo_pago_id) === ''){
                    $registro->cat_sat_metodo_pago_id = 2;
                }
                if(!isset($registro->cat_sat_uso_cfdi_id)|| trim($registro->cat_sat_uso_cfdi_id) === ''){
                    $registro->cat_sat_uso_cfdi_id = 22;
                }
                if(!isset($registro->com_tipo_cliente_id)|| trim($registro->com_tipo_cliente_id) === ''){
                    $registro->com_tipo_cliente_id = 7;
                }
                if(!isset($registro->cat_sat_tipo_persona_id)|| trim($registro->cat_sat_tipo_persona_id) === ''){
                    $registro->cat_sat_tipo_persona_id = 5;
                }
                if(!isset($registro->cp)|| trim($registro->cp) === ''){
                    $registro->cp = 99999;
                }
                if(!isset($registro->dp_municipio_id)|| trim($registro->dp_municipio_id) === ''){
                    $registro->dp_municipio_id = 2467;
                }

                $integra_relacion_com_cliente = (new _base_comprador())->integra_relacion_com_cliente(
                    inm_comprador_id: $registro_id, link: $link, registro_entrada: (array)$registro);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al integrar cliente',
                        data: $integra_relacion_com_cliente);
                }
            }

            $com_cliente = (new inm_comprador(link: $link))->get_com_cliente(inm_comprador_id: $registro_id,
                columnas_en_bruto: true, retorno_obj: true);

            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener com_cliente', data:  $com_cliente);
            }
            $dp_calle_pertenece_id = $com_cliente->dp_calle_pertenece_id;

            $dp_calle_pertenece = (new dp_calle_pertenece(link: $link))->registro(registro_id: $dp_calle_pertenece_id, retorno_obj: true);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al obtener dp_calle_pertenece', data:  $dp_calle_pertenece,);
            }


            if((int)$registro->dp_calle_pertenece_id !== (int)$dp_calle_pertenece->dp_calle_pertenece_id){
                $upd = array();
                $upd['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;

                $upd_data = $modelo->modifica_bd_base(registro: $upd,id:  $registro_id);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar upd_data', data:  $upd_data,);
                }
            }

            //print_r($dp_calle_pertenece);exit;
        }

        //print_r($registros);exit;



        return $out;
    }
    private function _add_inm_prospecto(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_prospecto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $campos_new = array('descuento_pension_alimenticia_dh','descuento_pension_alimenticia_fc',
            'monto_credito_solicitado_dh','monto_ahorro_voluntario','monto_final','sub_cuenta','descuento','puntos');

        $columnas = $init->campos_double(campos: $columnas,campos_new:  $campos_new);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar campo double', data:  $columnas);
        }


        $columnas->nss = new stdClass();
        $columnas->curp = new stdClass();
        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();
        $columnas->apellido_materno = new stdClass();


        $columnas->nombre_empresa_patron = new stdClass();
        $columnas->nrp_nep = new stdClass();
        $columnas->lada_nep = new stdClass();
        $columnas->numero_nep = new stdClass();
        $columnas->extension_nep = new stdClass();
        $columnas->lada_com = new stdClass();
        $columnas->numero_com = new stdClass();
        $columnas->cel_com = new stdClass();
        $columnas->genero = new stdClass();
        $columnas->correo_com = new stdClass();
        $columnas->etapa = new stdClass();
        $columnas->proceso = new stdClass();

        $columnas->razon_social = new stdClass();
        $columnas->razon_social->default = 'POR ASIGNAR';

        $columnas->rfc = new stdClass();
        $columnas->rfc->default = 'XAXX010101000';

        $columnas->numero_exterior = new stdClass();
        $columnas->numero_exterior->default = 'SN';

        $columnas->numero_interior = new stdClass();
        $columnas->numero_interior->default = 'SN';

        $columnas->observaciones = new stdClass();
        $columnas->observaciones->tipo_dato = 'TEXT';

        $columnas->fecha_nacimiento = new stdClass();
        $columnas->fecha_nacimiento->tipo_dato = 'DATE';
        $columnas->fecha_nacimiento->default = '1900-01-01';


        $columnas->telefono_casa = new stdClass();
        $columnas->telefono_casa->default = '3333333333';

        $columnas->correo_empresa = new stdClass();
        $columnas->correo_empresa->default = 'sincorreo@correo.com';

        $columnas->es_segundo_credito = new stdClass();
        $columnas->es_segundo_credito->defautl = 'NO';

        $columnas->con_discapacidad = new stdClass();
        $columnas->con_discapacidad->defautl = 'NO';

        $columnas->nombre_completo_valida = new stdClass();

        $columnas->liga_red_social = new stdClass();
        $columnas->liga_red_social->defautl = 'SIN LIGA';

        $columnas->correo_mi_cuenta_infonavit = new stdClass();
        $columnas->correo_mi_cuenta_infonavit->defautl = 'sincorreo@correo.com';

        $columnas->password_mi_cuenta_infonavit = new stdClass();
        $columnas->password_mi_cuenta_infonavit->defautl = 'SIN CONTRASEÑA';

        $columnas->direccion_empresa = new stdClass();
        $columnas->direccion_empresa->defautl = 'SIN DIRECCION';

        $columnas->area_empresa = new stdClass();
        $columnas->area_empresa->defautl = 'SIN AREA';

        $add_colums = $init->add_columns(campos: $columnas,table:  'inm_prospecto');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;


        $foraneas = array();
        $foraneas['com_prospecto_id'] = new stdClass();
        $foraneas['inm_producto_infonavit_id'] = new stdClass();
        $foraneas['inm_attr_tipo_credito_id'] = new stdClass();
        $foraneas['inm_destino_credito_id'] = new stdClass();
        $foraneas['inm_plazo_credito_sc_id'] = new stdClass();
        $foraneas['inm_tipo_discapacidad_id'] = new stdClass();
        $foraneas['inm_persona_discapacidad_id'] = new stdClass();
        $foraneas['inm_estado_civil_id'] = new stdClass();
        $foraneas['inm_institucion_hipotecaria_id'] = new stdClass();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['dp_municipio_id'] = new stdClass();
        $foraneas['dp_municipio_id']->name_indice_opt = 'dp_municipio_nacimiento_id';
        $foraneas['inm_nacionalidad_id'] = new stdClass();
        $foraneas['inm_ocupacion_id'] = new stdClass();
        $foraneas['inm_sindicato_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'inm_prospecto');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


        return $out;
    }

    private function _add_inm_prospecto_ubicacion(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_prospecto_ubicacion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();

        $campos_new = array('costo_directo','monto_opinion_promedio','costo','adeudo_hipoteca','adeudo_predial',
            'adeudo_agua','adeudo_luz','monto_devolucion');

        $columnas = $init->campos_double(campos: $columnas,campos_new:  $campos_new);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar campo double', data:  $columnas);
        }

        $columnas->nss = new stdClass();
        $columnas->curp = new stdClass();
        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();
        $columnas->apellido_materno = new stdClass();

        $columnas->lada_com = new stdClass();
        $columnas->numero_com = new stdClass();
        $columnas->cel_com = new stdClass();
        $columnas->correo_com = new stdClass();

        $columnas->lote = new stdClass();
        $columnas->manzana = new stdClass();
        $columnas->etapa = new stdClass();
        $columnas->cuenta_predial = new stdClass();
        $columnas->cuenta_agua = new stdClass();
        $columnas->n_opiniones_valor = new stdClass();
        $columnas->nivel = new stdClass();
        $columnas->recamaras = new stdClass();
        $columnas->metros_terreno = new stdClass();
        $columnas->metros_construccion = new stdClass();

        $columnas->razon_social = new stdClass();
        $columnas->razon_social->default = 'POR ASIGNAR';

        $columnas->rfc = new stdClass();
        $columnas->rfc->default = 'XAXX010101000';

        $columnas->numero_exterior = new stdClass();
        $columnas->numero_exterior->default = 'SN';

        $columnas->numero_interior = new stdClass();
        $columnas->numero_interior->default = 'SN';

        $columnas->observaciones = new stdClass();
        $columnas->observaciones->tipo_dato = 'TEXT';

        $columnas->nombre_completo_valida = new stdClass();

        $columnas->fecha_otorgamiento_credito = new stdClass();
        $columnas->fecha_otorgamiento_credito->tipo_dato = 'DATE';
        $columnas->fecha_otorgamiento_credito->default = '1900-01-01';

        $add_colums = $init->add_columns(campos: $columnas,table:  'inm_prospecto_ubicacion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $foraneas = array();
        $foraneas['com_prospecto_id'] = new stdClass();
        $foraneas['dp_calle_pertenece_id'] = new stdClass();
        $foraneas['com_tipo_prospecto_id'] = new stdClass();
        $foraneas['com_direccion_id'] = new stdClass();
        $foraneas['inm_prototipo_id'] = new stdClass();
        $foraneas['inm_complemento_id'] = new stdClass();
        $foraneas['inm_estado_vivienda_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'inm_prospecto_ubicacion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        return $out;
    }

    private function _add_inm_rel_prospecto_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_rel_prospecto_cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $foraneas = array();
        $foraneas['inm_comprador_id'] = new stdClass();
        $foraneas['inm_prospecto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  'inm_rel_prospecto_cliente');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;

        return $out;
    }

    private function _add_inm_sindicato(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_sindicato');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_prototipo(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_prototipo');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_complemento(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_complemento');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_estado_vivienda(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_estado_vivienda');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_condicion_vivienda(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_condicion_vivienda');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_attr_tipo_credito(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_attr_tipo_credito');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }
    private function _add_inm_beneficiario(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_beneficiario');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function _add_inm_co_acreditado(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $init->create_table_new(table: 'inm_co_acreditado');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        return $out;
    }

    private function inm_comprador(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_comprador(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $create = $this->_add_inm_rel_prospecto_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $modelo = new inm_comprador(link: $link);

        $registros = $modelo->registros();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener registros', data:  $registros);
        }

        ob_clean();
        foreach ($registros as $registro){
            $tiene_prospecto = $modelo->tiene_prospecto(inm_comprador_id: $registro['inm_comprador_id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si tiene prospecto', data:  $tiene_prospecto);
            }
            if($tiene_prospecto){
                $inm_prospecto = $modelo->inm_prospecto(inm_comprador_id: $registro['inm_comprador_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al obtener prospecto', data:  $inm_prospecto);
                }
                $upd_cte = array();
                $upd_cte['dp_calle_pertenece_id'] = $inm_prospecto->dp_calle_pertenece_id;
                $upd = $modelo->modifica_bd_base(registro: $upd_cte, id: $registro['inm_comprador_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar cliente', data:  $upd);
                }
            }



        }



        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Clientes';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        return $out;

    }

    private function inm_prospecto(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Clientes';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'convierte_cliente',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'inactivo', icono: 'bi bi-indent',link:  $link,
            lista:  'activo',titulo:  'Convierte Cliente');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'documentos',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-collection-fill',link:  $link,
            lista:  'activo',titulo:  'Documentos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'generales',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-briefcase-fill',link:  $link,
            lista:  'activo',titulo:  'Generales');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'integra_relacion',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-person-plus-fill',link:  $link,
            lista:  'activo',titulo:  'Integra Relacion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }


        /*$com_producto_modelo = new com_producto(link: $link);

        $com_productos_ins = array();
        $com_producto_ins['id'] = '84111506';
        $com_producto_ins['descripcion'] = 'Servicios de facturación';
        $com_producto_ins['codigo'] = '84111506D';
        $com_producto_ins['codigo_bis'] = '84111506D';
        $com_producto_ins['cat_sat_producto_id'] = '84111506';
        $com_producto_ins['cat_sat_unidad_id'] = '241';
        $com_producto_ins['cat_sat_obj_imp_id'] = '1';
        $com_producto_ins['com_tipo_producto_id'] = '99999999';
        $com_producto_ins['aplica_predial'] = 'inactivo';
        $com_producto_ins['cat_sat_conf_imps_id'] = '1';
        $com_producto_ins['es_automatico'] = 'inactivo';
        $com_producto_ins['precio'] = '0';
        $com_producto_ins['codigo_sat'] = '84111506';
        $com_producto_ins['cat_sat_cve_prod_id'] = '84111506';

        $com_productos_ins[] = $com_producto_ins;

        foreach ($com_productos_ins as $com_producto_ins){
            $existe = $com_producto_modelo->existe_by_id($com_producto_ins['id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al validar si existe com_tipo_producto', data:  $existe);
            }
            if(!$existe) {
                $alta = $com_producto_modelo->alta_registro(registro: $com_producto_ins);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al insertar producto', data: $alta);
                }
                $out->productos[] = $alta;
            }
            else{

                if((int)$com_producto_ins['id'] === 84111506){
                    $com_producto_r = $com_producto_modelo->registro(registro_id: $com_producto_ins['id']);
                    if (errores::$error) {
                        return (new errores())->error(mensaje: 'Error al obtener producto', data: $com_producto_r);
                    }
                    if((int)$com_producto_r['com_producto_codigo'] !== 84111506){
                        $upd_p['codigo'] = 84111506;
                        $com_producto_upd = $com_producto_modelo->modifica_bd(registro: $upd_p,id: 84111506);
                        if (errores::$error) {
                            return (new errores())->error(mensaje: 'Error al modificar producto', data: $com_producto_upd);
                        }
                    }
                }
            }
        }

        $com_productos = $com_producto_modelo->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener productos', data: $com_productos);
        }

        $upds = array();
        foreach ($com_productos as $com_producto){

            $com_producto_bruto = $com_producto_modelo->registro(registro_id: $com_producto['com_producto_id'],columnas_en_bruto: true);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al verificar com_producto_bruto', data: $com_producto_bruto);
            }

            if($com_producto['com_producto_codigo_sat'] !== 'SIN ASIGNAR'){
                $com_producto_upd = array();
                if(!is_numeric($com_producto['com_producto_codigo_sat'])){
                    continue;
                }
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto['com_producto_codigo_sat'];

                $existe_prod = (new cat_sat_cve_prod(link: $link))->existe_by_id(registro_id: $com_producto['com_producto_codigo_sat']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al verificar si existe', data: $existe_prod);
                }
                if(!$existe_prod){
                    $com_producto_upd['cat_sat_cve_prod_id'] = '1010101';
                    $com_producto_upd['codigo_sat'] = '1010101';
                }

                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd, id: $com_producto['com_producto_id']);
                if (errores::$error) {
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;

            }

            if((int)$com_producto_bruto['cat_sat_producto_id'] !== 97999999 && (int)$com_producto_bruto['cat_sat_producto_id'] !== 1){
                $com_producto_upd = array();
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto_bruto['cat_sat_producto_id'];
                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,id:  $com_producto['com_producto_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;
            }
        }

        $com_tmp_prod_css = (new com_tmp_prod_cs(link: $link))->registros();
        foreach ($com_tmp_prod_css as $com_tmp_prod_cs){
            $com_producto_upd = array();
            $com_producto_id = $com_tmp_prod_cs['com_producto_id'];
            $cat_sat_producto = $com_tmp_prod_cs['com_tmp_prod_cs_cat_sat_producto'];
            if(is_null($com_producto_id)){
                continue;
            }

            $com_producto_upd['cat_sat_cve_prod_id'] = $cat_sat_producto;
            $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,id:  $com_producto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
            }
            $upds[] = $upd;
        }

        $dels = array();
        foreach ($com_tmp_prod_css as $com_tmp_prod_cs){
            $del = (new com_tmp_prod_cs(link: $link))->elimina_bd(id: $com_tmp_prod_cs['com_tmp_prod_cs_id']);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al del com_tmp_prod_cs', data: $del);
            }
            $dels[] = $del;
        }

        $com_productos = $com_producto_modelo->registros();
        if (errores::$error) {
            return (new errores())->error(mensaje: 'Error al obtener productos', data: $com_productos);
        }
        foreach ($com_productos as $com_producto){
            if($com_producto['com_producto_codigo_sat'] === 'SIN ASIGNAR'){

                $com_producto_upd = array();
                $com_producto_upd['cat_sat_cve_prod_id'] = $com_producto['cat_sat_cve_prod_id'];
                $com_producto_upd['codigo_sat'] = $com_producto['cat_sat_cve_prod_id'];
                $upd = $com_producto_modelo->modifica_bd(registro: $com_producto_upd,
                    id:  $com_producto['com_producto_id']);
                if(errores::$error){
                    return (new errores())->error(mensaje: 'Error al actualizar producto', data: $upd);
                }
                $upds[] = $upd;
            }
        }


        $adm_menu_descripcion = 'Productos';
        $adm_sistema_descripcion = 'comercial';
        $etiqueta_label = 'Productos';
        $adm_seccion_pertenece_descripcion = 'comercial';
        $adm_namespace_descripcion = 'gamboa.martin/comercial';
        $adm_namespace_name = 'gamboamartin/comercial';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }*/


        return $out;

    }

    private function inm_prospecto_ubicacion(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_prospecto_ubicacion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Ubicaciones';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Prospecto Ubicacion';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }
        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'documentos',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-collection-fill',link:  $link,
            lista:  'activo',titulo:  'Documentos');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }

        $alta_accion = (new _adm())->inserta_accion_base(adm_accion_descripcion: 'integra_relacion',
            adm_seccion_descripcion:  __FUNCTION__, es_view: 'activo', icono: 'bi bi-person-plus-fill',link:  $link,
            lista:  'activo',titulo:  'Integra Relacion');
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar accion',data:  $alta_accion);
        }

        return $out;

    }

    private function inm_rel_prospecto_cliente(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_rel_prospecto_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Clientes';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }

    private function inm_sindicato(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_sindicato(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Clientes';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }
    private function inm_prototipo(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_prototipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Parametros Infonavit';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Prototipo';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }

    private function inm_complemento(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_complemento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Parametros Infonavit';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Complemento';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }

    private function inm_estado_vivienda(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_estado_vivienda(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Parametros Infonavit';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Estado Vivienda';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }

    private function inm_attr_tipo_credito(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $this->_add_inm_attr_tipo_credito(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->x = new stdClass();
        $columnas->y = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;

        $foraneas = array();
        $foraneas['inm_tipo_credito_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $adm_menu_descripcion = 'Parametros Infonavit';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Atributo Tipo De Credito';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        return $out;

    }

    private function inm_beneficiario(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $this->_add_inm_beneficiario(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->nombre = new stdClass();
        $columnas->apellido_paterno = new stdClass();
        $columnas->apellido_materno = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;

        $foraneas = array();
        $foraneas['inm_parentesco_id'] = new stdClass();
        $foraneas['inm_tipo_beneficiario_id'] = new stdClass();
        $foraneas['inm_prospecto_id'] = new stdClass();

        $result = $init->foraneas(foraneas: $foraneas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }
        $out->foraneas = $result;

        $adm_menu_descripcion = 'Clientes';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Beneficiario';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        return $out;

    }

    private function inm_co_acreditado(PDO $link): array|stdClass
    {
        $out = new stdClass();
        $init = (new _instalacion(link: $link));

        $create = $this->_add_inm_co_acreditado(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }
        $out->create = $create;

        $columnas = new stdClass();
        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_base = $add_colums;

        $columnas = new stdClass();
        $columnas->nss = new stdClass();
        $columnas->curp = new stdClass();
        $columnas->rfc = new stdClass();
        $columnas->apellido_paterno = new stdClass();
        $columnas->apellido_materno = new stdClass();
        $columnas->nombre = new stdClass();
        $columnas->lada = new stdClass();
        $columnas->numero = new stdClass();
        $columnas->celular = new stdClass();
        $columnas->genero = new stdClass();
        $columnas->correo = new stdClass();
        $columnas->nombre_empresa = new stdClass();
        $columnas->nrp = new stdClass();
        $columnas->lada_nep = new stdClass();
        $columnas->numero_nep = new stdClass();
        $columnas->extension_nep = new stdClass();

        $add_colums = $init->add_columns(campos: $columnas,table:  __FUNCTION__);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar columnas', data:  $add_colums);
        }
        $out->add_colums_entidad = $add_colums;


        $adm_menu_descripcion = 'Parametros Infonavit';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Co Acreditado';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }


        return $out;

    }

    private function inm_condicion_vivienda(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $create = $this->_add_inm_condicion_vivienda(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al agregar tabla', data:  $create);
        }

        $out->create = $create;

        $adm_menu_descripcion = 'Ubicaciones';
        $adm_sistema_descripcion = 'inmuebles';
        $etiqueta_label = 'Condicion Vivienda';
        $adm_seccion_pertenece_descripcion = 'inmuebles';
        $adm_namespace_descripcion = 'gamboa.martin/inmuebles';
        $adm_namespace_name = 'gamboamartin/inmuebles';

        $acl = (new _adm())->integra_acl(adm_menu_descripcion: $adm_menu_descripcion,
            adm_namespace_name: $adm_namespace_name, adm_namespace_descripcion: $adm_namespace_descripcion,
            adm_seccion_descripcion: __FUNCTION__, adm_seccion_pertenece_descripcion: $adm_seccion_pertenece_descripcion,
            adm_sistema_descripcion: $adm_sistema_descripcion, etiqueta_label: $etiqueta_label, link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al obtener acl', data:  $acl);
        }

        return $out;

    }

    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $inm_sindicato = $this->inm_sindicato(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_sindicato', data:  $inm_sindicato);
        }
        $out->inm_sindicato = $inm_sindicato;

        $inm_prototipo = $this->inm_prototipo(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_prototipo', data:  $inm_prototipo);
        }
        $out->inm_prototipo = $inm_prototipo;
        
        $inm_complemento = $this->inm_complemento(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_complemento', data:  $inm_complemento);
        }
        $out->inm_complemento = $inm_complemento;

        $inm_estado_vivienda = $this->inm_estado_vivienda(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_estado_vivienda', data:  $inm_estado_vivienda);
        }
        $out->inm_estado_vivienda = $inm_estado_vivienda;

        $inm_attr_tipo_credito = $this->inm_attr_tipo_credito(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_attr_tipo_credito', data:  $inm_attr_tipo_credito);
        }
        $out->inm_attr_tipo_credito = $inm_attr_tipo_credito;

        $inm_beneficiario = $this->inm_beneficiario(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_beneficiario', data:  $inm_beneficiario);
        }
        $out->inm_beneficiario = $inm_beneficiario;

        $inm_co_acreditado = $this->inm_co_acreditado(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_co_acreditado', data:  $inm_co_acreditado);
        }
        $out->inm_co_acreditado = $inm_co_acreditado;

        $inm_condicion_vivienda = $this->inm_condicion_vivienda(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_condicion_vivienda', data:  $inm_condicion_vivienda);
        }
        $out->inm_condicion_vivienda = $inm_condicion_vivienda;

        $inm_prospecto = $this->inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_prospecto', data:  $inm_prospecto);
        }
        $out->inm_prospecto = $inm_prospecto;

        $inm_prospecto_ubicacion = $this->inm_prospecto_ubicacion(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_prospecto_ubicacion', data:  $inm_prospecto_ubicacion);
        }
        $out->inm_prospecto_ubicacion = $inm_prospecto_ubicacion;

        $inm_comprador = $this->inm_comprador(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_comprador', data:  $inm_comprador);
        }
        $out->inm_comprador = $inm_comprador;

        $inm_rel_prospecto_cliente = $this->inm_rel_prospecto_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar inm_rel_prospecto_cliente', data:  $inm_rel_prospecto_cliente);
        }
        $out->inm_rel_prospecto_cliente = $inm_rel_prospecto_cliente;

        return $out;

    }


}
