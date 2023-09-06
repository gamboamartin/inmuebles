<?php
namespace controllers;


use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\_pdf;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use setasign\Fpdi\Fpdi;
use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _pdfTest extends test {
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

    public function test_add_template(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $pdf = new Fpdi();


        $_pdf = new _pdf($pdf);

        $_pdf = new liberator($_pdf);

        $file_plantilla = 'templates/solicitud_infonavit.pdf';
        $page = 2;
        $path_base = (new generales())->path_base;
        $plantilla_cargada = false;
        $resultado = $_pdf->add_template($file_plantilla, $page, $path_base, $plantilla_cargada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));


        errores::$error = false;
    }

    public function test_tpl_idx(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $pdf = new Fpdi();


        $_pdf = new _pdf($pdf);

        $_pdf = new liberator($_pdf);

        $file_plantilla = 'templates/solicitud_infonavit.pdf';
        $page = 1;
        $path_base = (new generales())->path_base;
        $plantilla_cargada = false;
        $resultado = $_pdf->tpl_idx($file_plantilla, $page, $path_base, $plantilla_cargada);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("/var/www/html/inmuebles/templates/solicitud_infonavit.pdf|1|1|0|CropBox",$resultado);
        errores::$error = false;


    }

    public function test_valida_datos_plantilla(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $pdf = new Fpdi();


        $_pdf = new _pdf($pdf);

        $_pdf = new liberator($_pdf);

        $file_plantilla = '';
        $page = -1;
        $path_base = '';

        $resultado = $_pdf->valida_datos_plantilla($file_plantilla, $page, $path_base);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error file_plantilla esta vacio",$resultado['mensaje_limpio']);
        errores::$error = false;

        $file_plantilla = 'a';
        $page = -1;
        $path_base = '';

        $resultado = $_pdf->valida_datos_plantilla($file_plantilla, $page, $path_base);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error path_base esta vacio",$resultado['mensaje_limpio']);
        errores::$error = false;

        $file_plantilla = 'a';
        $page = -1;
        $path_base = 'b';

        $resultado = $_pdf->valida_datos_plantilla($file_plantilla, $page, $path_base);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error page debe ser mayor a 0",$resultado['mensaje_limpio']);
        errores::$error = false;

        $file_plantilla = 'a';
        $page = 5;
        $path_base = 'b';

        $resultado = $_pdf->valida_datos_plantilla($file_plantilla, $page, $path_base);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error no existe la plantilla",$resultado['mensaje_limpio']);

        errores::$error = false;

        $file_plantilla = 'templates/solicitud_infonavit.pdf';
        $page = 1;
        $path_base = (new generales())->path_base;

        $resultado = $_pdf->valida_datos_plantilla($file_plantilla, $page, $path_base);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_write(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $pdf = new Fpdi();

        $_pdf = new _pdf($pdf);
        $_pdf = new liberator($_pdf);

        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->AddPage();



        $valor = 'axJ';
        $x = 1;
        $y = 2;

        $resultado = $_pdf->write($valor, $x, $y);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));
        errores::$error = false;


    }


}

