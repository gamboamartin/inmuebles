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
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/inmuebles/config/generales.php';
        $this->paths_conf->database = '/var/www/html/inmuebles/config/database.php';
        $this->paths_conf->views = '/var/www/html/inmuebles/config/views.php';
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

        $resultado = $inm->com_cliente_data_transaccion($numero_interior, $razon_social, $registro, $telefono);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('v',$resultado['telefono']);
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



        $resultado = $inm->com_cliente_ins($numero_interior, $razon_social, $registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A',$resultado['codigo']);
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
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['nombre'] = 'Z';
        $registro_entrada['apellido_paterno'] = 'k';

        $resultado = $inm->inserta_com_cliente(link: $this->link,registro_entrada:  $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('AAA010101AAA',$resultado->registro['com_cliente_rfc']);
        $this->assertEquals('Z k',$resultado->registro['com_cliente_razon_social']);
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
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '601';
        $registro_entrada['cat_sat_tipo_persona_id'] = '4';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['rfc'] = 'AAA010101AAA';
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



        $resultado = $inm->row_com_cliente_ins($registro_entrada);
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



        $resultado = $inm->row_upd($registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A A",$resultado['razon_social']);

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

        $resultado = $inm->valida_base_com($registro_entrada);
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

