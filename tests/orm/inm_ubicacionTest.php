<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
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


class inm_ubicacionTest extends test {
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


    public function test_get_costo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $inm_ubicacion_id = 1;

        $resultado = $inm->get_costo($inm_ubicacion_id);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0.00,$resultado);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }
        $inm_ubicacion_id = 1;
        $resultado = $inm->get_costo($inm_ubicacion_id);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1000.00,$resultado);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_costo(link: $this->link, codigo: 2, id: 2, monto: 500);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }
        $inm_ubicacion_id = 1;
        $resultado = $inm->get_costo($inm_ubicacion_id);

        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1500.00,$resultado);
        errores::$error = false;
    }

    public function test_monto_opinion_promedio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link, fecha: '2020-01-02', id: 2, monto_resultado: 200000);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link, fecha: '2020-01-03', id: 3, monto_resultado: 300000);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->monto_opinion_promedio($inm_ubicacion_id);
        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(250000.00,$resultado);
        errores::$error = false;
    }

    public function test_n_opiniones_valor(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);


        $del = (new base_test())->del_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->n_opiniones_valor($inm_ubicacion_id);
       // print_r($resultado);exit;

        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }



        $resultado = $inm->n_opiniones_valor($inm_ubicacion_id);

        $this->assertIsInt($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado);
        errores::$error = false;
    }

    public function test_regenera_costo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);




        $inm_ubicacion_id = 1;
        $resultado = $inm->regenera_costo($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_costo);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }
        $inm_ubicacion_id = 1;
        $resultado = $inm->regenera_costo($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1000,$resultado->registro_actualizado->inm_ubicacion_costo);
        errores::$error = false;

    }

    public function test_regenera_data_opinion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $inm_ubicacion_id = 1;
        $resultado = $inm->regenera_data_opinion($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_n_opiniones_valor);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_monto_opinion_promedio);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_costo);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link,fecha: '2020-01-02');
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->regenera_data_opinion($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro_actualizado->inm_ubicacion_n_opiniones_valor);
        $this->assertEquals(100000,$resultado->registro_actualizado->inm_ubicacion_monto_opinion_promedio);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_costo);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link, fecha: '2020-01-03', id: 2, monto_resultado: 500000);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->regenera_data_opinion($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(2,$resultado->registro_actualizado->inm_ubicacion_n_opiniones_valor);
        $this->assertEquals(300000,$resultado->registro_actualizado->inm_ubicacion_monto_opinion_promedio);
        $this->assertEquals(0,$resultado->registro_actualizado->inm_ubicacion_costo);

        errores::$error = false;

    }


}

