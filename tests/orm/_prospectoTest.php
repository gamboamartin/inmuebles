<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\_prospecto;
use gamboamartin\inmuebles\models\_referencias;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _prospectoTest extends test {
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

    public function test_asigna_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $registro = array();
        $registro['nombre'] = 'a';
        $registro['apellido_paterno'] = 'a';
        $registro['nss'] = 'a';
        $registro['curp'] = 'a';
        $registro['rfc'] = 'a';
        $resultado = $_pr->asigna_descripcion($registro);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase('a a  a a a 2023-10-',$resultado['descripcion']);
        errores::$error = false;
    }

    public function test_dp_calle_pertenece_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $modelo = new inm_prospecto(link: $this->link);
        $resultado = $_pr->dp_calle_pertenece_id($modelo);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(100,$resultado);
        errores::$error = false;
    }

    public function test_init_data_default(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $registro = array();
        $resultado = $_pr->init_data_default($registro);
        $this->assertEquals('',$resultado['apellido_materno']);
        $this->assertEquals('99999999999',$resultado['nss']);
        $this->assertEquals('XEXX010101HNEXXXA4',$resultado['curp']);
        $this->assertEquals('XAXX010101000',$resultado['rfc']);
        $this->assertEquals('1900-01-01',$resultado['fecha_nacimiento']);
        errores::$error = false;
    }

    public function test_init_data_fiscal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $registro = array();
        $resultado = $_pr->init_data_fiscal($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('99999999999',$resultado['nss']);
        $this->assertEquals('XEXX010101HNEXXXA4',$resultado['curp']);
        $this->assertEquals('XAXX010101000',$resultado['rfc']);


        errores::$error = false;
    }

    public function test_init_key(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $key = 'a_id';
        $registro = array();
        $resultado = $_pr->init_key($key, $registro);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('',$resultado['a_id']);


        errores::$error = false;


    }

    public function test_init_keys(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $keys = array();
        $registro = array();
        $keys[] = 'a';
        $resultado = $_pr->init_keys(keys: $keys,registro:  $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('',$resultado['a']);


        errores::$error = false;
    }

    public function test_init_keys_sin_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        $registro = array();
        $resultado = $_pr->init_keys_sin_data($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('',$resultado['apellido_materno']);
        errores::$error = false;
    }







}

