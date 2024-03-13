<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\_referencias;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _inm_referenciasTest extends test {
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

    public function test_operaciones_referencia(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_parentesco(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al del',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_parentesco(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $inm = new _referencias();
        //$inm = new liberator($inm);

        $indice = 1;
        $inm_comprador_id = 1;
        $inm_comprador_upd = array();

        $modelo_inm_comprador = new inm_comprador(link: $this->link);
        $resultado = $inm->operaciones_referencia($indice, $inm_comprador_id, $inm_comprador_upd, $modelo_inm_comprador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->aplica_alta_referencia);


        errores::$error = false;

        $del = (new base_test())->del_inm_referencia(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $indice = 1;
        $inm_comprador_id = 1;
        $inm_comprador_upd = array();

        $inm_comprador_upd['inm_referencia_apellido_paterno_1'] = '12';
        $inm_comprador_upd['inm_referencia_nombre_1'] = '12';
        $inm_comprador_upd['inm_referencia_dp_calle_pertenece_id_1'] = '1';
        $inm_comprador_upd['inm_referencia_lada_1'] = '12';
        $inm_comprador_upd['inm_referencia_numero_1'] = '12345678';
        $inm_comprador_upd['inm_referencia_celular_1'] = '1234567890';
        $inm_comprador_upd['inm_referencia_numero_dom_1'] = '12';
        $inm_comprador_upd['inm_referencia_telefono_1'] = '12345678';
        $inm_comprador_upd['inm_referencia_inm_parentesco_id_1'] = '1';

        $resultado = $inm->operaciones_referencia($indice, $inm_comprador_id, $inm_comprador_upd, $modelo_inm_comprador);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado->aplica_alta_referencia);
        $this->assertNotTrue( $resultado->data_referencia->data_referencia->existe_relacion);
        errores::$error = false;
    }







}

