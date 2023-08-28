<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _keys_selectsTest extends test {
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

    public function test_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->base($controler, $keys_selects, $row_upd);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Tipo de Cliente",$resultado['com_tipo_cliente_id']->label);
        errores::$error = false;
    }

    public function test_init_row_upd_fiscales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $row_upd = new stdClass();
        $resultado = $ks->init_row_upd_fiscales($row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(605,$resultado->cat_sat_regimen_fiscal_id);
        $this->assertEquals(161,$resultado->cat_sat_moneda_id);
        $this->assertEquals(99,$resultado->cat_sat_forma_pago_id);
        $this->assertEquals(2,$resultado->cat_sat_metodo_pago_id);
        $this->assertEquals(22,$resultado->cat_sat_uso_cfdi_id);
        $this->assertEquals(5,$resultado->cat_sat_tipo_persona_id);
        $this->assertEquals(-1,$resultado->bn_cuenta_id);
        errores::$error = false;
    }


    public function test_init_row_upd_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $row_upd = new stdClass();
        $resultado = $ks->init_row_upd_infonavit($row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado->inm_producto_infonavit_id);
        $this->assertEquals(-1,$resultado->inm_attr_tipo_credito_id);
        $this->assertEquals(-1,$resultado->inm_destino_credito_id);
        $this->assertEquals(7,$resultado->inm_plazo_credito_sc_id);
        $this->assertEquals(5,$resultado->inm_tipo_discapacidad_id);
        $this->assertEquals(6,$resultado->inm_persona_discapacidad_id);
        errores::$error = false;
    }

    public function test_ks_fiscales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->ks_fiscales($controler, $keys_selects, $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(605,$resultado['cat_sat_regimen_fiscal_id']->id_selected);
        errores::$error = false;
    }

    public function test_ks_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';




        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->ks_infonavit(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Producto',$resultado['inm_producto_infonavit_id']->label);
        errores::$error = false;
    }


}

