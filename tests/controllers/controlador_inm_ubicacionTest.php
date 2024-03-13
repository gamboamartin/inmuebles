<?php
namespace controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class controlador_inm_ubicacionTest extends test {
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
    public function test_alta(): void
    {
        errores::$error = false;

        $file = "inm_ubicacion.alta";


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=alta&adm_menu_id=64&session_id=4075502287&adm_menu_id=64");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        $this->assertStringContainsStringIgnoringCase('<form method="post" action="./index.php?seccion=inm_ubicacion&accion=alta_bd&adm_menu_id=64&se', $data);
        $this->assertStringContainsStringIgnoringCase('session_id=4075502287&adm_menu_id=64" class="form-additional"', $data);
        //print_r($data);exit;
        $this->assertStringContainsStringIgnoringCase('enctype="multipart/form-data" id="form_inm_ubicacion_alta">', $data);
        $this->assertStringContainsStringIgnoringCase("<div class='control-group col-sm-12'><label class='control-label' for='inm_tipo_ubicacion_id'>", $data);
        $this->assertStringContainsStringIgnoringCase("Tipo de Ubicacion</label><div class='controls'><select class='form-control selectpicker color-secondary", $data);
        $this->assertStringContainsStringIgnoringCase("<select class='form-control selectpicker color-secondary inm_tipo_ubicacion_id '", $data);
        $this->assertStringContainsStringIgnoringCase("data-live-search='true' id='inm_tipo_ubicacion_id' name='inm_tipo_ubicacion_id'", $data);
        $this->assertStringContainsStringIgnoringCase("required ><option value=''  >Selecciona una opcion", $data);
        $this->assertStringContainsStringIgnoringCase("name='inm_tipo_ubicacion_id' required ><option value=''  >", $data);
        $this->assertStringContainsStringIgnoringCase("<select class='form-control selectpicker color-secondary dp_estado_id '", $data);
        $this->assertStringContainsStringIgnoringCase("data-live-search='true' id='dp_estado_id' name='dp_estado_id' required >", $data);
        $this->assertStringContainsStringIgnoringCase("s='controls'><input type='text' name='lote' value='' class='form-control' id='lote' placeholder='Lote' tit", $data);
        $this->assertStringContainsStringIgnoringCase("id='lote'", $data);
        $this->assertStringContainsStringIgnoringCase("id='cuenta_predial'", $data);




        unlink($file);


    }

    public function test_asigna_comprador(): void
    {
        errores::$error = false;


        $file = "inm_ubicacion.asigna_comprador";
        $session_id = '1590259697';


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

    public function test_asigna_costo(): void
    {
        errores::$error = false;

        $file = "inm_ubicacion.asigna_costo";
        $session_id = '1590259697';


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=asigna_costo&adm_menu_id=64&session_id=$session_id&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);
        //print_r($data);exit;
        $this->assertStringContainsStringIgnoringCase("for='inm_concepto_id'>Concepto</label>", $data);
        $this->assertStringContainsStringIgnoringCase("id='inm_concepto_id' name='inm_concepto_id' required ><option value=''  >", $data);
        $this->assertStringContainsStringIgnoringCase("<th>Tipo Concepto</th>", $data);
        $this->assertStringContainsStringIgnoringCase("<th>Fecha</th>", $data);

        unlink($file);
    }

    public function test_detalle_costo(): void
    {
        $_SESSION['usuario_id'] = 2;
        errores::$error = false;

        $del = (new base_test())->del_inm_tipo_concepto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);exit;
        }

        $file = "inm_ubicacion.detalle_costo";
        $session_id = '1590259697';


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=detalle_costo&adm_menu_id=64&session_id=$session_id&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);
        unlink($file);
        //print_r($data);exit;
        $this->assertStringContainsStringIgnoringCase("<tbody>
                </tbody>", $data);

        $alta = (new base_test())->alta_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);exit;
        }


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_ubicacion&accion=detalle_costo&adm_menu_id=64&session_id=$session_id&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        $this->assertStringContainsStringIgnoringCase("<td>1</td>
            <td>TIPO CONCEPTO 1</td>
            <td>CONCEPTO 1</td>
            <td>$1,000.00</td>
            <td>2020-01-01</td>
            <td>REF 1</td>
            <td>Descripcion 1</td>
            <td>", $data);


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

