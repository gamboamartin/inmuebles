<?php
namespace controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class controlador_inm_ubicacionTest extends test {
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

        $file = "inm_ubicacion.alta";


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=alta&adm_menu_id=64&session_id=2833161769&adm_menu_id=64");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        $this->assertStringContainsStringIgnoringCase('<form method="post" action="./index.php?seccion=inm_ubicacion&accion=alta_bd&adm_menu_id=64&se', $data);
        $this->assertStringContainsStringIgnoringCase('session_id=2833161769&adm_menu_id=64" class="form-additional"', $data);
        $this->assertStringContainsStringIgnoringCase('enctype="multipart/form-data">', $data);
        $this->assertStringContainsStringIgnoringCase("<div class='control-group col-sm-12'><label class='control-label' for='inm_tipo_ubicacion_id'>", $data);
        $this->assertStringContainsStringIgnoringCase("Tipo de Ubicacion</label><div class='controls'><select class='form-control selectpicker color-secondary", $data);
        $this->assertStringContainsStringIgnoringCase("  inm_tipo_ubicacion_id' data-live-search='true' id='inm_tipo_ubicacion_id'", $data);
        $this->assertStringContainsStringIgnoringCase("name='inm_tipo_ubicacion_id' required ><option value=''  >", $data);
        $this->assertStringContainsStringIgnoringCase("dp_estado_id' data-live-search='true' id='dp_estado_id'", $data);
        $this->assertStringContainsStringIgnoringCase("s='controls'><input type='text' name='lote' value='' class='form-control' id='lote' placeholder='Lote' tit", $data);
        $this->assertStringContainsStringIgnoringCase("id='lote'", $data);
        $this->assertStringContainsStringIgnoringCase("id='cuenta_predial'", $data);




        unlink($file);


    }

    public function test_asigna_comprador(): void
    {
        errores::$error = false;

        $file = "inm_ubicacion.asigna_comprador";
        $session_id = '5983857742';


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=asigna_comprador&adm_menu_id=64&session_id=$session_id&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);
        $this->assertStringContainsStringIgnoringCase('<form method="post" action="./index.php?seccion=inm_rel_ubi_comp&accion=alta_bd&adm_menu_id=64&session_id='.$session_id.'&adm_menu_id=64"', $data);
        $this->assertStringContainsStringIgnoringCase("Comprador de vivienda</label><div class='controls'><selec", $data);
        $this->assertStringContainsStringIgnoringCase(">Chihuahua</option><option value='7'  >Coahuila</option><option value='8' ", $data);

        unlink($file);
    }
    

    public function test_init_datatable(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ctl = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->init_datatable();

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Tipo de Ubicacion",$resultado->columns['inm_tipo_ubicacion_descripcion']['titulo']);
        errores::$error = false;
    }


}

