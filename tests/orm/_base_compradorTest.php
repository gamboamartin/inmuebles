<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_base_comprador;
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


class _base_compradorTest extends test {
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

    public function test_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);


        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $com_cliente_id = 1;
        $resultado = $inm->com_cliente($com_cliente_id,$this->link);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error no existe com_cliente",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $com_cliente_id = 1;
        $resultado = $inm->com_cliente($com_cliente_id,$this->link);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_cliente_codigo']);
        $this->assertEquals(601,$resultado['cat_sat_regimen_fiscal_codigo']);
        errores::$error = false;
    }

    public function test_data_upd_post(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);


        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = new stdClass();
        $r_modifica->registro_actualizado->inm_comprador_es_segundo_credito = '';
        $r_modifica->registro_actualizado->inm_comprador_con_discapacidad = '';

        $resultado = $inm->data_upd_post($r_modifica);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
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

        $inm = new _base_comprador();
        //$inm = new liberator($inm);


        $registro = array();
        $registro['nombre'] = 'Z';
        $registro['apellido_paterno'] = 'Z';
        $registro['nss'] = 'Z';
        $registro['curp'] = 'Z';
        $registro['rfc'] = 'Z';
        $resultado = $inm->descripcion(registro: $registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("Z Z  Z Z Z 2024-05-",$resultado);
        errores::$error = false;
    }

    public function test_inm_rel_comprador_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_org_puesto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_org_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->inm_rel_comprador_cliente($inm_comprador_id, $this->link);


        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error no existe inm_rel_comprador_com_cliente",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->inm_rel_comprador_cliente($inm_comprador_id, $this->link);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['inm_comprador_id']);
        $this->assertEquals(1,$resultado['com_cliente_id']);
        errores::$error = false;
    }

    public function test_integra_relacion_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $inm_comprador_id = 1;
        $link = $this->link;;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'GAFF770616J87';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '11';
        $registro_entrada['numero_com'] = '33445566';
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

        $resultado = $inm->integra_relacion_com_cliente($inm_comprador_id, $link, $registro_entrada);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_transacciones_posterior_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);

        $del = (new base_test())->del_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_rel_comprador_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $inm_comprador_upd = array();
        $inm_comprador_id = 1;
        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = new stdClass();
        $r_modifica->registro_actualizado->inm_comprador_es_segundo_credito = '';
        $r_modifica->registro_actualizado->inm_comprador_con_discapacidad = '';
        $r_modifica->registro_actualizado->inm_comprador_nombre = 'A';
        $r_modifica->registro_actualizado->inm_comprador_apellido_paterno = 'B';
        $r_modifica->registro_actualizado->inm_comprador_id = 1;
        $r_modifica->registro_actualizado->dp_calle_pertenece_id = 1;

        $resultado = $inm->transacciones_posterior_upd($inm_comprador_upd, $inm_comprador_id, $modelo_inm_comprador,
            $r_modifica);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_valida_r_modifica(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _base_comprador();
        //$inm = new liberator($inm);


        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = new stdClass();
        $r_modifica->registro_actualizado->inm_comprador_es_segundo_credito = '';
        $r_modifica->registro_actualizado->inm_comprador_con_discapacidad = '';

        $resultado = $inm->valida_r_modifica($r_modifica);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }





}

