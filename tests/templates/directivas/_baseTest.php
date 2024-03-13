<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\html\_base;
use gamboamartin\inmuebles\html\inm_co_acreditado_html;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\template\html;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;



class _baseTest extends test {
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

    public function test_header_frontend(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new _base($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $controler->inputs = new stdClass();
        $n_apartado = '1';
        $tag_header = '';
        $resultado = $html->header_frontend($controler, $n_apartado, $tag_header);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<div class='col-md-12'><hr><h4> <a class='btn btn-primary' role='button' id='collapse_a1'>Ver/Ocultar</a> </h4><hr></div>",$resultado->apartado_1);
        errores::$error = false;
    }

    public function test_inputs_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new _base($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $controler->inputs = new stdClass();

        $resultado = $html->inputs_alta(controler: $controler);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("inm_producto_infonavit_descripcion",$resultado->keys_selects['inm_producto_infonavit_id']->columns_ds[0]);
        errores::$error = false;
    }



}

