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
use gamboamartin\inmuebles\models\inm_comprador_proceso;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_comprador_procesoTest extends test {
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

    public function test_alta_registro(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_comprador_proceso(link: $this->link);
        //$inm = new liberator($inm);

        $registro = array();
        $registro['inm_comprador_id'] = 1;
        $registro['pr_sub_proceso_id'] = 2;
        $registro['fecha'] = '2020-01-01';

        $resultado = $inm->alta_registro($registro);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro['inm_comprador_proceso_inm_comprador_id']);


        errores::$error = false;
    }

    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_comprador_proceso(link: $this->link);
        $inm = new liberator($inm);

        $registro = array();
        $registro['inm_comprador_id'] = 1;
        $registro['pr_sub_proceso_id'] = 1;
        $registro['fecha'] = '2020-01-01';

        $resultado = $inm->descripcion(key_entidad_base_id: 'inm_comprador_id',
            key_entidad_id: 'pr_sub_proceso_id', registro: $registro);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("1 1 2020-01-01",$resultado);
        errores::$error = false;
    }

    public function test_init_row(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_comprador_proceso(link: $this->link);
        //$inm = new liberator($inm);

        $registro = array();
        $registro['inm_comprador_id'] = '1';
        $registro['pr_sub_proceso_id'] = '1';
        $registro['fecha'] = '2020-01-01';

        $resultado = $inm->init_row(key_entidad_base_id: 'inm_comprador_id', key_entidad_id: 'pr_sub_proceso_id',
            registro: $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("1 1 2020-01-01",$resultado['descripcion']);
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


        $inm = new inm_comprador_proceso(link: $this->link);
        $inm = new liberator($inm);

        $registro = array();
        $registro['inm_comprador_id'] = 1;
        $registro['pr_sub_proceso_id'] = 1;
        $registro['fecha'] = '2020-01-01';


        $resultado = $inm->integra_descripcion(key_entidad_base_id: 'inm_comprador_id',
            key_entidad_id: 'pr_sub_proceso_id', registro: $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("1 1 2020-01-01",$resultado['descripcion']);
        errores::$error = false;
    }

    public function test_valida_init(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_comprador_proceso(link: $this->link);
        //$inm = new liberator($inm);

        $registro = array();
        $registro['inm_comprador_id'] = 1;
        $registro['pr_sub_proceso_id'] = 2;
        $registro['fecha'] = '2020-01-01';

        $resultado = $inm->valida_init(key_entidad_base_id: 'inm_comprador_id', key_entidad_id: 'pr_sub_proceso_id',
            registro: $registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }




}

