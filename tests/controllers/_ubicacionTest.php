<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\_ubicacion;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
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


class _ubicacionTest extends test {
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

    public function test_base_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $inm = new _ubicacion();
        //$inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $controler->registro_id = 1;

        $resultado = $inm->base_upd($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Mexico Jalisco San Pedro Tlaquepaque Residencial Revolución 45580   NUM EXT",$resultado->r_modifica->registro['inm_ubicacion_descripcion']);

        errores::$error = false;
    }

    public function test_base_view_accion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar',data:  $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_ubicacion(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al alta',data:  $alta);
            print_r($error);
            exit;
        }

        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $controler->registro_id = 1;

        $resultado = $inm->base_view_accion($controler, array());
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->r_modifica->registro['inm_ubicacion_id']);

        errores::$error = false;
    }

    public function test_base_view_accion_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        //$inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $funcion = 'z';
        $controler->inputs = new stdClass();

        $resultado = $inm->base_view_accion_data($controler, array(), $funcion);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->base_html->r_modifica->registro['inm_ubicacion_id']);

        errores::$error = false;
    }

    public function test_entidades_dp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $resultado = $inm->entidades_dp();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('dp_pais',$resultado[0]);
        $this->assertEquals('dp_estado',$resultado[1]);
        $this->assertEquals('dp_municipio',$resultado[2]);
        $this->assertEquals('dp_cp',$resultado[3]);
        $this->assertEquals('dp_colonia_postal',$resultado[4]);
        $this->assertEquals('dp_calle_pertenece',$resultado[5]);
        errores::$error = false;
    }

    public function test_get_id_preferido(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $data = new stdClass();
        $entidad = 'dp_calle';
        $modelo_preferido = new inm_ubicacion(link: $this->link);
        $resultado = $inm->get_id_preferido($data, $entidad, $modelo_preferido);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->dp_calle_id);
        errores::$error = false;
    }

    public function test_ids_pref_dp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $modelo_preferido = new inm_ubicacion(link: $this->link);


        $resultado = $inm->ids_pref_dp($modelo_preferido);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(151,$resultado->dp_pais_id);
        $this->assertEquals(14,$resultado->dp_estado_id);
        $this->assertEquals(1649,$resultado->dp_municipio_id);
        $this->assertEquals(2,$resultado->dp_cp_id);
        $this->assertEquals(23,$resultado->dp_colonia_postal_id);
        $this->assertEquals(1,$resultado->dp_calle_pertenece_id);
        errores::$error = false;
    }

    public function test_inputs_costo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        //$inm = new liberator($inm);

        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();
        $resultado = $inm->inputs_costo($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsString("<div class='control-group col-sm-12'><label class='control-label' for='fecha'>Fecha",$resultado->fecha);
        errores::$error = false;
    }

    public function test_integra_ids_preferidos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        //$inm = new liberator($inm);

        $data = new stdClass();
        $entidades = array();
        $modelo_preferido = new inm_ubicacion(link: $this->link);

        $entidades[] = 'dp_pais';
        $entidades[] = 'dp_cp';

        $resultado = $inm->integra_ids_preferidos($data, $entidades, $modelo_preferido);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(151,$resultado->dp_pais_id);
        $this->assertEquals(2,$resultado->dp_cp_id);
        errores::$error = false;
    }

    public function test_key_select_inm_tipo_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $inm_tipo_ubicacion_id = -1;
        $keys_selects = array();


        $resultado = $inm->key_select_inm_tipo_ubicacion($controler, $inm_tipo_ubicacion_id, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado['inm_tipo_ubicacion_id']->id_selected);
        errores::$error = false;
    }

    public function test_keys_selects(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $data_row = new stdClass();


        $resultado = $inm->keys_selects($controler, $data_row, array());
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(6,$resultado['dp_pais_id']->cols);

        errores::$error = false;
    }

    public function test_keys_selects_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        //$inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);

        $data_row = new stdClass();

        $resultado = $inm->keys_selects_base($controler, $data_row, array());
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado['dp_pais_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_estado_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_municipio_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_cp_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_colonia_postal_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_calle_pertenece_id']->id_selected);
        $this->assertEquals(-1,$resultado['inm_tipo_ubicacion_id']->id_selected);

        errores::$error = false;
    }

    public function test_keys_selects_view(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        $inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $data_row = new stdClass();


        $resultado = $inm->keys_selects_view($controler, $data_row, array());
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(-1,$resultado['dp_pais_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_estado_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_municipio_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_cp_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_colonia_postal_id']->id_selected);
        $this->assertEquals(-1,$resultado['dp_calle_pertenece_id']->id_selected);
        $this->assertEquals(-1,$resultado['inm_tipo_ubicacion_id']->id_selected);
        errores::$error = false;
    }

    public function test_init_alta(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $inm = new _ubicacion();
        //$inm = new liberator($inm);


        $controler = new controlador_inm_ubicacion(link: $this->link,paths_conf: $this->paths_conf);
        $controler->row_upd = new stdClass();

        $resultado = $inm->init_alta($controler, array());
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(151,$resultado['dp_pais_id']->id_selected);
        $this->assertEquals(14,$resultado['dp_estado_id']->id_selected);
        $this->assertEquals(1649,$resultado['dp_municipio_id']->id_selected);
        $this->assertEquals(2,$resultado['dp_cp_id']->id_selected);
        $this->assertEquals(23,$resultado['dp_colonia_postal_id']->id_selected);
        $this->assertEquals(1,$resultado['dp_calle_pertenece_id']->id_selected);
        $this->assertEquals(1,$resultado['inm_tipo_ubicacion_id']->id_selected);
        errores::$error = false;
    }

}

