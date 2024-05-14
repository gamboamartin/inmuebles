<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class controlador_inm_compradorTest extends test {
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


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_comprador&accion=alta&adm_menu_id=64&session_id=1590259697&adm_menu_id=64");
        $fp = fopen("inm_comprador.alta", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("inm_comprador.alta");

       // print_r($data);exit;

        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'><label class='control-label' for='inm_producto_infonavit_id'>Producto</label><div", $data);
        assertStringContainsStringIgnoringCase("Es Segundo Credito", $data);
        assertStringContainsStringIgnoringCase("<label class='form-check-label chk form-check-label'>", $data);
        assertStringContainsStringIgnoringCase('<label class="form-check-label chk', $data);
        assertStringContainsStringIgnoringCase("<input type='radio' name='es_segundo_credito' value='SI' class='form-check-input es_segundo_credito form-check-input' id='es_segundo_credito'", $data);
        assertStringContainsStringIgnoringCase("SI", $data);
        assertStringContainsStringIgnoringCase("title='Es Segundo Credito'", $data);
        assertStringContainsStringIgnoringCase(" form-check-input' id='es_segundo_credito'", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='inm_plazo_credito_sc_id'>Plazo Segundo Credito</label>", $data);
        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-6'><label class='control-label'", $data);
        assertStringContainsStringIgnoringCase("for='inm_plazo_credito_sc_id'>", $data);
        assertStringContainsStringIgnoringCase("<hr><h4>2. DATOS PARA DETERMINAR EL MONTO DE CRÉDITO <a class='btn btn-primary' role='button' id='collapse_a2'>Ver/Ocultar</a> </h4><hr", $data);
        assertStringContainsStringIgnoringCase('<div id="apartado_2">', $data);
        assertStringContainsStringIgnoringCase("ss='control-label' for='descuento_pension_alimenticia_dh'>Descuento Pension Alimenticia Derechohabiente</label><di", $data);
        assertStringContainsStringIgnoringCase("untario' value='0' class='form-control' required id='monto_ahorro_voluntario", $data);
        assertStringContainsStringIgnoringCase("<input type='radio' name='con_discapacidad", $data);
        assertStringContainsStringIgnoringCase("l-group col-sm-6'><label class='control-label' for='inm_tipo_discapacidad_id'>Tipo de Discapacidad</label><div class='controls'><select class=", $data);
        assertStringContainsStringIgnoringCase("<div class='control-group col-sm-12'><label class='control-label'", $data);
        assertStringContainsStringIgnoringCase("<label class='control-label' for='inm_estado_civil_id'>Estado Civil</label><div class='controls'>", $data);
        assertStringContainsStringIgnoringCase("<select class='form-control selectpicker color-secondary inm_estado_civil_id ' data-live-search='true'", $data);
        assertStringContainsStringIgnoringCase("m-6'><label class='control-label' for='cat_sat_regimen_fiscal_id'>Regimen Fiscal</label><div class='controls'><select class='form-control se", $data);
        assertStringContainsStringIgnoringCase("e Cliente</label><div class='controls'><select class='form-control selectpick", $data);
        assertStringContainsStringIgnoringCase("<h4>", $data);
        assertStringContainsStringIgnoringCase("DATOS PARA DETERMINAR EL MONTO DE CRÉDITO", $data);
        assertStringContainsStringIgnoringCase("<hr><h4>5. DATOS DE IDENTIFICACIÓN DEL (DE LA) DERECHOHABIENTE / DATOS QUE SERÁN VALIDADOS <a class='btn btn-primary' role='button' ", $data);
        assertStringContainsStringIgnoringCase("for='nombre_empresa_patron'>Nombre de la Empresa/Patrón", $data);
        assertStringContainsStringIgnoringCase("iv class='controls'><input type='text' name='nombre_empresa_patron' value='' ", $data);
        assertStringContainsStringIgnoringCase("<hr><h4>3. DATOS DE LA VIVIENDA/TERRENO DESTINO DEL CRÉDITO <a class='btn btn-primary' role='button' id='collapse_a3'>Ver/Ocultar</a> </h4><hr>", $data);
        assertStringContainsStringIgnoringCase("abel class='control-label' for='nrp_nep'>NÚMERO DE REGISTRO PATRONAL (NRP)", $data);
        assertStringContainsStringIgnoringCase("<label class='form-check-label chk form-check-label'>
                <input type='radio' name='es_segundo_credito' value='NO' class='form-check-input es_segundo_credito form-check-input' id='es_segundo_credito' 
                title='Es Segundo Credito' checked>
                NO
            </label>", $data);



        unlink('inm_comprador.alta');


    }

    public function test_asigna_co_acreditado(): void
    {
        errores::$error = false;

        $_SESSION['usuario_id'] = 2;

        $del = (new base_test())->del_org_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }


        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);;
            exit;
        }

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_comprador&accion=asigna_co_acreditado&adm_menu_id=64&session_id=1590259697&adm_menu_id=64&registro_id=1");
        $fp = fopen("inm_comprador.asigna_co_acreditado", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("inm_comprador.asigna_co_acreditado");

        assertStringContainsStringIgnoringCase('<body class="">', $data);
        assertStringContainsStringIgnoringCase('<div id="fb-root"></div>', $data);
        assertStringContainsStringIgnoringCase('<div class="container container-wrapper">', $data);
        assertStringContainsStringIgnoringCase('<section class="header-inner">', $data);
        assertStringContainsStringIgnoringCase('<span class="sr-only">Toggle navigation</span>', $data);
        assertStringContainsStringIgnoringCase('<button class="navbar-toggler hidden" type="button" data-toggle="collapse" data-target="#main-menu">', $data);
        assertStringContainsStringIgnoringCase('<ul class="nav navbar-nav clearfix">', $data);
        assertStringContainsStringIgnoringCase('<main class="main section-color-primary">', $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='registro_id' value='1'>", $data);
        assertStringContainsStringIgnoringCase('<div class="row">', $data);
        assertStringContainsStringIgnoringCase("for='inm_co_acreditado_id'>Co Acreditado</label><div class='controls'><select class='form-control selectpicker", $data);
        assertStringContainsStringIgnoringCase("color-secondary inm_co_acreditado_id ' data-live-search='true' id='inm_co_acreditado_id' name='inm_co_acreditado_id' required >", $data);
        assertStringContainsStringIgnoringCase("Selecciona una opcion</option><option value='1'  >12345678912 XEXX010101MNEXXXA8 NOMBRE AP APELLIDO MATERNO</option></select></div></div>", $data);


        errores::$error = false;

        unlink("inm_comprador.asigna_co_acreditado");
    }

    public function test_asigna_ubicacion(): void
    {
        errores::$error = false;

        $_SESSION['usuario_id'] = 2;

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar',data:  $alta);
            print_r($error);;
            exit;
        }

        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_comprador&accion=asigna_ubicacion&adm_menu_id=64&session_id=1590259697&adm_menu_id=64&registro_id=1");
        $fp = fopen("inm_comprador.asigna_ubicacion", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("inm_comprador.asigna_ubicacion");

        assertStringContainsStringIgnoringCase("<label class='control-label' for='inm_ubicacion_id'>", $data);
        assertStringContainsStringIgnoringCase("<input type='text' name='precio_operacion' value='' class='form-control'", $data);
        assertStringContainsStringIgnoringCase(" for='com_tipo_cliente_id'>Tipo de Cliente</label><div class='controls'><select class='form-control selectpicker", $data);
        assertStringContainsStringIgnoringCase(" color-secondary com_tipo_cliente_id ' data-live-search='true' id='com_tipo_cliente_id'", $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='inm_comprador_id' value='1'>", $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='inm_comprador_id' value='1'>", $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='seccion_retorno' value='inm_comprador'>", $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='btn_action_next' value='asigna_ubicacion'>", $data);
        assertStringContainsStringIgnoringCase("<input type='hidden' name='id_retorno' value='1'>", $data);

        unlink('inm_comprador.asigna_ubicacion');
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


        $ctl = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $ctl = new liberator($ctl);

        $resultado = $ctl->campos_view();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
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

    public function test_modifica(): void
    {
        errores::$error = false;


        $ch = curl_init("http://localhost/inmuebles/index.php?seccion=inm_comprador&accion=modifica&adm_menu_id=64&session_id=1590259697&adm_menu_id=64&registro_id=1");
        $fp = fopen("inm_comprador.modifica", "w");

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $data = file_get_contents("inm_comprador.modifica");
       // print_r($data);exit;

        $this->assertStringContainsStringIgnoringCase("for='inm_producto_infonavit_id'>Producto</label><div class='controls'><selec",$data);
        $this->assertStringContainsStringIgnoringCase("e' id='inm_attr_tipo_credito_id' name='inm_attr_tipo_credito_id' required ><option value=''  >Sele",$data);
        $this->assertStringContainsStringIgnoringCase("PRAR UNA VIVIENDA</option><option value='2'  >COMPRAR TERRENO</option><option value='3'  >CONSTRUIR VIVIENDA</op",$data);
        $this->assertStringContainsStringIgnoringCase("control-label' for='lada_nep'>Lada</label><div class='controls'><input type='text' name='lada_nep' ",$data);

        errores::$error = false;

        unlink("inm_comprador.modifica");
    }



}

