<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _keys_selectsTest extends test {
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

    public function test_ajusta_row_data_cliente(): void
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

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();

        $resultado = $ks->ajusta_row_data_cliente($controler);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener com_cliente",$resultado['mensaje_limpio']);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $ks->ajusta_row_data_cliente($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("AAA010101AAA",$resultado->rfc);
        $this->assertEquals("1",$resultado->numero_exterior);
        $this->assertEquals("1649",$resultado->dp_municipio_id);
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

        $ks = new _keys_selects();
        $ks = new liberator($ks);

        $row_upd = new stdClass();
        $resultado = $ks->init_row_upd_fiscales($row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(605,$resultado->cat_sat_regimen_fiscal_id);
        $this->assertEquals(161,$resultado->cat_sat_moneda_id);
        $this->assertEquals(99,$resultado->cat_sat_forma_pago_id);
        $this->assertEquals(2,$resultado->cat_sat_metodo_pago_id);
        $this->assertEquals(22,$resultado->cat_sat_uso_cfdi_id);
        $this->assertEquals(5,$resultado->cat_sat_tipo_persona_id);
        $this->assertEquals(-1,$resultado->bn_cuenta_id);
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
        $resultado = $ks->init_row_upd_infonavit($row_upd);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado->inm_producto_infonavit_id);
        $this->assertEquals(-1,$resultado->inm_attr_tipo_credito_id);
        $this->assertEquals(-1,$resultado->inm_destino_credito_id);
        $this->assertEquals(7,$resultado->inm_plazo_credito_sc_id);
        $this->assertEquals(5,$resultado->inm_tipo_discapacidad_id);
        $this->assertEquals(6,$resultado->inm_persona_discapacidad_id);
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

        $ks = new _keys_selects();
        //$ks = new liberator($ks);

        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $controler->row_upd = new stdClass();

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
        //$ks = new liberator($ks);

        $keys_selects = array();
        $resultado = $ks->keys_disabled($keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['nss']->disabled);
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
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->dp_pais_id);
        errores::$error = false;
    }


}

