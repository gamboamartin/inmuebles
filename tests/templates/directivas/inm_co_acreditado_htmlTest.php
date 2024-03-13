<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
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
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_co_acreditado_htmlTest extends test {
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

    public function test_genera_inputs(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $params = new stdClass();
        $params->campos = array();
        $params->campos[] = 'nss';
        $params->cols = array();
        $params->disableds = array();
        $params->names = array();

        $resultado = $html->genera_inputs('',$params);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='nss'>NSS</label><div class='controls'><input type='text' name='nss' value='' class='form-control nss' required id='nss' placeholder='NSS' pattern='(\d{2})(\d{2})(\d{2})\d{5}' title='NSS' /></div></div>",$resultado->nss);
        errores::$error = false;
    }

    public function test_init_campo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campo = 'a';
        $data = array();
        $resultado = $html->init_campo($campo, $data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a',$resultado['a']);

        errores::$error = false;
        $campo = 'b';
        $data = array('b'=>'x');
        $resultado = $html->init_campo($campo, $data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('x',$resultado['b']);
        errores::$error = false;
    }

    public function test_init_campos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campos[] = 'a';
        $datas = array();
        $resultado = $html->init_campos($campos, $datas);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a',$resultado['a']);
        errores::$error = false;
    }

    public function test_init_cols(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $cols_css = array();
        $resultado = $html->init_cols($cols_css);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6, $resultado['apellido_materno']);
        $this->assertEquals(6, $resultado['apellido_paterno']);
        $this->assertEquals(6, $resultado['celular']);
        $this->assertEquals(6, $resultado['curp']);
        $this->assertEquals(6, $resultado['lada']);
        $this->assertEquals(6, $resultado['lada_nep']);
        $this->assertEquals(6, $resultado['nombre']);
        $this->assertEquals(6, $resultado['nss']);
        $this->assertEquals(6, $resultado['numero']);
        $this->assertEquals(6, $resultado['numero_nep']);
        $this->assertEquals(6, $resultado['rfc']);
        $this->assertEquals(12, $resultado['correo']);
        $this->assertEquals(4, $resultado['extension_nep']);
        $this->assertEquals(12, $resultado['nombre_empresa_patron']);
        $this->assertEquals(12, $resultado['nrp']);
        errores::$error = false;
    }

    public function test_init_param(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campo = 'a';
        $params = new stdClass();

        $resultado = $html->init_param($campo, $params);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(12,$resultado->cols['a']);
        $this->assertFalse($resultado->disableds['a']);
        $this->assertEquals('a',$resultado->names['a']);
        errores::$error = false;
    }

    public function test_init_params(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campos = array();
        $cols_css = array();
        $disableds = array();
        $names = array();
        $resultado = $html->init_params($campos, $cols_css, $disableds, $names);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6,$resultado->cols['lada']);
        errores::$error = false;
    }

    public function test_inputs(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        //$html = new liberator($html);


        $entidad = '';

        $resultado = $html->inputs($entidad);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<div class='control-group col-sm-6'><label class='control-label' for='curp'>CURP</label><div class='controls'><input type='text' name='curp' value='' class='inm_co_acreditado_curp form-control curp' disabled required id='curp' placeholder='CURP' pattern='([A-Z][AEIOUX][A-Z]{2}\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])[HM](?:AS|B[CS]|C[CLMSH]|D[FG]|G[TR]|HG|JC|M[CNS]|N[ETL]|OC|PL|Q[TR]|S[PLR]|T[CSL]|VZ|YN|ZS)[B-DF-HJ-NP-TV-Z]{3}[A-Z\d])(\d)' title='CURP' /></div></div>",$resultado->curp);
        errores::$error = false;
    }

    public function test_integra_input(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campo = 'apellido_paterno';
        $params = new stdClass();
        $inputs = new stdClass();
        $row_upd = new stdClass();

        $resultado = $html->integra_input($campo,'inm_co_acreditado', $inputs, $params, $row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='apellido_paterno'>Apellido Paterno</label><div class='controls'><input type='text' name='apellido_paterno' value='' class='inm_co_acreditado_apellido_paterno form-control apellido_paterno' required id='apellido_paterno' placeholder='Apellido Paterno' title='Apellido Paterno' /></div></div>",$resultado->apellido_paterno);
        errores::$error = false;
    }

    public function test_params_inputs(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $cols_css = array();
        $disableds = array();
        $names = array();
        $integra_prefijo = false;
        $resultado = $html->params_inputs($cols_css, $disableds, $integra_prefijo, $names);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6,$resultado->cols['curp']);
        errores::$error = false;
    }

    public function test_select_inm_co_acreditado_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        //$_inm = new liberator($_inm);

        $del = (new base_test())->del_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar',data:  $alta);
            print_r($error);
            exit;
        }

        $cols = 2;
        $con_registros = true;
        $id_selected = -1;
        $link = $this->link;
        $resultado = $html->select_inm_co_acreditado_id($cols, $con_registros, $id_selected, $link);


        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("<div class='control-group col-sm-2'><label class='control-label' for='inm_co_acreditado_id'>Co Acreditado</label><div class='controls'><select class='form-control selectpicker color-secondary inm_co_acreditado_id ' data-live-search='true' id='inm_co_acreditado_id' name='inm_co_acreditado_id' required ><option value=''  >Selecciona una opcion</option><option value='1'  >NOMBRE AP APELLIDO MATERNO 12345678912 XEXX010101MNEXXXA8 AAA010101AAA",$resultado);

        errores::$error = false;
    }

    public function test_valida_campo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $campo = 'numero';


        $resultado = $html->valida_campo($campo);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_params(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_co_acreditado_html($html_);
        $html = new liberator($html);


        $params = new stdClass();
        $params->campos = array();
        $params->cols = array();
        $params->disableds = array();
        $params->names = array();

        $resultado = $html->valida_params($params);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }


}

