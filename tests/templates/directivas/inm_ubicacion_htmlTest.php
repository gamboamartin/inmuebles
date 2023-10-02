<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\template\html;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_ubicacion_htmlTest extends test {
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

    public function test_select_inm_ubicacion_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $cols = 2;
        $con_registros = true;
        $id_selected = -1;
        $link = $this->link;
        $resultado = $html->select_inm_ubicacion_id($cols, $con_registros, $id_selected, $link);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase(" inm_ubicacion_id' data-live-search='true' id='inm_ubicacion_id' name='inm_ubicacion_id' required",$resultado);
        errores::$error = false;
    }
   public function test_format_moneda_mx(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $monto = '';
        $resultado = $html->format_moneda_mx(monto: $monto);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error monto no puede ser vacio",$resultado['mensaje_limpio']);

        errores::$error = false;

        $monto = '1.1';
        $resultado = $html->format_moneda_mx(monto: $monto);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("$1.10",$resultado);

        errores::$error = false;
    }


}

