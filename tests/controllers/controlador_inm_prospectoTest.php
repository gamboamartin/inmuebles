<?php
namespace controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_rel_prospecto_cliente;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class controlador_inm_prospectoTest extends test {
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
        assertStringContainsStringIgnoringCase("<label class='control-label' for='com_tipo_prospecto_id'>Tipo de prospecto<", $data);
        assertStringContainsStringIgnoringCase("/label><div class='controls'><select class='form-control selectpicker", $data);
        assertStringContainsStringIgnoringCase("color-secondary com_tipo_prospecto_id ' data-live-search='true' id='com_tipo_prospecto_id' name='com_tipo_prospecto_id' required >", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='nombre' value='' class='form-control' required id='nombre' placeholder='Nombre' ", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_paterno'>Apellido Paterno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_materno'>Apellido Materno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("' id='lada_com' placeholder='Lada' pattern='[0-9]{2,3}' title='Lada' ", $data);
        assertStringContainsStringIgnoringCase(" class='form-control' id='numero_com' placeholder='Numero' pattern='[0-9]{7,8}' title='Numero' />", $data);
        assertStringContainsStringIgnoringCase("class='form-control' id='cel_com' placeholder='Cel' pattern='[1-9]{1}[0-9]{9}' title='Cel'", $data);
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

    public function test_convierte_cliente(): void
    {
        errores::$error = false;

        $_SESSION['usuario_id'] = 2;

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $file = "inm_prospecto.convierte_cliente";

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_prospecto&accion=convierte_cliente&adm_menu_id=64&session_id=9633405615&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);
        //print_r($data);exit;
        assertStringContainsStringIgnoringCase("Error no existe registro de inm_prospecto", $data);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);;
            exit;
        }

        $upd_data['cel_com'] = '';

        $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $upd_data,id:  1);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al upd',data:  $upd);
            print_r($error);;
            exit;
        }

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_prospecto&accion=convierte_cliente&adm_menu_id=64&session_id=9633405615&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        assertStringContainsStringIgnoringCase("Error el campo cel_com no puede venir vacio", $data);

        errores::$error = false;

        $inm_prospecto_upd['cel_com'] = '1234567891';
        $inm_prospecto_upd['correo_com'] = '';
        $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al upd',data:  $upd);
            print_r($error);;
            exit;
        }


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_prospecto&accion=convierte_cliente&adm_menu_id=64&session_id=9633405615&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);
        assertStringContainsStringIgnoringCase("Error el campo correo_com no puede venir vacio", $data);

        errores::$error = false;

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_rel_prospecto_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $inm_prospecto_upd['correo_com'] = 'a@test.com';
        $upd = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al upd',data:  $upd);
            print_r($error);;
            exit;
        }

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_prospecto&accion=convierte_cliente&adm_menu_id=64&session_id=9633405615&adm_menu_id=64&registro_id=1");
        $fp = fopen($file, "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents($file);

        //print_r($data);exit;
        $this->assertEmpty($data);

        $inm_rel_prospecto_cliente = (new inm_rel_prospecto_cliente(link: $this->link))->filtro_and(filtro: array('inm_prospecto.id'=>1));
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al obtener inm_rel_prospecto_cliente',data:  $inm_rel_prospecto_cliente);
            print_r($error);;
            exit;
        }

       // PRINT_R($inm_rel_prospecto_cliente);EXIT;

        $this->assertEquals(1, $inm_rel_prospecto_cliente->registros[0]['inm_prospecto_id']);


        errores::$error = false;

        unlink($file);


    }

    public function test_envia_documentos(): void
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
        assertStringContainsStringIgnoringCase("<label class='control-label' for='com_tipo_prospecto_id'>Tipo de prospecto<", $data);
        assertStringContainsStringIgnoringCase("/label><div class='controls'><select class='form-control selectpicker", $data);
        assertStringContainsStringIgnoringCase("color-secondary com_tipo_prospecto_id ' data-live-search='true' id='com_tipo_prospecto_id' name='com_tipo_prospecto_id' required >", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='nombre' value='' class='form-control' required id='nombre' placeholder='Nombre' ", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_paterno'>Apellido Paterno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='apellido_materno'>Apellido Materno</label><div class='controls'><input type=", $data);
        assertStringContainsStringIgnoringCase("' id='lada_com' placeholder='Lada' pattern='[0-9]{2,3}' title='Lada' ", $data);
        assertStringContainsStringIgnoringCase(" class='form-control' id='numero_com' placeholder='Numero' pattern='[0-9]{7,8}' title='Numero' />", $data);
        assertStringContainsStringIgnoringCase("class='form-control' id='cel_com' placeholder='Cel' pattern='[1-9]{1}[0-9]{9}' title='Cel'", $data);
        assertStringContainsStringIgnoringCase("_com' value='' class='form-control' id='correo_com' placeholder='Correo' pattern='[a-z0-9!#$%&'*+", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='razon_social' value='' class='form", $data);
        assertStringContainsStringIgnoringCase("placeholder='Observaciones' title='Observaciones' /></div>", $data);

        unlink($file);
        errores::$error = false;

    }


}

