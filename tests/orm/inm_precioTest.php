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
use gamboamartin\inmuebles\models\inm_precio;
use gamboamartin\inmuebles\models\inm_referencia;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_precioTest extends test {
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

    public function test_filtro_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);


        $inm_ubicacion_id = 1;
        $inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->filtro_base($inm_institucion_hipotecaria_id, $inm_ubicacion_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_ubicacion.id']);
        $this->assertEquals(1,$resultado['inm_institucion_hipotecaria.id']);

        errores::$error = false;
    }

    public function test_filtro_fecha(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);


        $fecha = '2020-01-01';
        $resultado = $inm->filtro_fecha($fecha);
       // print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("inm_precio.fecha_inicial",$resultado[0]['campo_1']);
        $this->assertEquals("inm_precio.fecha_final",$resultado[0]['campo_2']);
        $this->assertEquals($fecha,$resultado[0]['fecha']);

        errores::$error = false;
    }

    public function test_filtros(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);


        $fecha = '2020-01-01';
        $inm_institucion_hipotecaria_id = 1;
        $inm_ubicacion_id = 1;
        $resultado = $inm->filtros($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->filtro['inm_ubicacion.id']);
        $this->assertEquals(1,$resultado->filtro['inm_institucion_hipotecaria.id']);
        $this->assertEquals("inm_precio.fecha_inicial",$resultado->filtro_fecha[0]['campo_1']);
        $this->assertEquals("inm_precio.fecha_final",$resultado->filtro_fecha[0]['campo_2']);
        $this->assertEquals("2020-01-01",$resultado->filtro_fecha[0]['fecha']);

        errores::$error = false;
    }

    public function test_inm_precio_result(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $fecha =  '2020-01-01';
        $inm_institucion_hipotecaria_id = 1;
        $inm_ubicacion_id = 1;
        $valida_existe = true;

        $resultado = $inm->inm_precio_result($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id, $valida_existe);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al validar si existe",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $fecha =  '2020-01-01';
        $inm_institucion_hipotecaria_id = 1;
        $inm_ubicacion_id = 1;
        $valida_existe = true;

        $resultado = $inm->inm_precio_result($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id, $valida_existe);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(450000,$resultado['inm_precio_precio_venta']);

        errores::$error = false;
    }
    public function test_precio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $fecha = '2023-09-18';
        $inm_ubicacion_id = 1;
        $inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->precio($fecha, $inm_ubicacion_id, $inm_institucion_hipotecaria_id);


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(450000,$resultado->inm_precio_precio_venta);

        errores::$error = false;


    }

    public function test_r_inm_precio_by_fecha(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $fecha = '2020-01-01';
        $inm_institucion_hipotecaria_id = 1;
        $inm_ubicacion_id = 1;
        $resultado = $inm->r_inm_precio_by_fecha($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado->registros);

        errores::$error = false;
    }

    public function test_valida_existe(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);


        $r_inm_precio =  new stdClass();
        $r_inm_precio->n_registros = '0';
        $valida_existe = true;

        $resultado = $inm->valida_existe($r_inm_precio, $valida_existe);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error no existe precio configurado",$resultado['mensaje_limpio']);

        errores::$error = false;

        $r_inm_precio =  new stdClass();
        $r_inm_precio->n_registros = '1';
        $valida_existe = true;

        $resultado = $inm->valida_existe($r_inm_precio, $valida_existe);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_get_precio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_precio(link: $this->link);
        $inm = new liberator($inm);


        $fecha = '';
        $inm_ubicacion_id = -1;
        $inm_institucion_hipotecaria_id = -1;
        $resultado = $inm->valida_get_precio($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error fecha esta vacio",$resultado['mensaje_limpio']);

        errores::$error = false;

        $fecha = '2020-01-01';
        $inm_ubicacion_id = 1;
        $inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->valida_get_precio($fecha, $inm_institucion_hipotecaria_id, $inm_ubicacion_id);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }


}

