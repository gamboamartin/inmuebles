<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class controlador_inm_attr_tipo_infonavitTest extends test {
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


        $ctl = new controlador_inm_attr_tipo_credito(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->init_datatable();
       // print_r($resultado);exit;

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Id",$resultado->columns['inm_attr_tipo_credito_id']['titulo']);
        $this->assertEquals("Descripcion",$resultado->columns['inm_attr_tipo_credito_descripcion']['titulo']);
        $this->assertEquals("X",$resultado->columns['inm_attr_tipo_credito_x']['titulo']);
        $this->assertEquals("Y",$resultado->columns['inm_attr_tipo_credito_y']['titulo']);
        $this->assertEquals("Tipo de Credito",$resultado->columns['inm_tipo_credito_descripcion']['titulo']);
        $this->assertEquals("inm_attr_tipo_credito.id",$resultado->filtro[0]);
        $this->assertEquals("inm_attr_tipo_credito.descripcion",$resultado->filtro[1]);
        $this->assertEquals("inm_attr_tipo_credito.x",$resultado->filtro[2]);
        $this->assertEquals("inm_tipo_credito.descripcion",$resultado->filtro[3]);
        errores::$error = false;
    }


}

