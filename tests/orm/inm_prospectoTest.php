<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\comercial\models\com_prospecto;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_co_acreditado;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_rel_ubi_comp;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\proceso\models\pr_sub_proceso;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_prospectoTest extends test {
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


        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $modelo->registro['nombre'] = 'A';
        $modelo->registro['apellido_paterno'] = 'B';
        $modelo->registro['numero_com'] = 'C';
        $modelo->registro['lada_com'] = 'D';
        $modelo->registro['razon_social'] = 'E';
        $modelo->registro['com_agente_id'] = '1';
        $modelo->registro['com_tipo_prospecto_id'] = '1';

        $resultado = $modelo->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;

    }

    public function test_convierte_cliente(): void
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
        $del = (new base_test())->del_bn_cuenta(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_org_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_bn_cuenta(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $inm_prospecto_id = 1;
        $inm_prospecto_upd['cel_com'] = '1234567897';
        $inm_prospecto_upd['telefono_casa'] = '1234567897';
        $inm_prospecto_upd['correo_com'] = 'b@c.com';

        $modifica = $modelo->modifica_bd(registro: $inm_prospecto_upd,id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al modifica', data: $modifica);
            print_r($error);exit;
        }


        $resultado = $modelo->convierte_cliente($inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
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

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $id = 1;
        $resultado = $modelo->elimina_bd($id);

        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al eliminar",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $id = 1;
        $resultado = $modelo->elimina_bd($id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('sincorreo@correo.com',$resultado->registro['inm_prospecto_correo_empresa']);
        errores::$error = false;
    }

    public function test_existe_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $del = (new base_test())->del_inm_conyuge(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $inm_prospecto_id = 1;
        $resultado = $modelo->existe_conyuge($inm_prospecto_id);

        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);

        errores::$error = false;


    }

    public function test_get_com_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm_prospecto_id = 1;
        $resultado = $modelo->get_com_prospecto($inm_prospecto_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener inm_prospecto",$resultado['mensaje_limpio']);

        errores::$error = false;
        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }
        $resultado = $modelo->get_com_prospecto($inm_prospecto_id);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_tipo_agente_id']);
        errores::$error = false;
    }

    public function test_actualiza_descripcion(): void
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
        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $id = 1;
        $keys_integra_ds = array();
        $reactiva = false;
        $registro = new stdClass();
        $registro->nombre = 'A';
        $registro->apellido_paterno = 'N';
        $registro->nss = 'Q';
        $registro->curp = 'S';
        $registro->rfc = 'Z';
        $resultado = $modelo->actualiza_descripcion($id, $keys_integra_ds, $reactiva, $registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("A N  Q S Z",$resultado->registro_puro->descripcion);
        errores::$error = false;
    }

    public function test_ajusta_registro(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);


        $registro = new stdClass();
        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = new stdClass();
        $r_modifica->registro_actualizado->com_prospecto_rfc = '';
        $registro->nss = '';
        $registro->curp = '';
        $resultado = $modelo->ajusta_registro($r_modifica, $registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('99999999999',$resultado->nss);
        $this->assertEquals('XEXX010101HNEXXXA4',$resultado->curp);
        $this->assertEquals('',$resultado->rfc);
        errores::$error = false;
    }

    public function test_data_com_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);


        $registro = new stdClass();

        $resultado = $modelo->data_com_prospecto($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_inm_prospecto_proceso_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $inm_prospecto_id = 1;
        $pr_sub_proceso_id = 1;
        $resultado = $modelo->inm_prospecto_proceso_ins($inm_prospecto_id, $pr_sub_proceso_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['pr_sub_proceso_id']);
        $this->assertEquals(1,$resultado['inm_prospecto_id']);
        $this->assertEquals(date('Y-m-d'),$resultado['fecha']);

        errores::$error = false;
    }



    public function test_inserta_sub_proceso(): void
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


        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_prospecto_proceso(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $inm_prospecto_id = 1;
        $resultado = $modelo->inserta_sub_proceso($inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
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


        $modelo = new inm_prospecto(link: $this->link);
        //$modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }



        $id = 1;

        $registro = array();
        $registro['nombre'] = 'ABC';

        $resultado = $modelo->modifica_bd($registro, $id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        $registro = $modelo->registro(registro_id: $id);
        $this->assertStringContainsStringIgnoringCase('ABC AP1',$registro['inm_prospecto_descripcion']);
        $this->assertStringContainsStringIgnoringCase('ABC',$registro['com_prospecto_nombre']);


        errores::$error = false;
    }

    public function test_modifica_com_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $registro = (new inm_prospecto(link: $this->link))->registro(registro_id: 1, columnas_en_bruto: true,
            retorno_obj: true);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al registro', data: $registro);
            print_r($error);exit;
        }

        $resultado = $modelo->modifica_com_prospecto($registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('NOMBRE 1 AP1',$resultado->registro_actualizado->com_prospecto_codigo);
        errores::$error = false;
    }

    public function test_post_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $registro = (new inm_prospecto(link: $this->link))->registro(registro_id: 1, columnas_en_bruto: true,
            retorno_obj: true);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al registro', data: $registro);
            print_r($error);exit;
        }

        $registro_actualizado = (new inm_prospecto(link: $this->link))->registro(registro_id: 1,
            retorno_obj: true);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al registro', data: $registro);
            print_r($error);exit;
        }

        $id = 1;
        $keys_integra_ds = array();
        $reactiva = true;

        $r_modifica = new stdClass();
        $r_modifica->registro_actualizado = $registro_actualizado;
        $r_modifica->registro_puro = $registro;

        $resultado = $modelo->post_upd($id, $keys_integra_ds, $r_modifica, $reactiva);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('NOMBRE 1 AP1',$resultado->upd_com_prospecto->registro_actualizado->com_prospecto_codigo);
        errores::$error = false;
    }

    public function test_pr_sub_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);


        $resultado = $modelo->pr_sub_proceso();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('ALTA PROSPECTO',$resultado['pr_sub_proceso_descripcion']);
        $this->assertEquals('INMOBILIARIA PROSPECTOS',$resultado['pr_proceso_descripcion']);
        $this->assertEquals('inm_prospecto',$resultado['adm_seccion_descripcion']);

        errores::$error = false;
    }

    public function test_transacciones_externas(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new inm_prospecto(link: $this->link);
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }


        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $registro = (new inm_prospecto(link: $this->link))->registro(registro_id: 1, columnas_en_bruto: true,
            retorno_obj: true);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al registro', data: $registro);
            print_r($error);exit;
        }

        $id = 1;
        $keys_integra_ds = array();
        $reactiva = true;

        $resultado = $modelo->transacciones_externas($id, $keys_integra_ds, $reactiva, $registro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }





}

