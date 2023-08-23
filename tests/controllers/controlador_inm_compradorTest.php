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

    public function test_init_row_upd_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ctl = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $row_upd = new stdClass();
        $resultado = $ctl->init_row_upd_infonavit($row_upd);
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

    public function test_ks_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ctl = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ctl->ks_infonavit($keys_selects, $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Producto',$resultado['inm_producto_infonavit_id']->label);
        errores::$error = false;
    }




}

