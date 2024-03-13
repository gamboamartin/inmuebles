<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_alta_comprador;
use gamboamartin\inmuebles\models\_base_comprador;
use gamboamartin\inmuebles\models\_com_cliente;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _com_clienteTest extends test {
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

    public function test_actualiza_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $link = $this->link;
        $com_cliente_upd = array();
        $inm_comprador_id = 1;
        $resultado = $inm->actualiza_com_cliente($com_cliente_upd, $inm_comprador_id, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;

    }

    public function test_com_cliente_data_transaccion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $numero_interior = '';
        $razon_social = 'a';
        $registro = array();
        $telefono = 'v';
        $registro['cat_sat_forma_pago_id'] = 1;
        $registro['cat_sat_metodo_pago_id'] = 1;
        $registro['cat_sat_moneda_id'] = 1;
        $registro['cat_sat_regimen_fiscal_id'] = 1;
        $registro['cat_sat_tipo_persona_id'] = 1;
        $registro['cat_sat_uso_cfdi_id'] = 1;
        $registro['com_tipo_cliente_id'] = 1;
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;
        $registro['rfc'] = 1;
        $registro['dp_municipio_id'] = 1;

        $resultado = $inm->com_cliente_data_transaccion($numero_interior, $razon_social, $registro, $telefono);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('v',$resultado['telefono']);
        errores::$error = false;
    }

    public function test_com_cliente_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }


        $inm_comprador_id = 1;
        $link = $this->link;

        $resultado = $inm->com_cliente_id($inm_comprador_id, $link);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error inm_rel_comprador_com_cliente no existe",$resultado['mensaje_limpio']);
        errores::$error = false;

        $inm_comprador_id = 1;
        $link = $this->link;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $resultado = $inm->com_cliente_id($inm_comprador_id, $link);

        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }
    public function test_com_cliente_id_filtrado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $link = $this->link;
        $filtro = array();
        $filtro['com_cliente.id'] = 1;
        $resultado = $inm->com_cliente_id_filtrado($link, $filtro);
        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado);
        errores::$error = false;
    }
    public function test_com_cliente_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $numero_interior = '';
        $razon_social = 'A';
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '11';
        $registro_entrada['numero_com'] = '22222222';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['cp'] = '1';
        $registro_entrada['dp_municipio_id'] = '1';

        $resultado = $inm->com_cliente_ins($numero_interior, $razon_social, $registro_entrada);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A',$resultado['codigo']);
        errores::$error = false;
    }

    public function test_com_cliente_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro = new stdClass();
        $registro->inm_comprador_nombre = 'A';
        $registro->inm_comprador_apellido_paterno = 'B';
        $registro->dp_calle_pertenece_id = '1';

        $resultado = $inm->com_cliente_upd($this->link, $registro);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A B',$resultado['razon_social']);
        errores::$error = false;
    }
    public function test_data_rel(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $inm->data_rel($com_cliente_id, $inm_comprador_id, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_rel_comprador_com_cliente_ins['inm_comprador_id']);
        $this->assertEquals(1,$resultado->inm_rel_comprador_com_cliente_ins['com_cliente_id']);
        $this->assertNotTrue($resultado->existe);
        errores::$error = false;
    }
    public function test_data_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);


        $registro_entrada = array();
        $registro_entrada['nombre'] = 'A';
        $registro_entrada['apellido_paterno'] = 'A';
        $registro_entrada['lada_com'] = 'A';
        $registro_entrada['numero_com'] = 'A';
        $resultado = $inm->data_upd($registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A A',$resultado->razon_social);
        errores::$error = false;
    }
    public function test_existe_relacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }


        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $inm->existe_relacion($com_cliente_id, $inm_comprador_id, $link);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $inm->existe_relacion($com_cliente_id, $inm_comprador_id, $link);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }
    public function test_get_relacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;

        $resultado = $inm->get_relacion($com_cliente_id, $inm_comprador_id, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->n_registros);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;

        $resultado = $inm->get_relacion($com_cliente_id, $inm_comprador_id, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->n_registros);
        errores::$error = false;
    }

    public function test_init_keys_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $com_cliente_upd = array();

        $resultado = $inm->init_keys_com_cliente($com_cliente_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
        errores::$error = false;
    }
    public function test_inm_rel_com_cliente_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $com_cliente_id = 1;
        $inm_comprador_id = 1;

        $resultado = $inm->inm_rel_com_cliente_ins($com_cliente_id, $inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_comprador_id']);
        $this->assertEquals(1,$resultado['com_cliente_id']);
        errores::$error = false;
    }
    public function test_inserta_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar cliente',data:  $del);
            print_r($error);
            exit;
        }

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA010101AAA';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '11';
        $registro_entrada['numero_com'] = '22222222';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_moneda_id'] = '161';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['nombre'] = 'Z';
        $registro_entrada['apellido_paterno'] = 'k';
        $registro_entrada['cp'] = 'k';
        $registro_entrada['dp_municipio_id'] = '1';

        $resultado = $inm->inserta_com_cliente(link: $this->link,registro_entrada:  $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('AAA010101AAA',$resultado->registro['com_cliente_rfc']);
        $this->assertEquals('Z k',$resultado->registro['com_cliente_razon_social']);
        errores::$error = false;
    }
    public function test_inserta_inm_rel_comprador_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        //$inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del',data:  $del);
            print_r($error);
            exit;

        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar',data:  $alta);
            print_r($error);
            exit;

        }

        $com_cliente_id = 1;
        $inm_comprador_id = 1;
        $link = $this->link;
        $resultado = $inm->inserta_inm_rel_comprador_com_cliente($com_cliente_id, $inm_comprador_id, $link);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_key_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new _com_cliente();
        $inm = new liberator($inm);


        $resultado = $inm->key_com_cliente();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('com_tipo_cliente_id',$resultado[0]);
        $this->assertEquals('rfc',$resultado[1]);
        $this->assertEquals('dp_calle_pertenece_id',$resultado[2]);
        $this->assertEquals('numero_exterior',$resultado[3]);
        $this->assertEquals('numero_interior',$resultado[4]);
        $this->assertEquals('telefono',$resultado[5]);
        $this->assertEquals('cat_sat_regimen_fiscal_id',$resultado[6]);
        $this->assertEquals('cat_sat_moneda_id',$resultado[7]);
        $this->assertEquals('cat_sat_forma_pago_id',$resultado[8]);
        $this->assertEquals('cat_sat_metodo_pago_id',$resultado[9]);
        $this->assertEquals('cat_sat_uso_cfdi_id',$resultado[10]);
        $this->assertEquals('cat_sat_tipo_persona_id',$resultado[11]);
        errores::$error = false;
    }

    public function test_keys_name_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $con_prefijo = false;
        $resultado = $inm->keys_name_cliente($con_prefijo);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('nombre',$resultado->key_nombre);
        $this->assertEquals('apellido_paterno',$resultado->key_apellido_paterno);
        $this->assertEquals('apellido_materno',$resultado->key_apellido_materno);

        errores::$error = false;

        $con_prefijo = true;
        $resultado = $inm->keys_name_cliente($con_prefijo);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('inm_comprador_nombre',$resultado->key_nombre);
        $this->assertEquals('inm_comprador_apellido_paterno',$resultado->key_apellido_paterno);
        $this->assertEquals('inm_comprador_apellido_materno',$resultado->key_apellido_materno);

        errores::$error = false;
    }

    public function test_modifica_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        //$inm = new liberator($inm);


        $link = $this->link;

        $inm_comprador = new stdClass();
        $inm_comprador->inm_comprador_nombre = 'Z';
        $inm_comprador->inm_comprador_apellido_paterno = 'P';
        $inm_comprador->inm_comprador_id = 1;
        $inm_comprador->dp_calle_pertenece_id = 1;
        $resultado = $inm->modifica_com_cliente($inm_comprador, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Z P',$resultado->registro_actualizado->com_cliente_razon_social);
        errores::$error = false;
    }

    public function test_numero_interior(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();

        $resultado = $inm->numero_interior($registro_entrada);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('',$resultado);
        errores::$error = false;
    }

    public function test_r_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del cliente',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta cliente',data:  $alta);
            print_r($error);
            exit;
        }


        $registro_entrada = array();
        $registro_entrada['lada_com'] = 'A';
        $registro_entrada['numero_com'] = 'A';
        $registro_entrada['nombre'] = 'A';
        $registro_entrada['apellido_paterno'] = 'A';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '161';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['rfc'] = 'AAA010101AAA';
        $registro_entrada['dp_municipio_id'] = '1';
        $filtro = array();
        $filtro['com_cliente.id'] = 1;
        $link = $this->link;
        $resultado = $inm->r_com_cliente($filtro, $link, $registro_entrada);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_id);

        errores::$error = false;
    }

    public function test_razon_social(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro = new stdClass();
        $con_prefijo = false;

        $registro->nombre = 'A';
        $registro->apellido_paterno = 'A';

        $resultado = $inm->razon_social($con_prefijo, $registro);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A A',$resultado);

        errores::$error = false;
        $registro = new stdClass();
        $con_prefijo = true;

        $registro->inm_comprador_nombre = 'A';
        $registro->inm_comprador_apellido_paterno = 'A';

        $resultado = $inm->razon_social($con_prefijo, $registro);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A A',$resultado);

        errores::$error = false;
    }

    public function test_result_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del cliente',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta cliente',data:  $alta);
            print_r($error);
            exit;
        }

        $registro_entrada = array();
        $link = $this->link;
        $existe_cliente = true;
        $filtro = array();
        $filtro['com_cliente.id'] = 1;

        $registro_entrada['rfc'] = 'AAA880202A01';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_moneda_id'] = '161';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['nombre'] = '1';
        $registro_entrada['apellido_paterno'] = '1';
        $registro_entrada['cp'] = '1';
        $registro_entrada['dp_municipio_id'] = '1';



        $resultado = $inm->result_com_cliente($existe_cliente, $filtro, $link, $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_id);

        errores::$error = false;

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del cliente',data:  $del);
            print_r($error);
            exit;
        }

        $registro_entrada = array();
        $link = $this->link;
        $filtro = array();
        $existe_cliente = false;

        $registro_entrada['rfc'] = 'AAA090909654';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_moneda_id'] = '161';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['nombre'] = '1';
        $registro_entrada['apellido_paterno'] = '1';
        $registro_entrada['cp'] = '1';
        $registro_entrada['dp_municipio_id'] = '1';

        $filtro['com_cliente.id'] = 1;

        $resultado = $inm->result_com_cliente($existe_cliente, $filtro, $link, $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("AAA090909654",$resultado->registro['com_cliente_rfc']);

        errores::$error = false;
    }

    public function test_result_relacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del cliente',data:  $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del cliente',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta cliente',data:  $alta);
            print_r($error);
            exit;
        }

        $link = $this->link;
        $existe = false;
        $inm_rel_comprador_com_cliente_ins = array();
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = 1;
        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = 1;
        $resultado = $inm->result_relacion($existe, $inm_rel_comprador_com_cliente_ins, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Registro insertado con éxito",$resultado->mensaje);

        errores::$error = false;

        $link = $this->link;
        $existe = true;
        $inm_rel_comprador_com_cliente_ins = array();
        $inm_rel_comprador_com_cliente_ins['com_cliente_id'] = 1;
        $inm_rel_comprador_com_cliente_ins['inm_comprador_id'] = 1;
        $resultado = $inm->result_relacion($existe, $inm_rel_comprador_com_cliente_ins, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("1 1",$resultado->registros[0]['inm_rel_comprador_com_cliente_codigo']);
        errores::$error = false;

    }

    public function test_row_com_cliente_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);


        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '11';
        $registro_entrada['numero_com'] = '22222222';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['nombre'] = 'Z';
        $registro_entrada['apellido_paterno'] = 'k';
        $registro_entrada['dp_municipio_id'] = '1';
        $registro_entrada['cp'] = '1';

        $resultado = $inm->row_com_cliente_ins($registro_entrada);
       // print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Z k",$resultado['razon_social']);

        errores::$error = false;
    }

    public function test_row_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);


        $registro_entrada = array();
        $registro_entrada['lada_com'] ='A';
        $registro_entrada['numero_com'] ='A';
        $registro_entrada['nombre'] ='A';
        $registro_entrada['apellido_paterno'] ='A';
        $registro_entrada['cat_sat_forma_pago_id'] ='1';
        $registro_entrada['cat_sat_metodo_pago_id'] ='1';
        $registro_entrada['cat_sat_moneda_id'] ='1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] ='1';
        $registro_entrada['cat_sat_tipo_persona_id'] ='1';
        $registro_entrada['cat_sat_uso_cfdi_id'] ='1';
        $registro_entrada['com_tipo_cliente_id'] ='1';
        $registro_entrada['dp_calle_pertenece_id'] ='1';
        $registro_entrada['numero_exterior'] ='A';
        $registro_entrada['rfc'] ='A';
        $registro_entrada['dp_municipio_id'] ='1';

        $resultado = $inm->row_upd($registro_entrada);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A A",$resultado['razon_social']);

        errores::$error = false;
    }

    public function test_transacciona_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        //$inm = new liberator($inm);



        $registro_entrada = array();
        $link = $this->link;

        $registro_entrada['rfc'] = 'AAA010101HHH';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_moneda_id'] = '161';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['nombre'] = '4';
        $registro_entrada['apellido_paterno'] = '4';
        $registro_entrada['cp'] = '4';
        $registro_entrada['dp_municipio_id'] = '4';


        $resultado = $inm->transacciona_com_cliente($link, $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_valida_base_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        //$inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['cp'] = '1';

        $resultado = $inm->valida_base_com($registro_entrada);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_valida_data_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        //$inm = new liberator($inm);



        $inm_comprador = new stdClass();
        $inm_comprador->inm_comprador_nombre = 'A';
        $inm_comprador->inm_comprador_apellido_paterno = 'A';
        $inm_comprador->inm_comprador_id = '1';

        $resultado = $inm->valida_data_cliente($inm_comprador);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_valida_data_result_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new _com_cliente();

        $registro_entrada = array();
        $registro_entrada['lada_com'] = 'A';
        $registro_entrada['numero_com'] = 'A';
        $registro_entrada['nombre'] = 'A';
        $registro_entrada['apellido_paterno'] = 'A';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['rfc'] = '1';
        $registro_entrada['dp_municipio_id'] = '1';

        $resultado = $inm->valida_data_result_cliente(registro_entrada: $registro_entrada);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }

    public function test_valida_data_transaccion_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _com_cliente();
        $inm = new liberator($inm);


        $registro_entrada = array();
        $registro_entrada['lada_com'] ='A';
        $registro_entrada['numero_com'] ='A';
        $registro_entrada['nombre'] ='A';
        $registro_entrada['apellido_paterno'] ='A';
        $registro_entrada['cat_sat_forma_pago_id'] ='1';
        $registro_entrada['cat_sat_metodo_pago_id'] ='1';
        $registro_entrada['cat_sat_moneda_id'] ='1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] ='1';
        $registro_entrada['cat_sat_tipo_persona_id'] ='1';
        $registro_entrada['cat_sat_uso_cfdi_id'] ='1';
        $registro_entrada['com_tipo_cliente_id'] ='1';
        $registro_entrada['dp_calle_pertenece_id'] ='1';
        $registro_entrada['numero_exterior'] ='A';
        $registro_entrada['rfc'] ='A';
        $registro_entrada['dp_municipio_id'] ='1';



        $resultado = $inm->valida_data_transaccion_cliente($registro_entrada);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }
    public function test_valida_existencia_keys_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['cp'] = '1';

        $resultado = $inm->valida_existencia_keys_com($registro_entrada);

        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;

    }

    public function test_valida_ids_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _com_cliente();
        $inm = new liberator($inm);

        $registro_entrada = array();
        $registro_entrada['rfc'] = 'AAA0101016HG';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '1';
        $registro_entrada['numero_com'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';

        $resultado = $inm->valida_ids_com($registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);


        errores::$error = false;

    }


}

