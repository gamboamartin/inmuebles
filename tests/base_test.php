<?php
namespace gamboamartin\inmuebles\tests;
use base\orm\modelo_base;


use gamboamartin\banco\models\bn_cuenta;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;

use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use PDO;

class base_test{


    public function alta_bn_cuenta(PDO $link, int $cat_sat_regimen_fiscal_id = 601, int $cat_sat_tipo_persona_id= 4,
                                   $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\banco\tests\base_test())->alta_bn_cuenta(link: $link,
            cat_sat_regimen_fiscal_id: $cat_sat_regimen_fiscal_id, cat_sat_tipo_persona_id: $cat_sat_tipo_persona_id,
            id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_com_cliente(PDO $link, $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\comercial\test\base_test())->alta_com_cliente(link: $link, cat_sat_metodo_pago_id: 1,
            cat_sat_regimen_fiscal_id: 601, cat_sat_tipo_persona_id: 4, id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }
    public function alta_inm_comprador(
        PDO $link, string $apellido_materno = 'Apellido M', string $apellido_paterno = 'Apellido P',
        int $bn_cuenta_id = 1, int $cat_sat_forma_pago_id = 99, int $cat_sat_metodo_pago_id = 2,
        int $cat_sat_moneda_id = 161, int $cat_sat_regimen_fiscal_id = 605, int $cat_sat_tipo_persona_id = 5,
        int $cat_sat_uso_cfdi_id = 22, string $cel_com = '3344556655', int $com_tipo_cliente_id = 1,
        string $curp = 'XEXX010101MNEXXXA8', float $descuento_pension_alimenticia_dh = 0,
        float $descuento_pension_alimenticia_fc = 0, int $dp_calle_pertenece_id = 1, string $es_segundo_credito = 'NO',
        $id = 1, int $inm_attr_tipo_credito_id = 1, int $inm_destino_credito_id = 1, int $inm_estado_civil_id= 1,
        int $inm_producto_infonavit_id = 1, int $inm_tipo_discapacidad_id= 1, string $lada_com = '123',
        string $lada_nep = '33', float $monto_ahorro_voluntario = 0, float $monto_credito_solicitado_dh = 0,
        string $nombre='Nombre', string $nss = '12345678914', string $numero_com = '1234564',
        string $numero_exterior = '1', string $numero_nep = '99999999',
        string $rfc = 'AAA010101AAA'): array|\stdClass
    {

        $existe = (new bn_cuenta(link: $link))->existe_by_id(registro_id: $bn_cuenta_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_bn_cuenta(link: $link, id: $bn_cuenta_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['apellido_materno'] = $apellido_materno;
        $registro['nss'] = $nss;
        $registro['curp'] = $curp;
        $registro['rfc'] = $rfc;
        $registro['inm_producto_infonavit_id'] = $inm_producto_infonavit_id;
        $registro['inm_attr_tipo_credito_id'] = $inm_attr_tipo_credito_id;
        $registro['inm_destino_credito_id'] = $inm_destino_credito_id;
        $registro['es_segundo_credito'] = $es_segundo_credito;
        $registro['descuento_pension_alimenticia_dh'] = $descuento_pension_alimenticia_dh;
        $registro['descuento_pension_alimenticia_fc'] = $descuento_pension_alimenticia_fc;
        $registro['monto_credito_solicitado_dh'] = $monto_credito_solicitado_dh;
        $registro['monto_ahorro_voluntario'] = $monto_ahorro_voluntario;
        $registro['inm_tipo_discapacidad_id'] = $inm_tipo_discapacidad_id;
        $registro['inm_estado_civil_id'] = $inm_estado_civil_id;
        $registro['bn_cuenta_id'] = $bn_cuenta_id;
        $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $registro['numero_exterior'] = $numero_exterior;
        $registro['lada_com'] = $lada_com;
        $registro['numero_com'] = $numero_com;
        $registro['cat_sat_regimen_fiscal_id'] = $cat_sat_regimen_fiscal_id;
        $registro['cat_sat_moneda_id'] = $cat_sat_moneda_id;
        $registro['cat_sat_forma_pago_id'] = $cat_sat_forma_pago_id;
        $registro['cat_sat_metodo_pago_id'] = $cat_sat_metodo_pago_id;
        $registro['cat_sat_uso_cfdi_id'] = $cat_sat_uso_cfdi_id;
        $registro['com_tipo_cliente_id'] = $com_tipo_cliente_id;
        $registro['cat_sat_tipo_persona_id'] = $cat_sat_tipo_persona_id;
        $registro['lada_nep'] = $lada_nep;
        $registro['numero_nep'] = $numero_nep;
        $registro['cel_com'] = $cel_com;

        $alta = (new inm_comprador($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }
    public function alta_inm_rel_comprador_com_cliente(PDO $link, int $com_cliente_id = 1, int $inm_comprador_id = 1,
                                                       int $id = 1): array|\stdClass
    {

        $existe = (new inm_comprador(link: $link))->existe_by_id(registro_id: $inm_comprador_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_comprador(link: $link, id: $inm_comprador_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador', data: $alta);
            }
            $del = $this->del_inm_rel_comprador_com_cliente(link: $link);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al eliminar relacion', data: $del);
            }

        }

        $existe = (new com_cliente(link: $link))->existe_by_id(registro_id: $com_cliente_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_com_cliente(link: $link, id: $com_cliente_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador', data: $alta);
            }
        }


        $registro['id'] = $id;
        $registro['inm_comprador_id'] = $inm_comprador_id;
        $registro['com_cliente_id'] = $com_cliente_id;

        $alta = (new inm_rel_comprador_com_cliente($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function del(PDO $link, string $name_model): array
    {

        $model = (new modelo_base($link))->genera_modelo(modelo: $name_model);
        $del = $model->elimina_todo();
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al eliminar '.$name_model, data: $del);
        }
        return $del;
    }

    public function del_bn_empleado(PDO $link): array
    {

        $del = $this->del_inm_comprador(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\banco\tests\base_test())->del_bn_empleado(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_cliente(PDO $link): array
    {

        $del = $this->del_fc_receptor_email(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_comprador_com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\comercial\test\base_test())->del_com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_fc_csd(PDO $link): array
    {


        $del = (new \gamboamartin\facturacion\tests\base_test())->del_fc_csd(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_fc_receptor_email(PDO $link): array
    {

        $del = (new \gamboamartin\facturacion\tests\base_test())->del_fc_receptor_email(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_comprador(PDO $link): array
    {

        $del = $this->del_inm_comprador_proceso(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_comprador_com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_doc_comprador(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_comprador');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_comprador_proceso(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_comprador_proceso');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }


    public function del_inm_conf_empresa(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_conf_empresa');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_doc_comprador(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_doc_comprador');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_rel_comprador_com_cliente(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_rel_comprador_com_cliente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_org_empresa(PDO $link): array
    {

        $del = $this->del_fc_csd(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del_inm_conf_empresa(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\organigrama\tests\base_test())->del_org_empresa(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_org_puesto(PDO $link): array
    {

        $del = $this->del_bn_empleado(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\organigrama\tests\base_test())->del_org_puesto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }




}
