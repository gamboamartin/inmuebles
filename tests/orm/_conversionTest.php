<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_conversion;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\_prospecto;
use gamboamartin\inmuebles\models\_referencias;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _conversionTest extends test {
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

    public function test_data_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $inm_prospecto_id = 1;
        $modelo = new inm_prospecto(link: $this->link);
        $resultado = $conversion->data_prospecto($inm_prospecto_id, $modelo);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6,$resultado->inm_prospecto->inm_producto_infonavit_id);

        errores::$error = false;
    }
    public function test_defaults_alta_comprador(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $inm_comprador_ins = array();
        $resultado = $conversion->defaults_alta_comprador($inm_comprador_ins);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('XEXX010101MNEXXXA8',$resultado['curp']);

        errores::$error = false;
    }

    public function test_inm_comprador_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $data = new stdClass();
        $link = $this->link;
        $data->inm_prospecto = new stdClass();
        $data->inm_prospecto->inm_producto_infonavit_id = 1;
        $data->inm_prospecto->inm_attr_tipo_credito_id = 1;
        $data->inm_prospecto->inm_destino_credito_id = 1;
        $data->inm_prospecto->es_segundo_credito = 1;
        $data->inm_prospecto->inm_plazo_credito_sc_id = 1;
        $data->inm_prospecto->descuento_pension_alimenticia_dh = 1;
        $data->inm_prospecto->descuento_pension_alimenticia_fc = 1;
        $data->inm_prospecto->monto_credito_solicitado_dh = 1;
        $data->inm_prospecto->monto_ahorro_voluntario = 1;
        $data->inm_prospecto->curp = 1;
        $data->inm_prospecto->nss = 1;
        $data->inm_prospecto->nombre = 1;
        $data->inm_prospecto->apellido_paterno = 1;
        $data->inm_prospecto->apellido_materno = 1;
        $data->inm_prospecto->con_discapacidad = 1;
        $data->inm_prospecto->nombre_empresa_patron = 1;
        $data->inm_prospecto->nrp_nep = 1;
        $data->inm_prospecto->lada_nep = 1;
        $data->inm_prospecto->numero_nep = 1;
        $data->inm_prospecto->extension_nep = 1;
        $data->inm_prospecto->lada_com = 1;
        $data->inm_prospecto->numero_com = 1;
        $data->inm_prospecto->cel_com = 1;
        $data->inm_prospecto->genero = 1;
        $data->inm_prospecto->correo_com = 1;
        $data->inm_prospecto->inm_tipo_discapacidad_id = 1;
        $data->inm_prospecto->inm_persona_discapacidad_id = 1;
        $data->inm_prospecto->inm_estado_civil_id = 1;
        $data->inm_prospecto->inm_institucion_hipotecaria_id = 1;
        $data->inm_prospecto->inm_sindicato_id = 1;
        $data->inm_prospecto->dp_municipio_nacimiento_id = 1;
        $data->inm_prospecto->fecha_nacimiento = 1;
        $data->inm_prospecto->sub_cuenta = 1;
        $data->inm_prospecto->monto_final = 1;
        $data->inm_prospecto->descuento = 1;
        $data->inm_prospecto->puntos = 1;
        $data->inm_prospecto->inm_nacionalidad_id = 1;
        $data->inm_prospecto->inm_ocupacion_id = 1;
        $data->inm_prospecto->telefono_casa = 1;
        $data->inm_prospecto->correo_empresa = 1;
        $data->inm_prospecto->dp_calle_pertenece_id = 1;
        $data->inm_prospecto_completo = new stdClass();
        $data->inm_prospecto_completo->com_prospecto_rfc = '';
        $resultado = $conversion->inm_comprador_ins($data, $link);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['inm_tipo_discapacidad_id']);
        $this->assertEquals('1',$resultado['telefono_casa']);

        errores::$error = false;
    }

    public function test_inm_comprador_ins_init(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $data = new stdClass();
        $keys = array();
        $keys[] = 'a';
        $data->inm_prospecto = new stdClass();
        $data->inm_prospecto->a = 'x';
        $resultado = $conversion->inm_comprador_ins_init($data, $keys);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('x',$resultado['a']);

        errores::$error = false;
    }

    public function test_inm_rel_prospecto_cliente_ins(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $inm_comprador_id = 1;
        $inm_prospecto_id = 1;
        $resultado = $conversion->inm_rel_prospecto_cliente_ins($inm_comprador_id, $inm_prospecto_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['inm_prospecto_id']);
        $this->assertEquals('1',$resultado['inm_comprador_id']);

        errores::$error = false;
    }

    public function test_inserta_inm_comprador(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $conversion = new _conversion();
        //$conversion = new liberator($conversion);

        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }


        $inm_prospecto_upd['cel_com'] = '1234567890';
        $inm_prospecto_upd['correo_com'] = 'a@com.com';
        $inm_prospecto_upd['telefono_casa'] = '1234567890';
        $inm_prospecto_upd['correo_empresa'] = 'a@com.com';

        $modifica = (new inm_prospecto(link: $this->link))->modifica_bd(registro: $inm_prospecto_upd,id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al modificar prospecto',data:  $modifica);
            print_r($error);
            exit;
        }


        $inm_prospecto_id = 1;
        $modelo = new inm_prospecto(link: $this->link);
        $resultado = $conversion->inserta_inm_comprador($inm_prospecto_id, $modelo);
        //print_r($resultado);exit;
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(0,$resultado->registro['inm_comprador_descuento_pension_alimenticia_dh']);

        errores::$error = false;
    }

    public function test_inserta_rel_prospecto_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_rel_prospecto_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }

        $conversion = new _conversion();
        //$conversion = new liberator($conversion);

        $inm_comprador_id = 1;
        $inm_prospecto_id = 1;
        $resultado = $conversion->inserta_rel_prospecto_cliente($inm_comprador_id, $inm_prospecto_id, $this->link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('NO',$resultado->registro['inm_prospecto_es_segundo_credito']);

        errores::$error = false;
    }

    public function test_integra_id_pref(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $entidad = 'inm_sindicato';
        $inm_comprador_ins = array();
        $modelo = new inm_comprador(link: $this->link);
        $resultado = $conversion->integra_id_pref($entidad, $inm_comprador_ins, $modelo);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1',$resultado['inm_sindicato_id']);

        errores::$error = false;
    }

    public function test_integra_ids_prefs(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }


        $alta = (new base_test())->alta_inm_comprador(link: $this->link, id: 3);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $inm_comprador_ins = array();
        $link = $this->link;
        $resultado = $conversion->integra_ids_prefs($inm_comprador_ins, $link);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(99,$resultado['cat_sat_forma_pago_id']);

        errores::$error = false;
    }

    public function test_keys_data_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';



        $conversion = new _conversion();
        $conversion = new liberator($conversion);


        $resultado = $conversion->keys_data_prospecto();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('fecha_nacimiento',$resultado[31]);

        errores::$error = false;
    }

    /**
     * Validates a key using the "valida_key" method of the _conversion class.
     *
     * This method performs the following steps:
     * 1. Sets the errores::$error property to false.
     * 2. Creates an instance of the _conversion class.
     * 3. Calls the "valida_key" method of the _conversion instance with a given key.
     * 4. Asserts that the returned value is a boolean.
     * 5. Asserts that errores::$error is not true.
     * 6. Asserts that the returned value is true.
     * 7. Sets the errores::$error property to false.
     *
     * @return void
     */
    public function test_valida_key(): void
    {
        errores::$error = false;


        $conversion = new _conversion();
        $conversion = new liberator($conversion);

        $key = 'a';
        $resultado = $conversion->valida_key($key);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;
    }



}

