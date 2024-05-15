<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_compradorTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/inmuebles/config/generales.php';
        $this->paths_conf->database = '/var/www/html/inmuebles/config/database.php';
        $this->paths_conf->views = '/var/www/html/inmuebles/config/views.php';
    }

    public function test_alta_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);

        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'Apellido Paterno';
        $inm->registro['nss'] = '12345678890';
        $inm->registro['curp'] = 'XEXX010101HNEXXXA4';
        $inm->registro['rfc'] = 'AAA010101AAA';
        $inm->registro['lada_nep'] = '111';
        $inm->registro['numero_nep'] = '2222222';
        $inm->registro['lada_com'] = '22';
        $inm->registro['numero_com'] = '55555555';
        $inm->registro['bn_cuenta_id'] = 1;
        $inm->registro['cel_com'] = '5577665544';
        $inm->registro['correo_com'] = 'a@alfa.com';
        $inm->registro['descuento_pension_alimenticia_dh'] = '0';
        $inm->registro['descuento_pension_alimenticia_fc'] = '0';
        $inm->registro['es_segundo_credito'] = 'SI';
        $inm->registro['inm_attr_tipo_credito_id'] = '1';
        $inm->registro['inm_destino_credito_id'] = '1';
        $inm->registro['inm_estado_civil_id'] = '1';
        $inm->registro['inm_producto_infonavit_id'] = '1';
        $inm->registro['monto_ahorro_voluntario'] = '1';
        $inm->registro['monto_credito_solicitado_dh'] = '1';
        $inm->registro['nombre_empresa_patron'] = '1';
        $inm->registro['nrp_nep'] = '1';
        $inm->registro['dp_calle_pertenece_id'] = '1';
        $inm->registro['numero_exterior'] = '1';
        $inm->registro['cat_sat_regimen_fiscal_id'] = '605';
        $inm->registro['cat_sat_moneda_id'] = '161';
        $inm->registro['cat_sat_forma_pago_id'] = '1';
        $inm->registro['cat_sat_metodo_pago_id'] = '1';
        $inm->registro['cat_sat_uso_cfdi_id'] = '1';
        $inm->registro['com_tipo_cliente_id'] = '1';
        $inm->registro['cat_sat_tipo_persona_id'] = '5';
        $inm->registro['inm_institucion_hipotecaria_id'] = '1';
        $inm->registro['fecha_nacimiento'] = '1981-01-01';
        $inm->registro['telefono_casa'] = '1234567890';
        $inm->registro['correo_empresa'] = 'a@test.com';
        $resultado = $inm->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("Nombre Apellido Paterno  12345678890 XEXX010101HNEXXXA4 AAA010101AAA 2024-05",
            $resultado->registro['inm_comprador_descripcion']);

        $inm_comprador_id = $resultado->registro_id;

        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_inm_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$r_inm_rel_comprador_com_cliente->n_registros);

        $com_cliente_id = $r_inm_rel_comprador_com_cliente->registros[0]['com_cliente_id'];

        $com_cliente = (new com_cliente(link: $this->link))->registro(registro_id: $com_cliente_id);
        $this->assertIsArray($com_cliente);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Nombre Apellido Paterno", $com_cliente['com_cliente_razon_social']);
        $this->assertEquals("2147483647", $com_cliente['com_cliente_telefono']);

        errores::$error = false;

        $inm = new inm_comprador(link: $this->link);

        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error nombre no existe en el registro", $resultado['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error apellido_paterno no existe en el registro", $resultado['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error nss no existe en el registro", $resultado['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error curp no existe en el registro", $resultado['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error rfc no existe en el registro", $resultado['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $resultado = $inm->alta_bd();
       //RINT_R($resultado);exit;
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error lada_nep no existe en el registro", $resultado['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = 'LADA NEP';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error numero_nep no existe en el registro", $resultado['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = 'LADA NEP';
        $inm->registro['numero_nep'] = 'numero NEP';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error lada_com no existe en el registro", $resultado['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = 'LADA NEP';
        $inm->registro['numero_nep'] = 'numero NEP';
        $inm->registro['lada_com'] = 'lada com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error numero_com no existe en el registro", $resultado['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = 'LADA NEP';
        $inm->registro['numero_nep'] = 'numero NEP';
        $inm->registro['lada_com'] = 'lada com';
        $inm->registro['numero_com'] = 'nuemro com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error lada debe ser un numero", $resultado['data']['data']['data']['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = '1';
        $inm->registro['numero_nep'] = 'numero NEP';
        $inm->registro['lada_com'] = 'lada';
        $inm->registro['numero_com'] = 'nuemro com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error lada invalida", $resultado['data']['data']['data']['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = '12';
        $inm->registro['numero_nep'] = 'numero NEP';
        $inm->registro['lada_com'] = 'lada';
        $inm->registro['numero_com'] = 'nuemro com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error tel debe ser un numero", $resultado['data']['data']['data']['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'RFC';
        $inm->registro['lada_nep'] = '12';
        $inm->registro['numero_nep'] = '1';
        $inm->registro['lada_com'] = 'lada';
        $inm->registro['numero_com'] = 'nuemro com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error telefono invalido", $resultado['data']['data']['data']['data']['data']['data']['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = 'NSS';
        $inm->registro['curp'] = 'CURP';
        $inm->registro['rfc'] = 'AAA010101AAA';
        $inm->registro['lada_nep'] = '12';
        $inm->registro['numero_nep'] = '12345678';
        $inm->registro['lada_com'] = '12';
        $inm->registro['numero_com'] = '12345678';
        $inm->registro['cel_com'] = '12345678';
        $inm->registro['correo_com'] = '12345678';
        $inm->registro['descuento_pension_alimenticia_dh'] = '12345678';
        $inm->registro['descuento_pension_alimenticia_fc'] = '12345678';
        $inm->registro['es_segundo_credito'] = '12345678';
        $inm->registro['inm_attr_tipo_credito_id'] = '12345678';
        $inm->registro['inm_destino_credito_id'] = '12345678';
        $inm->registro['inm_estado_civil_id'] = '12345678';
        $inm->registro['inm_producto_infonavit_id'] = '12345678';
        $inm->registro['monto_ahorro_voluntario'] = '12345678';
        $inm->registro['monto_credito_solicitado_dh'] = '12345678';
        $inm->registro['nombre_empresa_patron'] = '12345678';
        $inm->registro['nrp_nep'] = '12345678';
        $inm->registro['inm_institucion_hipotecaria_id'] = '12345678';
        $inm->registro['fecha_nacimiento'] = '12345678';
        $inm->registro['telefono_casa'] = '12345678';
        $inm->registro['correo_empresa'] = '12345678';
        $resultado = $inm->alta_bd();

        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al insertar", $resultado['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = '12345678912';
        $inm->registro['curp'] = 'XEXX010101MNEXXXA8';
        $inm->registro['rfc'] = 'AAA010101AAA';
        $inm->registro['lada_nep'] = '12';
        $inm->registro['numero_nep'] = '12345678';
        $inm->registro['lada_com'] = '12';
        $inm->registro['numero_com'] = '12345678';
        $inm->registro['cel_com'] = '1234567811';
        $inm->registro['correo_com'] = 'x@x.com';
        $inm->registro['descuento_pension_alimenticia_dh'] = '12345678';
        $inm->registro['descuento_pension_alimenticia_fc'] = '12345678';
        $inm->registro['es_segundo_credito'] = '12345678';
        $inm->registro['inm_attr_tipo_credito_id'] = '12345678';
        $inm->registro['inm_destino_credito_id'] = '12345678';
        $inm->registro['inm_estado_civil_id'] = '12345678';
        $inm->registro['inm_producto_infonavit_id'] = '12345678';
        $inm->registro['monto_ahorro_voluntario'] = '12345678';
        $inm->registro['monto_credito_solicitado_dh'] = '12345678';
        $inm->registro['nombre_empresa_patron'] = '12345678';
        $inm->registro['nrp_nep'] = '12345678';
        $inm->registro['inm_institucion_hipotecaria_id'] = '12345678';
        $inm->registro['fecha_nacimiento'] = '12345678';
        $inm->registro['telefono_casa'] = '1234567811';
        $inm->registro['correo_empresa'] = 'aaa@sss.com';
        $resultado = $inm->alta_bd();
        $this->assertIsArray($com_cliente);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al insertar", $resultado['mensaje_limpio']);

        errores::$error = false;
        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'AP';
        $inm->registro['nss'] = '12345678912';
        $inm->registro['curp'] = 'XEXX010101MNEXXXA8';
        $inm->registro['rfc'] = 'AAA010101AAA';
        $inm->registro['lada_nep'] = '12';
        $inm->registro['numero_nep'] = '12345678';
        $inm->registro['lada_com'] = '12';
        $inm->registro['numero_com'] = '12345678';
        $inm->registro['cel_com'] = '1234567811';
        $inm->registro['correo_com'] = 'x@x.com';
        $inm->registro['descuento_pension_alimenticia_dh'] = '12345678';
        $inm->registro['descuento_pension_alimenticia_fc'] = '12345678';
        $inm->registro['es_segundo_credito'] = '12345678';
        $inm->registro['inm_attr_tipo_credito_id'] = '1';
        $inm->registro['inm_destino_credito_id'] = '1';
        $inm->registro['inm_estado_civil_id'] = '1';
        $inm->registro['inm_producto_infonavit_id'] = '1';
        $inm->registro['monto_ahorro_voluntario'] = '12345678';
        $inm->registro['monto_credito_solicitado_dh'] = '12345678';
        $inm->registro['nombre_empresa_patron'] = '12345678';
        $inm->registro['nrp_nep'] = '12345678';
        $inm->registro['inm_institucion_hipotecaria_id'] = '1';
        $inm->registro['fecha_nacimiento'] = '12345678';
        $inm->registro['telefono_casa'] = '1234567811';
        $inm->registro['correo_empresa'] = 'aaa@sss.com';
        $inm->registro['numero_exterior'] = 'x';
        $inm->registro['cat_sat_regimen_fiscal_id'] = '601';
        $inm->registro['cat_sat_moneda_id'] = '161';
        $inm->registro['cat_sat_forma_pago_id'] = '1';
        $inm->registro['cat_sat_metodo_pago_id'] = '1';
        $inm->registro['cat_sat_uso_cfdi_id'] = '1';
        $inm->registro['com_tipo_cliente_id'] = '1';
        $inm->registro['cat_sat_tipo_persona_id'] = '4';
        $resultado = $inm->alta_bd();


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;


    }

    public function test_asigna_nuevo_co_acreditado_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_com_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);
            exit;
        }

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);

        $inm_comprador_id = 1;
        $inm_co_acreditado = array();
        $inm_co_acreditado['nombre'] = 'A';
        $inm_co_acreditado['apellido_paterno'] = 'A';
        $inm_co_acreditado['nss'] = '12345678901';
        $inm_co_acreditado['curp'] = 'XEXX010101HNEXXXA4';
        $inm_co_acreditado['rfc'] = 'XXX010101AAA';
        $inm_co_acreditado['apellido_materno'] = 'A';
        $inm_co_acreditado['lada'] = '12';
        $inm_co_acreditado['numero'] = '12345677';
        $inm_co_acreditado['celular'] = '1234567890';
        $inm_co_acreditado['genero'] = 'A';
        $inm_co_acreditado['correo'] = 'a@a.com';
        $inm_co_acreditado['nombre_empresa_patron'] = 'A';
        $inm_co_acreditado['nrp'] = 'A';
        $inm_co_acreditado['lada_nep'] = 'A';
        $inm_co_acreditado['numero_nep'] = 'A';


        $resultado = $inm->asigna_nuevo_co_acreditado_bd($inm_comprador_id, $inm_co_acreditado);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("A A A 12345678901 XEXX010101HNEXXXA4 XXX010101AAA 2024-05-",
            $resultado->inm_co_acreditado->registro['inm_co_acreditado_descripcion']);

        errores::$error = false;
    }


    public function test_data_pdf(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_conf_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->data_pdf($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener comprador",$resultado['mensaje_limpio']);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_rel_ubi_comp(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_conf_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }


        $resultado = $inm->data_pdf($inm_comprador_id);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_comprador['inm_comprador_id']);
        $this->assertEquals(1,$resultado->inm_comprador['inm_producto_infonavit_id']);
        $this->assertEquals("UBICACION ASIGNADA",$resultado->inm_comprador['inm_comprador_etapa']);

        errores::$error = false;
    }


    public function test_elimina_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);

        $id = 1;

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $resultado = $inm->elimina_bd(id: $id);


        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al eliminar registro de comprador",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->elimina_bd(id: $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro['inm_comprador_id']);
        errores::$error = false;
    }

    public function test_get_co_acreditados(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $inm_comprador_id = 1;
        $resultado = $inm->get_co_acreditados($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_co_acred(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm_comprador_id = 1;
        $resultado = $inm->get_co_acreditados($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado[0]['inm_co_acreditado_id']);
        errores::$error = false;
    }
    public function test_get_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->get_com_cliente($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener imp_rel_comprador_com_cliente",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->get_com_cliente($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('MXN',$resultado['cat_sat_moneda_codigo_bis']);
        $this->assertEquals(151,$resultado['cat_sat_moneda_dp_pais_id']);

        errores::$error = false;
    }
    public function test_get_referencias(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_referencia(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->get_referencias($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);


        errores::$error = false;

        $alta = (new base_test())->alta_inm_referencia(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm_comprador_id = 1;
        $resultado = $inm->get_referencias($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado[0]['inm_referencia_id']);
        errores::$error = false;
    }

    public function test_modifica_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);
        $registro = array();
        $id = 1;
        $resultado = $inm->modifica_bd($registro, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_actualizado->inm_comprador_id);
        errores::$error = false;
    }

    public function test_r_modifica_post(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        $inm = new liberator($inm);



        $data_upd = new stdClass();
        $id = 1;

        $resultado = $inm->r_modifica_post($data_upd, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_actualizado->inm_comprador_id);
        errores::$error = false;
    }

    public function test_result_upd_post(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        $inm = new liberator($inm);



        $data_upd = new stdClass();
        $data_upd->aplica_upd_posterior = false;
        $id = 1;

        $resultado = $inm->result_upd_post($data_upd, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;

        $data_upd = new stdClass();
        $data_upd->aplica_upd_posterior = true;
        $id = 1;

        $resultado = $inm->result_upd_post($data_upd, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_id);
        errores::$error = false;
    }

    public function test_upd_post(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);



        $id = 1;
        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = new stdClass();
        $r_modifica->registro_actualizado->inm_comprador_es_segundo_credito = 'NO';
        $r_modifica->registro_actualizado->inm_comprador_con_discapacidad = 'NO';
        $resultado = $inm->upd_post($id, $r_modifica);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_actualizado->inm_comprador_id);
        errores::$error = false;
    }





}

