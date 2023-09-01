<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class controlador_inm_compradorTest extends test {
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

    public function test_alta(): void
    {
        errores::$error = false;


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_comprador&accion=alta&adm_menu_id=64&session_id=5850109279&adm_menu_id=64");
        $fp = fopen("inm_comprador.alta", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("inm_comprador.alta");

        //print_r($data);exit;

        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'><label class='control-label' for='inm_producto_infonavit_id'>Producto</label><div", $data);
        assertStringContainsStringIgnoringCase("for='Es Segundo Credito'>Es Segundo Credito</label>", $data);
        assertStringContainsStringIgnoringCase('<label class="form-check-label chk', $data);
        assertStringContainsStringIgnoringCase("<input type='radio' name='es_segundo_credito' value='SI' class='form-check-input  es_segundo_credito  form-check-input' id=' es_segundo_credito ' title='Es Segundo Credito' >", $data);
        assertStringContainsStringIgnoringCase("id=' es_segundo_credito '", $data);
        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'><label class='control-label' for='inm_plazo_credito_sc_id'>Plazo Segundo Credito</label><div class='controls'><select class='form-c", $data);
        assertStringContainsStringIgnoringCase("<h4>2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO</h4>", $data);
        assertStringContainsStringIgnoringCase("ss='control-label' for='descuento_pension_alimenticia_dh'>Descuento Pension Alimenticia Derechohabiente</label><di", $data);
        assertStringContainsStringIgnoringCase("untario' value='0' class='form-control' required id='monto_ahorro_voluntario", $data);
        assertStringContainsStringIgnoringCase("<input type='radio' name='con_discapacidad", $data);
        assertStringContainsStringIgnoringCase("l-group col-sm-6'><label class='control-label' for='inm_tipo_discapacidad_id'>Tipo de Discapacidad</label><div class='controls'><select class=", $data);
        assertStringContainsStringIgnoringCase("abel class='control-label' for='nombre_empresa_patron'>Nombre Empresa o Patron</label><div class='controls'><input type='text' name='n", $data);
        assertStringContainsStringIgnoringCase("_civil_id' data-live-search='true' id='inm_estado_civil_id' name='inm_estado_civil_id' required ><option value=''  >Selecciona una", $data);
        assertStringContainsStringIgnoringCase("m-6'><label class='control-label' for='cat_sat_regimen_fiscal_id'>Regimen Fiscal</label><div class='controls'><select class='form-control se", $data);
        assertStringContainsStringIgnoringCase("e Cliente</label><div class='controls'><select class='form-control selectpick", $data);
        unlink('inm_comprador.alta');


    }


    public function test_init_datatable(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ctl = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->init_datatable();
        //print_r($resultado);exit;

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Id",$resultado->columns['inm_comprador_id']['titulo']);
        $this->assertEquals("Nombre",$resultado->columns['inm_comprador_nombre']['titulo']);

        errores::$error = false;
    }








}

