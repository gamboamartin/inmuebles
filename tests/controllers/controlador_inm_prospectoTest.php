<?php
namespace controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class controlador_inm_prospectoTest extends test {
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

        $file = "inm_prospecto.alta";

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_prospecto&accion=alta&adm_menu_id=64&session_id=1590259697&adm_menu_id=64");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        //print_r($data);exit;


        assertStringContainsStringIgnoringCase("data-live-search='true' id='com_agente_id' name='com_agente_id' required >", $data);
        assertStringContainsStringIgnoringCase(" com_tipo_prospecto_id' data-live-search='true' id='com_tipo_prospecto_id' name='com_tipo_prospecto_id' required >", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='nombre' value='' class='form-control' required id='nombre' placeholder='Nombre' ", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_paterno'>Apellido Paterno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_materno'>Apellido Materno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("' required id='lada_com' placeholder='Lada' pattern='[0-9]{2,3}' title='Lada' ", $data);
        assertStringContainsStringIgnoringCase(" class='form-control' required id='numero_com' placeholder='Numero' pattern='[0-9]{7,8}' title='Numero' />", $data);
        assertStringContainsStringIgnoringCase("class='form-control' required id='cel_com' placeholder='Cel' pattern='[1-9]{1}[0-9]{9}' title='Cel'", $data);
        assertStringContainsStringIgnoringCase("_com' value='' class='form-control' id='correo_com' placeholder='Correo' pattern='[a-z0-9!#$%&'*+", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='razon_social' value='' class='form", $data);
        assertStringContainsStringIgnoringCase("placeholder='Observaciones' title='Observaciones' /></div>", $data);

        unlink($file);
        errores::$error = false;

    }
    public function test_campos_view(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ctl = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->campos_view();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }


}

