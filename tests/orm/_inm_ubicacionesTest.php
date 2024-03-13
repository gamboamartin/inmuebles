<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _inm_ubicacionesTest extends test {
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

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }


        $_inm = new inm_ubicacion(link: $this->link);
        //$_inm = new liberator($_inm);
        $_inm->registro['dp_calle_pertenece_id'] = 1;
        $_inm->registro['numero_exterior'] = 1;
        $_inm->registro['cuenta_predial'] = 1;
        $_inm->registro['descripcion'] = 1;
        $_inm->registro['inm_tipo_ubicacion_id'] = 1;
        $resultado = $_inm->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro['inm_tipo_ubicacion_id']);
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

        $_inm = new inm_ubicacion(link: $this->link);
        //$_inm = new liberator($_inm);

        $registro = array();
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;
        $resultado = $_inm->init_row(key_entidad_base_id: '', key_entidad_id: '', registro: $registro);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['dp_calle_pertenece_id']);
        $this->assertEquals(1,$resultado['numero_exterior']);
        $this->assertEquals('',$resultado['manzana']);
        $this->assertEquals('',$resultado['lote']);
        $this->assertEquals('',$resultado['numero_interior']);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   1",$resultado['descripcion']);

        errores::$error = false;
    }

    public function test_valida_row(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $_inm = new inm_ubicacion(link: $this->link);
        $_inm = new liberator($_inm);
        $registro = array();
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;

        $resultado = $_inm->valida_row($registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }



}

