<?php

use base\conexion;
use gamboamartin\errores\errores;

$_SESSION['usuario_id'] = 2;

require "init.php";
require 'vendor/autoload.php';

$con = new conexion();
$link = conexion::$link;

$link->beginTransaction();
$administrador = new gamboamartin\administrador\instalacion\instalacion();

$instala = $administrador->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar administrador', data: $instala);
    print_r($error);
    exit;
}



$cat_sat = new gamboamartin\cat_sat\instalacion\instalacion(link: $link);

$instala = $cat_sat->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar cat_sat', data: $instala);
    print_r($error);
    exit;
}

$organigrama = new gamboamartin\organigrama\instalacion\instalacion();

$instala = $organigrama->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar organigrama', data: $instala);
    print_r($error);
    exit;
}


$comercial = new gamboamartin\comercial\instalacion\instalacion();

$instala = $comercial->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar comercial', data: $instala);
    print_r($error);
    exit;
}


$facturacion = new gamboamartin\facturacion\instalacion\instalacion();

$instala = $facturacion->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar facturacion', data: $instala);
    print_r($error);
    exit;
}


$inmuebles = new gamboamartin\inmuebles\instalacion\instalacion();

$instala = $inmuebles->instala(link: $link);
if(errores::$error){
    if($link->inTransaction()) {
        $link->rollBack();
    }
    $error = (new errores())->error(mensaje: 'Error al instalar inmuebles', data: $instala);
    print_r($error);
    exit;
}


if($link->inTransaction()) {
    $link->commit();
}