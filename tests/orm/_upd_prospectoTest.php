<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_comprador;
use gamboamartin\inmuebles\models\_inm_prospecto;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\_upd_prospecto;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _upd_prospectoTest extends test {
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

    public function test_inm_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_conyuge(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_rel_conyuge_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $modelo = new _upd_prospecto();
        //$modelo = new liberator($modelo);

        $columnas_en_bruto = false;
        $inm_prospecto_id = 1;
        $link = $this->link;
        $retorno_obj = true;
        $resultado = $modelo->inm_conyuge($columnas_en_bruto, $inm_prospecto_id, $link, $retorno_obj);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_conyuge_id);

        errores::$error = false;
    }

    public function test_inserta_conyuge(): void
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

        $modelo = new _upd_prospecto();
        $modelo = new liberator($modelo);

        $conyuge = array();
        $inm_prospecto_id = 1;
        $conyuge['nombre'] = 'A';
        $conyuge['apellido_paterno'] = 'B';
        $conyuge['curp'] = 'XEXX010101MNEXXXA8';
        $conyuge['rfc'] = 'AAA020202AAA';
        $conyuge['dp_municipio_id'] = '1';
        $conyuge['inm_nacionalidad_id'] = '1';
        $conyuge['inm_ocupacion_id'] = '1';
        $conyuge['telefono_casa'] = '1234567891';
        $conyuge['telefono_celular'] = '1234567891';
        $conyuge['fecha_nacimiento'] = '2020-01-01';
        $resultado = $modelo->inserta_conyuge($conyuge, $inm_prospecto_id,$this->link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_modifica_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $modelo = new _upd_prospecto();
        $modelo = new liberator($modelo);

        $del = (new base_test())->del_inm_rel_conyuge_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_inm_rel_conyuge_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $inm_prospecto_id = 1;
        $link = $this->link;
        $conyuge = array();
        $resultado = $modelo->modifica_conyuge($conyuge, $inm_prospecto_id, $link);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }




}

