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
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
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

