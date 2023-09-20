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
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _alta_compradorTest extends test {
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

    public function test_default_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $resultado = $inm->default_infonavit($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('D',$resultado['nombre']);
        $this->assertEquals('D',$resultado['apellido_paterno']);
        $this->assertEquals('D',$resultado['nss']);
        $this->assertEquals('D',$resultado['curp']);
        $this->assertEquals('D',$resultado['rfc']);

        $this->assertEquals(7,$resultado['inm_plazo_credito_sc_id']);
        $this->assertEquals(5,$resultado['inm_tipo_discapacidad_id']);
        $this->assertEquals(6,$resultado['inm_persona_discapacidad_id']);
        errores::$error = false;
    }

    public function test_init_row_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        //$inm = new liberator($inm);


        $registro = array();
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'B';
        $registro['nss'] = '5566755443';
        $registro['curp'] = 'XEXX010101MNEXXXA8';
        $registro['rfc'] = 'GAF660911675';
        $registro['lada_nep'] = '123';
        $registro['numero_nep'] = '1235434';
        $registro['lada_com'] = '43';
        $registro['numero_com'] = '43554433';

        $resultado = $inm->init_row_alta($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A',$resultado['nombre']);
        $this->assertEquals('B',$resultado['apellido_paterno']);
        $this->assertEquals('5566755443',$resultado['nss']);
        $this->assertEquals('XEXX010101MNEXXXA8',$resultado['curp']);
        $this->assertEquals(7,$resultado['inm_plazo_credito_sc_id']);
        errores::$error = false;
    }

    public function test_integra_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);

        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $resultado = $inm->integra_descripcion($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('D',$resultado['nombre']);
        $this->assertEquals('D',$resultado['apellido_paterno']);
        $this->assertEquals('D',$resultado['nss']);
        $this->assertEquals('D',$resultado['curp']);
        $this->assertEquals('D',$resultado['rfc']);
        $this->assertEquals('D D  D D D',$resultado['descripcion']);

        errores::$error = false;
    }

    public function test_numero_completo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $key_lada = 'a';
        $key_numero = 'z';

        $registro['a'] = '111';
        $registro['z'] = '1111111';

        $resultado = $inm->numero_completo($key_lada, $key_numero, $registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1111111111',$resultado);

        errores::$error = false;
    }

    public function test_numero_completo_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $key_lada = 'a';
        $key_numero = 'b';
        $registro['a'] = '123';
        $registro['b'] = '12345676';
        $resultado = $inm->numero_completo_base($key_lada, $key_numero, $registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

    }

    public function test_numero_completo_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['lada_com'] = '11';
        $registro['numero_com'] = '11223344';

        $resultado = $inm->numero_completo_com($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1111223344',$resultado);
        errores::$error = false;
    }

    public function test_numero_completo_nep(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $registro['lada_nep'] = '012';
        $registro['numero_nep'] = '0156789';
        $resultado = $inm->numero_completo_nep($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('0120156789',$resultado);
        errores::$error = false;
    }

    public function test_pr_sub_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $link = $this->link;
        $pr_proceso_descripcion = 'INMOBILIARIA CLIENTES';
        $pr_sub_proceso_descripcion = 'ALTA';
        $tabla = 'inm_comprador';
        $resultado = $inm->pr_sub_proceso($link, $pr_proceso_descripcion, $pr_sub_proceso_descripcion, $tabla);
        $this->assertIsaRRAY($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('ALTA',$resultado['pr_sub_proceso_descripcion']);
        errores::$error = false;
    }

    public function test_valida_base_comprador(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['lada_nep'] = '12';
        $registro['numero_nep'] = '12456677';
        $registro['lada_com'] = '333';
        $registro['numero_com'] = '5678902';
        $registro['rfc'] = 'GAVM830930876';

        $resultado = $inm->valida_base_comprador($registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

}

