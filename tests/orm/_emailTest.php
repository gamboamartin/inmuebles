<?php
namespace gamboamartin\inmuebles\tests\models;

use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\_email;
use gamboamartin\inmuebles\models\inm_prospecto;
use gamboamartin\test\test;

use stdClass;

class _emailTest extends test {
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

    public function test_emisor(): void
    {
        errores::$error = false;

        $_GET['seccion'] = 'inm_prospecto';
        $_GET['accion'] = 'lista';
        $_SESSION['grupo_id'] = 1;
        $_SESSION['usuario_id'] = 2;
        $_GET['session_id'] = '1';

        $correo = "test";
        $resultado = (new _email(modelo: (new inm_prospecto($this->link))))->emisor(correo: $correo);
        $mensaje_error = sprintf(_email::ERROR_CORREO_NO_VALIDO, $correo);
        $this->assertEquals($resultado['mensaje_limpio'], $mensaje_error);

        errores::$error = false;

    }
}

