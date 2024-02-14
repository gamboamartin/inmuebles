<?php
namespace gamboamartin\inmuebles\instalacion;
use gamboamartin\administrador\models\_instalacion;
use gamboamartin\errores\errores;
use PDO;
use stdClass;

class instalacion
{
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
        $foraneas['dp_municipio_nacimiento_id'] = new stdClass();
        $foraneas['inm_nacionalidad_id'] = new stdClass();
        $foraneas['inm_ocupacion_id'] = new stdClass();


        $result = $init->foraneas(foraneas: $foraneas,table:  'inm_prospecto');

        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al ajustar foranea', data:  $result);
        }

        $out->foraneas = $result;


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

    final public function instala(PDO $link): array|stdClass
    {
        $out = new stdClass();

        $com_producto = $this->inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error integrar com_producto', data:  $com_producto);
        }
        $out->com_producto = $com_producto;
        return $out;

    }


}
