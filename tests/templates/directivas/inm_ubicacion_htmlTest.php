<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\controlador_inm_ubicacion;
use gamboamartin\inmuebles\html\inm_ubicacion_html;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;


class inm_ubicacion_htmlTest extends test {
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

    public function test_ajusta_registros(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);


        $registros = array();
        $acciones_grupo = array();
        $arreglo_costos = array();
        $row = array();
        $row['inm_costo_id'] = 1;
        $arreglo_costos['registros']['d'][] = array();
        $key = 'd';

        $resultado = $html->ajusta_registros($acciones_grupo, $arreglo_costos, $key, array(), $registros, $row);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_base_costos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $funcion = 'a';
        $resultado = $html->base_costos($controler, $funcion, array());
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_base_inm_ubicacion_upd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $resultado = $html->base_inm_ubicacion_upd($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_columnas_dp(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        //$controler->inputs = new stdClass();
        $keys_selects = array();
        $registro = new stdClass();
        $registro->dp_pais_id = 1;
        $registro->dp_estado_id = 1;
        $registro->dp_municipio_id = 1;
        $registro->dp_colonia_postal_id = 1;
        $registro->dp_cp_id = 1;
        $registro->dp_calle_pertenece_id = 1;
        $resultado = $html->columnas_dp($controler, $keys_selects, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado['dp_pais_id']->id_selected);
        errores::$error = false;
    }

    public function test_data_comprador(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();
        $resultado = $html->data_comprador($controler);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_data_form(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $funcion = 'a';
        $controler->inputs = new stdClass();
        $controler->inputs->dp_estado_id = 1;
        $controler->inputs->dp_municipio_id = 1;
        $controler->inputs->dp_cp_id = 1;
        $controler->inputs->dp_colonia_postal_id = 1;
        $controler->inputs->dp_calle_pertenece_id = 1;
        $controler->inputs->numero_exterior = 1;
        $controler->inputs->numero_interior = 1;
        $controler->inputs->manzana = 1;
        $controler->inputs->lote = 1;
        $resultado = $html->data_form($controler, $funcion);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

    }

    public function test_form_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $controler->inputs = new stdClass();
        $controler->inputs->dp_estado_id = 'a';
        $controler->inputs->dp_municipio_id = 'b';
        $controler->inputs->dp_cp_id = 'c';
        $controler->inputs->dp_colonia_postal_id = 'd';
        $controler->inputs->dp_calle_pertenece_id = 'e';
        $controler->inputs->numero_exterior = 'f';
        $controler->inputs->numero_interior = 'g';
        $controler->inputs->manzana = 'h';
        $controler->inputs->lote = 'i';
        $controler->inputs->inm_ubicacion_id = 'j';
        $controler->inputs->seccion_retorno = 'k';
        $controler->inputs->btn_action_next = 'l';
        $controler->inputs->id_retorno = 'm';
        $resultado = $html->form_ubicacion(controlador: $controler);
        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("abcdefghijklm",$resultado);

        errores::$error = false;
    }

    public function test_format_moneda_mx(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $monto = '';
        $resultado = $html->format_moneda_mx(monto: $monto);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error monto no puede ser vacio",$resultado['mensaje_limpio']);

        errores::$error = false;

        $monto = '1.1';
        $resultado = $html->format_moneda_mx(monto: $monto);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("$1.10",$resultado);

        errores::$error = false;
    }
    public function test_format_moneda_mx_arreglo(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $registros = array();
        $registros[] = array();
        $indice = '';
        $resultado = $html->format_moneda_mx_arreglo(registros: $registros, campo_integrar: $indice);

        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error no existe indice de arreglo",$resultado['mensaje_limpio']);

        errores::$error = false;

        $registros = array();
        $registros[] = array('x'=>'1');
        $indice = 'x';
        $resultado = $html->format_moneda_mx_arreglo(registros: $registros, campo_integrar: $indice);

        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("$1.00",$resultado[0]['x']);

        errores::$error = false;
    }

    public function test_key_select_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $registro = new stdClass();
        $registro->dp_pais_id = 1;
        $registro->dp_estado_id = 1;
        $registro->dp_municipio_id = 1;
        $registro->dp_cp_id = 1;
        $registro->dp_colonia_postal_id = 1;
        $registro->dp_calle_pertenece_id = 1;

        $resultado = $html->key_select_ubicacion($controler, $registro);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertTrue($resultado['lote']->disabled);

        errores::$error = false;
    }

    public function test_keys_select_dom(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $keys_selects = array();
        $resultado = $html->keys_select_dom($keys_selects);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_init_costos(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $controler->registro_id = 1;
        $resultado = $html->init_costos($controler, array());
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;

    }

    public function test_inputs_base_ubicacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $controler = new controlador_inm_ubicacion(link: $this->link, paths_conf: $this->paths_conf);
        $funcion = 'a';
        $controler->inputs = new stdClass();
        $resultado = $html->inputs_base_ubicacion($controler, $funcion);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("<input type='hidden' name='id_retorno' value='-1'>",$resultado->id_retorno);
        errores::$error = false;
    }

