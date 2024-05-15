<?php
namespace gamboamartin\inmuebles\tests;
use base\orm\modelo_base;


use gamboamartin\banco\models\bn_cuenta;
use gamboamartin\comercial\models\com_agente;
use gamboamartin\comercial\models\com_cliente;
use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\comercial\models\com_tipo_prospecto;
use gamboamartin\comisiones\models\comi_comision;
use gamboamartin\errores\errores;

use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_concepto;
use gamboamartin\inmuebles\models\inm_conf_empresa;
use gamboamartin\inmuebles\models\inm_conyuge;
use gamboamartin\inmuebles\models\inm_costo;
use gamboamartin\inmuebles\models\inm_opinion_valor;
use gamboamartin\inmuebles\models\inm_parentesco;
use gamboamartin\inmuebles\models\inm_precio;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_referencia;
use gamboamartin\inmuebles\models\inm_rel_co_acred;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_rel_conyuge_prospecto;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_tipo_concepto;
use gamboamartin\inmuebles\models\inm_tipo_ubicacion;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\models\inm_valuador;
use gamboamartin\organigrama\models\org_empresa;
use PDO;
use stdClass;

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

    public function alta_com_agente(PDO $link, int $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\comercial\test\base_test())->alta_com_agente(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_com_cliente(PDO $link, int $cat_sat_regimen_fiscal_id = 601, int $cat_sat_tipo_persona_id = 4,
                                     string $codigo = '1',string $descripcion = 'YADIRA MAGALY MONTAÑEZ FELIX',
                                     $dp_calle_pertenece_id = 1, int $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\comercial\test\base_test())->alta_com_cliente(link: $link, cat_sat_metodo_pago_id: 1,
            cat_sat_regimen_fiscal_id: $cat_sat_regimen_fiscal_id, cat_sat_tipo_persona_id: $cat_sat_tipo_persona_id,
            codigo: $codigo, descripcion: $descripcion, dp_calle_pertenece_id: $dp_calle_pertenece_id, id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_com_tipo_cliente(PDO $link, $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\comercial\test\base_test())->alta_com_tipo_cliente(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_com_tipo_prospecto(PDO $link, $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\comercial\test\base_test())->alta_com_tipo_prospecto(link: $link, id: $id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_co_acreditado(PDO $link, string $apellido_materno = 'APELLIDO MATERNO',
                                           string $apellido_paterno = 'AP', string $celular = '1234567890',
                                           string $correo = 'test@test.com.mx', string $curp= 'XEXX010101MNEXXXA8',
                                           string $genero = 'GENERO', int $id = 1, string $lada = '12',
                                           string $lada_nep = 'LADA NEP', string $nombre = 'NOMBRE',
                                           string $nombre_empresa_patron = 'NOMBRE EMPRESA', string $nrp = 'NRP',
                                           string $nss = '12345678912', string $numero = '12345678',
                                           string $numero_nep = 'NUMERO NEP',
                                           string $rfc = 'AAA010101AAA' ): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['nss'] = $nss;
        $registro['curp'] = $curp;
        $registro['rfc'] = $rfc;
        $registro['apellido_materno'] = $apellido_materno;
        $registro['lada'] = $lada;
        $registro['numero'] = $numero;
        $registro['celular'] = $celular;
        $registro['genero'] = $genero;
        $registro['correo'] = $correo;
        $registro['nombre_empresa_patron'] = $nombre_empresa_patron;
        $registro['nrp'] = $nrp;
        $registro['lada_nep'] = $lada_nep;
        $registro['numero_nep'] = $numero_nep;

        $alta = (new inm_co_acreditado($link))->alta_registro($registro);
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
        string $correo_com = 'a@a.com', string $correo_empresa  ='sincorreo@correo.com',
        string $curp = 'XEXX010101MNEXXXA8', float $descuento_pension_alimenticia_dh = 0,
        float $descuento_pension_alimenticia_fc = 0, int $dp_calle_pertenece_id = 1, string $es_segundo_credito = 'NO',
        string $fecha_nacimiento = '1983-01-01', $id = 1, int $inm_attr_tipo_credito_id = 1,
        int $inm_destino_credito_id = 1, int $inm_estado_civil_id= 1, int $inm_institucion_hipotecaria_id = 1,
        int $inm_producto_infonavit_id = 1, int $inm_tipo_discapacidad_id= 1, string $lada_com = '123',
        string $lada_nep = '33', float $monto_ahorro_voluntario = 0, float $monto_credito_solicitado_dh = 0,
        string $nombre='Nombre', string $nombre_empresa_patron = 'NOMBRE EMPRESA PATRON', string $nrp_nep = 'NRP',
        string $nss = '12345678914', string $numero_com = '1234564', string $numero_exterior = '1',
        string $numero_nep = '99999999', string $rfc = 'AAA010101AAA',
        string $telefono_casa = '1234567890'): array|stdClass
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

        $existe = (new com_tipo_cliente(link: $link))->existe_by_id(registro_id: $com_tipo_cliente_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe com_tipo_cliente_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_com_tipo_cliente(link: $link, id: $com_tipo_cliente_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar com_tipo_cliente_id', data: $alta);
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
        $registro['correo_com'] = $correo_com;
        $registro['nombre_empresa_patron'] = $nombre_empresa_patron;
        $registro['nrp_nep'] = $nrp_nep;
        $registro['inm_institucion_hipotecaria_id'] = $inm_institucion_hipotecaria_id;
        $registro['fecha_nacimiento'] = $fecha_nacimiento;
        $registro['telefono_casa'] = $telefono_casa;
        $registro['correo_empresa'] = $correo_empresa;

        $alta = (new inm_comprador($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_concepto(PDO $link, string $descripcion = 'CONCEPTO 1', int $id = 1,
                                      int $inm_tipo_concepto_id = 1): array|\stdClass
    {

        $existe = (new inm_tipo_concepto(link: $link))->existe_by_id(registro_id: $inm_tipo_concepto_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_tipo_concepto_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_tipo_concepto(link: $link, id: $inm_tipo_concepto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_tipo_concepto_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;
        $registro['inm_tipo_concepto_id'] = $inm_tipo_concepto_id;

        $alta = (new inm_concepto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_conf_empresa(PDO $link, int $id = 1, int $inm_tipo_inmobiliaria_id = 1,
                                          int $org_empresa_id = 1): array|\stdClass
    {


        $existe = (new org_empresa(link: $link))->existe_by_id(registro_id: $org_empresa_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe org_empresa_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_org_empresa(link: $link, id: $org_empresa_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar org_empresa', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['inm_tipo_inmobiliaria_id'] = $inm_tipo_inmobiliaria_id;
        $registro['org_empresa_id'] = $org_empresa_id;

        $alta = (new inm_conf_empresa($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_conyuge(PDO $link, string $apellido_paterno = 'AP1', string $curp = 'XEXX010101HNEXXXA4',
                                     int $dp_municipio_id = 1, string $fecha_nacimiento = '2020-01-01', int $id = 1, int $inm_nacionalidad_id = 1,
                                     int $inm_ocupacion_id = 1, string $nombre = 'NOMBRE 1' ,
                                     string $rfc = 'AAA010101CCC', string $telefono_casa = '9874563214', string $telefono_celular = '5698745874'): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['curp'] = $curp;
        $registro['rfc'] = $rfc;
        $registro['dp_municipio_id'] = $dp_municipio_id;
        $registro['inm_nacionalidad_id'] = $inm_nacionalidad_id;
        $registro['inm_ocupacion_id'] = $inm_ocupacion_id;
        $registro['telefono_casa'] = $telefono_casa;
        $registro['telefono_celular'] = $telefono_celular;
        $registro['fecha_nacimiento'] = $fecha_nacimiento;

        $alta = (new inm_conyuge($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_costo(PDO $link, string $codigo = '1', string $descripcion = 'Descripcion 1',
                                   string $fecha = '2020-01-01', int $id = 1, int $inm_concepto_id = 1,
                                   int $inm_ubicacion_id = 1, float $monto = 1000,
                                   string $referencia = 'REF 1'): array|\stdClass
    {

        $existe = (new inm_ubicacion(link: $link))->existe_by_id(registro_id: $inm_ubicacion_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_ubicacion_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_ubicacion(link: $link, id: $inm_ubicacion_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_ubicacion', data: $alta);
            }
        }

        $existe = (new inm_concepto(link: $link))->existe_by_id(registro_id: $inm_concepto_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_concepto_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_concepto(link: $link, id: $inm_concepto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_concepto_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['inm_ubicacion_id'] = $inm_ubicacion_id;
        $registro['descripcion'] = $descripcion;
        $registro['inm_concepto_id'] = $inm_concepto_id;
        $registro['monto'] = $monto;
        $registro['fecha'] = $fecha;
        $registro['codigo'] = $codigo;
        $registro['referencia'] = $referencia;
        $alta = (new inm_costo($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_opinion_valor(PDO $link, string $fecha = '2020-01-01', int $id = 1,
                                           int $inm_ubicacion_id = 1, int $inm_valuador_id = 1,
                                           float $monto_resultado = 100000): array|\stdClass
    {

        $existe = (new inm_valuador(link: $link))->existe_by_id(registro_id: $inm_valuador_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_valuador_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_valuador(link: $link, id: $inm_valuador_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_valuador_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['inm_ubicacion_id'] = $inm_ubicacion_id;
        $registro['inm_valuador_id'] = $inm_valuador_id;
        $registro['monto_resultado'] = $monto_resultado;
        $registro['fecha'] = $fecha;
        $alta = (new inm_opinion_valor($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }


    public function alta_inm_parentesco(PDO $link, string $descripcion = 'SIN PARENTESCO', int $id = 1): array|\stdClass
    {


        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;

        $alta = (new inm_parentesco($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_precio(PDO $link, string $fecha_final = '2030-01-01', string $fecha_inicial = '2020-01-01',
                                    int $id = 1, int $inm_institucion_hipotecaria_id = 1, int $inm_ubicacion_id = 1,
                                    float $precio_venta = 450000): array|\stdClass
    {

        $existe = (new inm_ubicacion(link: $link))->existe_by_id(registro_id: $inm_ubicacion_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_ubicacion(link: $link, id: $inm_ubicacion_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_ubicacion_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['inm_ubicacion_id'] = $inm_ubicacion_id;
        $registro['precio_venta'] = $precio_venta;
        $registro['fecha_inicial'] = $fecha_inicial;
        $registro['fecha_final'] = $fecha_final;
        $registro['inm_institucion_hipotecaria_id'] = $inm_institucion_hipotecaria_id;
        $alta = (new inm_precio($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_prospecto(PDO $link, string $apellido_paterno = 'AP1', int $com_agente_id = 1,
                                       int $com_tipo_prospecto_id = 1,int $id = 1, int $lada_com = 12,
                                       string $nombre = 'NOMBRE 1', int $numero_com = 12345678,
                                       string $razon_social ='RS1'): array|\stdClass
    {

        $existe = (new com_agente(link: $link))->existe_by_id(registro_id: $com_agente_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe com_agente_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_com_agente(link: $link, id: $com_agente_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar com_agente_id', data: $alta);
            }
        }

        $existe = (new com_tipo_prospecto(link: $link))->existe_by_id(registro_id: $com_tipo_prospecto_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe com_tipo_prospecto_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_com_tipo_prospecto(link: $link, id: $com_tipo_prospecto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar com_tipo_prospecto_id', data: $alta);
            }
        }


        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['numero_com'] = $numero_com;
        $registro['lada_com'] = $lada_com;
        $registro['razon_social'] = $razon_social;
        $registro['com_agente_id'] = $com_agente_id;
        $registro['com_tipo_prospecto_id'] = $com_tipo_prospecto_id;

        $alta = (new inm_prospecto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_referencia(PDO $link, string $apellido_materno = 'AM', string $apellido_paterno = 'AP',
                                        string $celular = '1234567890', int $dp_calle_pertenece_id = 109, int $id = 1,
                                        int $inm_comprador_id= 1, int $inm_parentesco_id = 1, string $lada = '123',
                                        string $nombre = 'NOMBRE', string $numero = '1234567',
                                        string $numero_dom = 'NUMERO DOM'): array|\stdClass
    {
        $existe = (new inm_comprador(link: $link))->existe_by_id(registro_id: $inm_comprador_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_comprador(link: $link, id: $inm_comprador_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['nombre'] = $nombre;
        $registro['apellido_paterno'] = $apellido_paterno;
        $registro['apellido_materno'] = $apellido_materno;
        $registro['inm_comprador_id'] = $inm_comprador_id;
        $registro['lada'] = $lada;
        $registro['numero'] = $numero;
        $registro['celular'] = $celular;
        $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $registro['numero_dom'] = $numero_dom;
        $registro['inm_parentesco_id'] = $inm_parentesco_id;

        $alta = (new inm_referencia($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_rel_co_acred(PDO $link, int $id = 1, int $inm_co_acreditado_id = 1,
                                          int $inm_comprador_id = 1): array|\stdClass
    {

        $existe = (new inm_co_acreditado(link: $link))->existe_by_id(registro_id: $inm_co_acreditado_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_co_acreditado_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_co_acreditado(link: $link, id: $inm_co_acreditado_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_co_acreditado_id', data: $alta);
            }
        }

        $existe = (new inm_comprador(link: $link))->existe_by_id(registro_id: $inm_comprador_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_comprador_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_comprador(link: $link, id: $inm_comprador_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador_id', data: $alta);
            }
        }

        $registro['id'] = $id;
        $registro['inm_co_acreditado_id'] = $inm_co_acreditado_id;
        $registro['inm_comprador_id'] = $inm_comprador_id;


        $alta = (new inm_rel_co_acred($link))->alta_registro($registro);
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

    public function alta_inm_rel_conyuge_prospecto(PDO $link, int $id = 1, int $inm_conyuge_id = 1,
                                                   int $inm_prospecto_id = 1): array|\stdClass
    {
        $existe = (new inm_conyuge(link: $link))->existe_by_id(registro_id: $inm_conyuge_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_conyuge_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_conyuge(link: $link, id: $inm_conyuge_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_conyuge', data: $alta);
            }
        }

        $existe = (new inm_prospecto(link: $link))->existe_by_id(registro_id: $inm_prospecto_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_prospecto_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_prospecto(link: $link, id: $inm_prospecto_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_prospecto_id', data: $alta);
            }
        }


        $registro['id'] = $id;
        $registro['inm_conyuge_id'] = $inm_conyuge_id;
        $registro['inm_prospecto_id'] = $inm_prospecto_id;

        $alta = (new inm_rel_conyuge_prospecto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_rel_ubi_comp(PDO $link, int $inm_comprador_id = 1, int $inm_ubicacion_id = 1, int $id = 1,
                                          float $precio_operacion = 450000): array|\stdClass
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

        }

        $existe = (new inm_ubicacion(link: $link))->existe_by_id(registro_id: $inm_ubicacion_id);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al validar si existe inm_ubicacion_id', data: $existe);
        }
        if(!$existe){
            $alta = $this->alta_inm_ubicacion(link: $link, id: $inm_ubicacion_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador', data: $alta);
            }
        }

        $filtro['inm_ubicacion.id'] = $inm_ubicacion_id;

        $existe = (new inm_precio(link: $link))->existe(filtro: $filtro);
        if(!$existe){
            $alta = $this->alta_inm_precio(link: $link,inm_ubicacion_id: $inm_ubicacion_id);
            if(errores::$error){
                return (new errores())->error(mensaje: 'Error al insertar inm_comprador', data: $alta);
            }
        }


        $registro['id'] = $id;
        $registro['inm_comprador_id'] = $inm_comprador_id;
        $registro['inm_ubicacion_id'] = $inm_ubicacion_id;
        $registro['precio_operacion'] = $precio_operacion;

        $alta = (new inm_rel_ubi_comp($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_tipo_concepto(PDO $link, string $descripcion = 'TIPO CONCEPTO 1', int $id = 1): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;

        $alta = (new inm_tipo_concepto($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_tipo_ubicacion(PDO $link, string $descripcion = 'TIPO 1', int $id = 1): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;
        $alta = (new inm_tipo_ubicacion($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_ubicacion(PDO $link, string $cuenta_predial = 'CP', int $dp_calle_pertenece_id = 1,
                                       int $id = 1,int $inm_tipo_ubicacion_id = 1,
                                       string $numero_exterior = 'NUM EXT'): array|\stdClass
    {

        $registro['id'] = $id;
        $registro['dp_calle_pertenece_id'] = $dp_calle_pertenece_id;
        $registro['numero_exterior'] = $numero_exterior;
        $registro['cuenta_predial'] = $cuenta_predial;
        $registro['inm_tipo_ubicacion_id'] = $inm_tipo_ubicacion_id;

        $alta = (new inm_ubicacion($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_inm_valuador(PDO $link, string $descripcion = 'VALUADOR 1', int $id = 1): array|\stdClass
    {


        $registro['id'] = $id;
        $registro['descripcion'] = $descripcion;
        $alta = (new inm_valuador($link))->alta_registro($registro);
        if(errores::$error){
            return (new errores())->error(mensaje: 'Error al insertar', data: $alta);
        }
        return $alta;
    }

    public function alta_org_empresa(PDO $link, $id = 1): array|\stdClass
    {

        $alta = (new \gamboamartin\organigrama\tests\base_test())->alta_org_empresa(link: $link,
            cat_sat_regimen_fiscal_id: 601, cat_sat_tipo_persona_id: 4, id: $id);
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

    public function del_bn_cuenta(PDO $link): array
    {

        $del = $this->del_inm_comprador(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\banco\tests\base_test())->del_bn_cuenta(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
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

    public function del_com_agente(PDO $link): array
    {

        $del = $this->del_comi_comision(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del_inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = (new \gamboamartin\comercial\test\base_test())->del_com_agente(link: $link);
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
        $del = $this->del_fc_nota_credito(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_fc_factura(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\comercial\test\base_test())->del_com_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_com_prospecto(PDO $link): array
    {

        $del = $this->del_inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\comercial\test\base_test())->del_com_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }


    public function del_com_tipo_prospecto(PDO $link): array
    {
        $del = $this->del_inm_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = (new \gamboamartin\comercial\test\base_test())->del_com_tipo_prospecto(link: $link);
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

    public function del_fc_factura(PDO $link): array
    {


        $del = (new \gamboamartin\facturacion\tests\base_test())->del_fc_factura(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }
    public function del_fc_nota_credito(PDO $link): array
    {

        $del = (new \gamboamartin\facturacion\tests\base_test())->del_fc_nota_credito(link: $link);
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
    public function del_inm_beneficiario(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_beneficiario');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }


    public function del_inm_co_acreditado(PDO $link): array
    {
        $del = $this->del_inm_rel_co_acred(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_co_acreditado');
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
        $del = $this->del_inm_comprador_etapa(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_ubi_comp(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_co_acred(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_referencia(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_prospecto_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }


        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_comprador');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_comprador_etapa(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_comprador_etapa');
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

    public function del_inm_concepto(PDO $link): array
    {

        $del = $this->del_inm_costo(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_concepto');
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

    public function del_inm_conyuge(PDO $link): array
    {
        $del = $this->del_inm_rel_conyuge_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_conyuge');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_costo(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_costo');
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

    public function del_inm_doc_prospecto(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_doc_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_opinion_valor(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_opinion_valor');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_parentesco(PDO $link): array
    {

        $del = $this->del_inm_referencia_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_referencia(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_parentesco');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_precio(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_precio');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_prospecto(PDO $link): array
    {

        $del = $this->del_inm_doc_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_prospecto_proceso(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_prospecto_cliente(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_conyuge_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_beneficiario(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_referencia_prospecto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_comi_comision(PDO $link): array
    {

        $del = (new comi_comision(link: $link))->elimina_todo();
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_prospecto_proceso(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_prospecto_proceso');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_referencia(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_referencia');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_referencia_prospecto(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_referencia_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_rel_co_acred(PDO $link): array
    {

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_rel_co_acred');
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

    public function del_inm_rel_conyuge_prospecto(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_rel_conyuge_prospecto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_rel_prospecto_cliente(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_rel_prospecto_cliente');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_rel_ubi_comp(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_rel_ubi_comp');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_tipo_concepto(PDO $link): array
    {

        $del = $this->del_inm_concepto(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_tipo_concepto');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_ubicacion(PDO $link): array
    {
        $del = $this->del_inm_costo(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_opinion_valor(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_precio(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_rel_ubi_comp(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        $del = $this->del_inm_ubicacion_etapa(link: $link);
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }

        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_ubicacion');
        if(errores::$error){
            return (new errores())->error('Error al eliminar', $del);
        }
        return $del;
    }

    public function del_inm_ubicacion_etapa(PDO $link): array
    {
        $del = $this->del($link, 'gamboamartin\\inmuebles\\models\\inm_ubicacion_etapa');
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
        $del = $this->del_org_puesto(link: $link);
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
