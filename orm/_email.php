<?php

namespace gamboamartin\inmuebles\models;

use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\models\not_emisor;
use gamboamartin\notificaciones\models\not_receptor;
use PDO;
use stdClass;

class _email
{
    public const ERROR_CORREO_NO_VALIDO = "El correo '%s' no es válido.";
    public const ERROR_CORREO_NO_EXISTE = "El correo '%s' no es existe.";
    public const ERROR_FILTRO = "Error al filtrar al '%s'";
    public const ERROR_VALIDACION = "Error al validar el correo '%s'";
    public const ERROR_CORREO_NO_ENCONTRADO = "No se encontró el emisor con el correo '%s'";
    public const ERROR_AL_INSERTAR = "Error al insertar al '%s'";
    private PDO $link;

    public function __construct(PDO $link)
    {
        $this->link = $link;
    }

    public function correo_validacion(string $correo, modelo $modelo, string $campo): array|stdClass
    {
        $validacion = $this->validar_correo($correo);
        if (!$validacion) {
            $mensaje_error = sprintf(self::ERROR_CORREO_NO_VALIDO, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $correo);
        }

        $filtro = array();
        $filtro['email'] = $correo;
        $datos = $modelo->filtro_and(filtro: $filtro);
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_FILTRO, $campo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        return $datos;
    }

    public function emisor(string $correo): array
    {
        $datos = $this->correo_validacion(correo: $correo, modelo: (new not_emisor(link: $this->link)), campo: 'emisor');
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_VALIDACION, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        if ($datos->n_registros == 0) {
            $mensaje_error = sprintf(self::ERROR_CORREO_NO_EXISTE, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        return $datos->registros[0];
    }

    public function receptor(string $correo): array
    {
        $datos = $this->correo_validacion(correo: $correo, modelo: (new not_receptor(link: $this->link)), campo: 'receptor');
        if (errores::$error) {
            $mensaje_error = sprintf(self::ERROR_VALIDACION, $correo);
            return (new errores())->error(mensaje: $mensaje_error, data: $datos);
        }

        if ($datos->n_registros == 0) {
            $alta_not_receptor = (new not_receptor(link: $this->link))->alta_registro(
                array(
                    'email' => $correo,
                    'descripcion' => $correo,
                    'descripcion' => $correo,
                    'codigo' => $correo,
                ));
            if (errores::$error) {
                $mensaje_error = sprintf(self::ERROR_AL_INSERTAR, 'receptor');
                return (new errores())->error(mensaje: $mensaje_error, data: $alta_not_receptor);
            }

            return (new not_receptor(link: $this->link))->registro(registro_id: $alta_not_receptor->registro_id);
        }

        return $datos->registros[0];
    }

    public function validar_correo($correo): mixed
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

}