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
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
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

    public function test_apartado_1(): void
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


        $data = new stdClass();
        $data->inm_comprador = array();

        $data->inm_comprador['inm_producto_infonavit_x'] = '1';
        $data->inm_comprador['inm_producto_infonavit_y'] = '1';
        $data->inm_comprador['inm_tipo_credito_x'] = '1';
        $data->inm_comprador['inm_tipo_credito_y'] = '1';
        $data->inm_comprador['inm_attr_tipo_credito_x'] = '1';
        $data->inm_comprador['inm_attr_tipo_credito_y'] = '1';
        $data->inm_comprador['inm_destino_credito_x'] = '1';
        $data->inm_comprador['inm_destino_credito_y'] = '1';
        $data->inm_comprador['inm_plazo_credito_sc_x'] = '1';
        $data->inm_comprador['inm_plazo_credito_sc_y'] = '1';
        $data->inm_comprador['inm_tipo_discapacidad_x'] = '1';
        $data->inm_comprador['inm_tipo_discapacidad_y'] = '1';
        $data->inm_comprador['inm_persona_discapacidad_x'] = '1';
        $data->inm_comprador['inm_persona_discapacidad_y'] = '1';
        $data->inm_comprador['inm_comprador_es_segundo_credito'] = 'SI';


        $resultado = $_pdf->apartado_1($data);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));
        errores::$error = false;
    }

    public function test_apartado_2(): void
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


        $data = new stdClass();
        $data->inm_comprador = array();

        $data->inm_comprador['inm_comprador_descuento_pension_alimenticia_dh'] = '0';
        $data->inm_comprador['inm_comprador_descuento_pension_alimenticia_fc'] = '0';
        $data->inm_comprador['inm_comprador_monto_credito_solicitado_dh'] = '0';
        $data->inm_comprador['inm_comprador_monto_ahorro_voluntario'] = '1';


        $resultado = $_pdf->apartado_2($data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado[3]);
        errores::$error = false;
    }

    public function test_apartado_3(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $pdf = new Fpdi();
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->AddPage();

        $_pdf = new _pdf($pdf);
        $_pdf = new liberator($_pdf);



        $data = new stdClass();
        $data->imp_rel_ubi_comp = array();
        $data->imp_rel_ubi_comp['inm_rel_ubi_comp_precio_operacion'] = 0;
        $data->inm_comprador = array();
        $data->inm_comprador['inm_comprador_con_discapacidad'] = 'SI';
        $data->inm_comprador['inm_destino_credito_id'] = '1';
        $resultado = $_pdf->apartado_3($data);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));
        errores::$error = false;
    }

    public function test_entidades_infonavit(): void
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


        $data = new stdClass();
        $data->inm_comprador = array();
        $data->inm_comprador['inm_producto_infonavit_x'] = 1;
        $data->inm_comprador['inm_producto_infonavit_y'] = 1;
        $data->inm_comprador['inm_tipo_credito_x'] = 1;
        $data->inm_comprador['inm_tipo_credito_y'] = 1;
        $data->inm_comprador['inm_attr_tipo_credito_x'] = 1;
        $data->inm_comprador['inm_attr_tipo_credito_y'] = 1;
        $data->inm_comprador['inm_destino_credito_x'] = 1;
        $data->inm_comprador['inm_destino_credito_y'] = 1;
        $data->inm_comprador['inm_plazo_credito_sc_x'] = 1;
        $data->inm_comprador['inm_plazo_credito_sc_y'] = 1;
        $data->inm_comprador['inm_tipo_discapacidad_x'] = 1;
        $data->inm_comprador['inm_tipo_discapacidad_y'] = 1;
        $data->inm_comprador['inm_persona_discapacidad_x'] = 1;
        $data->inm_comprador['inm_persona_discapacidad_y'] = 1;

        $resultado = $_pdf->entidades_infonavit($data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;

    }

    public function test_es_segundo_credito(): void
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


        $data = new stdClass();
        $data->inm_comprador = array();

        $data->inm_comprador['inm_comprador_es_segundo_credito'] = 'NO';


        $resultado = $_pdf->es_segundo_credito($data);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));
        errores::$error = false;
    }

    public function test_get_x_var(): void
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



        $condiciones= array();
        $key_id = 'a';
        $row = array();
        $x_init = '-1';

        $row['a'] = 'z';

        $resultado = $_pdf->get_x_var($condiciones, $key_id, $row, $x_init);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado);
        errores::$error = false;
    }

    public function test_keys_comprador(): void
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



        $resultado = $_pdf->keys_comprador();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(57,$resultado['inm_comprador_lada_nep']['x']);
        $this->assertEquals(256,$resultado['inm_comprador_lada_nep']['y']);
        errores::$error = false;
    }

    public function test_keys_ubicacion(): void
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

        $resultado = $_pdf->keys_ubicacion();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(15.5,$resultado['dp_calle_ubicacion_descripcion']['x']);
        $this->assertEquals(164,$resultado['dp_calle_ubicacion_descripcion']['y']);
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

    public function test_x_y_compare(): void
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



        $condiciones= array();
        $key = 'a';
        $row = array();
        $x_init = '-1';
        $y_init = '-1';

        $row['a'] = 'd';

        $resultado = $_pdf->x_y_compare($condiciones, $key, $row, $x_init, $y_init);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

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

    public function test_write_condicion(): void
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

        $key = 'a';
        $row = array();
        $row['a'] = '1';
        $value_compare = '';
        $x = 1;
        $y = .01;


        $resultado = $_pdf->write_condicion($key, $row, $value_compare, $x, $y);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_write_data(): void
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


        $keys= array();
        $row = array();

        $keys['a'] = array('x'=>.01,'y'=>99);
        $resultado = $_pdf->write_data($keys, $row);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado[0]));
        errores::$error = false;
    }

    public function test_write_x(): void
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


        $name_entidad = 'a';
        $row = array();
        $row['a_x'] = 0.1;
        $row['a_y'] = 5;
        $resultado = $_pdf->write_x($name_entidad, $row);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("setasign\Fpdi\Fpdi",get_class($resultado));
        errores::$error = false;

    }


}

