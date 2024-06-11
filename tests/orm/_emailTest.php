<?php

namespace gamboamartin\inmuebles\tests\models;

use gamboamartin\comercial\models\com_tipo_cliente;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_email;
use gamboamartin\notificaciones\models\not_emisor;
use gamboamartin\test\test;

use stdClass;

class _emailTest extends test
{
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

    public function test_correo_validacion(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_prospecto';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $correo = "test";
        $resultado = (new _email(link: $this->link))
            ->correo_validacion(correo: $correo,
                modelo: (new not_emisor($this->link)),
                campo: 'emisor');
        $mensaje_error = sprintf(_email::ERROR_CORREO_NO_VALIDO, $correo);
        $this->assertEquals($resultado['mensaje_limpio'], $mensaje_error);
        errores::$error = false;

        $correo = "xxxxx@ivitec.mx";
        $resultado = (new _email(link: $this->link))
            ->correo_validacion(correo: $correo,
                modelo: (new com_tipo_cliente($this->link)),
                campo: 'emisor');
        $mensaje_error = sprintf(_email::ERROR_FILTRO, 'emisor');
        $this->assertEquals($resultado['mensaje_limpio'], $mensaje_error);
        errores::$error = false;
    }

    public function test_emisor(): array
    {
        $this->test_correo_validacion();

        $correo = "xxxxx@ivitec.mx";
        $resultado = (new _email(link: $this->link))->emisor(correo: $correo);
        $mensaje_error = sprintf(_email::ERROR_CORREO_NO_EXISTE, $correo);
        $this->assertEquals($resultado['mensaje_limpio'], $mensaje_error);
        errores::$error = false;

        $correo = "test@ivitec.mx";
        $resultado = (new _email(link: $this->link))->emisor(correo: $correo);
        $this->assertIsArray($resultado);
        $this->assertGreaterThan(0, $resultado);
        errores::$error = false;

        return $resultado;
    }

    public function test_receptor(): array
    {
        $this->test_correo_validacion();

        $correo = "test@ivitec.mx";
        $resultado = (new _email(link: $this->link))->receptor(correo: $correo);
        $this->assertIsArray($resultado);
        $this->assertGreaterThan(0, $resultado);
        errores::$error = false;

        return $resultado;
    }

    public function test_mensaje(): array
    {
        $emisor = $this->test_emisor();

        $asunto = "asunto";
        $mensaje = "mensaje";
        $emisor = $emisor['not_emisor_id'];
        $resultado = (new _email(link: $this->link))->mensaje(asunto: $asunto, mensaje: $mensaje, emisor: $emisor);
        $this->assertIsArray($resultado);
        errores::$error = false;

        return $resultado;
    }

    public function test_mensaje_receptor(): void
    {
        $mensaje = $this->test_mensaje();
        $receptor = $this->test_receptor();

        $mensaje = $mensaje['not_mensaje_id'];
        $receptor = $receptor['not_receptor_id'];
        $resultado = (new _email(link: $this->link))->mensaje_receptor(mensaje: $mensaje, receptor: $receptor);
        $this->assertIsArray($resultado);
        errores::$error = false;
    }
}

