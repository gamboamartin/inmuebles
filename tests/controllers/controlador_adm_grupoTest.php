<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class controlador_adm_grupoTest extends test {
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



    public function test_modifica(): void
    {
        errores::$error = false;


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=adm_grupo&accion=modifica&adm_menu_id=64&session_id=4075502287&adm_menu_id=64&registro_id=2");
        $fp = fopen("adm_grupo.modifica", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("adm_grupo.modifica");
        //print_r($data);exit;

        assertStringContainsStringIgnoringCase('<form method="post" action="./index.php?seccion=adm_grupo&accion=modifica_bd&registro_id=2&adm_menu_id=64&session_id=4075502287', $data);
        assertStringContainsStringIgnoringCase('&session_id=4075502287&adm_menu_id=64" class="form-additional" enctype="multipart/form-data">', $data);
        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'><label class='control-label' for='codigo'>Cod</label><div class='controls'>", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='codigo' value='", $data);
        assertStringContainsStringIgnoringCase("disabled required id='codigo' placeholder='Cod' title='Cod' /></div></div><div class='control-group", $data);
        assertStringContainsStringIgnoringCase("col-sm-6'><label class='control-label' for='descripcion'>Grupo</label><div class='controls'>", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='descripcion' value='Administrador Sistema'", $data);
        assertStringContainsStringIgnoringCase("class='form-control' required id='descripcion' placeholder='Grupo' title='Grupo' /></div></div>", $data);
        unlink('adm_grupo.modifica');


    }


}

