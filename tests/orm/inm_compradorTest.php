<?php
namespace gamboamartin\inmuebles\tests\controllers;


use gamboamartin\comercial\models\com_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\controllers\_keys_selects;
use gamboamartin\inmuebles\controllers\controlador_inm_attr_tipo_credito;
use gamboamartin\inmuebles\controllers\controlador_inm_comprador;
use gamboamartin\inmuebles\controllers\controlador_inm_plazo_credito_sc;
use gamboamartin\inmuebles\controllers\controlador_inm_producto_infonavit;
use gamboamartin\inmuebles\models\_inm_ubicaciones;
use gamboamartin\inmuebles\models\inm_comprador;
use gamboamartin\inmuebles\models\inm_rel_comprador_com_cliente;
use gamboamartin\inmuebles\models\inm_ubicacion;
use gamboamartin\inmuebles\tests\base_test;
use gamboamartin\test\liberator;
use gamboamartin\test\test;


use stdClass;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertStringContainsStringIgnoringCase;


class inm_compradorTest extends test {
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

    public function test_alta_bd(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }
        $del = (new base_test())->del_com_cliente(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);

        $inm->registro['nombre'] = 'Nombre';
        $inm->registro['apellido_paterno'] = 'Apellido Paterno';
        $inm->registro['nss'] = '12345678890';
        $inm->registro['curp'] = 'XEXX010101HNEXXXA4';
        $inm->registro['rfc'] = 'AAA010101AAA';
        $inm->registro['lada_nep'] = '111';
        $inm->registro['numero_nep'] = '2222222';
        $inm->registro['lada_com'] = '22';
        $inm->registro['numero_com'] = '55555555';
        $inm->registro['bn_cuenta_id'] = 1;
        $inm->registro['cel_com'] = '5577665544';
        $inm->registro['correo_com'] = 'a@alfa.com';
        $inm->registro['descuento_pension_alimenticia_dh'] = '0';
        $inm->registro['descuento_pension_alimenticia_fc'] = '0';
        $inm->registro['es_segundo_credito'] = 'SI';
        $inm->registro['inm_attr_tipo_credito_id'] = '1';
        $inm->registro['inm_destino_credito_id'] = '1';
        $inm->registro['inm_estado_civil_id'] = '1';
        $inm->registro['inm_producto_infonavit_id'] = '1';
        $inm->registro['monto_ahorro_voluntario'] = '1';
        $inm->registro['monto_credito_solicitado_dh'] = '1';
        $inm->registro['nombre_empresa_patron'] = '1';
        $inm->registro['nrp_nep'] = '1';
        $inm->registro['dp_calle_pertenece_id'] = '1';
        $inm->registro['numero_exterior'] = '1';
        $inm->registro['cat_sat_regimen_fiscal_id'] = '605';
        $inm->registro['cat_sat_moneda_id'] = '1';
        $inm->registro['cat_sat_forma_pago_id'] = '1';
        $inm->registro['cat_sat_metodo_pago_id'] = '1';
        $inm->registro['cat_sat_uso_cfdi_id'] = '1';
        $inm->registro['com_tipo_cliente_id'] = '1';
        $inm->registro['cat_sat_tipo_persona_id'] = '5';
        $inm->registro['inm_institucion_hipotecaria_id'] = '1';
        $resultado = $inm->alta_bd();
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Nombre Apellido Paterno  12345678890 XEXX010101HNEXXXA4 AAA010101AAA",$resultado->registro['inm_comprador_descripcion']);

        $inm_comprador_id = $resultado->registro_id;

        $filtro['inm_comprador.id'] = $inm_comprador_id;

        $r_inm_rel_comprador_com_cliente = (new inm_rel_comprador_com_cliente(link: $this->link))->filtro_and(filtro: $filtro);
        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$r_inm_rel_comprador_com_cliente->n_registros);

        $com_cliente_id = $r_inm_rel_comprador_com_cliente->registros[0]['com_cliente_id'];

        $com_cliente = (new com_cliente(link: $this->link))->registro(registro_id: $com_cliente_id);
        $this->assertIsArray($com_cliente);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals("Nombre Apellido Paterno", $com_cliente['com_cliente_razon_social']);
        $this->assertEquals("2147483647", $com_cliente['com_cliente_telefono']);

        errores::$error = false;
    }


    public function test_data_pdf(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }

        $del = (new base_test())->del_inm_conf_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->data_pdf($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener comprador",$resultado['mensaje_limpio']);
        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_rel_ubi_comp(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }
        $alta = (new base_test())->alta_inm_conf_empresa(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }


        $resultado = $inm->data_pdf($inm_comprador_id);

        $this->assertIsObject($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals(1,$resultado->inm_comprador['inm_comprador_id']);
        $this->assertEquals(1,$resultado->inm_comprador['inm_producto_infonavit_id']);
        $this->assertEquals("UBICACION ASIGNADA",$resultado->inm_comprador['inm_comprador_etapa']);

        errores::$error = false;
    }





    public function test_get_com_cliente(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_producto_infonavit';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $inm = new inm_comprador(link: $this->link);
        //$inm = new liberator($inm);


        $del = (new base_test())->del_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al eliminar', data: $del);
            print_r($error);exit;
        }


        $inm_comprador_id = 1;
        $resultado = $inm->get_com_cliente($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertTrue(errores::$error);
        $this->assertEquals("Error al obtener imp_rel_comprador_com_cliente",$resultado['mensaje_limpio']);

        errores::$error = false;

        $alta = (new base_test())->alta_inm_comprador(link: $this->link);
        if(errores::$error){
            $error = (new errores())->error(mensaje:'Error al insertar', data: $alta);
            print_r($error);exit;
        }

        $resultado = $inm->get_com_cliente($inm_comprador_id);
        $this->assertIsArray($resultado);
        $this->assertNotTrue(errores::$error);
        $this->assertEquals('MXN',$resultado['cat_sat_moneda_codigo_bis']);
        $this->assertEquals(151,$resultado['cat_sat_moneda_dp_pais_id']);

        errores::$error = false;
    }





}

