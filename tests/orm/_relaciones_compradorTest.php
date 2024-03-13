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
use gamboamartin\inmuebles\models\_relaciones_comprador;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _relaciones_compradorTest extends test {
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

    public function test_aplica_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $inm_co_acreditado_ins = array();
        $inm_co_acreditado_ins[]  ='x';


        $resultado = $inm->aplica_alta($inm_co_acreditado_ins);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }


    public function test_asigna_campo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $registro = array();
        $key_co_acreditado = 'b';
        $inm_co_acreditado_ins = array();
        $campo_co_acreditado = 'a';
        $registro['b'] = 'p';
        $resultado = $inm->asigna_campo($campo_co_acreditado, $inm_co_acreditado_ins, $key_co_acreditado, $registro);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('p',$resultado['a']);
        errores::$error = false;

    }

    public function test_data_relacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $co_acreditados = array();
        $co_acreditados[] = '';

        $resultado = $inm->data_relacion(indice: 1, relaciones: $co_acreditados);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado->existe_relacion);
        errores::$error = false;
    }

    public function test_get_data_relacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $inm_comprador_id = 1;
        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        $resultado = $inm->get_data_relacion('co_acreditado', 1, $inm_comprador_id, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_inm_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        //$inm = new liberator($inm);


        $registro = array();
        $registro['inm_co_acreditado_nss'] = '1';

        $keys = array('nss','curp','rfc', 'apellido_paterno','apellido_materno','nombre', 'lada',
            'numero','celular','correo','genero','nombre_empresa_patron','nrp','lada_nep','numero_nep');

        $resultado = $inm->inm_ins(entidad: 'inm_co_acreditado',indice: -1,inm_comprador_id: 1,keys:  $keys,
            registro:  $registro);
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


        $inm = new _relaciones_comprador();
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

        $inm = new _relaciones_comprador();
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

    public function test_inserta_data_referencia(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_referencia(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $inm_referencia_ins = array();
        $inm_referencia_ins['nombre'] = 'A';
        $inm_referencia_ins['apellido_paterno'] = 'A';
        $inm_referencia_ins['inm_comprador_id'] = '1';
        $inm_referencia_ins['lada'] = '12';
        $inm_referencia_ins['numero'] = '2345678';
        $inm_referencia_ins['celular'] = '1234567890';
        $inm_referencia_ins['dp_calle_pertenece_id'] = '1';
        $inm_referencia_ins['numero_dom'] = '1';
        $inm_referencia_ins['inm_parentesco_id'] = '1';
        $link = $this->link;


        $resultado = $inm->inserta_data_referencia($inm_referencia_ins, $link);
       // print_r($resultado);exit;

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Registro insertado con éxito',$resultado->alta_inm_referencia->mensaje);
        errores::$error = false;
    }

    public function test_integra_campo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $registro = array();
        $key_co_acreditado = 'z';
        $inm_co_acreditado_ins = array();
        $campo_co_acreditado = 'd';
        $registro['z'] = 'FF';
        $resultado = $inm->integra_campo($campo_co_acreditado, $inm_co_acreditado_ins,
            $key_co_acreditado, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('FF',$resultado['d']);
        errores::$error = false;
    }

    public function test_integra_value(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $registro = array();
        $inm_co_acreditado_ins = array();
        $campo_co_acreditado = 'd';
        $registro['z'] = 'FF';

        $resultado = $inm->integra_value(campo: $campo_co_acreditado,entidad:  'inm_co_acreditado', indice: 1,
            inm_ins: $inm_co_acreditado_ins, registro: $registro);

        $this->assertIsArray($resultado);
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


        $inm = new _relaciones_comprador();
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

    public function test_transacciones_referencia(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        //$inm = new liberator($inm);


        $inm_referencia_ins = array();
        $inm_referencia_ins['nombre'] = 'Z';
        $inm_referencia_ins['apellido_paterno'] = 'Y';
        $inm_referencia_ins['inm_comprador_id'] = 1;
        $inm_referencia_ins['dp_calle_pertenece_id'] = 1;
        $inm_referencia_ins['lada'] = 123;
        $inm_referencia_ins['numero'] = 12345678;
        $inm_referencia_ins['celular'] = 1234567890;
        $inm_referencia_ins['numero_dom'] = 1;
        $inm_referencia_ins['inm_parentesco_id'] = 1;
        $indice = 1;
        $inm_comprador_id = 1;
        $modelo_inm_comprador = new inm_comprador(link: $this->link);

        $del = (new base_test())->del_inm_referencia(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $resultado = $inm->transacciones_referencia($indice, $inm_referencia_ins, $inm_comprador_id, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->data_referencia->existe_relacion);

        errores::$error = false;


        $resultado = $inm->transacciones_referencia($indice, $inm_referencia_ins, $inm_comprador_id, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado->data_referencia->existe_relacion);

        errores::$error = false;

    }



    public function test_valida_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _relaciones_comprador();
        $inm = new liberator($inm);


        $registro = array();
        $key_co_acreditado = 'z';
        $campo_co_acreditado = 'd';
        $registro['z'] = 'FF';
        $resultado = $inm->valida_data($campo_co_acreditado, $key_co_acreditado, $registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }



}

