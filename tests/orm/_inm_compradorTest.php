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

