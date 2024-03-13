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
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/inmuebles/config/generales.php';
        $this->paths_conf->database = '/var/www/html/inmuebles/config/database.php';
        $this->paths_conf->views = '/var/www/html/inmuebles/config/views.php';
    }

    public function test_alta_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $inm->registro['dp_calle_pertenece_id'] = 1;
        $inm->registro['numero_exterior'] = 1;
        $inm->registro['cuenta_predial'] = 1;
        $inm->registro['inm_tipo_ubicacion_id'] = 1;

        $resultado = $inm->alta_bd();
      //  print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   1",
            $resultado->registro['inm_ubicacion_descripcion']);
        errores::$error = false;
    }

    public function test_asigna_precio_venta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $inm_comprador = new stdClass();
        $inm_ubicacion = array();
        $inm_ubicacion['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $indice = 0;
        $inm_ubicaciones = array();
        $resultado = $inm->asigna_precio_venta($indice, $inm_comprador, $inm_ubicacion, $inm_ubicaciones);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0.0,$resultado[0]['inm_ubicacion_precio']);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->asigna_precio_venta($indice, $inm_comprador, $inm_ubicacion, $inm_ubicaciones);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(450000,$resultado[0]['inm_ubicacion_precio']);
        errores::$error = false;

    }

    public function test_asigna_precios(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $inm_comprador = new stdClass();


        $r_inm_ubicacion = new stdClass();
        $r_inm_ubicacion->registros = array();
        $r_inm_ubicacion->registros[0]['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->asigna_precios($inm_comprador, $r_inm_ubicacion);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0.0,$resultado[0]['inm_ubicacion_precio']);
        errores::$error = false;


        $alta = (new base_test())->alta_inm_precio(link: $this->link,precio_venta: 250000);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $r_inm_ubicacion = new stdClass();
        $r_inm_ubicacion->registros = array();
        $r_inm_ubicacion->registros[0]['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->asigna_precios($inm_comprador, $r_inm_ubicacion);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(250000,$resultado[0]['inm_ubicacion_precio']);
        errores::$error = false;
    }

    public function test_data_ubicaciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $etapa = '';
        $inm_comprador_id = 1;
        $todas = true;

        $resultado = $inm->data_ubicaciones($etapa, $inm_comprador_id, $todas);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_comprador->inm_comprador_id);
        errores::$error = false;
    }

    public function test_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $key_entidad_base_id = '';
        $key_entidad_id = '';
        $registro = array();
        $dp_calle_pertenece = new stdClass();
        $dp_calle_pertenece->dp_pais_descripcion = 'A';
        $dp_calle_pertenece->dp_estado_descripcion = 'B';
        $dp_calle_pertenece->dp_municipio_descripcion = 'C';
        $dp_calle_pertenece->dp_colonia_descripcion = 'D';
        $dp_calle_pertenece->dp_cp_descripcion = 'E';
        $registro['manzana'] = 'A';
        $registro['lote'] = 'A';
        $resultado = $inm->descripcion($key_entidad_base_id, $key_entidad_id, $registro,$dp_calle_pertenece);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A B C D E A A",$resultado);
        errores::$error = false;
    }

    public function test_elimina_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $id = 1;
        $resultado = $inm->elimina_bd($id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);

        errores::$error = false;
        $alta = (new base_test())->alta_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->elimina_bd($id);


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->registro['inm_ubicacion_id']);
        errores::$error = false;

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

    public function test_init_row(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $key_entidad_base_id = '';
        $key_entidad_id = '';
        $registro = array();
        $registro['dp_calle_pertenece_id'] = 1;
        $registro['numero_exterior'] = 1;

        $resultado = $inm->init_row($key_entidad_base_id, $key_entidad_id, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   1",$resultado['descripcion']);
        errores::$error = false;
    }

    public function test_inm_precio_venta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $inm_comprador = new stdClass();
        $inm_ubicacion = array();
        $inm_ubicacion['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->inm_precio_venta($inm_comprador, $inm_ubicacion);

        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_precio(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $inm_comprador = new stdClass();
        $inm_ubicacion = array();
        $inm_ubicacion['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->inm_precio_venta($inm_comprador, $inm_ubicacion);

        $this->assertIsFloat($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(450000,$resultado);

        errores::$error = false;


    }

    public function test_integra_descripcion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);


        $registro = array();
        $dp_calle_pertenece = new stdClass();
        $dp_calle_pertenece->dp_pais_descripcion = 'A';
        $dp_calle_pertenece->dp_estado_descripcion = 'B';
        $dp_calle_pertenece->dp_municipio_descripcion = 'C';
        $dp_calle_pertenece->dp_colonia_descripcion = 'D';
        $dp_calle_pertenece->dp_cp_descripcion = 'E';
        $registro['manzana'] = 'A';
        $registro['lote'] = 'A';
        $resultado = $inm->integra_descripcion($dp_calle_pertenece, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("A B C D E A A",$resultado['descripcion']);
        errores::$error = false;
    }

    public function test_modifica_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);


        $registro = array();
        $id = 1;
        $resultado = $inm->modifica_bd($registro, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   NUM EXT",$resultado->registro_actualizado->inm_ubicacion_descripcion);
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

    public function test_opiniones_valor(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);
        $del = (new base_test())->del_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->opiniones_valor($inm_ubicacion_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;
        $resultado = $inm->opiniones_valor($inm_ubicacion_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertCount(1,$resultado);
        errores::$error = false;

    }

    public function test_r_inm_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $etapa = 'a';
        $todas = false;

        $resultado = $inm->r_inm_ubicacion($etapa, $todas);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->n_registros);
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

    public function test_regenera_datas(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';




        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_opinion_valor(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $inm_ubicacion_id = 1;

        $resultado = $inm->regenera_datas($inm_ubicacion_id);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->regenera_op->registro_actualizado->inm_ubicacion_n_opiniones_valor);
        $this->assertEquals(0,$resultado->regenera_op->registro_actualizado->inm_ubicacion_monto_opinion_promedio);


        errores::$error = false;


        $inm_ubicacion_id = 1;

        $alta = (new base_test())->alta_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->regenera_datas($inm_ubicacion_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->regenera_op->registro_actualizado->inm_ubicacion_n_opiniones_valor);
        $this->assertEquals(0,$resultado->regenera_op->registro_actualizado->inm_ubicacion_monto_opinion_promedio);
        $this->assertEquals(1000,$resultado->regenera_op->registro_actualizado->inm_ubicacion_costo);
        errores::$error = false;

        $del = (new base_test())->del_inm_costo(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        errores::$error = false;
    }

    public function test_ubicaciones_con_precio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_ubicacion(link: $this->link);
        //$inm = new liberator($inm);

        $etapa = 'a';
        $inm_comprador_id = 1;

        $resultado = $inm->ubicaciones_con_precio($etapa, $inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_valida_ids_precio(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new inm_ubicacion(link: $this->link);
        $inm = new liberator($inm);

        $inm_comprador = new stdClass();
        $inm_ubicacion = array();
        $inm_ubicacion['inm_ubicacion_id'] = 1;
        $inm_comprador->inm_institucion_hipotecaria_id = 1;
        $resultado = $inm->valida_ids_precio($inm_comprador, $inm_ubicacion);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }


}

