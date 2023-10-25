<?php
namespace controllers;


use config\generales;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_inm_prospecto;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\_pdf;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\controllers\controlador_inm_prospecto;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use setasign\Fpdi\Fpdi;
use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class _inm_prospectoTest extends test {
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

    public function test_datos_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        //$_inm = new liberator($_inm);

        $inm_prospecto_id = -1;
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe_conyuge);
        $this->assertEmpty($resultado->conyuge);
        $this->assertIsArray($resultado->conyuge);
        $this->assertNotTrue($resultado->tiene_dato_conyuge);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe_conyuge);
        $this->assertEmpty($resultado->conyuge);
        $this->assertIsArray($resultado->conyuge);
        $this->assertNotTrue($resultado->tiene_dato_conyuge);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $_POST['conyuge'] = array();
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe_conyuge);
        $this->assertEmpty($resultado->conyuge);
        $this->assertIsArray($resultado->conyuge);
        $this->assertNotTrue($resultado->tiene_dato_conyuge);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $_POST['conyuge']['a'] = '';
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe_conyuge);
        $this->assertNotEmpty($resultado->conyuge);
        $this->assertIsArray($resultado->conyuge);
        $this->assertNotTrue($resultado->tiene_dato_conyuge);

        errores::$error = false;
        $inm_prospecto_id = 1;
        $_POST['conyuge']['a'] = 'x';
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe_conyuge);
        $this->assertNotEmpty($resultado->conyuge);
        $this->assertIsArray($resultado->conyuge);
        $this->assertTrue($resultado->tiene_dato_conyuge);

        errores::$error = false;
    }

    public function test_disabled_segundo_credito(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);
        $registro = array();
        $registro['inm_prospecto_es_segundo_credito'] = 'NO';
        $resultado = $_inm->disabled_segundo_credito($registro);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);

        errores::$error = false;

        $registro = array();
        $registro['inm_prospecto_es_segundo_credito'] = 'SI';
        $resultado = $_inm->disabled_segundo_credito($registro);

        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);

        errores::$error = false;
    }

    public function test_filtro_user(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();

        $_inm = new liberator($_inm);

        $adm_usuario = array();
        $adm_usuario['adm_grupo_root'] = 'activo';
        $resultado = $_inm->filtro_user($adm_usuario);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);

        errores::$error = false;

        $adm_usuario = array();
        $adm_usuario['adm_grupo_root'] = 'inactivo';
        $resultado = $_inm->filtro_user($adm_usuario);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(2,$resultado['adm_usuario.id']);
        errores::$error = false;
    }

    public function test_genera_filtro_user(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $resultado = $_inm->genera_filtro_user(link: $this->link);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
        errores::$error = false;
    }

    public function test_genera_keys_selects(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $controlador = new controlador_inm_prospecto(link: $this->link,paths_conf: $this->paths_conf);
        $identificadores = array();
        $keys_selects = array();
        $identificadores[] = array();
        $resultado = $_inm->genera_keys_selects($controlador, $identificadores, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_identificadores_comercial(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $filtro = array();
        $resultado = $_inm->identificadores_comercial($filtro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('Agente',$resultado['com_agente_id']['title']);
        $this->assertEquals(12,$resultado['com_tipo_prospecto_id']['cols']);
        errores::$error = false;
    }

    public function test_init_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $resultado = $_inm->init_conyuge();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEmpty($resultado);
        errores::$error = false;
    }

    public function test_integra_keys_selects_comercial(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);
        $controlador = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $controlador->registro['com_agente_id'] = 1;
        $controlador->registro['com_tipo_prospecto_id'] = 1;
        $keys_selects = array();

        $resultado = $_inm->integra_keys_selects_comercial($controlador, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_agente_id']->id_selected);
        $this->assertEquals(1,$resultado['com_tipo_prospecto_id']->id_selected);
        errores::$error = false;
    }

    public function test_keys_selects_comercial(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $controlador = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $filtro = array();
        $keys_selects = array();
        $controlador->registro['com_agente_id'] = 1;
        $controlador->registro['com_tipo_prospecto_id'] = 1;
        $resultado = $_inm->keys_selects_comercial($controlador, $filtro, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_agente_id']->id_selected);
        $this->assertEquals(1,$resultado['com_tipo_prospecto_id']->id_selected);
        errores::$error = false;
    }

    public function test_tiene_dato_conyuge(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);
        $conyuge = array();
        $resultado = $_inm->tiene_dato_conyuge($conyuge);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);
        errores::$error = false;

        $conyuge = array();
        $conyuge[] = '';
        $resultado = $_inm->tiene_dato_conyuge($conyuge);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado);
        errores::$error = false;

        $conyuge = array();
        $conyuge[] = 'a';
        $resultado = $_inm->tiene_dato_conyuge($conyuge);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }
}

