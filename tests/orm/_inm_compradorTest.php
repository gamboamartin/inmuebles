<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _inm_compradorTest extends test {
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

    public function test_inm_ubicacion_id_input(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        //$controler->row_upd = new stdClass();
        //$controler->inputs = new stdClass();

        $resultado = $_inm->inm_ubicacion_id_input($controler);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("for='inm_ubicacion_id'>Ubicacion</label><div class='controls'>",$resultado);
        errores::$error = false;
    }

    public function test_inm_ubicaciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        //$controler->row_upd = new stdClass();
        //$controler->inputs = new stdClass();

        $del = (new base_test())->del_inm_rel_ubi_comp(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $_inm->inm_ubicaciones($inm_comprador_id, $link);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_ubi_comp(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $_inm->inm_ubicaciones($inm_comprador_id, $link);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado[0]['inm_ubicacion_id']);
        errores::$error = false;

    }

    public function test_keys_selects(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $resultado = $_inm->keys_selects($controler);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("inm_producto_infonavit_descripcion",$resultado['inm_producto_infonavit_id']->columns_ds[0]);
        errores::$error = false;
    }

    public function test_radios(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $controler->inputs = new stdClass();
        $checked_default_1 = 2;
        $checked_default_2 = 2;
        $resultado = $_inm->radios($checked_default_1,$checked_default_2, $controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'>",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase("<label class='control-label' for='Es Segundo Credito'>Es Segundo Credito</label>",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase(" <label class='form-check-label chk form-check-label'>",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase(" <input type='radio' name='es_segundo_credito' value='SI' class='form-check-input es_segundo_credito form-check-input' id='es_segundo_credito'",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase(" title='Es Segundo Credito' >",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase("SI",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase("</label>",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase("<input type='radio' name='es_segundo_credito' value='NO' class='form-check-input es_segundo_credito form-check-input' id='es_segundo_credito'",$resultado->es_segundo_credito);
        $this->assertStringContainsStringIgnoringCase(" title='Es Segundo Credito' checked>",$resultado->es_segundo_credito);
    }

    public function test_row_upd_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        $_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $resultado = $_inm->row_upd_base($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->descuento_pension_alimenticia_dh);
        $this->assertEquals(0,$resultado->monto_credito_solicitado_dh);
        $this->assertEquals(0,$resultado->descuento_pension_alimenticia_fc);
        $this->assertEquals(0,$resultado->monto_ahorro_voluntario);
        $this->assertEquals(1,$resultado->inm_producto_infonavit_id);
        $this->assertEquals(6,$resultado->inm_attr_tipo_credito_id);
        $this->assertEquals(1,$resultado->inm_destino_credito_id);
        errores::$error = false;
    }

    public function test_row_upd_ids(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        $_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $resultado = $_inm->row_upd_ids($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_producto_infonavit_id);
        $this->assertEquals(6,$resultado->inm_attr_tipo_credito_id);
        $this->assertEquals(1,$resultado->inm_destino_credito_id);
        errores::$error = false;
    }



    public function test_row_upd_montos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $_inm = new _inm_comprador();
        $_inm = new liberator($_inm);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $resultado = $_inm->row_upd_montos($controler);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->descuento_pension_alimenticia_dh);
        $this->assertEquals(0,$resultado->monto_credito_solicitado_dh);
        $this->assertEquals(0,$resultado->descuento_pension_alimenticia_fc);
        $this->assertEquals(0,$resultado->monto_ahorro_voluntario);


        errores::$error = false;
    }



}

