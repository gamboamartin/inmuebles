<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_alta_comprador;
use gamboamartin\inmuebles\models\_base_comprador;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_comprador_etapa;
use gamboamartin\inmuebles\models\inm_comprador_proceso;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _alta_compradorTest extends test {
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

    public function test_default_infonavit(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $resultado = $inm->default_infonavit($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('D',$resultado['nombre']);
        $this->assertEquals('D',$resultado['apellido_paterno']);
        $this->assertEquals('D',$resultado['nss']);
        $this->assertEquals('D',$resultado['curp']);
        $this->assertEquals('D',$resultado['rfc']);

        $this->assertEquals(7,$resultado['inm_plazo_credito_sc_id']);
        $this->assertEquals(5,$resultado['inm_tipo_discapacidad_id']);
        $this->assertEquals(6,$resultado['inm_persona_discapacidad_id']);
        errores::$error = false;
    }

    public function test_filtro_etapa_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
         $inm = new liberator($inm);


        $accion = 'a';
        $etapa = 'b';
        $pr_proceso_descripcion = 'c';
        $tabla = 'd';
        $resultado = $inm->filtro_etapa_proceso($accion, $etapa, $pr_proceso_descripcion, $tabla);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('d',$resultado['adm_seccion.descripcion']);
        $this->assertEquals('a',$resultado['adm_accion.descripcion']);
        $this->assertEquals('b',$resultado['pr_etapa.descripcion']);
        $this->assertEquals('c',$resultado['pr_proceso.descripcion']);
        errores::$error = false;
    }

    public function test_init_row_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        //$inm = new liberator($inm);

        $modelo = new inm_comprador(link: $this->link);

        $registro = array();
        $registro['nombre'] = 'A';
        $registro['apellido_paterno'] = 'B';
        $registro['nss'] = '5566755443';
        $registro['curp'] = 'XEXX010101MNEXXXA8';
        $registro['rfc'] = 'GAF660911675';
        $registro['lada_nep'] = '123';
        $registro['numero_nep'] = '1235434';
        $registro['lada_com'] = '43';
        $registro['numero_com'] = '43554433';

        $resultado = $inm->init_row_alta($modelo, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('A',$resultado['nombre']);
        $this->assertEquals('B',$resultado['apellido_paterno']);
        $this->assertEquals('5566755443',$resultado['nss']);
        $this->assertEquals('XEXX010101MNEXXXA8',$resultado['curp']);
        $this->assertEquals(7,$resultado['inm_plazo_credito_sc_id']);
        errores::$error = false;
    }

    public function test_inm_comprador_etapa_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $accion = 'a';
        $etapa = 'b';
        $inm_comprador_id= -1;
        $link= $this->link;
        $pr_proceso_descripcion= 'c';
        $tabla= 'd';

        $resultado = $inm->inm_comprador_etapa_alta($accion, $etapa, $inm_comprador_id, $link, $pr_proceso_descripcion,
            $tabla);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals('Error inm_comprador_id debe ser mayor a 0',$resultado['mensaje_limpio']);
        errores::$error = false;

        $accion = 'alta_bd';
        $etapa = 'b';
        $inm_comprador_id= -1;
        $link= $this->link;
        $pr_proceso_descripcion= 'c';
        $tabla= 'd';

        $resultado = $inm->inm_comprador_etapa_alta($accion, $etapa, $inm_comprador_id, $link, $pr_proceso_descripcion,
            $tabla);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals('Error inm_comprador_id debe ser mayor a 0',$resultado['mensaje_limpio']);
        errores::$error = false;

        $accion = 'alta_bd';
        $etapa = 'ALTA';
        $inm_comprador_id= -1;
        $link= $this->link;
        $pr_proceso_descripcion= 'c';
        $tabla= 'd';

        $resultado = $inm->inm_comprador_etapa_alta($accion, $etapa, $inm_comprador_id, $link, $pr_proceso_descripcion,
            $tabla);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals('Error inm_comprador_id debe ser mayor a 0',$resultado['mensaje_limpio']);


        errores::$error = false;

        $accion = 'alta_bd';
        $etapa = 'ALTA';
        $inm_comprador_id= -1;
        $link= $this->link;
        $pr_proceso_descripcion= 'INMOBILIARIA CLIENTES';
        $tabla= 'd';

        $resultado = $inm->inm_comprador_etapa_alta($accion, $etapa, $inm_comprador_id, $link, $pr_proceso_descripcion,
            $tabla);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals('Error inm_comprador_id debe ser mayor a 0',$resultado['mensaje_limpio']);

        errores::$error = false;

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_comprador_etapa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $accion = 'alta_bd';
        $etapa = 'ALTA';
        $inm_comprador_id= 1;
        $link= $this->link;
        $pr_proceso_descripcion= 'INMOBILIARIA CLIENTES';
        $tabla= 'inm_comprador';

        $resultado = $inm->inm_comprador_etapa_alta($accion, $etapa, $inm_comprador_id, $link, $pr_proceso_descripcion,
            $tabla);


        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Registro insertado con éxito',$resultado->mensaje);
        errores::$error = false;
    }

    public function test_inm_comprador_etapa_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $inm_comprador_id = 1;
        $pr_etapa_proceso = array();
        $pr_etapa_proceso['pr_etapa_proceso_id'] = 1;

        $resultado = $inm->inm_comprador_etapa_ins($inm_comprador_id, $pr_etapa_proceso);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['pr_etapa_proceso_id']);
        $this->assertEquals('1',$resultado['inm_comprador_id']);
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

        $inm = new _alta_comprador();
        $inm = new liberator($inm);

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);;
            exit;
        }

        $del = (new base_test())->del_inm_comprador_proceso(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);;
            exit;
        }

        $link = $this->link;
        $inm_comprador_id = 1;
        $pr_sub_proceso_id = 2;
        $resultado = $inm->inserta_sub_proceso($inm_comprador_id, $link, $pr_sub_proceso_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('ALTA',$resultado->registro['pr_sub_proceso_descripcion']);
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

        $inm = new _alta_comprador();
        $inm = new liberator($inm);

        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $resultado = $inm->integra_descripcion($registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('D',$resultado['nombre']);
        $this->assertEquals('D',$resultado['apellido_paterno']);
        $this->assertEquals('D',$resultado['nss']);
        $this->assertEquals('D',$resultado['curp']);
        $this->assertEquals('D',$resultado['rfc']);
        $this->assertStringContainsStringIgnoringCase('D D  D D D 2024-05-',$resultado['descripcion']);

        errores::$error = false;
    }

    public function test_numero_completo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $key_lada = 'a';
        $key_numero = 'z';

        $registro['a'] = '111';
        $registro['z'] = '1111111';

        $resultado = $inm->numero_completo($key_lada, $key_numero, $registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1111111111',$resultado);

        errores::$error = false;
    }

    public function test_numero_completo_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $key_lada = 'a';
        $key_numero = 'b';
        $registro['a'] = '123';
        $registro['b'] = '12345676';
        $resultado = $inm->numero_completo_base($key_lada, $key_numero, $registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

    }

    public function test_numero_completo_com(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['lada_com'] = '11';
        $registro['numero_com'] = '11223344';

        $resultado = $inm->numero_completo_com($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1111223344',$resultado);
        errores::$error = false;
    }

    public function test_numero_completo_nep(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['nombre'] = 'D';
        $registro['apellido_paterno'] = 'D';
        $registro['nss'] = 'D';
        $registro['curp'] = 'D';
        $registro['rfc'] = 'D';
        $registro['lada_nep'] = '012';
        $registro['numero_nep'] = '0156789';
        $resultado = $inm->numero_completo_nep($registro);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('0120156789',$resultado);
        errores::$error = false;
    }

    public function test_posterior_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
       // $inm = new liberator($inm);



        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_com_cliente(link: $this->link);
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

        $del = (new base_test())->del_inm_comprador_proceso(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_comprador_etapa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $link = $this->link;
        $inm_comprador_id = 1;
        $registro_entrada = array();
        $tabla = 'inm_comprador';
        $registro_entrada['rfc'] = 'AAA010101AAA';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = '1';
        $registro_entrada['lada_com'] = '111';
        $registro_entrada['numero_com'] = '1234567';
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
        $resultado = $inm->posterior_alta(accion: 'alta_bd', etapa: 'ALTA', inm_comprador_id: $inm_comprador_id,
            link: $link, pr_proceso_descripcion: 'INMOBILIARIA CLIENTES', registro_entrada: $registro_entrada,
            tabla: $tabla);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertIsObject($resultado->integra_relacion_com_cliente);

        $filtro['inm_comprador.id'] = 1;
        $r_inm_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro:$filtro);
        $this->assertIsObject($r_inm_rel_comprador_com_cliente);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$r_inm_rel_comprador_com_cliente->registros[0]['inm_comprador_id']);

        $com_cliente_id = $r_inm_rel_comprador_com_cliente->registros[0]['com_cliente_id'];

        $r_com_cliente = (new com_cliente(link: $this->link))->registro(registro_id: $com_cliente_id);

        $this->assertIsArray($r_com_cliente);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("AAA010101AAA",$r_com_cliente['com_cliente_rfc']);

        $filtro['inm_comprador.id'] = 1;
        $r_inm_comprador_proceso = (new inm_comprador_proceso(link: $this->link))->filtro_and(filtro:$filtro);
        $this->assertIsObject($r_inm_comprador_proceso);
        $this->assertNotTrue(errores::$error);
        $this->assertGreaterThanOrEqual(1,$r_inm_comprador_proceso->n_registros);

        $filtro['inm_comprador.id'] = 1;
        $r_inm_comprador_etapa = (new inm_comprador_etapa(link: $this->link))->filtro_and(filtro:$filtro);
        $this->assertIsObject($r_inm_comprador_etapa);
        $this->assertNotTrue(errores::$error);
        $this->assertGreaterThanOrEqual(1,$r_inm_comprador_etapa->n_registros);


        errores::$error = false;
    }

    public function test_pr_etapa_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $accion = 'alta_bd';
        $etapa = 'ALTA';
        $link = $this->link;
        $pr_proceso_descripcion = 'INMOBILIARIA CLIENTES';
        $tabla = 'inm_comprador';

        $resultado = $inm->pr_etapa_proceso($accion, $etapa, $link, $pr_proceso_descripcion, $tabla);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("ALTA",$resultado['pr_etapa_descripcion']);


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

        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $link = $this->link;
        $pr_proceso_descripcion = 'INMOBILIARIA CLIENTES';
        $pr_sub_proceso_descripcion = 'ALTA';
        $tabla = 'inm_comprador';
        $resultado = $inm->pr_sub_proceso($link, $pr_proceso_descripcion, $pr_sub_proceso_descripcion, $tabla);
        $this->assertIsaRRAY($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('ALTA',$resultado['pr_sub_proceso_descripcion']);
        errores::$error = false;
    }

    public function test_sub_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador_proceso(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }


        $inm = new _alta_comprador();
        $inm = new liberator($inm);


        $link = $this->link;
        $inm_comprador_id = 1;
        $pr_sub_proceso_descripcion = 'ALTA';
        $pr_proceso_descripcion = 'INMOBILIARIA CLIENTES';
        $tabla = 'inm_comprador';
        $resultado = $inm->sub_proceso($inm_comprador_id, $link, $pr_proceso_descripcion, $pr_sub_proceso_descripcion, $tabla);
       // print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_valida_base_comprador(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $registro = array();
        $registro['lada_nep'] = '12';
        $registro['numero_nep'] = '12456677';
        $registro['lada_com'] = '333';
        $registro['numero_com'] = '5678902';
        $registro['rfc'] = 'GAVM830930876';

        $resultado = $inm->valida_base_comprador($registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_data_etapa(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);

        $accion = '';
        $etapa = '';
        $pr_proceso_descripcion = '';
        $tabla = '';

        $resultado = $inm->valida_data_etapa($accion, $etapa, $pr_proceso_descripcion, $tabla);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error accion esta vacia",$resultado['mensaje_limpio']);
        errores::$error = false;

        $accion = 'a';
        $etapa = '';
        $pr_proceso_descripcion = '';
        $tabla = '';

        $resultado = $inm->valida_data_etapa($accion, $etapa, $pr_proceso_descripcion, $tabla);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error etapa esta vacia",$resultado['mensaje_limpio']);
        errores::$error = false;

        $accion = 'a';
        $etapa = 'b';
        $pr_proceso_descripcion = '';
        $tabla = '';

        $resultado = $inm->valida_data_etapa($accion, $etapa, $pr_proceso_descripcion, $tabla);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error pr_proceso_descripcion esta vacia",$resultado['mensaje_limpio']);

        errores::$error = false;
        $accion = 'a';
        $etapa = 'b';
        $pr_proceso_descripcion = 'c';
        $tabla = '';

        $resultado = $inm->valida_data_etapa($accion, $etapa, $pr_proceso_descripcion, $tabla);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error tabla esta vacia",$resultado['mensaje_limpio']);
        errores::$error = false;

        $accion = 'a';
        $etapa = 'b';
        $pr_proceso_descripcion = 'c';
        $tabla = 'd';

        $resultado = $inm->valida_data_etapa($accion, $etapa, $pr_proceso_descripcion, $tabla);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_sub_proceso(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        $inm = new liberator($inm);



        $pr_proceso_descripcion = 'a';
        $pr_sub_proceso_descripcion = 'V';
        $tabla = 'D';


        $resultado = $inm->valida_sub_proceso($pr_proceso_descripcion, $pr_sub_proceso_descripcion, $tabla);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_transacciones(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new _alta_comprador();
        //$inm = new liberator($inm);



        $inm_comprador_id = -1;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = 'A';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = 'A';
        $registro_entrada['numero_com'] = 'A';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = 'A';
        $registro_entrada['cat_sat_moneda_id'] = 'A';
        $registro_entrada['cat_sat_forma_pago_id'] = 'A';
        $registro_entrada['cat_sat_metodo_pago_id'] = 'A';
        $registro_entrada['cat_sat_uso_cfdi_id'] = 'A';
        $registro_entrada['com_tipo_cliente_id'] = 'A';
        $registro_entrada['cat_sat_tipo_persona_id'] = 'A';


        $resultado = $inm->valida_transacciones($inm_comprador_id, $registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al validar registro_entrada",$resultado['mensaje_limpio']);
        errores::$error = false;

        $inm_comprador_id = -1;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = 'A';
        $registro_entrada['numero_com'] = 'A';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';


        $resultado = $inm->valida_transacciones($inm_comprador_id, $registro_entrada);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al validar registro_entrada",$resultado['mensaje_limpio']);
        errores::$error = false;

        $inm_comprador_id = -1;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '123';
        $registro_entrada['numero_com'] = '1234567';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';


        $resultado = $inm->valida_transacciones($inm_comprador_id, $registro_entrada);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al validar registro_entrada",$resultado['mensaje_limpio']);
        errores::$error = false;

        $inm_comprador_id = -1;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '123';
        $registro_entrada['numero_com'] = '1234567';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['nombre'] = '1';
        $registro_entrada['apellido_paterno'] = '1';


        $resultado = $inm->valida_transacciones($inm_comprador_id, $registro_entrada);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al validar registro_entrada",$resultado['mensaje_limpio']);
        errores::$error = false;

        $inm_comprador_id = 1;
        $registro_entrada = array();
        $registro_entrada['rfc'] = 'A';
        $registro_entrada['dp_calle_pertenece_id'] = '1';
        $registro_entrada['numero_exterior'] = 'A';
        $registro_entrada['lada_com'] = '123';
        $registro_entrada['numero_com'] = '1234567';
        $registro_entrada['cat_sat_regimen_fiscal_id'] = '1';
        $registro_entrada['cat_sat_moneda_id'] = '1';
        $registro_entrada['cat_sat_forma_pago_id'] = '1';
        $registro_entrada['cat_sat_metodo_pago_id'] = '1';
        $registro_entrada['cat_sat_uso_cfdi_id'] = '1';
        $registro_entrada['com_tipo_cliente_id'] = '1';
        $registro_entrada['cat_sat_tipo_persona_id'] = '1';
        $registro_entrada['nombre'] = '1';
        $registro_entrada['apellido_paterno'] = '1';
        $registro_entrada['cp'] = '1';
        $registro_entrada['dp_municipio_id'] = '1';


        $resultado = $inm->valida_transacciones($inm_comprador_id, $registro_entrada);

        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;

    }

}

