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

    public function test_asigna_datos_alta(): void
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

        $registro = array();
        $registro['nombre'] = 'z';
        $registro['apellido_paterno'] = 'z';
        $resultado = $_pr->asigna_datos_alta($modelo, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase('XEXX010101HNEXXXA4',$resultado['curp']);
        errores::$error = false;
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

    public function test_asigna_dp_calle_pertenece_id(): void
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
        $modelo = new inm_prospecto(link: $this->link);
        $resultado = $_pr->asigna_dp_calle_pertenece_id($modelo, $registro);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(100,$resultado['dp_calle_pertenece_id']);
        errores::$error = false;
    }

    public function test_com_prospecto_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        //$modelo = new inm_prospecto(link: $this->link);

        $registro = array();
        $registro['nombre'] = 'z';
        $registro['apellido_paterno'] = 'z';
        $registro['lada_com'] = 'z';
        $registro['numero_com'] = 'z';
        $registro['razon_social'] = 'z';
        $registro['com_agente_id'] = 'z';
        $registro['com_tipo_prospecto_id'] = 'z';
        $resultado = $_pr->com_prospecto_ins($registro);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('z',$resultado['razon_social']);
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

    public function test_init_numbers_dom(): void
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
        $resultado = $_pr->init_numbers_dom($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('SN',$resultado['numero_exterior']);
        $this->assertEquals('SN',$resultado['numero_interior']);
        errores::$error = false;
    }

    public function test_inserta_com_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_tipo_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_com_agente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_com_tipo_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $_pr = new _prospecto();
        $_pr = new liberator($_pr);

        //$modelo = new inm_prospecto(link: $this->link);

        $registro = array();
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'A';
        $registro['lada_com'] = 'A';
        $registro['numero_com'] = 'A';
        $registro['razon_social'] = 'A';
        $registro['com_agente_id'] = '1';
        $registro['com_tipo_prospecto_id'] = '1';
        $link = $this->link;
        $resultado = $_pr->inserta_com_prospecto($link, $registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;

    }

}

