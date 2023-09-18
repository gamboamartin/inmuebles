<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_alta_comprador;
use gamboamartin\inmuebles\models\_base_comprador;
use gamboamartin\inmuebles\models\_com_cliente;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _com_clienteTest extends test {
    public errores $errores;
    private stdClass $paths_conf;
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/inmuebles/config/generales.php';
        $this->paths_conf->database = '/var/www/html/inmuebles/config/database.php';
        $this->paths_conf->views = '/var/www/html/inmuebles/config/views.php';
    }

    public function test_com_cliente_data_transaccion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $numero_interior = '';
        $razon_social = 'a';
        $registro = array();
        $telefono = 'v';
        $registro['cat_sat_forma_pago_id'] = 1;
        $registro['cat_sat_metodo_pago_id'] = 1;
        $registro['cat_sat_moneda_id'] = 1;
        $registro['cat_sat_regimen_fiscal_id'] = 1;
        $registro['cat_sat_tipo_persona_id'] = 1;
        $registro['cat_sat_uso_cfdi_id'] = 1;
        $registro['com_tipo_cliente_id'] = 1;
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;
        $registro['rfc'] = 1;

        $resultado = $inm->com_cliente_data_transaccion($numero_interior, $razon_social, $registro, $telefono);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('v',$resultado['telefono']);
        errores::$error = false;
    }

    public function test_com_cliente_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $numero_interior = '';
        $razon_social = 'A';
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '11';
        $registro_entrada['numero_com'] = '22222222';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';



        $resultado = $inm->com_cliente_ins($numero_interior, $razon_social, $registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A',$resultado['codigo']);
        errores::$error = false;
    }

    public function test_numero_interior(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();

        $resultado = $inm->numero_interior($registro_entrada);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('',$resultado);
        errores::$error = false;
    }

    public function test_razon_social(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro = new stdClass();
        $con_prefijo = false;

        $registro->nombre = 'A';
        $registro->apellido_paterno = 'A';

        $resultado = $inm->razon_social($con_prefijo, $registro);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A A',$resultado);

        errores::$error = false;
        $registro = new stdClass();
        $con_prefijo = true;

        $registro->inm_comprador_nombre = 'A';
        $registro->inm_comprador_apellido_paterno = 'A';

        $resultado = $inm->razon_social($con_prefijo, $registro);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A A',$resultado);

        errores::$error = false;
    }

    public function test_valida_base_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        //$inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';

        $resultado = $inm->valida_base_com($registro_entrada);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_valida_existencia_keys_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';

        $resultado = $inm->valida_existencia_keys_com($registro_entrada);

        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;

    }

    public function test_valida_ids_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';

        $resultado = $inm->valida_ids_com($registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;

    }


}

