<?php

use base\conexion;
use gamboamartin\errores\errores;
use gamboamartin\inmuebles\models\inm_prospecto;

require "init.php";
require 'vendor/autoload.php';

$_SESSION['usuario_id'] = 2;
$_SESSION['session_id'] = mt_rand(10000000,99999999);
$_GET['session_id'] = $_SESSION['session_id'];

$con = new conexion();
$link = conexion::$link;

errores::$error = false;

$link->beginTransaction();

$inm_prospecto_modelo = new inm_prospecto(link: $link);

$regenera_nombre_completo_valida = $inm_prospecto_modelo->regenera_nombre_completo_valida();
if (errores::$error) {
    $link->rollBack();
    $error = (new errores())->error(mensaje: 'Error', data: $regenera_nombre_completo_valida);
    print_r($error);
    exit;
}
echo "<br><br>-----regenera_nombre_completo_valida-----<br><br>";
print_r($regenera_nombre_completo_valida);

$regenera_agentes_iniciales = $inm_prospecto_modelo->regenera_agentes_iniciales();

if (errores::$error) {
    $link->rollBack();
    $error = (new errores())->error(mensaje: 'Error', data: $regenera_agentes_iniciales);
    print_r($error);
    exit;
}
echo "<br><br>-----regenera_agentes_iniciales-----<br><br>";
print_r($regenera_agentes_iniciales);
$link->commit();

exit;

