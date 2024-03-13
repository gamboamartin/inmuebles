<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\js_base\eventos\adm_seccion;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _keys_selectsTest extends test {
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

    public function test_ajusta_row_data_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_org_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();
        $controler->registro['dp_pais_id'] = 1;
        $controler->registro['dp_estado_id'] = 1;
        $controler->registro['dp_municipio_id'] = 1;
        $controler->registro['dp_cp_id'] = 1;
        $controler->registro['dp_calle_id'] = 1;
        $controler->registro['dp_colonia_id'] = 1;
        $controler->registro['dp_colonia_postal_id'] = 1;
        $controler->registro['dp_calle_pertenece_id'] = 1;

        $resultado = $ks->ajusta_row_data_cliente($controler);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener com_cliente",$resultado['mensaje_limpio']);
        errores::$error = false;

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $ks->ajusta_row_data_cliente($controler);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("AAA010101AAA",$resultado->rfc);
        $this->assertEquals("1",$resultado->numero_exterior);
        $this->assertEquals("1",$resultado->dp_municipio_id);
        $this->assertEquals("1",$resultado->com_tipo_cliente_id);
        errores::$error = false;
    }

    public function test_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->base($controler, $keys_selects, $row_upd);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Tipo de Cliente",$resultado['com_tipo_cliente_id']->label);
        errores::$error = false;
    }

    public function test_base_plantilla(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar', data: $alta);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar', data: $alta);
            print_r($error);
            exit;
        }



        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();
        $controler->inputs = new stdClass();
        $controler->registro = array();
        $controler->registro['dp_pais_id'] = 1;
        $controler->registro['dp_estado_id'] = 1;
        $controler->registro['dp_municipio_id'] = 1;
        $controler->registro['dp_cp_id'] = 1;
        $controler->registro['dp_calle_id'] = 1;
        $controler->registro['dp_colonia_id'] = 1;
        $controler->registro['dp_colonia_postal_id'] = 1;
        $controler->registro['dp_calle_pertenece_id'] = 1;

        $function = 'd';

        $resultado = $ks->base_plantilla($controler, $function);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<input type='hidden' name='id_retorno' value='1'>",$resultado->id_retorno);
        $this->assertEquals("<input type='hidden' name='btn_action_next' value='d'>",$resultado->btn_action_next);
        $this->assertEquals("<input type='hidden' name='seccion_retorno' value='inm_producto_infonavit'>",$resultado->seccion_retorno);
        $this->assertEquals("<input type='hidden' name='registro_id' value='1'>",$resultado->registro_id);
        $this->assertEquals("<input type='hidden' name='inm_comprador_id' value='1'>",$resultado->inm_comprador_id);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='precio_operacion'>Precio de operacion</label><div class='controls'><input type='text' name='precio_operacion' value='' class='form-control' required id='precio_operacion' placeholder='Precio de operacion' /></div></div>",$resultado->precio_operacion);
        $this->assertEquals("",$resultado->inm_ubicacion_id);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='inm_co_acreditado_id'>Co Acreditado</label><div class='controls'><select class='form-control selectpicker color-secondary inm_co_acreditado_id ' data-live-search='true' id='inm_co_acreditado_id' name='inm_co_acreditado_id' required ><option value=''  >Selecciona una opcion</option><option value='1'  >12345678912 XEXX010101MNEXXXA8 NOMBRE AP APELLIDO MATERNO</option></select></div></div>",$resultado->inm_co_acreditado_id);
        errores::$error = false;
    }

    public function test_hiddens(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $funcion = 'a';
        $resultado = $ks->hiddens($controler,$funcion);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<input type='hidden' name='id_retorno' value='-1'>",$resultado->id_retorno);
        $this->assertEquals("<input type='hidden' name='seccion_retorno' value='inm_producto_infonavit'>",$resultado->seccion_retorno);
        $this->assertEquals("<input type='hidden' name='btn_action_next' value='a'>",$resultado->btn_action_next);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='precio_operacion'>Precio de operacion</label><div class='controls'><input type='text' name='precio_operacion' value='' class='form-control' required id='precio_operacion' placeholder='Precio de operacion' /></div></div>",$resultado->precio_operacion);
        $this->assertEquals("<input type='hidden' name='registro_id' value='-1'>",$resultado->in_registro_id);
        errores::$error = false;
    }

    public function test_id_selected_agente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $resultado = $ks->id_selected_agente($this->link);
        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado);

        errores::$error = false;
    }

    public function test_init(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();

        $resultado = $ks->init($controler, $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("inm_tipo_credito_descripcion",$resultado['inm_attr_tipo_credito_id']->columns_ds[0]);
        $this->assertEquals("inm_attr_tipo_credito_descripcion",$resultado['inm_attr_tipo_credito_id']->columns_ds[1]);
        errores::$error = false;
    }
    public function test_init_row_upd_fiscales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta', data: $alta);
            print_r($error);
            exit;
        }

        $ks = new _keys_selects();
        $ks = new liberator($ks);
        $modelo = new inm_comprador(link: $this->link);
        $row_upd = new stdClass();
        $resultado = $ks->init_row_upd_fiscales($modelo, $row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(601,$resultado->cat_sat_regimen_fiscal_id);
        $this->assertEquals(161,$resultado->cat_sat_moneda_id);
        $this->assertEquals(3,$resultado->cat_sat_forma_pago_id);
        $this->assertEquals(1,$resultado->cat_sat_metodo_pago_id);
        $this->assertEquals(1,$resultado->cat_sat_uso_cfdi_id);
        $this->assertEquals(4,$resultado->cat_sat_tipo_persona_id);
        $this->assertEquals(1,$resultado->bn_cuenta_id);
        errores::$error = false;
    }
    public function test_init_row_upd_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $row_upd = new stdClass();
        $modelo = new inm_comprador(link: $this->link);
        $resultado = $ks->init_row_upd_infonavit(modelo: $modelo,row_upd: $row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_producto_infonavit_id);
        $this->assertEquals(1,$resultado->inm_attr_tipo_credito_id);
        $this->assertEquals(1,$resultado->inm_destino_credito_id);
        $this->assertEquals(7,$resultado->inm_plazo_credito_sc_id);
        $this->assertEquals(5,$resultado->inm_tipo_discapacidad_id);
        $this->assertEquals(6,$resultado->inm_persona_discapacidad_id);
        errores::$error = false;
    }

    public function test_inputs_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $function = 'a';
        $inm_comprador_id = 1;
        $resultado = $ks->inputs_base($controler, $function, $inm_comprador_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<input type='hidden' name='id_retorno' value='-1'>",$resultado->id_retorno);
        $this->assertEquals("<input type='hidden' name='btn_action_next' value='a'>",$resultado->btn_action_next);
        $this->assertEquals("<input type='hidden' name='seccion_retorno' value='inm_producto_infonavit'>",$resultado->seccion_retorno);
        $this->assertEquals("<input type='hidden' name='registro_id' value='-1'>",$resultado->registro_id);
        $this->assertEquals("1",$resultado->inm_comprador_id);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='precio_operacion'>Precio de operacion</label><div class='controls'><input type='text' name='precio_operacion' value='' class='form-control' required id='precio_operacion' placeholder='Precio de operacion' /></div></div>",$resultado->precio_operacion);
        errores::$error = false;
    }

    public function test_input_full(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $function = 's';
        $resultado = $ks->input_full($controler, $function);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<input type='hidden' name='id_retorno' value='-1'>",$resultado->id_retorno);
        $this->assertEquals("<input type='hidden' name='btn_action_next' value='s'>",$resultado->btn_action_next);
        $this->assertEquals("<input type='hidden' name='seccion_retorno' value='inm_producto_infonavit'>",$resultado->seccion_retorno);
        $this->assertEquals("<input type='hidden' name='registro_id' value='-1'>",$resultado->registro_id);
        $this->assertEquals("<input type='hidden' name='inm_comprador_id' value='-1'>",$resultado->inm_comprador_id);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='precio_operacion'>Precio de operacion</label><div class='controls'><input type='text' name='precio_operacion' value='' class='form-control' required id='precio_operacion' placeholder='Precio de operacion' /></div></div>",$resultado->precio_operacion);
        $this->assertEquals('',$resultado->inm_ubicacion_id);
        $this->assertEquals("<div class='control-group col-sm-12'><label class='control-label' for='inm_co_acreditado_id'>Co Acreditado</label><div class='controls'><select class='form-control selectpicker color-secondary inm_co_acreditado_id ' data-live-search='true' id='inm_co_acreditado_id' name='inm_co_acreditado_id' required ><option value=''  >Selecciona una opcion</option><option value='1'  >12345678912 XEXX010101MNEXXXA8 NOMBRE AP APELLIDO MATERNO</option></select></div></div>",$resultado->inm_co_acreditado_id);
        errores::$error = false;
    }

    public function test_inputs_form_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();
        $btn_action_next = 'b';
        $id_retorno = 'a';
        $in_registro_id = 'd';
        $inm_comprador_id = 'f';
        $inm_ubicacion_id = '';
        $precio_operacion = 'e';
        $seccion_retorno = 'c';
        $resultado = $ks->inputs_form_base($btn_action_next, $controler, $id_retorno, $in_registro_id,
            $inm_comprador_id, $inm_ubicacion_id, $precio_operacion, $seccion_retorno);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a',$resultado->id_retorno);
        errores::$error = false;
    }

    public function test_integra_disabled(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $keys_selects = array();
        $key = 'a';
        $resultado = $ks->integra_disabled($key, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['a']->disabled);
        errores::$error = false;
    }

    public function test_integra_disableds(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $keys_selects = array();
        $keys[] = 'z';
        $resultado = $ks->integra_disableds($keys, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['z']->disabled);
        errores::$error = false;
    }

    public function test_key_select_agente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->key_select_agente($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado['com_agente_id']->id_selected);
        errores::$error = false;
    }

    public function test_key_select_nacionalidad(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->key_select_nacionalidad($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_nacionalidad_id']->id_selected);
        errores::$error = false;
    }

    public function test_key_select_ocupacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->key_select_ocupacion($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_ocupacion_id']->id_selected);
        errores::$error = false;
    }

    public function test_key_select_sindicato(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->key_select_sindicato($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_sindicato_id']->id_selected);
        errores::$error = false;
    }

    public function test_key_select_tipo_agente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_com_tipo_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->key_select_tipo_agente($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_tipo_prospecto_id']->id_selected);
        errores::$error = false;
    }

    public function test_key_selects_asigna_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();
        $controler->registro['dp_pais_id'] = 1;
        $controler->registro['dp_estado_id'] = 1;
        $controler->registro['dp_municipio_id'] = 1;
        $controler->registro['dp_cp_id'] = 1;
        $controler->registro['dp_calle_id'] = 1;
        $controler->registro['dp_colonia_id'] = 1;
        $controler->registro['dp_colonia_postal_id'] = 1;
        $controler->registro['dp_calle_pertenece_id'] = 1;

        $resultado = $ks->key_selects_asigna_ubicacion($controler);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['inm_plazo_credito_sc_id']->disabled);
        errores::$error = false;
    }

    public function test_key_selects_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();
        $controler->registro['dp_pais_id'] = 1;
        $controler->registro['dp_estado_id'] = 1;
        $controler->registro['dp_municipio_id'] = 1;
        $controler->registro['dp_cp_id'] = 1;
        $controler->registro['dp_calle_id'] = 1;
        $controler->registro['dp_colonia_id'] = 1;
        $controler->registro['dp_colonia_postal_id'] = 1;
        $controler->registro['dp_calle_pertenece_id'] = 1;

        $resultado = $ks->key_selects_base($controler);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener row_upd",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $ks->key_selects_base($controler);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Producto",$resultado['inm_producto_infonavit_id']->label);
        $this->assertEmpty($resultado['inm_attr_tipo_credito_id']->filtro);
        $this->assertEquals('inm_destino_credito_descripcion',$resultado['inm_destino_credito_id']->columns_ds[0]);
        $this->assertEquals(6,$resultado['inm_plazo_credito_sc_id']->cols);
        $this->assertEquals(5,$resultado['inm_tipo_discapacidad_id']->id_selected);
        $this->assertEquals(6,$resultado['inm_persona_discapacidad_id']->id_selected);
        errores::$error = false;
    }

    public function test_keys_disabled(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $keys_selects = array();
        $resultado = $ks->keys_disabled($keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['nss']->disabled);
        errores::$error = false;
    }



    public function test_keys_selects_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_prospecto(link: $this->link, com_tipo_prospecto_id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $keys_selects = array();
        $resultado = $ks->keys_selects_prospecto($controler, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado['com_agente_id']->id_selected);
        $this->assertEquals(1,$resultado['com_tipo_prospecto_id']->id_selected);
        $this->assertEquals(1,$resultado['inm_sindicato_id']->id_selected);
        $this->assertEquals(1,$resultado['inm_nacionalidad_id']->id_selected);
        $this->assertEquals(1,$resultado['inm_ocupacion_id']->id_selected);
        errores::$error = false;
    }

    public function test_ks_fiscales(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link, cat_sat_regimen_fiscal_id: 605,
            cat_sat_tipo_persona_id: 5, codigo: 2, descripcion: 'A', id: 2);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->ks_fiscales($controler, $keys_selects, $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(605,$resultado['cat_sat_regimen_fiscal_id']->id_selected);
        errores::$error = false;
    }

    public function test_ks_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $row_upd = new stdClass();
        $keys_selects = array();
        $resultado = $ks->ks_infonavit(controler: $controler, keys_selects: $keys_selects, row_upd: $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Producto',$resultado['inm_producto_infonavit_id']->label);
        errores::$error = false;
    }

    public function test_row_data_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();
        $controler->registro['dp_pais_id'] = 1;
        $controler->registro['dp_estado_id'] = 1;
        $controler->registro['dp_municipio_id'] = 1;
        $controler->registro['dp_cp_id'] = 1;
        $controler->registro['dp_calle_id'] = 1;
        $controler->registro['dp_colonia_id'] = 1;
        $controler->registro['dp_colonia_postal_id'] = 1;
        $controler->registro['dp_calle_pertenece_id'] = 1;

        $com_cliente = array();
        $com_cliente['com_cliente_rfc'] = -1;
        $com_cliente['com_cliente_numero_exterior'] = -1;
        $com_cliente['com_cliente_telefono'] = -1;
        $com_cliente['dp_pais_id'] = 1;
        $com_cliente['dp_estado_id'] = 1;
        $com_cliente['dp_municipio_id'] = 1;
        $com_cliente['dp_cp_id'] = 1;
        $com_cliente['dp_colonia_postal_id'] = 1;
        $com_cliente['dp_calle_pertenece_id'] = 1;
        $com_cliente['com_tipo_cliente_id'] = 1;
        $resultado = $ks->row_data_cliente($com_cliente, $controler);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->dp_pais_id);
        errores::$error = false;
    }


}

