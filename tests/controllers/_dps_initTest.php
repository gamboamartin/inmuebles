<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\comercial\models\com_cliente;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_dps_init;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _dps_initTest extends test {
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



    public function test_dps_init_ids(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $dps = new _dps_init();
        $dps = new liberator($dps);

        $del = (new base_test())->del_com_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al del', data: $del);
            print_r($error);exit;
        }

        $alta = (new base_test())->alta_com_cliente(link: $this->link,dp_calle_pertenece_id: 1);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al alta', data: $alta);
            print_r($error);exit;
        }

        $dp_municipio_id = (new com_cliente(link: $this->link))->id_preferido_detalle(entidad_preferida: 'dp_municipio');
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al obtener municipio', data: $dp_municipio_id);
            print_r($error);
            exit;
        }





        $row_upd = new stdClass();
        $modelo = new com_cliente(link: $this->link);
        $resultado = $dps->dps_init_ids($modelo, $row_upd);

        //print_r($resultado);exit;

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(151,$resultado->dp_pais_id);
        $this->assertEquals(14,$resultado->dp_estado_id);
        $this->assertEquals($dp_municipio_id,$resultado->dp_municipio_id);
        errores::$error = false;


    }

    public function test_key_con_descripcion(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $dps = new _dps_init();
        $dps = new liberator($dps);

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $entidad = 'a';
        $keys_selects = array();
        $row_upd = new stdClass();
        $label = '';
        $resultado = $dps->key_con_descripcion($controler, $entidad, $keys_selects, $label, $row_upd);
        //print_r($resultado);exit;
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('a_descripcion',$resultado['a_id']->columns_ds[0]);
        errores::$error = false;
    }

    public function test_ks_dp(): void
    {
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';
        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        errores::$error = false;

        $dps = new _dps_init();
        //$dps = new liberator($dps);

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_org_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }
        $del = (new base_test())->del_inm_prospecto(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al eliminar', data: $del);
            print_r($error);
            exit;
        }

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje: 'Error al insertar', data: $alta);
            print_r($error);
            exit;
        }


        $controler = new controlador_inm_comprador(link: $this->link, paths_conf: $this->paths_conf);
        $keys_selects = array();
        $row_upd = new stdClass();
        $resultado = $dps->ks_dp($controler, $keys_selects, $row_upd);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('dp_pais_descripcion',$resultado['dp_pais_id']->columns_ds[0]);
        $this->assertEquals(151,$resultado['dp_estado_id']->filtro['dp_pais.id']);
        $this->assertEquals('Municipio',$resultado['dp_municipio_id']->label);
        $this->assertTrue($resultado['dp_cp_id']->con_registros);
        //$this->assertEquals(6,$resultado['dp_colonia_postal_id']->cols);
        //$this->assertEquals($dp_calle_pertenece_id,$resultado['dp_calle_pertenece_id']->id_selected);
        errores::$error = false;
    }




}

