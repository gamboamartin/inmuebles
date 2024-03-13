<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_tipo_beneficiario;
use gamboamartin\test\test;


use stdClass;


class inm_tipo_beneficiarioTest extends test {
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

    public function test_registros(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_tipo_beneficiario(link: $this->link);
        //$inm = new liberator($inm);


        $resultado = $inm->registros();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado[0]['inm_tipo_beneficiario_id']);
        $this->assertEquals("ACREDITADO",$resultado[0]['inm_tipo_beneficiario_descripcion']);
        $this->assertEquals(2,$resultado[1]['inm_tipo_beneficiario_id']);
        $this->assertEquals("CO ACREDITADO",$resultado[1]['inm_tipo_beneficiario_descripcion']);
        $this->assertCount(2, $resultado);

        errores::$error = false;
    }




}

