<?php
namespace controllers;


use gamboamartin\comercial\models\com_cliente;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_base;
use gamboamartin\inmuebles\controllers\_dps_init;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _baseTest extends test {
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



    public function test_id_retorno(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $base = new _base();
        $base = new liberator($base);

        $resultado = $base->id_retorno();

        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

        unset($_POST['id_retorno']);

        $resultado = $base->id_retorno();
        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado);

        errores::$error = false;

        $_POST['id_retorno'] = 10;

        $resultado = $base->id_retorno();
        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(10,$resultado);

        errores::$error = false;


    }

    public function test_init_retorno(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $base = new _base();
        //$base = new liberator($base);

        $resultado = $base->init_retorno();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

        $_POST['id_retorno'] = 'a';
        $resultado = $base->init_retorno();
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);

        errores::$error = false;
        $_POST['id_retorno'] = '12';
        $_POST['btn_action_next'] = 'ALFA';
        $resultado = $base->init_retorno();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(12,$resultado->id_retorno);
        $this->assertEquals('ALFA',$resultado->siguiente_view);
        errores::$error = false;
    }

    public function test_out(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $base = new _base();
        //$base = new liberator($base);

        $header = false;
        $result = '';
        $retorno = new stdClass();
        $ws = false;
        $controlador = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $resultado = $base->out($controlador, $header, $result, $retorno, $ws);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }


}