    public function test_integra_link(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $arreglo_costos = array();
        $row = array();
        $row['inm_costo_id'] = 1;
        $arreglo_costos['registros'] = array();
        $key = 'a';
        $adm_accion_grupo = array();
        $links = array();
        $arreglo_costos['registros']['a'][] = array();

        $adm_accion_grupo['adm_accion_css'] = 'danger';
        $adm_accion_grupo['adm_accion_es_status'] = 'inactivo';
        $adm_accion_grupo['adm_accion_descripcion'] = 'inactivo';
        $adm_accion_grupo['adm_seccion_descripcion'] = 'inactivo';
        $adm_accion_grupo['adm_accion_muestra_icono_btn'] = 'inactivo';
        $adm_accion_grupo['adm_accion_muestra_titulo_btn'] = 'activo';
        $adm_accion_grupo['adm_accion_titulo'] = 'activo';

        $resultado = $html->integra_link($adm_accion_grupo, $arreglo_costos, $key, $links, array(), $row);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("<a role='button' title='activo' href='index.php?seccion=inactivo&accion=inactivo&registro_id=1&session_id=1&adm_menu_id=-1' class='btn btn-danger ' style='margin-left: 2px; margin-bottom: 2px; '>activo</a>",$resultado['inactivo']);
        errores::$error = false;
    }

    public function test_links(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $acciones_grupo = array();
        $arreglo_costos = array();
        $key = 'a';
        $row = array();

        $acciones_grupo[] = array();
        $row['inm_costo_id'] = 1;
        $arreglo_costos['registros']['a'][] = '';

        $acciones_grupo[0]['adm_accion_css'] = 'light';
        $acciones_grupo[0]['adm_accion_es_status'] = 'inactivo';
        $acciones_grupo[0]['adm_accion_descripcion'] = 'a';
        $acciones_grupo[0]['adm_seccion_descripcion'] = 'a';
        $acciones_grupo[0]['adm_accion_muestra_icono_btn'] = 'inactivo';
        $acciones_grupo[0]['adm_accion_muestra_titulo_btn'] = 'activo';
        $acciones_grupo[0]['adm_accion_titulo'] = 'activo';

        $resultado = $html->links($acciones_grupo, $arreglo_costos, $key, array(), $row);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("<a role='button' title='activo' href='index.php?seccion=a&accion=a&registro_id=1&session_id=1&adm_menu_id=-1' class='btn btn-light ' style='margin-left: 2px; margin-bottom: 2px; '>activo</a>",$resultado['a']);
        errores::$error = false;
    }

    public function test_params_get_data(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$html = new liberator($html);


        $accion_retorno = 'a';
        $id_retorno = 1;
        $seccion_retorno = 'c';

        $resultado = $html->params_get_data($accion_retorno, $id_retorno, $seccion_retorno);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('c',$resultado['seccion_retorno']);
        $this->assertEquals('a',$resultado['accion_retorno']);
        $this->assertEquals('1',$resultado['id_retorno']);
        errores::$error = false;
    }

    public function test_registros(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $acciones_grupo = array();
        $r_inm_costos = new stdClass();
        $r_inm_costos->registros = array();

        $resultado = $html->registros($acciones_grupo, array(), $r_inm_costos);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }

    public function test_registros_con_link(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);


        $acciones_grupo = array();
        $r_inm_costos = new stdClass();
        $r_inm_costos->registros = array();
        $resultado = $html->registros_con_link($acciones_grupo, array(), $r_inm_costos);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        errores::$error = false;
    }


    public function test_select_inm_ubicacion_id(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        //$_inm = new liberator($_inm);

        $cols = 2;
        $con_registros = true;
        $id_selected = -1;
        $link = $this->link;
        $resultado = $html->select_inm_ubicacion_id($cols, $con_registros, $id_selected, $link);

        $this->assertIsString($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertStringContainsStringIgnoringCase("y inm_ubicacion_id ' data-live-search='true' id='inm_ubicacion_id' name='inm_ubicacion_id' required ><option",$resultado);
        errores::$error = false;
    }

    public function test_valida_data_link(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $html_ = new \gamboamartin\template_1\html();
        $html = new inm_ubicacion_html($html_);
        $html = new liberator($html);

        $arreglo_costos = array();
        $key = '';
        $row = array();
        $row['inm_costo_id'] = 1;
        $arreglo_costos['registros'] = array();
        $arreglo_costos['registros']['z'][] = array();
        $key = 'z';



        $resultado = $html->valida_data_link($arreglo_costos, $key, $row);
        $this->assertIsBool($resultado);
        $this->assertNotTrue(errores::$error);

        errores::$error = false;
    }





}

