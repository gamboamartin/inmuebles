<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_dps_init;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _dps_initTest extends test {
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



    public function test_dps_init_ids(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $dps = new _dps_init();
        $dps = new liberator($dps);

        $row_upd = new stdClass();
        $resultado = $dps->dps_init_ids($row_upd);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(151,$resultado->dp_pais_id);
        $this->assertEquals(14,$resultado->dp_estado_id);
        $this->assertEquals(-1,$resultado->dp_municipio_id);
        $this->assertEquals(-1,$resultado->dp_cp_id);
        $this->assertEquals(-1,$resultado->dp_colonia_postal_id);
        $this->assertEquals(-1,$resultado->dp_calle_pertenece_id);
        errores::$error = false;


    }




}

