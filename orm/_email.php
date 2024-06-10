<?php

namespace gamboamartin\inmuebles\models;

use base\orm\modelo;
use gamboamartin\errores\errores;
use gamboamartin\notificaciones\models\not_emisor;
use gamboamartin\notificaciones\models\not_receptor;
use stdClass;

class _email
{
    public modelo $modelo;

    public const ERROR_CORREO_NO_VALIDO = "El correo '%s' no es válido.";

    public function __construct(modelo $modelo)
    {
        $this->modelo = $modelo;
    }

    public function emisor(string $correo): array
    {
        $validacion = $this->validar_correo($correo);
        if (!$validacion) {
            $mensaje_error = sprintf(self::ERROR_CORREO_NO_VALIDO, $correo);
            return $this->modelo->error->error(mensaje: $mensaje_error, data: $correo);
        }

        $filtro = array();
        $filtro['email'] = $correo;
        $not_emisor = (new not_emisor(link: $this->modelo->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->modelo->error->error(mensaje: "Error al filtrar al emisor", data: $not_emisor);
        }

        if ($not_emisor->nro_regisros == 0) {
            return $this->modelo->error->error(mensaje: "No se encontró el emisor con el correo '$correo'",
                data: $not_emisor);
        }

        return $not_emisor->registros[0]['not_emisor_id'];
    }

    public function receptor(string $correo): array
    {
        $validacion = $this->validar_correo($correo);
        if (!$validacion) {
            return $this->modelo->error->error(mensaje: "El correo '$correo' no es válido.", data: $correo);
        }

        $filtro = array();
        $filtro['email'] = $correo;
        $not_receptor = (new not_receptor(link: $this->modelo->link))->filtro_and(filtro: $filtro);
        if (errores::$error) {
            return $this->modelo->error->error(mensaje: "Error al filtrar al emisor", data: $not_receptor);
        }

        if ($not_receptor->nro_regisros == 0) {
            $alta_not_receptor = (new not_receptor(link: $this->modelo->link))->alta_registro(
                array(
                    'email' => $correo,
                    'descripcion' => $correo,
                    'descripcion' => $correo,
                    'codigo' => $correo,
                ));
            if (errores::$error) {
                return $this->modelo->error->error(mensaje: "Error al insertar al receptor", data: $alta_not_receptor);
            }

            return $alta_not_receptor->registro_id;
        }

        return $not_receptor->registros[0]['not_receptor_id'];
    }

    public function validar_correo($correo): mixed
    {
        return filter_var($correo, FILTER_VALIDATE_EMAIL);
    }

}