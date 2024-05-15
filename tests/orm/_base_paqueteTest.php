<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_base_paquete;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\_referencias;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _base_paqueteTest extends test {
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



    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        //$_inm = new liberator($_inm);

        $registro = array();
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'A';
        $registro['nss'] = 'A';
        $registro['curp'] = 'A';
        $registro['rfc'] = 'A';

        $resultado = $_inm->descripcion($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("A A  A A A 2024-05-",$resultado);

        errores::$error = false;
    }

    public function test_init_data_domicilio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        //$_inm = new liberator($_inm);

        $init_data = array();

        $resultado = $_inm->init_data_domicilio($init_data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_pais']);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_estado']);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_municipio']);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_cp']);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_colonia_postal']);
        $this->assertEquals("gamboamartin\direccion_postal",$resultado['dp_calle_pertenece']);
        errores::$error = false;
    }

    public function test_init_data_row(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        //$_inm = new liberator($_inm);

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $alta);
            print_r($error);exit;
        }

        $id = 1;
        $key_entidad_base_id = '';
        $key_entidad_id = '';
        $modelo = new inm_ubicacion(link: $this->link);

        $resultado = $_inm->init_data_row($id, $key_entidad_base_id, $key_entidad_id, $modelo);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   NUM EXT",$resultado['descripcion']);

        errores::$error = false;
    }

    public function test_montos_0(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        //$_inm = new liberator($_inm);

        $registro = array();
        $resultado = $_inm->montos_0($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado['monto_final']);
        $this->assertEquals(0,$resultado['sub_cuenta']);
        $this->assertEquals(0,$resultado['descuento']);
        $this->assertEquals(0,$resultado['puntos']);

        errores::$error = false;
    }

    public function test_rename_data_nac(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        //$_inm = new liberator($_inm);

        $renombres = array();
        $enlace = '';
        $resultado = $_inm->rename_data_nac($enlace, $renombres);
        $this->assertEquals("dp_estado",$resultado['dp_estado_nacimiento']['nombre_original']);
        $this->assertEquals("dp_municipio_nacimiento",$resultado['dp_estado_nacimiento']['enlace']);
        $this->assertEquals("id",$resultado['dp_estado_nacimiento']['key']);
        $this->assertEquals("dp_estado_id",$resultado['dp_estado_nacimiento']['key_enlace']);
        $this->assertEquals("dp_municipio_nacimiento_id",$resultado['dp_municipio_nacimiento']['key_enlace']);
        errores::$error = false;
    }

    public function test_rename_estado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _base_paquete();
        $_inm = new liberator($_inm);

        $renombres = array();

        $resultado = $_inm->rename_estado($renombres);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("dp_estado",$resultado['dp_estado_nacimiento']['nombre_original']);
        $this->assertEquals("dp_municipio_nacimiento",$resultado['dp_estado_nacimiento']['enlace']);
        $this->assertEquals("id",$resultado['dp_estado_nacimiento']['key']);
        $this->assertEquals("dp_estado_id",$resultado['dp_estado_nacimiento']['key_enlace']);
        errores::$error = false;
    }

}

