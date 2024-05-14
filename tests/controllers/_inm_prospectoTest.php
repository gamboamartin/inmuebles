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
    public function __construct(?string $name = null)
    {
        parent::__construct($name);
        $this->errores = new errores();
        $this->paths_conf = new stdClass();
        $this->paths_conf->generales = '/var/www/html/inmuebles/config/generales.php';
        $this->paths_conf->database = '/var/www/html/inmuebles/config/database.php';
        $this->paths_conf->views = '/var/www/html/inmuebles/config/views.php';
    }

    public function test_dato(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        //$_inm = new liberator($_inm);
        $_POST['a'] = array('z'=>'p');
        $existe = false;
        $key_data = 'a';
        $resultado = $_inm->dato($existe, $key_data);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertEquals('p',$resultado->row['z']);
        errores::$error = false;
    }

    public function test_datos_conyuge(): void
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

        $_inm = new _inm_prospecto();
        //$_inm = new liberator($_inm);

        $inm_prospecto_id = -1;
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertEmpty($resultado->row);
        $this->assertIsArray($resultado->row);
        $this->assertNotTrue($resultado->tiene_dato);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertEmpty($resultado->row);
        $this->assertIsArray($resultado->row);
        $this->assertNotTrue($resultado->tiene_dato);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $_POST['conyuge'] = array();
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertEmpty($resultado->row);
        $this->assertIsArray($resultado->row);
        $this->assertNotTrue($resultado->tiene_dato);

        errores::$error = false;

        $inm_prospecto_id = 1;
        $_POST['conyuge']['a'] = '';
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertNotEmpty($resultado->row);
        $this->assertIsArray($resultado->row);
        $this->assertNotTrue($resultado->tiene_dato);

        errores::$error = false;
        $inm_prospecto_id = 1;
        $_POST['conyuge']['a'] = 'x';
        $resultado = $_inm->datos_conyuge($this->link, $inm_prospecto_id);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertNotTrue($resultado->existe);
        $this->assertNotEmpty($resultado->row);
        $this->assertIsArray($resultado->row);
        $this->assertTrue($resultado->tiene_dato);

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

    public function test_headers_prospecto(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $resultado = $_inm->headers_prospecto();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('1. DATOS PERSONALES',$resultado[1]);
        $this->assertEquals('5. MONTO CREDITO',$resultado[5]);
        $this->assertEquals('10. REFERENCIAS',$resultado[10]);

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

    public function test_identificadores_dp(): void
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

        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $controlador = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $resultado = $_inm->identificadores_dp($controlador);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }

    public function test_identificadores_infonavit(): void
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
        $controlador->registro['inm_prospecto_es_segundo_credito'] = 'NO';

        $resultado = $_inm->identificadores_infonavit($controlador);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Institucion Hipotecaria",$resultado['inm_institucion_hipotecaria_id']['title']);
        $this->assertEquals(6,$resultado['inm_producto_infonavit_id']['cols']);
        $this->assertNotTrue($resultado['inm_attr_tipo_credito_id']['disabled']);
        $this->assertEquals("Destino de Credito",$resultado['inm_destino_credito_id']['title']);
        $this->assertEquals(6,$resultado['inm_tipo_discapacidad_id']['cols']);
        $this->assertNotTrue($resultado['inm_persona_discapacidad_id']['disabled']);
        $this->assertEquals("Plazo de Segundo Credito",$resultado['inm_plazo_credito_sc_id']['title']);
        errores::$error = false;
    }

    public function test_identificadores_personal(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $resultado = $_inm->identificadores_personal();
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Sindicato",$resultado['inm_sindicato_id']['title']);
        errores::$error = false;
    }

    public function test_init_post(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);
        $key_data = 'a';
        $_POST['a'] = array('');
        $resultado = $_inm->init_post($key_data);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("",$resultado[0]);
        errores::$error = false;
    }

    public function test_inputs_base(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        //$_inm = new liberator($_inm);
        $controlador = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $controlador->registro['com_agente_id'] = 1;
        $controlador->registro['com_tipo_prospecto_id'] = 1;
        $controlador->registro['inm_prospecto_es_segundo_credito'] = 'activo';
        $controlador->registro['com_medio_prospeccion_id'] = 100;
        $controlador->registro['com_prospecto_id'] = 1;
        $controlador->row_upd = new stdClass();
        $controlador->inputs = new stdClass();
        $resultado = $_inm->inputs_base($controlador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

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
        $controlador->registro['com_prospecto_id'] = 1;
        $controlador->registro['com_medio_prospeccion_id'] = 1;
        $keys_selects = array();

        $resultado = $_inm->integra_keys_selects_comercial($controlador, $keys_selects);
       // print_r($resultado);exit;
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
        $controlador->registro['com_prospecto_id'] = 1;
        $controlador->registro['com_medio_prospeccion_id'] = 1;
        $resultado = $_inm->keys_selects_comercial($controlador, $filtro, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['com_agente_id']->id_selected);
        $this->assertEquals(1,$resultado['com_tipo_prospecto_id']->id_selected);
        errores::$error = false;
    }

    public function test_keys_selects_dp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);

        $keys_selects = array();
        $controlador = new controlador_inm_prospecto(link: $this->link, paths_conf: $this->paths_conf);
        $resultado = $_inm->keys_selects_dp($controlador, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_keys_selects_infonavit(): void
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
        $controlador->registro['inm_prospecto_es_segundo_credito'] = 'SI';
        $controlador->registro['com_medio_prospeccion_id'] = '100';
        $controlador->registro['com_prospecto_id'] = '1';
        $keys_selects = array();
        $resultado = $_inm->keys_selects_infonavit($controlador, $keys_selects);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(12,$resultado['com_agente_id']->cols);
        $this->assertTrue($resultado['com_tipo_prospecto_id']->con_registros);
        $this->assertEquals("Institucion Hipotecaria",$resultado['inm_institucion_hipotecaria_id']->label);
        $this->assertEquals(-1,$resultado['inm_producto_infonavit_id']->id_selected);
        $this->assertEmpty($resultado['inm_attr_tipo_credito_id']->filtro);
        $this->assertCount(1,$resultado['inm_destino_credito_id']->columns_ds);
        $this->assertNotTrue($resultado['inm_tipo_discapacidad_id']->disabled);
        $this->assertEquals(6,$resultado['inm_persona_discapacidad_id']->cols);
        $this->assertTrue($resultado['inm_plazo_credito_sc_id']->con_registros);

        errores::$error = false;
    }

    public function test_keys_selects_personal(): void
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
        $keys_selects = array();
        $resultado = $_inm->keys_selects_personal($controlador, $keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(12,$resultado['inm_sindicato_id']->cols);
        errores::$error = false;
    }

    public function test_row_base_fiscal(): void
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
        $controlador->row_upd = new stdClass();
        $resultado = $_inm->row_base_fiscal($controlador);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("99999999999",$controlador->row_upd->nss);
        $this->assertEquals("XEXX010101HNEXXXA4",$controlador->row_upd->curp);
        $this->assertEquals("XAXX010101000",$controlador->row_upd->rfc);
        errores::$error = false;
    }

    public function test_tiene_dato(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';


        $_inm = new _inm_prospecto();
        $_inm = new liberator($_inm);
        $row = array();
        $row[] = 'a';
        $resultado = $_inm->tiene_dato($row);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }

    public function test_valida_base(): void
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
        $controlador->registro['inm_prospecto_es_segundo_credito'] = 1;
        $resultado = $_inm->valida_base($controlador);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado);
        errores::$error = false;
    }


}

