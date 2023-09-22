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
use gamboamartin\inmuebles\models\_co_acreditado;
use gamboamartin\inmuebles\models\_com_cliente;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _co_acreditadoTest extends test {
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

    public function test_aplica_alta_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $inm_co_acreditado_ins = array();
        $inm_co_acreditado_ins[]  ='x';


        $resultado = $inm->aplica_alta_co_acreditado($inm_co_acreditado_ins);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }



    public function test_data_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $co_acreditados = array();
        $co_acreditados[] = '';

        $resultado = $inm->data_co_acreditado($co_acreditados);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado->existe_co_acreditado);
        errores::$error = false;
    }

    public function test_get_data_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $inm_comprador_id = 1;
        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        $resultado = $inm->get_data_co_acreditado($inm_comprador_id, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_inm_co_acreditado_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $registro = array();
        $registro['inm_co_acreditado_nss'] = '1';

        $resultado = $inm->inm_co_acreditado_ins($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['nss']);
        errores::$error = false;
    }

    public function test_inm_rel_co_acreditado_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $inm_co_acreditado_id = 1;
        $inm_comprador_id = 1;

        $resultado = $inm->inm_rel_co_acreditado_ins($inm_co_acreditado_id, $inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['inm_co_acreditado_id']);
        $this->assertEquals('1',$resultado['inm_comprador_id']);
        errores::$error = false;
    }

    public function test_inserta_data_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_co_acreditado(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new _co_acreditado();
        $inm = new liberator($inm);



        $inm_co_acreditado_ins = array();
        $inm_comprador_id = 1;
        $link = $this->link;

        $inm_co_acreditado_ins['nombre'] = 'A';
        $inm_co_acreditado_ins['apellido_paterno'] = 'A';
        $inm_co_acreditado_ins['nss'] = '12345678909';
        $inm_co_acreditado_ins['curp'] = 'XEXX010101HNEXXXA4';
        $inm_co_acreditado_ins['rfc'] = 'CVA121201HJ7';
        $inm_co_acreditado_ins['apellido_materno'] = 'A';
        $inm_co_acreditado_ins['lada'] = '11';
        $inm_co_acreditado_ins['numero'] = '12345678';
        $inm_co_acreditado_ins['celular'] = '1234445556';
        $inm_co_acreditado_ins['genero'] = 'A';
        $inm_co_acreditado_ins['correo'] = 'a@b.com.mx';
        $inm_co_acreditado_ins['nombre_empresa_patron'] = 'A';
        $inm_co_acreditado_ins['nrp'] = 'A';
        $inm_co_acreditado_ins['lada_nep'] = 'A';
        $inm_co_acreditado_ins['numero_nep'] = 'A';

        $resultado = $inm->inserta_data_co_acreditado($inm_co_acreditado_ins, $inm_comprador_id, $link);
       // print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Registro insertado con éxito',$resultado->alta_inm_co_acreditado->mensaje);
        errores::$error = false;
    }



    public function test_operaciones_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        //$inm = new liberator($inm);


        $inm_comprador_id = -1;
        $inm_comprador_upd = array();
        $modelo_inm_comprador = new inm_comprador(link: $this->link);
        $resultado = $inm->operaciones_co_acreditado($inm_comprador_id, $inm_comprador_upd, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_transacciones_co_acreditado(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _co_acreditado();
        $inm = new liberator($inm);


        $inm_co_acreditado_ins = array();
        $inm_co_acreditado_ins['nombre'] = 'A';
        $inm_co_acreditado_ins['apellido_paterno'] = 'A';
        $inm_co_acreditado_ins['nss'] = '12345678909';
        $inm_co_acreditado_ins['curp'] = 'XEXX010101HNEXXXA4';
        $inm_co_acreditado_ins['rfc'] = 'CVA121201HJ7';
        $inm_co_acreditado_ins['apellido_materno'] = 'A';
        $inm_co_acreditado_ins['lada'] = '11';
        $inm_co_acreditado_ins['numero'] = '12345678';
        $inm_co_acreditado_ins['celular'] = '1234445556';
        $inm_co_acreditado_ins['genero'] = 'A';
        $inm_co_acreditado_ins['correo'] = 'a@b.com.mx';
        $inm_co_acreditado_ins['nombre_empresa_patron'] = 'A';
        $inm_co_acreditado_ins['nrp'] = 'A';
        $inm_co_acreditado_ins['lada_nep'] = 'A';
        $inm_co_acreditado_ins['numero_nep'] = 'A';
        $inm_comprador_id = 1;
        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        $resultado = $inm->transacciones_co_acreditado($inm_co_acreditado_ins, $inm_comprador_id, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }




}

