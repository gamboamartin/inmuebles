<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_co_acreditadoTest extends test {
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

    public function test_inm_co_acreditados(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_co_acreditado(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_rel_co_acred(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->inm_co_acreditados($inm_comprador_id);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_co_acred(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $inm_comprador_id = 1;
        $resultado = $inm->inm_co_acreditados($inm_comprador_id);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado[0]['inm_comprador_id']);
        $this->assertEquals(1,$resultado[0]['inm_co_acreditado_id']);
        errores::$error = false;
    }


    public function test_valida_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_co_acreditado(link: $this->link);
        //$inm = new liberator($inm);



        $inm_co_acreditado = array();
        $inm_co_acreditado['nombre'] = 'A';
        $inm_co_acreditado['apellido_paterno'] = 'A';
        $inm_co_acreditado['nss'] = '12345678901';
        $inm_co_acreditado['curp'] = 'XEXX010101HNEXXXA4';
        $inm_co_acreditado['rfc'] = 'AAA020202ABC';
        $inm_co_acreditado['apellido_materno'] = 'A';
        $inm_co_acreditado['lada'] = '111';
        $inm_co_acreditado['numero'] = '1234567';
        $inm_co_acreditado['celular'] = '1234567890';
        $inm_co_acreditado['genero'] = 'A';
        $inm_co_acreditado['correo'] = 'a@a.com';
        $inm_co_acreditado['nombre_empresa_patron'] = 'A';
        $inm_co_acreditado['nrp'] = 'A';
        $inm_co_acreditado['lada_nep'] = 'A';
        $inm_co_acreditado['numero_nep'] = 'A';
        $resultado = $inm->valida_alta($inm_co_acreditado);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_data_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_co_acreditado(link: $this->link);
        //$inm = new liberator($inm);



        $inm_co_acreditado = array();
        $inm_co_acreditado['nombre'] = 'A';
        $inm_co_acreditado['apellido_paterno'] = 'A';
        $inm_co_acreditado['nss'] = 'A';
        $inm_co_acreditado['curp'] = 'A';
        $inm_co_acreditado['rfc'] = 'A';
        $inm_co_acreditado['apellido_materno'] = 'A';
        $resultado = $inm->valida_data_alta($inm_co_acreditado);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }


}

